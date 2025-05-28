<?php

class Expense extends AppModel {

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
    }
    
    var $name = 'Expense';
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
            'fields' => array('Transaction.id', 'Transaction.amount', 'Transaction.date', 'Transaction.type'),
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
        'ExpenseCategory' => array(
            'className' => 'ExpenseCategory',
            'foreignKey' => 'expense_category_id',
            'conditions' => '',
            'fields' => array('ExpenseCategory.id', 'ExpenseCategory.name'),
            'order' => ''
        ),
        'ExpenseSubCategory' => array(
            'className' => 'ExpenseSubCategory',
            'foreignKey' => 'expense_sub_category_id',
            'conditions' => '',
            'fields' => array('ExpenseSubCategory.id', 'ExpenseSubCategory.name'),
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
        if ($check['user_id'] != $this->field('user_id', array('Expense.id' => $this->id)) AND $this->id) {
            return false;
        }
        return true;
    }

    function getAmountById($id) {
        $this->field('Expense.amount', array('Expense.id' => intval($id)));
    }

    function saveExpense($data) {
        $dataSource = $this->getDataSource();
        $dataSource->begin($this);
        
        //save transaction
        $data['Transaction']['type'] = 'debt';
        $this->Transaction->inputConvertDate = $this->inputConvertDate;
        $this->Transaction->create();
        if (!$this->Transaction->save($data)) {
            $dataSource->rollback($this);
            return false;
        }
        
        //save expense        
        $transactionId = $this->Transaction->getLastInsertID();
        $data['Expense']['transaction_id'] = $transactionId;
        $this->create();
        if (!$this->save($data)) {
            $dataSource->rollback($this);
            return false;
        }
        
        $this->Transaction->TransactionTag->replaceTags($transactionId, empty($data['TransactionTag']['tag_id'])? array() : $data['TransactionTag']['tag_id'] );
        
        //save expense id back to transaction
        $this->Transaction->id = $data['Expense']['transaction_id'];
        if (!$this->Transaction->saveField('expense_id', $this->getLastInsertID())) {
            $dataSource->rollback($this);
            return false;
        }
        
        //update the account balance
        if (!$this->Transaction->Account->updateBalance($data['Transaction']['account_id'], '-'.$data['Transaction']['amount'])) {
            $dataSource->rollback($this);
            return false;
        }
        
        //commit
        $dataSource->commit($this);
        return $transactionId;
    }

}

?>
