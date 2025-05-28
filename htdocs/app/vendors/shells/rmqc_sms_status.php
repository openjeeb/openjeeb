<?php

App::import('Component', 'Sms');
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
    *      1) php cake.php rmqc_sms_status smsCheckStatus [-dry-run | -fail]
    *
    *
    */


/*
 * *** Rabbitmq Consumer SmsStatus Shell
 */

class RmqcSmsStatusShell extends Shell
{

    public $uses = array('Reminder', 'ReminderLog', 'ServiceTransaction', 'SmsRead', 'User', 'Note', 'Installment', 'Debt', 'Check', 'Config');
    public $components = array('Sms', 'Email');

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
    public function smsCheckStatus()
    {
        $this->dryRun = false;
        if (!empty($this->params["dry-run"]) || !empty($this->params["-dry-run"]) )
            $this->dryRun = true;
        print 'rabbitmq sms check status worker start with pid:' . getmypid() . "\n";
        $queue_name = "check_sms_status";
        $queue_name_retry = "check_sms_status_retry";
        $exchange = "check_status_exchange";
        $retryExchange = "check_sms_status_retry_exchange";
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


            $callback = [$this, 'checkStatusWorker'];
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

        print 'rabbitmq check status worker end with pid:' . getmypid() . "\n";
    }

    public function checkStatusWorker($msg)
    {
        $body = json_decode($msg->body, true);
        if($this->dryRun == false){
            $result = $this->checkStatus($body);
        }
        else{
            if(!empty($this->params['fail']) || !empty($this->params['-fail']))
                $result = ['status' => 'fail', 'failed' => 'fail'];
            else
                $result =  ['status' => 'success', 'code' => 4];
        }
        if (!is_array($result) || empty($result)) {
            echo "NACK!! ".$body['ReminderLog']['id']." , ".$body['ReminderLog']['identifier'] ." \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
            return 0;
        }

        if ($result['status'] == 'fail') {
            echo "NACK! ".$body['ReminderLog']['id']." , ".$body['ReminderLog']['identifier'] ." code: ".$result['code']." \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
            return false;
        }
        elseif ($result['status'] == 'success') {
            echo "Sent, " .$body['ReminderLog']['id']." , ".$body['ReminderLog']['identifier'] ." code: ".$result['code']." \n";
            //send delivery acknowledge to rabbitmq to remove this message from the queue
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        }
        else {
            //unknown error
            echo "NACK! unknown error \n";
            //notify the queue that this message has failed due to some errors and must be retried
            $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag']);
        }
    }

    public function checkStatus($data)
    {
        $this->Sms = new SmsComponent();
        $this->date = new PersianDate();
        $this->Sms->initialize($this);

        $result = $this->_checkStatus($data);

        return $result;
    }

    private function _checkStatus($data)
    {
        $temp = $data['ReminderLog'];
        $list = [
            "id" => $temp["identifier"]
        ];
        $id = $data['ReminderLog']['id'];

        $user_id = $data['ReminderLog']['user_id'];

        $status = $this->Sms->checkSmsStatus($list);

        $codes = [-1,0,1,2,3,4,5,15];
        $result=[];
        if (!is_numeric($status) || !in_array($status,$codes))
            return ['status'=>'fail','code'=>$status];
        switch ($status){
            case -1:
                // no info available
                $this->ReminderLog->updateAll(
                    array('ReminderLog.sms_status' => '"sent"'),
                    array("ReminderLog.id " => $id)
                );
                $result = ['status'=>'success','code'=>$status];
                break;
            case 0:
                // send duration, try later
                $this->ReminderLog->updateAll(
                    array('ReminderLog.sms_status' => '"sent"'),
                    array("ReminderLog.id " => $id)
                );
                $result = ['status'=>'success','code'=>$status];
                break;
            case 1:
                // send duration, try later
                $this->ReminderLog->updateAll(
                    array('ReminderLog.sms_status' => '"sent"'),
                    array("ReminderLog.id " => $id)
                );
                $result = ['status'=>'success','code'=>$status];
                break;
            case 2:
                // delivered to operator
                $this->ReminderLog->updateAll(
                    array('ReminderLog.sms_status' => '"sent"'),
                    array("ReminderLog.id " => $id)
                );
                $result = ['status'=>'success','code'=>$status];
                break;
            case 3:
                // failed to sent by operator
                $this->ReminderLog->updateAll(
                    array('ReminderLog.sms_status' => '"faileddelivery"'),
                    array("ReminderLog.id " => $id)
                );
                $result = ['status'=>'success','code'=>$status];
                break;
            case 4:
                // sent by operator
                $this->ReminderLog->updateAll(
                    array('ReminderLog.sms_status' => '"delivered"'),
                    array("ReminderLog.id " => $id)
                );
                $result = ['status'=>'success','code'=>$status];
                break;
            case 5:
                // stop sending in operator
                $this->ReminderLog->updateAll(
                    array('ReminderLog.sms_status' => '"blocked"'),
                    array("ReminderLog.id " => $id)
                );
                $this->User->updateAll(
                    array('User.blocked' => '1'),
                    array("User.id " => $id)
                );
                $result = ['status'=>'success','code'=>$status];
                break;
            case 15:
                // sending multiple in small duration
                // delivered to operator
                $this->ReminderLog->updateAll(
                    array('ReminderLog.sms_status' => '"sent"'),
                    array("ReminderLog.id " => $id)
                );
                $result = ['status'=>'success','code'=>$status];
                break;
        }

        return $result;
    }


    // php cake.php rmqc_sms_status testCheckStatus
    // check for specific sms identifier status
    public function testCheckStatus()
    {
        $this->Sms = new SmsComponent();
        $this->date = new PersianDate();
        $this->Sms->initialize($this);

        $identifier = strtoupper($this->in(__('please enter sms identifier', true)));

        if (empty($identifier)){
            echo "\n sms identifier is require \n";
            exit;
        }

        $list = [
            "id" => $identifier
        ];

        $status = $this->Sms->checkSmsStatus($list);

        echo "\n status code is: ".$status."\n";
    }

}
