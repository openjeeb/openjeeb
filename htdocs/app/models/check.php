<?php

class Check extends AppModel {

    var $name = 'Check';
    var $validate = array(
        'amount' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'مبلغ بایستی یک عدد باشد.',
                'allowEmpty' => false,
                'required' => true,
            ),
            'range' => array(
                'rule' => array('range', -9999999999999, 9999999999999),
                'message' => 'مبلغ معتبر نیست.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
        'due_date' => array(
            'date' => array(
                'rule' => array('date'),
                'message' => 'تاریخ معتبر نیست.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
    );
    var $belongsTo = array(
        'Bank' => array(
            'className' => 'Bank',
            'foreignKey' => 'bank_id',
            'conditions' => '',
            'fields' => array('Bank.id','Bank.name'),
            'order' => ''
        ),
        'Account' => array(
            'className' => 'Account',
            'foreignKey' => 'account_id',
            'conditions' => '',
            'fields' => array('Account.id','Account.name','Account.bank_id','Account.type'),
            'order' => ''
        ),
        'Individual' => array(
            'className' => 'Individual',
            'foreignKey' => 'individual_id',
            'conditions' => '',
            'fields' => array('Individual.id','Individual.name'),
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => array('User.id'),
            'order' => ''
        ),
        'Transaction' => array(
            'className' => 'Transaction',
            'foreignKey' => 'clear_transaction_id',
            'conditions' => '',
            'fields' => array('Transaction.id'),
            'order' => ''
        ),
    );
    
    var $hasMany = array(
        'CheckTag' => array(
            'className' => 'CheckTag',
            'foreignKey' => 'check_id'
        )
    );

    function getChecks($startDate, $endDate, $status='due', $order='Check.due_date ASC', $notify='yes') {
        $this->recursive = 0;
        return $this->find('all', array(
                'conditions' => array(
                    'Check.due_date >=' => $startDate,
                    'Check.due_date <=' => $endDate,
                    'Check.status' => $status,
                    'Check.notify' => $notify,
                ),
                'order' => $order
            ));
    }

    function beforeSave() {
        if (isset($this->data['Check']['due_date'])) {
            $this->data['Check']['pyear'] = $this->pDate($this->data['Check']['due_date'], 'Y');
            $this->data['Check']['pmonth'] = $this->pDate($this->data['Check']['due_date'], 'n');
        }
        return true;
    }

}

?>