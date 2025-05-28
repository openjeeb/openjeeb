<?php

class ReminderLog extends AppModel {

    var $name = 'ReminderLog';
//    var $displayField = 'name';
//    var $virtualFields = array('settled' => "(SELECT SUM(amount) FROM debt_settlements WHERE debt_settlements.debt_id=Debt.id)");
//    var $validate = array(
//        'name' => array(
//            'notempty' => array(
//                'rule' => array('notempty'),
//                'message' => 'لطفا عنوان را وارد کنید.',
//                'allowEmpty' => false,
//                'required' => true,
//            ),
//        ),
//        'amount' => array(
//            'numeric' => array(
//                'rule' => array('numeric'),
//                'message' => 'مبلغ بایستی یک عدد باشد.',
//                'allowEmpty' => false,
//                'required' => true,
//            ),
//            'range' => array(
//                'rule' => array('range', -9999999999999, 9999999999999),
//                'message' => 'مبلغ معتبر نیست.',
//                'allowEmpty' => false,
//                'required' => true,
//            ),
//        ),
//        'due_date' => array(
//            'date' => array(
//                'rule' => array('date'),
//                'message' => 'تاریخ معتبر نیست.',
//                'allowEmpty' => false,
//                'required' => true,
//            ),
//        ),
//    );
    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => array('User.id'),
            'order' => ''
        )
    );
    
//    var $hasMany = array(
//        'User' => array(
//            'className' => 'User',
//            'foreignKey' => 'user_id',
//            'dependent' => false,
//            'conditions' => '',
//            'fields' => '',
//            'order' => '',
//            'limit' => '',
//            'offset' => '',
//            'exclusive' => '',
//            'finderQuery' => '',
//            'counterQuery' => ''
//        )
//    );


    public function markLogsForSmsDeliveryCheck($minDate){
        if (empty($minDate)){
            $minDate = date("Y-m-d H:i:s",time()-86400);
        }
        $this->query( "UPDATE `reminder_logs` SET sms_status = 'deliverycheck' WHERE senddate > '".$minDate."' AND sms_status='sent' AND identifier IS NOT NULL" );
    }

    public function getLogsForSmsDeliveryCheck()
    {
        return $this->find( 'all' , array(
            'conditions' => array(
                'sms_status' => 'deliverycheck',
                'not'=>array('identifier ' => null)
            )
        ));
    }
}

?>