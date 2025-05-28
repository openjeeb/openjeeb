<?php

class InstallmentReminderShell extends Shell {

    var $uses = array('User', 'Installment');
    var $tasks = array('SendMail');

    function main() {
        //get installments with due date in 7 days
        $this->Installment->ownData = false;
        $this->Installment->recursive = -1;
        $installments7Days = $this->Installment->find('all', array(
            'fields' => array('Installment.id', 'Installment.amount', 'Installment.due_date ', 'User.id', 'User.email', 'Loan.name'),
            'conditions' => array(
                'Installment.notify' => 'yes',
                'Installment.status' => 'due',
                'Installment.due_date = DATE_ADD( DATE(CURDATE()) , INTERVAL 7 DAY)',
                'User.notifications'=>'yes',
                'User.expire_date > CURDATE()'
            ),
            'joins' => array(
                array(
                    'table' => 'loans',
                    'alias' => 'Loan',
                    'type' => 'LEFT',
                    'conditions' => 'Installment.loan_id = Loan.id'
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => 'Installment.user_id = User.id'
                )
            ),
            'order' => 'User.id',
            ));

        //get Installments with due date in 3 days
        $installments3Days = $this->Installment->find('all', array(
            'fields' => array('Installment.id', 'Installment.amount', 'Installment.due_date ', 'User.id', 'User.email', 'Loan.name'),
            'conditions' => array(
                'Installment.notify' => 'yes',
                'Installment.status' => 'due',
                'Installment.due_date = DATE_ADD( DATE(CURDATE()) , INTERVAL 3 DAY)',
                'User.notifications'=>'yes',
                'User.expire_date > CURDATE()'
            ),
            'joins' => array(
                array(
                    'table' => 'loans',
                    'alias' => 'Loan',
                    'type' => 'LEFT',
                    'conditions' => 'Installment.loan_id = Loan.id'
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => 'Installment.user_id = User.id'
                )
            ),
            'order' => 'User.id',
            ));

        //get Installments with due date in 1 days
        $installments1Days = $this->Installment->find('all', array(
            'fields' => array('Installment.id', 'Installment.amount', 'Installment.due_date ', 'User.id', 'User.email', 'Loan.name'),
            'conditions' => array(
                'Installment.notify' => 'yes',
                'Installment.status' => 'due',
                'Installment.due_date = DATE_ADD( DATE(CURDATE()) , INTERVAL 1 DAY)',
                'User.notifications'=>'yes',
                'User.expire_date > CURDATE()'
            ),
            'joins' => array(
                array(
                    'table' => 'loans',
                    'alias' => 'Loan',
                    'type' => 'LEFT',
                    'conditions' => 'Installment.loan_id = Loan.id'
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => 'Installment.user_id = User.id'
                )
            ),
            'order' => 'User.id',
            ));

        //merge
        $installments = array_merge_recursive($installments7Days, $installments3Days, $installments1Days);

        //email subject
        $subject = 'جیب :: یادآوری قسط';

        //send reminders
        foreach ($installments as $installment) {
            echo $installment['User']['id'] . ':' . $installment['User']['email'] . "\n";
            //wait 3 seconds
            sleep(3);
            //generate message
            $message = '<p>کاربر گرامی،</p>
    <p>شما یک قسط از ';
            $message .= '<b>' .$installment['Loan']['name'] .'</b>';
            $message .= ' به مبلغ ';
            $message .= '<b>' . number_format(abs($installment['Installment']['amount'])) . ' ریال</b>';
            $message .= ' به تاریخ ';
            $message .= '<b>' . $installment['Installment']['due_date'] . '</b>';
            $message .= ' دارید.</p>';
            $message .= '<br/><h3>نکات:</h3>
<ul>
<li>در صورتی که این قسط تسویه شده است وارد سامانه جیب شوید و در صفحه «پیشخوان» در قسمت «یادآوری‌ها» قسط را تسویه کنید.</li>
<li>در صورتی که مایل به دریافت یادآوری برای این قسط نیستید میتوانید وارد سامانه جیب شده از بخش «وام» وام مورد نظر را بیابید و در لیست اقساط وام، قسط مورد نظر را یافته ویرایش کنید و گزینه آگاه‌سازی را خاموش کنید.</li>
<li>در صورتی که مایل به دریافت هیچ ایمیلی از سامانه جیب نیستید بر روی لینک « حذف از لیست دریافت ایمیل» در انتهای این ایمیل کلیک کنید.</li>
</ul>
<br/>
<p><b>هدف ما جلب رضایت شماست</b></p>
<p><b>با احترام</b></p>
<p><b>تیم سامانه جیب</b></p>';
            //send
            if (!@$this->SendMailElastic->execute($installment['User']['email'], $subject, $message)) {
                //log fails
                $this->log('Failed: ' . $installment['User']['email'], 'installment_reminder');
            }
        }
    }

}

?>
