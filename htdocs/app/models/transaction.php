<?php

class Transaction extends AppModel {

    var $name = 'Transaction';
    var $validate = array(
        'amount' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'مبلغ بایستی یک عدد باشد.',
                'allowEmpty' => false,
                'required' => true,
            ),
            'range' => array(
                'rule' => array('range', 0, 9999999999999),
                'message' => 'مبلغ بایستی یک عدد بزرگتر از صفر باشد.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
        'date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'تاریخ معتبر نیست.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
        'pyear' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'مشکلی در ذخیره اطلاعات پیش آمد.'
            ),
        ),
        'pmonth' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'مشکلی در ذخیره اطلاعات پیش آمد.'
            ),
        ),
        'pday' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'مشکلی در ذخیره اطلاعات پیش آمد.'
            ),
        ),
        'user_id' => array(
            'rule' => array('owner'),
            'message' => 'مشکلی در ذخیره اطلاعات پیش آمد.'
        )
    );

    var $hasOne = array(
        'Expense' => array(
            'className' => 'Expense',
            'foreignKey' => 'transaction_id',
            'conditions' => '',
            'fields' => array('Expense.id','Expense.transaction_id','Expense.description'),
            'order' => '',
            'dependent' => true
        ),
        'Income' => array(
            'className' => 'Income',
            'foreignKey' => 'transaction_id',
            'conditions' => '',
            'fields' => array('Income.id','Income.transaction_id ','Income.description'),
            'order' => '',
            'dependent' => true
        )/*,
        'TransferCredit' => array (
            'className' => 'Transfer',
            'foreignKey'=>false,
            'conditions' => 'TransferCredit.transaction_credit_id=Transaction.id'
            ),
        'TransferDebt' => array (
            'className' => 'Transfer',
            'foreignKey'=>false,
            'conditions' => 'TransferDebt.transaction_debt_id=Transaction.id'
            )*/
    );
    
    var $belongsTo = array(
        'Account' => array(
            'className' => 'Account',
            'foreignKey' => 'account_id',
            'conditions' => '',
            'fields' => array('Account.id','Account.name','Account.bank_id','Account.type'),
            'order' => ''
        ),
//        'Expense' => array(
//            'className' => 'Expense',
//            'foreignKey' => 'expense_id',
//            'conditions' => '',
//            'fields' => array('Expense.id','Expense.transaction_id','Expense.description'),
//            'order' => ''
//        ),
//        'Income' => array(
//            'className' => 'Income',
//            'foreignKey' => 'income_id',
//            'conditions' => '',
//            'fields' => array('Income.id','Income.transaction_id ','Income.description'),
//            'order' => ''
//        ),
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
            'foreignKey' => 'transaction_id'
        )
    );

//    function beforeSave() {
//        if(isset($this->data['Transaction']['date'])) {
//            $this->data['Transaction']['pyear'] = $this->pDate($this->data['Transaction']['date'], 'Y');
//            $this->data['Transaction']['pmonth'] = $this->pDate($this->data['Transaction']['date'], 'n');
//            $this->data['Transaction']['pday'] = $this->pDate($this->data['Transaction']['date'], 'd');
//        }
//        return true;
//    }

    function owner($check) {
        $this->recursive = -1;
        if ($check['user_id'] != $this->field('user_id', array('Transaction.id' => $this->id)) AND $this->id) {
            return false;
        }
        return true;
    }
    
    function deleteAndUpdateBalance($id){
        $transaction=$this->read(array('id','amount','account_id','type'),intval($id));
        if(!$this->delete(intval($id))) {
            return false;
        }
        $amount=$transaction['Transaction']['amount'];
        if($transaction['Transaction']['type']=='credit') {
          $amount=-1*$amount;
        }
        if(!$this->Account->updateBalance($transaction['Transaction']['account_id'],$amount)) {
            return false;
        }
        return true;
    }

}

?>