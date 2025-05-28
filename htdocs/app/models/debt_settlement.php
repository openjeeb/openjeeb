<?php

class DebtSettlement extends AppModel {

    var $name = 'DebtSettlement';
    var $belongsTo = array(
        'Debt' => array(
            'className' => 'Debt',
            'foreignKey' => 'debt_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Transaction' => array(
            'className' => 'Transaction',
            'foreignKey' => 'transaction_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
