<?php

class Account extends AppModel {

    var $name = 'Account';
    var $displayField = 'name';
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            'message' => 'لطفا عنوان حساب را وارد کنید',
            'allowEmpty' => false,
            ),
        ),
        'balance' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            'message' => 'موجودی حساب بایستی یک عدد باشد',
            'allowEmpty' => false,
            ),
        ),
        'init_balance' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            'message' => 'موجودی حساب بایستی یک عدد باشد',
            'allowEmpty' => false,
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
            'fields' => '',
            'order' => ''
        ),
        'Bank' => array(
            'className' => 'Bank',
            'foreignKey' => 'bank_id',
            'conditions' => '',
            'fields' => array('Bank.id','Bank.name'),
            'order' => ''
        ),
    );
    var $hasMany = array(
        'Transaction' => array(
            'className' => 'Transaction',
            'foreignKey' => 'account_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => array('Transaction.id','Transaction.amount','Transaction.date','Transaction.type'),
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

    function updateBalance($id, $amount) {
        if (floatval($amount)==0) {
            return true;
        }
        //choose operation
        $operation='';
        if($amount>0) {
            $operation='+';
        }
        //update balance
        if ($this->updateAll(
                array('Account.balance' => 'Account.balance '. $operation . (floatval($amount))), array('Account.id' => intval($id)))) {
            return true;
        }
        return false;
    }

    function owner($check) {
        $this->recursive = -1;
        if ($check['user_id'] != $this->field('user_id', array('Account.id' => $this->id)) AND $this->id) {
            return false;
        }
        return true;
    }
    
    function listAccounts($type=null)
    {
        $cond = array(
            'Account.status' => 'active'
        );
        if($type){
            $cond['Account.type'] = $type; 
        }
        $recursive = $this->recursive;
        $this->recursive = -1;
        $list = $this->find( 'all' , array(
            'fields' => 'Account.id,Account.name,Account.balance',
            'conditions' => $cond,
            'order' => 'Account.sort ASC'
            ) );
        $this->recursive = $recursive;
        
        $accounts = $accountsbalance = array();
        foreach($list as $val){
            $accounts[$val['Account']['id']] = $val['Account']['name']; 
            $accountsbalance[$val['Account']['id']] = $val['Account']['balance'];
        }
        
        return array($accounts,$accountsbalance);
    }
    
    function moveSortItem($sid, $orientation)
    {
        $ort = ($orientation=='up')? "<=" : ">=";
        $ord = ($orientation=='up')? 'DESC' : 'ASC';
        $this->recursive = -1;
        
        if(count( $accounts = $this->find('all', array(
            'fields' => 'Account.id, Account.sort',
            'conditions'=> array(
                'sort '.$ort=>intval($sid)
            ),
            'order' => 'sort '.$ord,
            'limit' => 2) ) )<2 ){
            return true;
         };
         
         $data = array(
             array(
                 'id' => $accounts[0]['Account']['id'],
                 'sort' => $accounts[1]['Account']['sort']
             ),
             array(
                 'id' => $accounts[1]['Account']['id'],
                 'sort' => $accounts[0]['Account']['sort']
             ));
         
         return $this->saveAll($data, array('atomic'=>true));
    }

}

?>