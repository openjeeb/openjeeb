<?php

class Income extends AppModel {

    var $name = 'Income';
    var $validate = array(
        'user_id' => array(
            'rule' => array('owner'),
            'message' => 'مشکلی در ذخیره اطلاعات پیش آمد.'
        )
    );
    var $belongsTo = array(
        'Transaction' => array(
            'className' => 'Transaction',
            'foreignKey' => 'transaction_id',
            'conditions' => '',
            'fields' => array('Transaction.id','Transaction.amount','Transaction.date','Transaction.type'),
            'order' => '',
            'dependent' => true
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => array('User.id'),
            'order' => ''
        ),
        'IncomeType' => array(
            'className' => 'IncomeType',
            'foreignKey' => 'income_type_id',
            'conditions' => '',
            'fields' => array('IncomeType.id','IncomeType.name'),
            'order' => ''
        ),
        'IncomeSubType' => array(
            'className' => 'IncomeSubType',
            'foreignKey' => 'income_sub_type_id',
            'conditions' => '',
            'fields' => array('IncomeSubType.id','IncomeSubType.name'),
            'order' => ''
        ),
        'Individual' => array(
            'className' => 'Individual',
            'foreignKey' => 'individual_id',
            'conditions' => '',
            'fields' => array('Individual.id', 'Individual.name'),
            'order' => ''
        ),
    );

    function owner($check) {
        $this->recursive = -1;
        if ($check['user_id'] != $this->field('user_id', array('Income.id' => $this->id)) AND $this->id) {
            return false;
        }
        return true;
    }

    function saveIncome($data) {
        $dataSource = $this->getDataSource();
        $dataSource->begin($this);
        //save transaction
        $data['Transaction']['type'] = 'credit';
        $this->Transaction->inputConvertDate=$this->inputConvertDate;
        $this->Transaction->create();
        if (!$this->Transaction->save($data)) {
            $dataSource->rollback($this);
            return false;
        }
        //save income
        $transactionId = $this->Transaction->getLastInsertID();
        $data['Income']['transaction_id'] = $transactionId;
        $this->create();
        if (!$this->save($data)) {
            $dataSource->rollback($this);
            return false;
        }
        //save income id back to transaction
        $this->Transaction->id = $data['Income']['transaction_id'];
        if (!$this->Transaction->saveField('income_id', $this->getLastInsertID())) {
            $dataSource->rollback($this);
            return false;
        }
        
        $this->Transaction->TransactionTag->replaceTags($transactionId, empty($data['TransactionTag']['tag_id'])? array() : $data['TransactionTag']['tag_id'] );
        
        //update the account balance
        if (!$this->Transaction->Account->updateBalance($data['Transaction']['account_id'], $data['Transaction']['amount'])) {
            $dataSource->rollback($this);
            return false;
        }
        //commit
        $dataSource->commit($this);
        return $transactionId;
    }

}

?>