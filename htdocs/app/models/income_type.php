<?php

class IncomeType extends AppModel {

    var $name = 'IncomeType';
    var $displayField = 'name';
    var $actsAs = array('Containable');
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'لطفا عنوان نوع درآمد را وارد کنید.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
        'user_id' => array(
            'rule' => array('owner'),
            'message' => 'مشکلی در ذخیره اطلاعات پیش آمد.'
        )
    );
    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => array('User.id'),
            'order' => ''
        )
    );
    var $hasMany = array(
        'IncomeSubType' => array(
            'className' => 'IncomeSubType',
            'foreignKey' => 'income_type_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => array('id','name'),
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Income' => array(
            'className' => 'Income',
            'foreignKey' => 'income_type_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => array('Income.id','Income.transaction_id ','Income.description'),
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    function owner($check) {
        $this->recursive = -1;
        if ($check['user_id'] != $this->field('user_id', array('id' => $this->id)) AND $this->id) {
            return false;
        }
        return true;
    }

    function getIncomeTypeIdByName($name) {
        return $this->field('id',array('name'=>$name));
    }
    
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

?>