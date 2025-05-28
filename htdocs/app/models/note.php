<?php

class Note extends AppModel {

    var $name = 'Note';
    var $displayField = 'subject';
    var $validate = array(
        'subject' => array(
            'notempty' => array(
                'rule' => array( 'notempty' ),
            'message' => 'عنوان نمیتواند خالی باشد.',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
    );
    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
