<?php

class Aro extends AppModel {

    var $name = 'Aro';
    var $belongsTo = array(
        'ParentAro' => array(
            'className' => 'Aro',
            'foreignKey' => 'parent_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    var $hasMany = array(
        'ChildAro' => array(
            'className' => 'Aro',
            'foreignKey' => 'parent_id',
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
    var $hasAndBelongsToMany = array(
        'Aco' => array(
            'className' => 'Aco',
            'joinTable' => 'aros_acos',
            'foreignKey' => 'aro_id',
            'associationForeignKey' => 'aco_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
        )
    );

}

?>