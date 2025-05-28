<?php

class Loan extends AppModel {

    var $name = 'Loan';
    var $displayField = 'name';
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            'message' => 'عنوان وام را وارد کنید.',
            'allowEmpty' => false,
            'required' => true,
            ),
        ),
    );
    var $belongsTo = array(
        'Bank' => array(
            'className' => 'Bank',
            'foreignKey' => 'bank_id',
            'conditions' => '',
            'fields' => array('Bank.id','Bank.name'),
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
    var $hasMany = array(
        'Installment' => array(
            'className' => 'Installment',
            'foreignKey' => 'loan_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => array('Installment.id','Installment.amount','Installment.due_date','Installment.status'),
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'LoanTag' => array(
            'className' => 'LoanTag',
            'foreignKey' => 'loan_id'
        )
    );

}

?>