<?php

class CheckReminderShell extends Shell {

    var $uses = array('User', 'Check');
    var $tasks = array('SendMail');

    function main() {
        //get checks with due date in 7 days
        $this->Check->ownData = false;
        $this->Check->recursive = -1;
        $checks7Days = $this->Check->find('all', array(
            'fields' => array('Check.id', 'Check.amount', 'Check.due_date ', 'Check.serial', 'Check.type', 'User.id', 'User.email', 'Bank.name'),
            'conditions' => array(
                'Check.notify' => 'yes',
                'Check.status' => 'due',
                'Check.due_date = DATE_ADD( DATE(CURDATE()) , INTERVAL 7 DAY)',
                'User.notifications'=>'yes',
            ),
            'joins' => array(
                array(
                    'table' => 'banks',
                    'alias' => 'Bank',
                    'type' => 'LEFT',
                    'conditions' => 'Check.bank_id = Bank.id'
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => 'Check.user_id = User.id'
                )
            ),
            'order' => 'User.id',
            ));

        //get checks with due date in 3 days
        $checks3Days = $this->Check->find('all', array(
            'fields' => array('Check.id', 'Check.amount', 'Check.due_date ', 'Check.serial', 'Check.type', 'User.id', 'User.email', 'Bank.name'),
            'conditions' => array(
                'Check.notify' => 'yes',
                'Check.status' => 'due',
                'Check.due_date = DATE_ADD( DATE(CURDATE()) , INTERVAL 3 DAY)',
                'User.notifications'=>'yes',
            ),
            'joins' => array(
                array(
                    'table' => 'banks',
                    'alias' => 'Bank',
                    'type' => 'LEFT',
                    'conditions' => 'Check.bank_id = Bank.id'
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => 'Check.user_id = User.id'
                )
            ),
            'order' => 'User.id',
            ));

        //get checks with due date in 1 days
        $checks1Days = $this->Check->find('all', array(
            'fields' => array('Check.id', 'Check.amount', 'Check.due_date ', 'Check.serial', 'Check.type', 'User.id', 'User.email', 'Bank.name'),
            'conditions' => array(
                'Check.notify' => 'yes',
                'Check.status' => 'due',
                'Check.due_date = DATE_ADD( DATE(CURDATE()) , INTERVAL 1 DAY)',
                'User.notifications'=>'yes',
            ),
            'joins' => array(
                array(
                    'table' => 'banks',
                    'alias' => 'Bank',
                    'type' => 'LEFT',
                    'conditions' => 'Check.bank_id = Bank.id'
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => 'Check.user_id = User.id'
                )
            ),
            'order' => 'User.id',
            ));

        //merge
        $checks = array_merge_recursive($checks7Days, $checks3Days, $checks1Days);

        //email subject
        $subject = 'جیب :: یادآوری چک';

        //send reminders
        foreach ($checks as $check) {
            echo $check['User']['id'] . ':' . $check['User']['email'] . "\n";
            //wait 3 seconds
            sleep(3);
            //generate message
            $message = '<p>کاربر گرامی،</p>
    <p>شما یک چک ';
            $message .= __($check['Check']['type'], true) . ' به مبلغ ';
            $message .= '<b>' . number_format(abs($check['Check']['amount'])) . ' ریال</b>';
            $message .= ' به تاریخ ';
            $message .= '<b>' . $check['Check']['due_date'] . '</b>';
            if (!empty($check['Check']['serial'])) {
                $message .= ' به شماره سریال ';
                $message .= $check['Check']['serial'];
            }
            $message .= ' در ';
            $message .= '<b>' . $check['Bank']['name'] . '</b>';
            $message .= ' دارید.</p>';
            $message .= '<br/><h3>نکات:</h3>
<ul>
<li>در صورتی که این چک تسویه شده است وارد سامانه جیب شوید و در صفحه «پیشخوان» در قسمت «یادآوری‌ها» چک را تسویه کنید.</li>
<li>در صورتی که مایل به دریافت یادآوری برای این چک نیستید میتوانید وارد سامانه جیب شده از بخش «چک‌» چک مورد نظر را ویرایش کنید و گزینه آگاه‌سازی را خاموش کنید.</li>
<li>در صورتی که مایل به دریافت هیچ ایمیلی از سامانه جیب نیستید بر روی لینک « حذف از لیست دریافت ایمیل» در انتهای این ایمیل کلیک کنید.</li>
</ul>
<br/>
<p><b>هدف ما جلب رضایت شماست</b></p>
<p><b>با احترام</b></p>
<p><b>تیم سامانه جیب</b></p>';
            //send
            if (!@$this->SendMailElastic->execute($check['User']['email'], $subject, $message)) {
                //log fails
                $this->log('Failed: ' . $check['User']['email'], 'check_reminder');
            }
        }
    }

}

?>
