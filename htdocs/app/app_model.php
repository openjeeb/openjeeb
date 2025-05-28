<?php

class AppModel extends Model {

    var $actsAs = array('PersianDate', 'PersianSanitize', 'PersianDigit');
    var $userId;
    var $ownData = true;
    var $outputConvertDate = true;
    var $inputConvertDate = true;
    var $convertDateFormat = 'j F Y';
    var $convertDateTimeFormat = 'H:i j F Y';
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->userId = Configure::read('user_id');
    }

    function beforeFind($queryData) {
        parent::beforeFind($queryData);
        $this->userId = Configure::read('user_id');
        if ($this->userId AND $this->ownData) {
            if (isset($this->_schema['user_id'])) {
                if (isset($this->userId)) {
                    $queryData['conditions'][$this->name . '.user_id'] = $this->userId;
                }
            }
            if ($this->name == 'User') {
                $queryData['conditions'][$this->name . '.id'] = $this->userId;
            }
        }
        return $queryData;
    }

    function beforeValidate() {
        $this->userId = Configure::read('user_id');
        if ($this->userId AND $this->ownData) {
            if (isset($this->_schema['user_id'])) {
                //get if this is an update
                if ($this->id) {
                    $this->recursive=-1;
                    $userId = $this->field('user_id', array($this->name.'.id' => $this->id));
                    //check if this belongs to the current user
                    if ($this->userId != $userId) {
                        //return false and invalidate the save action if this belongs to another user
                        return false;
                    }
                } else {
                    $this->data[$this->name]['user_id'] = $this->userId;
                }
            }
        }
        return true;
    }
    
    function paginateCount($conditions, $recursive, $extra)
    {
        $parameters = compact('conditions');
        if ($recursive != $this->recursive) {
            $parameters['recursive'] = $recursive;
        }
        $parameters['fields'] = 'COUNT(DISTINCT '.$this->name.'.id) AS cnt';
        unset($extra['group']);
        $c = $this->find('first', array_merge($parameters, $extra));
        return $c[0]['cnt'];
    }
    
    function toggleStatus($aid)
    {
        $this->recursive = -1;        
        if(!$item = $this->find( 'first', array( 'fields'=>array('id','status'), 'conditions'=>array( 'id'=>$aid ) ) ) ){
            return false;
        }
        
        if( @$item[$this->name]['delete'] == 'no' ) {
            return false;
        }
        
        $this->id = $aid;
        $nstatus = ($item[$this->name]['status']=='active')? 'inactive' : 'active';
        return $this->saveField('status', $nstatus)? $nstatus : false;        
    }
    function unbindModelAll() { 
        
        foreach(array( 
                    'hasOne' => array_keys($this->hasOne), 
                    'hasMany' => array_keys($this->hasMany), 
                    'belongsTo' => array_keys($this->belongsTo), 
                    'hasAndBelongsToMany' => array_keys($this->hasAndBelongsToMany) 
            ) as $relation => $model) { 
            $this->unbindModel(array($relation => $model));            
        } 
    } 

}

?>
