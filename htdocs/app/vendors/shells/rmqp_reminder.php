<?php

require_once APP . '/vendors/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

/*
 * Notices:
 *
    *  **** to create exchanges and queue on first time run beloved commands
    *
    *      1) php cake.php rmqc_reminder smsReminders
    *      2)php cake.php rmqc_reminder emailReminders
    *
    *
    *
    *
    *  **** command to run rabbitmq producer for reminders:
    *
    *       php cake.php rmqp_reminder reminders [-dry-run]
    */


/*
 * *** Rabbitmq Producer Reminder Shell class
 */

class RmqpReminderShell extends Shell
{

    public $uses = array('Reminder', 'ReminderLog', 'ServiceTransaction', 'SmsRead', 'User', 'Note', 'Installment', 'Debt', 'Check', 'Config');

    public $dryRun;

    function main()
    {
        print 1;
    }

    /**
     *  reminders function that execute in shell
     */
    public function reminders()
    {

        $this->dryRun = false;
        if (!empty($this->params["dry-run"]) || !empty($this->params["-dry-run"]) )
            $this->dryRun = true;
        print 'rabbitmq producer reminder start with pid:' . getmypid() . "\n";

        if ($this->dryRun){
            $reminders = $this->Reminder->find( 'all' , array(
                'conditions' => array(
                    'medium'=>'sms,email'
                ),
                'limit'=>100
            ));
        }
        else{
            // marked not sent reminders or reminders that not marked
            // and return producer marked reminder
            $reminders = $this->Reminder->markedAndGet();
        }


        // add marked reminder to rabbit mq
        $this->_sendRemindersToRabbitMQ($reminders);

        print 'rabbitmq producer reminder end with pid:' . getmypid() . "\n";
    }

    private function getRabbitConnection()
    {
        Configure::load("rabbitmq");
        $connection = new AMQPStreamConnection(Configure::read('Rabbitmq.host'), Configure::read('Rabbitmq.port'), Configure::read('Rabbitmq.user'), Configure::read('Rabbitmq.pass'));
        return $connection;
    }

    /*
     * send a list of reminders to rabbit mq queues
     */
    private function _sendRemindersToRabbitMQ($list)
    {
        $connection = $this->getRabbitConnection();
        $channel = $connection->channel();
        $connection->set_close_on_destruct(false);

        try {

            foreach ($list as $item) {
                $this->Reminder->id = $item['Reminder']['id'];
                if (!isset($item[ucfirst($item['Reminder']['type'])])) {
                    $this->Reminder->id = $item['Reminder']['id'];
                    if($this->dryRun == false)
                        $this->Reminder->save(array('deleted' => -1));
                    continue;
                } else {
                    if($this->dryRun == false)
                        $this->Reminder->save(array('deleted' => 1));
                }
                $txt = $this->Reminder->makeText($item);

                $ReminderLog = $this->_reminderlog(
                    $item['Reminder']['id'],
                    $item['Reminder']['user_id'],
                    'reminder-' . $item['Reminder']['type'],
                    $item['User']['cell'],
                    $item['User']['email'],
                    $item['Reminder']['medium'],
                    $txt['subject'],
                    $txt['sms'],
                    $txt['email']
                );
                if (empty($ReminderLog) || empty($ReminderLog['id']))
                    continue;
                $item['ReminderLog'] = $ReminderLog;

                $data = json_encode($item);
                $this->out('PUSH TO QUEUE:: reminder,reminder_log => ['.$item['Reminder']['id'].':'.$ReminderLog['id'].'] '.date('Y-m-d H:i:s'));
                $msg = new AMQPMessage($data, ['delivery_mode' => 2]);
                if ($item['ReminderLog']['sms_status'] == 'pending')
                    $channel->basic_publish($msg, 'reminders', 'sms');
                if ($item['ReminderLog']['email_status'] == 'pending')
                    $channel->basic_publish($msg, 'reminders', 'email');
            }
            $channel->close();
            $connection->close();
        } catch (Exception $exc) {
            $this->out('Send Exception: ' . $exc->getMessage() . ' ' . $exc->getTraceAsString(), 1, Shell::class);
            $this->log('Send Exception: ' . $exc->getMessage() . ' ' . $exc->getTraceAsString(), 'rabbitmq_errors');
            return false;
        }

    }

    /*
     *  *** save a log for reminders in reminder_logs table
     */
    private function _reminderlog($rid, $uid, $type, $cell, $email, $medium, $subject, $textsms, $textemail, $charge = 1, $blocked = 0, $subscription = 1)
    {
        // به خاطر تاخیر سرویس ارسال پیامک راهی برای فهمیدن اینکه اس ام اس به کاربر ارسال شده است یا نه نداریم بنابراین سه پارامتر آخر تابع باید همیشه مقادیر پیش فرض باشند.
        $this->ReminderLog->create();
        $this->ReminderLog->inputConvertDate = false;

        $cnt = ceil(mb_strlen($textsms) / 70);
        if ($cell && strpos($medium, 'sms') !== false && !$blocked) {
            if($this->dryRun == false) {
                if (!$this->ServiceTransaction->debtorCredit('reminder_sms', $cnt, $uid)) {
                    return false;
                }
            }
            $smsstatus = 'pending';
        } else {
            $smsstatus = 'notset';
        }

        $emailstatus = ((($email && strpos($medium, 'email') !== false) && $subscription) ? 'pending' : 'notset');

        $data = array(
            'user_id' => $uid,
            'reference_id' => $rid,
            'type' => $type,
            'medium' => $medium,
            'cell' => preg_replace('/^0/i', '+98', $cell),
            'email' => $email,
            'subject' => $subject,
            'textsms' => $textsms,
            'textemail' => $textemail,
            'msg_count' => $cnt,
            'email_status' => $emailstatus,
            'sms_status' => $smsstatus,
            'charge' => $charge,
            'senddate' => date('Y-m-d H:i:s')
        );
        if($this->dryRun) {
            $data['id'] = rand(100000000,1000000000);
            return $data;
        }
        $this->ReminderLog->save($data);
        $data['id'] = $this->ReminderLog->getLastInsertId();
        return $data;
    }

}
