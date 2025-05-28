<?php

class Bank extends AppModel {

    var $name = 'Bank';
    var $displayField = 'name';
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
    );
    var $hasMany = array(
        'Account' => array(
            'className' => 'Account',
            'foreignKey' => 'account_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => array('Account.id','Account.name'),
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Check' => array(
            'className' => 'Check',
            'foreignKey' => 'bank_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => array('Check.id','Check.amount','Check.due_date','Check.type','Check.status','Check.account_id','Check.bank_id'),
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        
    );

}

?>