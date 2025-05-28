<?php

class Tag extends AppModel {

    var $name = 'Tag';
    var $displayField = 'name';
    var $actsAs = array('Containable');
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            'message' => 'لطفا عنوان را وارد کنید.',
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
        'TransactionTag' => array(
            'className' => 'TransactionTag',
            'foreignKey' => 'tag_id'
        ),
        'LoanTag' => array(
            'className' => 'LoanTag',
            'foreignKey' => 'tag_id'
        ),
        'DebtTag' => array(
            'className' => 'DebtTag',
            'foreignKey' => 'tag_id'
        ),
        'CheckTag' => array(
            'className' => 'CheckTag',
            'foreignKey' => 'tag_id'
        )
    );
    
    private $taglist;
            
    function owner($check) {
        $this->recursive = -1;
        if ($check['user_id'] != $this->field('user_id', array('id' => $this->id)) AND $this->id) {
            return false;
        }
        return true;
    }

    function getExpenseCategoryIdByName($name) {
        return $this->field('id',array('name'=>$name));
    }
    
    function fetchList($optional=null)
    {
        $options = array(
            'conditions' => array(
                'status' => 'active'
                )
            );
        foreach(($optional? $optional : array()) as $k=>$option) {
            $options[$k] = $option;
        }
        return $this->find( 'list' );
    }
    
    function &loadTags()
    {
        if(!isset( $this->taglist )) {
            $this->taglist = $this->find('list');
        }
        return $this->taglist;
    }
    
    function newTag($tag)
    {
        $tags = $this->loadTags();
        
        if($k = array_search($tag, $tags)) {
            return $k;
        }
        
        $this->create();
        $this->save( array(
            'Tag' => array( 'user_id' => $this->userId , 'name' => $tag )
        ) );
        
        return $this->getLastInsertID();
        
    }
    
    public function prepareList($tags)
    {
        foreach($tags as $k=>$v) {
            unset($tags[$k]);
            $tags['t'.$k] = $v;
        }
        return $tags;
    }
    
}

?>