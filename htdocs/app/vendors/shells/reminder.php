<?php

/**
 * CakePHP Shell
 * @author root
 */
App::import('Component', 'Sms');
App::import('Component', 'Email');
App::import('Vendor', 'PersianLib', array('file' => 'persian.lib.php'));

class reminderShell extends Shell
{

    public $uses = array('Reminder', 'ReminderLog', 'ServiceTransaction', 'SmsRead', 'User', 'Note', 'Installment', 'Debt', 'Check', 'Config');
    public $components = array('Sms', 'Email');
    public $tasks = array('SendMail', 'SendMailElastic');

    function main()
    {
        print 1;
    }

    public function reminders()
    {
        $this->Sms = new SmsComponent();
        $this->date = new PersianDate();
        $this->Sms->initialize($this);

        while ($reminders = $this->Reminder->getMarked()) {
            $this->_sendreminders($reminders);
        }

        $this->_processLogList();

    }

    private function _sendreminders($list)
    {
        foreach ($list as $item) {

            $this->Reminder->id = $item['Reminder']['id'];

            if (!isset($item[ucfirst($item['Reminder']['type'])])) {
                $this->Reminder->id = $item['Reminder']['id'];
                $this->Reminder->save(array('deleted' => -1));
                continue;
            } else {
                $this->Reminder->save(array('deleted' => 1));
            }

            $txt = $this->Reminder->makeText($item);

            $logId = $this->_reminderlog(
                $item['Reminder']['id'],
                $item['Reminder']['user_id'],
                'reminder-' . $item['Reminder']['type'],
                $item['User']['cell'],
                $item['User']['email'],
                $item['Reminder']['medium'],
                $txt['subject'],
                $txt['sms'],
                $txt['email'],
                $item['User']['blocked'],
                $item['User']['subscription']
            );

        }
    }

    private function _reminderlog($rid, $uid, $type, $cell, $email, $medium, $subject, $textsms, $textemail, $charge = 1, $blocked = 0, $subscription = 1)
    {
        $this->ReminderLog->create();
        $this->ReminderLog->inputConvertDate = false;

        $cnt = ceil(mb_strlen($textsms) / 70);
        if ($cell && strpos($medium, 'sms') !== false && !$blocked) {
            if (!$this->ServiceTransaction->debtorCredit('reminder_sms', $cnt, $uid)) {
                return false;
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

        $this->ReminderLog->save($data);
        return $this->ReminderLog->getLastInsertId();
    }

    private function _processLogList()
    {
        $this->_sendPendingsSms();
        $this->_checkSentStatus();
    }

    private function _recoverFailedOnes()
    {
        $list = $this->ReminderLog->find('all', array(
            'fields' => 'ReminderLog.id',
            'conditions' => array(
                'ReminderLog.sms_status' => 'failed',
                'senddate >= (CURRENT_TIMESTAMP - INTERVAL 1 DAY)',
            )
        ));
        $list = Set::extract("/ReminderLog/id", $list);

        $list = $this->Sms->getMessageId($list);

        foreach ($list as $id => $item) {
            $this->ReminderLog->id = $id;
            $this->ReminderLog->save(array(
                'sms_status' => $item ? 'sent' : 'pending',
                'identifier' => $item ? $item : NULL
            ));
        }
    }

    private function _sendPendingsSms()
    {
        $list = $this->ReminderLog->find('all', array(
            'fields' => 'ReminderLog.id AS id, ReminderLog.cell AS mobile, ReminderLog.textsms AS message',
            'conditions' => array(
                'ReminderLog.sms_status' => 'pending',
            )
        ));

        if (!$list) {
            return;
        }

        $list = Set::extract("/ReminderLog/.", $list);
        $ids = Set::extract("/id", $list);

        $this->ReminderLog->recursive = -1;
        $this->ReminderLog->updateAll(
            array('ReminderLog.sms_status' => '"sending"', '`ReminderLog`.`senddate` ' => 'CURRENT_TIMESTAMP'),
            array("ReminderLog.id IN (" . implode(",", $ids) . ")")
        );

        $result = $this->Sms->adpSendMany($list);
        $failed = array_diff($ids, array_keys($result));

        if ($failed) {
            $this->ReminderLog->updateAll(
                array('ReminderLog.sms_status' => '"failed"', '`ReminderLog`.`senddate` ' => NULL),
                array("ReminderLog.id IN (" . implode(",", $failed) . ")")
            );
        }

        foreach ($result as $k => $item) {
            if (!$item['identifier'])
                continue;
            $this->ReminderLog->id = $item['id'];
            $this->ReminderLog->save(array(
                'sms_status' => 'sent',
                'identifier' => $item['identifier']
            ));
        }
    }

    private function _checkSentStatus()
    {
        $list = $this->ReminderLog->find('all', array(
            'fields' => 'ReminderLog.identifier',
            'conditions' => array(
                'ReminderLog.sms_status' => 'sent',
                'ReminderLog.senddate >= CURRENT_TIMESTAMP - INTERVAL 25 HOUR'
            )
        ));

        if (!$list) {
            return;
        }

        $ids = Set::extract("/ReminderLog/identifier", $list);
        $res = $this->Sms->adpGetStatus($ids);

        foreach ($res as $k => $item) {
            $this->ReminderLog->updateAll(
                array('ReminderLog.sms_status' => "'$item'", '`ReminderLog`.`resultdate` ' => 'CURRENT_TIMESTAMP'),
                array("ReminderLog.identifier " => $k)
            );
        }


    }

    public function SendPendingEmail()
    {
        do {
            $list = $this->ReminderLog->find('all', array(
                'fields' => 'ReminderLog.id AS id, ReminderLog.email AS email, ReminderLog.subject AS subject, ReminderLog.textemail AS message, ReminderLog.user_id AS user_id',
                'conditions' => array(
                    'ReminderLog.email_status' => 'pending'
                ),
                'limit' => 50
            ));
            if (!$list) {
                break;
            }

            $list = Set::extract("/ReminderLog/.", $list);
            $ids = Set::extract("/id", $list);
            $this->ReminderLog->updateAll(
                array('ReminderLog.email_status' => '"sending"', '`ReminderLog`.`senddate` ' => 'CURRENT_TIMESTAMP'),
                array("ReminderLog.id IN (" . implode(",", $ids) . ") ")
            );

            $success = $failed = array();
            foreach ($list as $item) {

                $this->User->ownData = false;
                $this->User->recursive = -1;
                $user = $this->User->find('first', [
                    'conditions' => [
                        'User.id' => $item['user_id']
                    ]
                ]);

                $this->out('User notification for ' . $item['user_id'] . ': ' . $user['User']['notifications']);

                if ($user['User']['notifications'] == 'no') {
                    $success[] = $item['id'];
                    continue;
                }

                if ($this->SendMailElastic->execute($item['email'], $item['subject'], $item['message'])) {
                    $success[] = $item['id'];
                } else {
                    $failed[] = $item['id'];
                }
            }

            $this->ReminderLog->updateAll(
                array('ReminderLog.email_status' => '"sent"', '`ReminderLog`.`senddate` ' => 'CURRENT_TIMESTAMP'),
                array("ReminderLog.id IN (" . implode(",", $success) . ")")
            );
            $this->ReminderLog->updateAll(
                array('ReminderLog.email_status' => '"failed"', '`ReminderLog`.`senddate` ' => 'CURRENT_TIMESTAMP'),
                array("ReminderLog.id IN (" . implode(",", $failed) . ")")
            );

        } while (1);


    }

    public function adpgetmessages()
    {
        $this->Sms = new SmsComponent();
        $this->date = new PersianDate();
        $this->Sms->initialize($this, array('read' => 1));

        $this->SmsRead->inputConvertDate = false;

        $messages = $this->Sms->adpReadLast();

        foreach ($messages as $msg) {

            $uid = $this->User->find('first', array(
                'conditions' => array(
                    'cell' => $msg->from
                )
            ));

            $uid = Set::extract("/User/id", $uid);
            $uid = $uid ? $uid[0] : NULL;

            $this->SmsRead->create();
            $this->SmsRead->save(array(
                'identifier' => $msg->id,
                'user_id' => $uid,
                'fromnumber' => $msg->from,
                'tonumber' => $msg->to,
                'date' => $msg->time,
                'body' => $msg->content
            ));

            $sid = $this->SmsRead->getLastInsertId();
            $this->Note->inputConvertDate = false;
            if ($uid) {
                $this->Note->create();
                $this->Note->save(array(
                    'user_id' => $uid,
                    'subject' => 'دریافت شده از طریق پیامک',
                    'content' => $msg->content,
                    'notify' => false,
                    'date' => $msg->time
                ));
                $this->SmsRead->id = $sid;
                $this->SmsRead->saveField('processed', 1);
            }

        }

    }

    public function getmessages()
    {
        $this->Sms = new SmsComponent();
        $this->date = new PersianDate();
        $this->Sms->initialize($this, array('read' => 1));

        $this->SmsRead->inputConvertDate = false;

        $msg = $this->Sms->readLast();
        while ($msg->ID) {
            if ($r = $this->SmsRead->getData($msg->ID)) {
                break;
            }
            $uid = $this->User->find('first', array(
                'conditions' => array(
                    'cell' => $msg->From
                )
            ));
            $uid = Set::extract("/User/id", $uid);
            $uid = $uid ? $uid[0] : NULL;
            $this->SmsRead->create();
            $this->SmsRead->save(array(
                'identifier' => $msg->ID,
                'user_id' => $uid,
                'fromnumber' => $msg->From,
                'tonumber' => $msg->To,
                'date' => $msg->Date,
                'body' => $msg->Body
            ));

            $sid = $this->SmsRead->getLastInsertId();
            $this->Note->inputConvertDate = false;
            if ($uid) {
                $this->Note->create();
                $this->Note->save(array(
                    'user_id' => $uid,
                    'subject' => 'دریافت شده از طریق پیامک',
                    'content' => $msg->Body,
                    'notify' => false,
                    'date' => $msg->Date
                ));
                $this->SmsRead->id = $sid;
                $this->SmsRead->saveField('processed', 1);
            }

            $msg = $this->Sms->readPrior($msg->ID);

        }

    }

    public function reinsert()
    {
        print "Installment\n";

        $this->Installment->convertDateFormat = 'Y/m/d';
        $inst = $this->Installment->find('all', array(
            'conditions' => array(
                "timestamp(Installment.created) between '2013-11-18 11:00:00' and '2013-11-18 11:45:00'"
            )
        ));

        foreach ($inst as $ins) {
            print $ins['Installment']['id'] . " : ";
            print $this->Reminder->addReminder('installment', $ins['Installment']['due_date'], $ins['Installment']['id'], $ins['Installment']['user_id']);
            print "\n";
        }

        print "Debt\n";

        $this->Debt->convertDateFormat = 'Y/m/d';
        $debts = $this->Debt->find('all', array(
            'conditions' => array(
                "timestamp(Debt.created) between '2013-11-18 11:00:00' and '2013-11-18 11:45:00'"
            )
        ));

        foreach ($debts as $debt) {
            print $debt['Debt']['id'] . " : ";
            print $this->Reminder->addReminder('debt', $debt['Debt']['due_date'], $debt['Debt']['id'], $debt['Debt']['user_id']);
            print "\n";
        }

    }

}
