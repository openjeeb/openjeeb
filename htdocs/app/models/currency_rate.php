<?php

class CurrencyRate extends AppModel {

    var $name = 'CurrencyRate';
    var $validate = array(
        'currency_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'current' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'min' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'max' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'average' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
    );
    var $belongsTo = array(
        'Currency' => array(
            'className' => 'Currency',
            'foreignKey' => 'currency_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
