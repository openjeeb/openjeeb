<?php

require_once APP . '/vendors/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

/*
 * Notices:
 *
    *  **** to create exchanges and queue on first time run belove commands
    *
    *      1) php cake.php rmqp_sms_status checkSmsStatus
    *
    *
    *
    *
    *
    *  **** command to run rabbitmq producer for checkSmsStatus:
    *
    *       php cake.php rmqp_sms_status checkSmsStatus [-dry-run]
    */


/*
 * *** Rabbitmq Producer Reminder Shell class
 */

class RmqpSmsStatusShell extends Shell
{

    public $uses = array('Reminder', 'ReminderLog', 'ServiceTransaction', 'SmsRead', 'User', 'Note', 'Installment', 'Debt', 'Check', 'Config');

    public $dryRun;

    function main()
    {
        print 1;
    }

    /**
     *  checkSmsStatus function that execute in shell
     */
    public function checkSmsStatus()
    {
        $this->dryRun = false;
        if (!empty($this->params["dry-run"]) || !empty($this->params["-dry-run"]) )
            $this->dryRun = true;
        print 'rabbitmq producer checkSmsStatus start with pid:' . getmypid() . "\n";

        if ($this->dryRun){
            $logs = $this->ReminderLog->find( 'all' , array(
                'conditions' => array(
                    'sms_status'=>'sent',
                    'not'=>array('identifier'=>null),
                    'senddate >'=>date("Y-m-d H:i:s",time()-86400)
                ),
                'limit'=>100
            ));
        }
        else{
            // change reminder log status to deliverycheck

            $this->ReminderLog->markLogsForSmsDeliveryCheck(date("Y-m-d H:i:s",time()-86400));
            // return deliverycheck log
            $logs = $this->ReminderLog->getLogsForSmsDeliveryCheck();
        }


        // send logs to rabbitmq
        $this->_sendToRabbitMQ($logs);

        print 'rabbitmq producer checkSmsStatus end with pid:' . getmypid() . "\n";
    }

    private function getRabbitConnection()
    {
        Configure::load("rabbitmq");
        $connection = new AMQPStreamConnection(Configure::read('Rabbitmq.host'), Configure::read('Rabbitmq.port'), Configure::read('Rabbitmq.user'), Configure::read('Rabbitmq.pass'));
        return $connection;
    }

    /*
     * send a list of sms check to rabbit mq queues
     */
    private function _sendToRabbitMQ($list)
    {
        $connection = $this->getRabbitConnection();
        $channel = $connection->channel();
        $connection->set_close_on_destruct(false);

        try {

            foreach ($list as $item) {
                echo "add ".$item['ReminderLog']['id']." , ".$item['ReminderLog']['identifier']." to queue \n";
                $data = json_encode($item);
                $msg = new AMQPMessage($data, ['delivery_mode' => 2]);
                $channel->basic_publish($msg, 'check_status_exchange', 'sms');
            }
            $channel->close();
            $connection->close();
        } catch (Exception $exc) {
            $this->out('Send Exception: ' . $exc->getMessage() . ' ' . $exc->getTraceAsString(), 1, Shell::class);
            $this->log('Send Exception: ' . $exc->getMessage() . ' ' . $exc->getTraceAsString(), 'rabbitmq_errors');
            return false;
        }

    }

}
