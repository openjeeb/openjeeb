<?php

class DebtTag extends AppModel {

    var $name = 'DebtTag';
    var $displayField = 'id';
    var $actsAs = array('Containable');
    var $validate = array(
        'tag_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            'message' => 'لطفا تگ را انتخاب نمائید',
            'allowEmpty' => false,
            'required' => true,
            ),
        ),
        'debt_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            'message' => 'لطفا تگ را انتخاب نمائید',
            'allowEmpty' => false,
            'required' => true,
            ),
        ),
    );
    var $belongsTo = array(
        'Tag' => array(
            'className' => 'Tag',
            'foreignKey' => 'tag_id',
            'conditions' => '',
            'fields' => array('Tag.id'),
            'order' => ''
        ),
        'Debt' => array(
            'className' => 'Debt',
            'foreignKey' => 'debt_id',
            'conditions' => '',
            'fields' => array('Debt.id'),
            'order' => ''
        )
    );
    
    public function replaceTags($rid, $tags=array())
    {
        $usertags = $this->Tag->loadTags();
        $this->emptyTags($rid);
        
        if(!is_array($tags)) {
            $tags = array($tags);
        }
        
        foreach( $tags as &$tag ) {
            $id = substr($tag, 0, 1);
            $rest = substr($tag, 1);
            switch($id) {
                case 't':
                    if(!isset($usertags[$rest])) {
                        $tag = $this->Tag->newTag($rest);
                    } else {
                        $tag = $rest;
                    }
                    break;
                case 'n':                    
                    if( $k=array_search($rest, $usertags) ) {
                        $tag = $k;
                    } else {
                        $tag = $this->Tag->newTag($rest);
                    }                    
                    break;
                default:
                    continue;
                    break;
            } // end fo switch
            
            $this->create();
            $this->save( array( 'DebtTag' => array( 'debt_id' => $rid , 'tag_id' => $tag ) ) );
        }
        
    }
    
    public function emptyTags($rid)
    {
        $this->deleteAll( array('DebtTag.debt_id' => $rid) );
    }
    
}

?>