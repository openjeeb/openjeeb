<?php

//App::import('Component', 'Sms');
App::import('Component', 'SmsKave');
App::import('Component', 'Email');
App::import('Vendor', 'PersianLib', array('file' => 'persian.lib.php'));

require_once APP . '/vendors/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/*
 * Notices:
 *
    *
    *  **** command to run rabbitmq Consumers for reminders:
    *
    *      1) php cake.php rmqc_reminder smsReminders [-dry-run | -fail]
    *
    *      2) php cake.php rmqc_reminder emailReminders [-dry-run | -fail]
    *
 *     **** command for sending other emails
 *
 *          php cake.php rmqc_reminder jeebEmail
    */


/*
 * *** Rabbitmq Consumer Reminder Shell
 */

class RmqcReminderShell extends Shell
{

    public $uses = array('Reminder', 'ReminderLog', 'ServiceTransaction', 'SmsRead', 'User', 'Note', 'Installment', 'Debt', 'Check', 'Config');
    public $components = array('Sms', 'Email');
    public $tasks = array('SendMail', 'SendMailElastic');

    public $dryRun;
    function main()
    {
        print 1;
    }

    private function getRabbitConnection()
    {
        Configure::load("rabbitmq");
        $connection = new AMQPStreamConnection(Configure::read('Rabbitmq.host'), Configure::read('Rabbitmq.port'), Configure::read('Rabbitmq.user'), Configure::read('Rabbitmq.pass'));
        return $connection;
    }

    /*
     *  sms reminder functions
     */
    public function smsReminders()
    {
        $this->dryRun = false;
        if (!empty($this->params["dry-run"]) || !empty($this->params["-dry-run"]) )
            $this->dryRun = true;
        print 'rabbitmq sms worker reminder start with pid:' . getmypid() . "\n";
        $queue_name = "reminders_sms";
        $queue_name_retry = "reminders_sms_retry";
        $exchange = "reminders";
        $retryExchange = "reminder_sms_retry_exchange";
        $routing_key = "sms";
        $xMessageTtl = 60000;
        try {
            // get rabbit mq instance
            $connection = $this->getRabbitConnection();
            $channel = $connection->channel();

            // declare reminder exchange
            $channel->exchange_declare($exchange, 'direct', false, true);

            // declare sms queue
            $channel->queue_declare($queue_name, false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable([
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => $queue_name_retry
            ]));
            $channel->queue_bind($queue_name, $exchange, $routing_key);


            //declare retry exchange
            $channel->exchange_declare($retryExchange, 'direct', false, true);
            //declare the retry queue

            $channel->queue_declare($queue_name_retry, false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable([
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => $queue_name,
                'x-message-ttl' => $xMessageTtl
            ]));
            //bind
            $channel->queue_bind($queue_name_retry, $retryExchange);

            //fair dispatch
            $channel->basic_qos(null, 1, null);


            $callback = [$this, 'smsWorker'];
            $channel->basic_consume($queue_name, '', false, false, false, false, $callback);

            while (count($channel->callbacks)) {
                $channel->wait();
            }
            $channel->close();
            $connection->close();

        } catch (Exception $exc) {
            sleep(3);
            echo 'Consume Exception: ' . $exc->getMessage() . ' ' . $exc->getTraceAsString();
            return false;
        }

        print 'rabbitmq sms worker reminder end with pid:' . getmypid() . "\n";
    }

    public function smsWorker($msg)
    {

        if($this->dryRun == false){
            $result = $this->sendSMS($msg->body);
        }
        else{
            if(!empty($this->params['fail']) || !empty($this->params['-fail']))
                $result = ['status' => 'fail', 'failed' => 'fail'];
            else
                $result =  ['status' => 'success', 'result' => 'success'];
        }


        if (!is_array($result) || empty($result)) {
            echo "NACK!! \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
            return 0;
        }

        if ($result['status'] == 'fail') {
            echo "NACK! \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
            return false;
        } elseif ($result['status'] == 'success') {
            echo "Sent \n";
            //send delivery acknowledge to rabbitmq to remove this message from the queue
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        } else {
            //unknown error
            echo "NACK! \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
        }

    }

    public function sendSMS($msg)
    {
        $this->Sms = new SmsKaveComponent();
        $this->date = new PersianDate();
        $this->Sms->initialize($this);

        $data = json_decode($msg, true);

        $result = $this->_sendPendingsSms($data);

        return $result;
    }

    private function _sendPendingsSms($data)
    {
        $temp = $data['ReminderLog'];
        $list = [
            "id" => $temp["id"],
            "mobile" => $temp["cell"],
            "message" => $temp['textsms']
        ];
        $id = $data['ReminderLog']['id'];
        //$this->ReminderLog->unbindModelAll();
        $this->ReminderLog->updateAll(
            array('ReminderLog.sms_status' => '"sending"', 'senddate' => 'CURRENT_TIMESTAMP'),
            array("ReminderLog.id " => $id)
        );


        $result = $this->Sms->sendOne($list['mobile'],$list['message']);
        if (empty($result) || empty($result["messageid"])) {
            $this->ReminderLog->updateAll(
                array('ReminderLog.sms_status' => '"failed"'),
                array("ReminderLog.id " => $id)
            );
            return ['status' => 'fail', 'failed' => $id];
        }

        $this->ReminderLog->updateAll(
            array('ReminderLog.sms_status' => '"sent"','ReminderLog.identifier'=>$result['messageid']),
            array("ReminderLog.id " => $id)
        );
        $this->out("\n ".$list['mobile']."sms reminder for reminderlog with id: ".$id." sent. identefier is: ".$result['messageid']."\n");


        return ['status' => 'success', 'result' => $result];
    }


    /*
     *  email reminders functions
     *
     */
    public function emailReminders()
    {
        $this->dryRun = false;
        if (!empty($this->params["dry-run"]) || !empty($this->params["-dry-run"]) )
            $this->dryRun = true;

        print 'rabbitmq email worker reminder start with pid:' . getmypid() . "\n";
        $queue_name = "reminders_email";
        $queue_name_retry = "reminders_email_retry";
        $exchange = "reminders";
        $retryExchange = "reminder_email_retry_exchange";
        $routing_key = "email";
        $xMessageTtl = 60000;
        try {
            /// get rabbit mq instance
            $connection = $this->getRabbitConnection();
            $channel = $connection->channel();

            $channel->exchange_declare($exchange, 'direct', false, true);

            $channel->queue_declare($queue_name, false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable([
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => $queue_name_retry
            ]));
            $channel->queue_bind($queue_name, $exchange, $routing_key);

            //declare retry exchange
            $channel->exchange_declare($retryExchange, 'direct', false, true);
            //declare the retry queue
            $channel->queue_declare($queue_name_retry, false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable([
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => $queue_name,
                'x-message-ttl' => $xMessageTtl
            ]));
            //bind
            $channel->queue_bind($queue_name_retry, $retryExchange);

            //fair dispatch
            $channel->basic_qos(null, 1, null);

            $callback = [$this, 'emailWorker'];
            $channel->basic_consume($queue_name, '', false, false, false, false, $callback);

            while (count($channel->callbacks)) {
                $channel->wait();
            }
            $channel->close();
            $connection->close();

        } catch (Exception $exc) {
            sleep(3);
            echo 'Consume Exception: ' . $exc->getMessage() . ' ' . $exc->getTraceAsString();
            return false;
        }

        print 'rabbitmq email worker reminder end with pid:' . getmypid() . "\n";
    }

    public function emailWorker($msg)
    {
        if($this->dryRun == false){
            $result = $this->sendEmail($msg->body);
        }
        else{
            if(!empty($this->params['fail']) || !empty($this->params['-fail']))
                $result = ['status' => 'fail', 'failed' => 'fail'];
            else
                $result =  ['status' => 'success', 'result' => 'success'];
        }

        if (!is_array($result) || empty($result)) {
            echo "NACK!! \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
            return 0;
        }

        if ($result['status'] == 'fail') {
            print_r($result);
            echo "NACK! \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
            return false;
        } elseif ($result['status'] == 'success') {
            echo "Sent \n";
            //send delivery acknowledge to rabbitmq to remove this message from the queue
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        } else {
            //unknown error
            echo "NACK! \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
        }

    }

    public function sendEmail($msg)
    {

        $data = json_decode($msg, true);

        $result = $this->_sendPendingsEmail($data);
        return $result;
    }

    public function _sendPendingsEmail($data)
    {
        $temp = $data['ReminderLog'];
        $list = [
            "id" => $temp["id"],
            "email" => $temp["email"],
            "message" => $temp['textemail'],
            "subject" => $temp["subject"],
            "user_id" => $temp['user_id']
        ];
        $id = $data['ReminderLog']['id'];
        //$this->ReminderLog->unbindModelAll();
        $this->ReminderLog->updateAll(
            array('ReminderLog.email_status' => '"sending"', 'ReminderLog.senddate' => 'CURRENT_TIMESTAMP'),
            array("ReminderLog.id " => $id)
        );

        $this->User->ownData = false;
        $this->User->recursive = -1;
        $user = $this->User->find('first', [
            'conditions' => [
                'User.id' => $list['user_id']
            ]
        ]);

        $this->out('User notification for  ReminderLog.id = ' . $list['id'] . ", userid". $list['user_id'] . ': ' . $user['User']['notifications']);

        $success = false;
        $failed = false;
        if ($user['User']['notifications'] == 'no') {
            $success = true;
        } else {
            if ($this->SendMailElastic->execute($list['email'], $list['subject'], $list['message'])) {
                $success = true;
            } else {
                $failed = true;
            }
        }

        if ($success) {
            $this->ReminderLog->updateAll(
                array('ReminderLog.email_status' => '"sent"', 'ReminderLog.senddate' => 'CURRENT_TIMESTAMP'),
                array("ReminderLog.id " => $id)
            );
            $this->out('Email sent to  ReminderLog.id = ' . $list['id'] . ': ' . $user['User']['notifications']);
            return ['status' => "success"];
        }

        if ($failed) {
            $this->ReminderLog->updateAll(
                array('ReminderLog.email_status' => '"failed"', 'ReminderLog.senddate' => 'CURRENT_TIMESTAMP'),
                array("ReminderLog.id " => $id)
            );

            return ['status' => "fail"];
        }
    }

    /**
     *  This function create jeeb_email queue and manage it.
     *  jeeb_email queue used for send all site emails except Reminder emails.
     *  Reminder emails' count is huge, so we used different queue for reminder emails.
     *
     * @return bool
     */
    public function jeebEmail()
    {
        $queue_name = "jeeb_email";
        $queue_name_retry = "jeeb_email_retry";
        $exchange = "jeeb_email";
        $retryExchange = "jeeb_email_retry_exchange";
        $routing_key = "jeeb_email";
        $xMessageTtl = 60000;
        print 'rabbitmq jeeb_email worker start with pid:' . getmypid() . "\n";
        try {
            /// get rabbit mq instance
            $connection = $this->getRabbitConnection();
            $channel = $connection->channel();

            $channel->exchange_declare($exchange, 'direct', false, true);

            $channel->queue_declare($queue_name, false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable([
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => $queue_name_retry
            ]));
            $channel->queue_bind($queue_name, $exchange, $routing_key);

            //declare retry exchange
            $channel->exchange_declare($retryExchange, 'direct', false, true);
            //declare the retry queue
            $channel->queue_declare($queue_name_retry, false, true, false, false, false, new \PhpAmqpLib\Wire\AMQPTable([
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => $queue_name,
                'x-message-ttl' => $xMessageTtl
            ]));
            //bind
            $channel->queue_bind($queue_name_retry, $retryExchange);

            //fair dispatch
            $channel->basic_qos(null, 1, null);

            $callback = [$this, 'jeebEmailWorker'];
            $channel->basic_consume($queue_name, '', false, false, false, false, $callback);

            while (count($channel->callbacks)) {
                $channel->wait();
            }
            $channel->close();
            $connection->close();

        } catch (Exception $exc) {
            sleep(3);
            echo 'Consume Exception: ' . $exc->getMessage() . ' ' . $exc->getTraceAsString();
            return false;
        }

        print 'rabbitmq jeeb_email worker end with pid:' . getmypid() . "\n";
    }
    public function jeebEmailWorker($msg){
        $result = $this->sendJeebEmail($msg->body);
        if (!is_array($result) || empty($result)) {
            echo "NACK!! \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
            return 0;
        }

        if ($result['status'] == 'fail') {
            print_r($result);
            echo "NACK! \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
            return false;
        } elseif ($result['status'] == 'success') {
            echo "Sent \n";
            //send delivery acknowledge to rabbitmq to remove this message from the queue
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        } else {
            //unknown error
            echo "NACK! \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
        }
    }
    public function sendJeebEmail($msg){
        $data = json_decode($msg, true);
        $result = $this->_sendJeebEmail($data);
        return $result;
    }
    public function _sendJeebEmail($list)
    {
        $success = false;
        $failed = false;
        if ($this->SendMailElastic->sendByTemplate($list['to'], $list['subject'], $list['params'],$list['layout'],$list['template'],$list['options'])) {
            $success = true;
        } else {
            $failed = true;
        }

        if ($success) {
            return ['status' => "success"];
        }

        if ($failed) {
            return ['status' => "fail"];
        }
    }

}
