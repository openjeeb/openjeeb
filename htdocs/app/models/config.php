<?php

class Config extends AppModel {
    
    var $name = 'Config';
    
    var $recursive = -1;
    
    function setValue($comp, $key, $val=NULL)
    {
        $key = $comp.'.'.$key;
        $id = $this->find('first',array('conditions'=>array('key'=>$key)));
        if(isset($id['Config']['id'])){
            $id = $id['Config']['id'];
        }else{
            $this->create();
            $this->save(array('Config'=>array('key'=>$key)));
            $id = $this->getLastInsertID();            
        }
        $this->id = $id;
        switch($type = gettype($val)) {
            case 'array':
            case 'object':
                $val = serialize($val);
            case 'string':
                break;
            case 'boolean':
                $val = intval($val);
            case 'double':
            case 'integer':
                break;
        }
        $this->save(array(
            'Config' => array('value' => $val, 'type' => $type)
        ));
    }
    
    function getValue($comp,$key,$uid=null)
    {
        $key = $comp.'.'.$key;
        $this->recursive = -1;
        $cond = array( 'key' => $key );
        if( $uid ) {
            $cond['Config.user_id'] = $uid;
        }
        $r = $this->find( 'first' , array(
            'conditions' => $cond
        ) );
        if(!$r) return false;
        
        return $this->parseValue($r['Config']['value'], $r['Config']['type']);
    }
    
    function getAll($comp)
    {
        $r = array();
        foreach ( $this->find( 'all' , array( 'conditions' => array( 'key LIKE' => $comp.'.%' ) ) ) as $val) {
            $val['Config']['key'] = explode('.',$val['Config']['key'],2);
            $r[$val['Config']['key'][1]] = $this->parseValue($val['Config']['value'], $val['Config']['type']);
        }
        return $r;
    }
    
    function parseValue($value, $type)
    {
        switch($type) {
            case 'integer':
                return intval($value);
                break;
            case 'double':
                return doubleval($value);
                break;
            case 'string':
                return $value;
                break;
            case 'boolean':
                return ($value==0);
                break;
            case 'array':
            case 'object':
                return unserialize($value);
                break;            
            default:
                return false;
        }
    }
    
    function setupConfig($uid=null)
    {
        if( $uid ) {
            Configure::write('user_id' , $uid);
        }
        $this->setValue( 'reminder', 'check_freq', 'theday,1,3,7');
        $this->setValue( 'reminder', 'check_medium', 'email,sms' );
        $this->setValue( 'reminder', 'note_freq', 'theday,1,3,7');
        $this->setValue( 'reminder', 'note_medium', 'email,sms' );
        $this->setValue( 'reminder', 'debt_freq', 'theday,1,3,7' );
        $this->setValue( 'reminder', 'debt_medium', 'email,sms' );
        $this->setValue( 'reminder', 'installment_freq', 'theday,1,3,7' );
        $this->setValue( 'reminder', 'installment_medium', 'email,sms' );
        $this->setValue( 'reminder', 'loginreminder_medium', 'email,sms' );
    }
    
}

?>
