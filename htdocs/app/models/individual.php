<?php

class Individual extends AppModel {

    var $name = 'Individual';
    var $displayField = 'name';
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array( 'notempty' ),
            'message' => 'نام نمیتواند خالی باشد.',
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
    
    
    
    function moveSortItem($sid, $orientation)
    {
        $ort = ($orientation=='up')? "<=" : ">=";
        $ord = ($orientation=='up')? 'DESC' : 'ASC';
        $this->recursive = -1;
        
        if(count( $items = $this->find('all', array(
            'fields' => 'id, sort',
            'conditions'=> array(
                'sort '.$ort=>intval($sid)
            ),
            'order' => 'sort '.$ord,
            'limit' => 2) ) )<2 ){
            return true;
         };
         
         $data = array(
             array(
                 'id' => $items[0][$this->name]['id'],
                 'sort' => $items[1][$this->name]['sort']
             ),
             array(
                 'id' => $items[1][$this->name]['id'],
                 'sort' => $items[0][$this->name]['sort']
             ));
         
         return $this->saveAll($data, array('atomic'=>true, 'validate'=>false));
    }
    
    function fetchList($optional=null)
    {
        $options = array(
            'conditions' => array(
                'status' => 'active'
                ),
            'order' => 'sort ASC'
            );
        foreach(($optional? $optional : array()) as $k=>$option) {
            $options[$k] = $option;
        }
        return $this->find( 'list', $options );
    }

}
