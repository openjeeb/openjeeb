<?php

class DebtReminderShell extends Shell {

    var $uses = array('User','Debt');
    var $tasks = array('SendMail');

    function main() {
        //get debts with due date in 7 days
        $this->Debt->ownData = false;
        $this->Debt->recursive = -1;
        $debts7Days = $this->Debt->find('all', array(
            'fields' => array('Debt.id', 'Debt.amount','Debt.due_date ','Debt.type','User.id','User.email'),
            'conditions' => array(
                'Debt.notify' => 'yes',
                'Debt.status' => 'due',
                'Debt.due_date = DATE_ADD( DATE(CURDATE()) , INTERVAL 7 DAY)',
                'User.notifications'=>'yes',
            ),
            'joins'=>array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => 'Debt.user_id = User.id'
                )
            ),
            'order' => 'User.id',
        ));
        
        //get Debts with due date in 3 days
        $debts3Days = $this->Debt->find('all', array(
            'fields' => array('Debt.id', 'Debt.amount','Debt.due_date ','Debt.type','User.id','User.email'),
            'conditions' => array(
                'Debt.notify' => 'yes',
                'Debt.status' => 'due',
                'Debt.due_date = DATE_ADD( DATE(CURDATE()) , INTERVAL 3 DAY)',
                'User.notifications'=>'yes',
            ),
            'joins'=>array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => 'Debt.user_id = User.id'
                )
            ),
            'order' => 'User.id',
        ));
        
        //get Debts with due date in 1 days
        $debts1Days = $this->Debt->find('all', array(
            'fields' => array('Debt.id', 'Debt.amount','Debt.due_date ','Debt.type','User.id','User.email'),
            'conditions' => array(
                'Debt.notify' => 'yes',
                'Debt.status' => 'due',
                'Debt.due_date = DATE_ADD( DATE(CURDATE()) , INTERVAL 1 DAY)',
                'User.notifications'=>'yes',
            ),
            'joins'=>array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => 'Debt.user_id = User.id'
                )
            ),
            'order' => 'User.id',
        ));
        
        //merge
        $debts=array_merge_recursive($debts7Days,$debts3Days,$debts1Days);
        
        
        //send reminders
        foreach ($debts as $debt) {
            echo $debt['User']['id'] . ':' . $debt['User']['email'] . "\n";
            //wait 3 seconds
            sleep(3);
            //email subject
            $subject = 'جیب :: یادآوری '.__($debt['Debt']['type'],true);
            //generate message
            $message = '<p>کاربر گرامی،</p>
    <p>شما یک ';
            $message .= __($debt['Debt']['type'],true).' به مبلغ ';
            $message .= '<b>'.number_format(abs($debt['Debt']['amount'])).' ریال</b>';
            $message .= ' به تاریخ ';
            $message .= '<b>'.$debt['Debt']['due_date'].'</b>';
            $message .= ' دارید.</p>';
            $message .= "<br/><p><b>نکات:</b></p>
<ul>
<li>در صورتی که این ".__($debt['Debt']['type'],true)." تسویه شده است وارد سامانه جیب شوید و در صفحه «پیشخوان» در قسمت «یادآوری‌ها» ".__($debt['Debt']['type'],true)." را تسویه کنید.</li>
<li>در صورتی که مایل به دریافت یادآوری برای این ".__($debt['Debt']['type'],true)." نیستید میتوانید وارد سامانه جیب شده از بخش «بدهی / طلب» ".__($debt['Debt']['type'],true)." مورد نظر را ویرایش کنید و گزینه آگاه‌سازی را خاموش کنید.</li>
<li>در صورتی که مایل به دریافت هیچ ایمیلی از سامانه جیب نیستید بر روی لینک « حذف از لیست دریافت ایمیل» در انتهای این ایمیل کلیک کنید.</li>
</ul>
<br/>
<p><b>هدف ما جلب رضایت شماست</b></p>
<p><b>با احترام</b></p>
<p><b>تیم سامانه جیب</b></p>";
            //send
            if (!@$this->SendMailElastic->execute($debt['User']['email'], $subject, $message)) {
                //log fails
                $this->log('Failed: ' . $debt['User']['email'], 'debt_reminder');
            }
        }
    }

}

?>
