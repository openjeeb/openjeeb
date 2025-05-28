<?php

class Investment extends AppModel {

    var $name = 'Investment';
    var $displayField = 'name';
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            'message' => 'عنوان را وارد کنید.',
            'allowEmpty' => false,
            'required' => true,
            ),
        ),
        'amount' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            'message' => 'میزان بایستی یک عدد باشد.',
            'allowEmpty' => false,
            'required' => true,
            ),
        ),
        'date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'تاریخ معتبر نیست.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
    );

    var $belongsTo = array(
        /*'Currency' => array(
            'className' => 'Currency',
            'foreignKey' => 'currency_id',
            'conditions' => '',
            'fields' => array('Currency.id','Currency.persian_name'),
            'order' => ''
        ),*/
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => array('User.id'),
            'order' => ''
        )
    );

}
