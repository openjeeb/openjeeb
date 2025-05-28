<?php

class ServiceTransaction extends AppModel {

    var $name = 'ServiceTransaction';
    var $displayField = 'name';
    /*var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
    );*/
    
    var $belongsTo = array(
        'User'
    );
    
    var $hasOne = array(
        'Service'
    );
    
    /* var $hasMany = array(
        'Service' => array(
            'className' => 'Service',
            'foreignKey' => 'service_id',
            'dependent' => false,
            'conditions' => '',
            /*'fields' => array('Account.id','Account.name'),
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
        
    ); */
    
    function remainingCredit($service, $userid=null)
    {
        $conditions = array( 'service_identifier'=>$service );
        if($userid) {
            $conditions['user_id'] = $userid;
        }
        
        $this->recursive = -1;
        $c = $this->find('first', array(
            'conditions' => $conditions,
            'fields' => array(
                'SUM(creditor-debtor) AS c'
            )
        ));
        return intval(Set::classicExtract($c, '0.c'));
    }
    
    function hasCredit($service, $count, $userid=null)
    {
        return $this->remainingCredit($service, $userid) >= $count;
    }
    
    function debtorCredit($service, $credit, $userid=null)
    {
        $rem = $this->remainingCredit($service, $userid);
        if(!$rem) {
            return false;
        }
        
        $this->inputConvertDate = false;
        $service = $this->Service->getService($service);
        
        $debtor = min($rem,$credit);
        
        $this->create();
        $data = array(
            'service_id' => $service['Service']['id'],
            'service_identifier' => $service['Service']['identifier'],
            'debtor' => $debtor,
            'remain' => $rem - $debtor,
            'date' => date('Y-m-d H:i:s')
        );
        if($userid) {
            $data['user_id'] = $userid;
        }
        $this->save($data);
        
        return true;
    }
    
    function makeCreditor( $service, $credit, $userid=null )
    {
        $rem = $this->remainingCredit($service, $userid);
        
        $this->inputConvertDate = false;
        $service = $this->Service->getService($service);
        
        $this->create();
        $data = array(
            'service_id' => $service['Service']['id'],
            'service_identifier' => $service['Service']['identifier'],
            'creditor' => $credit,
            'remain' => $rem + $credit,
            'date' => date('Y-m-d H:i:s')
        );
        if($userid) {
            $data['user_id'] = $userid;
        }
        $this->save($data);
        
        return true;
        
    }

}

?>