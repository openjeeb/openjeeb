<?php

class Currency extends AppModel {

    var $name = 'Currency';
    var $displayField = 'persian_name';
    var $hasMany = array(
        'CurrencyRate' => array(
            'className' => 'CurrencyRate',
            'foreignKey' => 'currency_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

}
