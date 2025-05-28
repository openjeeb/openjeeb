<?php

class UserGroup extends AppModel {

    var $name = 'UserGroup';
    var $displayField = 'name';
    var $actsAs = array('Acl' => array('type' => 'requester'));
    
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
    );
    
    var $hasMany = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_group_id',
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

    function parentNode() {
        return null;
    }

}

?>