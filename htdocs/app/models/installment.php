<?php

class Installment extends AppModel {
    
    var $setNotification = true;

    var $name = 'Installment';
//    var $validate = array(
//        'amount' => array(
//            'numeric' => array(
//                'rule' => array('numeric'),
//                'message' => 'مبلغ معتبر نیست.',
//                'allowEmpty' => false,
//                'required' => true,
//            ),
//        ),
//        'due_date' => array(
//            'date' => array(
//                'rule' => array('date'),
//                'message' => 'تاریخ سر رسید معتبر نیست.',
//                'allowEmpty' => false,
//                'required' => true,
//            ),
//        ),
//    );
    var $belongsTo = array(
        'Loan' => array(
            'className' => 'Loan',
            'foreignKey' => 'loan_id',
            'conditions' => '',
            'fields' => array('Loan.id','Loan.name','Loan.description'),
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => array('User.id'),
            'order' => ''
        )
    );

    function generateInstallmentDates($installments, $period, $startYear, $startMonth, $dueDay) {
        $dates = array();
        for ($i = 0; $i < $installments * $period; $i+=$period) {
            $y = 0;
            $y = floor(($startMonth - 1 + $i) / 12);
            $dates[] = $startYear + $y . '/' . (($startMonth - 1 + $i) % 12 + 1) . '/' . intval($dueDay);
        }
        return $dates;
    }

    function saveInstallments($loanId, $amount, $dates, $payed)
    {
        ClassRegistry::init('Reminder');
        $reminder = new Reminder();
        $this->saved_reminders = 0;
        //
        $dataSource = $this->getDataSource();
        //begin transaction
        $dataSource->begin($this);
        //save the installments
        $flag=0;
        foreach ($dates as $entry) {
            $data = array();
            $data['Installment']['loan_id'] = $loanId;
            $data['Installment']['amount'] = $amount;
            $data['Installment']['due_date'] = $entry; //auto convert by persian date behaviour
            $data['Installment']['status'] = 'due';
            if($flag < $payed){
                $data['Installment']['status'] = 'done';
                $flag++;
            }
            $this->create();
            if(!$this->save($data)) {
                $dataSource->rollback($this);
                return false;
            }
            if($this->setNotification) {
                $this->saved_reminders += $reminder->addReminder('installment',$data['Installment']['due_date'],$this->getLastInsertID());
            }
        }
        $dataSource->commit($this);
        return true;
    }
    
    function getInstallments($startDate,$endDate,$status='due',$order='Installment.due_date ASC',$notify='yes') {
        $this->recursive=0;
        return $this->find('all',array(
            'conditions'=>array(
                'Installment.due_date >=' => $startDate,
                'Installment.due_date <=' => $endDate,
                'Installment.status' => $status,
                'Installment.notify' => $notify,
            ),
            'order'=>$order
        ));
    }

}

?>