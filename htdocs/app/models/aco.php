<?php

class Aco extends AppModel {

    var $name = 'Aco';
    var $displayField = 'alias';
    
    var $belongsTo = array(
        'ParentAco' => array(
            'className' => 'Aco',
            'foreignKey' => 'parent_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    var $hasMany = array(
        'ChildAco' => array(
            'className' => 'Aco',
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
        'Aro' => array(
            'className' => 'Aro',
            'joinTable' => 'aros_acos',
            'foreignKey' => 'aco_id',
            'associationForeignKey' => 'aro_id',
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