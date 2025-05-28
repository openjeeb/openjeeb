<?php

class Debt extends AppModel {

    var $name = 'Debt';
    var $displayField = 'name';
    var $virtualFields = array('settled' => "(SELECT SUM(amount) FROM debt_settlements WHERE debt_settlements.debt_id=Debt.id)");
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'لطفا عنوان را وارد کنید.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
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
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => array('User.id'),
            'order' => ''
        ),
        'Individual' => array(
            'className' => 'Individual',
            'foreignKey' => 'individual_id',
            'conditions' => '',
            'fields' => array('Individual.id','Individual.name'),
            'order' => ''
        )
    );
    var $hasMany = array(
        'DebtSettlement' => array(
            'className' => 'DebtSettlement',
            'foreignKey' => 'debt_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'DebtTag' => array(
            'className' => 'DebtTag',
            'foreignKey' => 'debt_id'
        )
    );
    

    function getDebts($startDate, $endDate, $status=array('due','part'), $order='Debt.due_date ASC', $notify='yes') {
        $this->recursive = 0;
        return $this->find('all', array(
                'conditions' => array(
                    'Debt.due_date >=' => $startDate,
                    'Debt.due_date <=' => $endDate,
                    'Debt.status' => $status,
                    'Debt.notify' => $notify,
                ),
                'order' => $order
            ));
    }

    function beforeSave() {
        if (isset($this->data['Debt']['due_date'])) {
            $this->data['Debt']['pyear'] = $this->pDate($this->data['Debt']['due_date'], 'Y');
            $this->data['Debt']['pmonth'] = $this->pDate($this->data['Debt']['due_date'], 'n');
        }
        return true;
    }

}

?>