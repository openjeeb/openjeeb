<?php

class IncomeSubType extends AppModel {

    var $name = 'IncomeSubType';
    var $displayField = 'name';
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'لطفا عنوان را وارد کنید.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
    );
    var $belongsTo = array(
        'IncomeType' => array(
            'className' => 'IncomeType',
            'foreignKey' => 'income_type_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}

?>