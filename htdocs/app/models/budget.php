<?php

class Budget extends AppModel {

    var $name = 'Budget';
    var $displayField = 'expense_category_id';
    //var $virtualFields = array('settled' => "(SELECT SUM(amount) FROM debt_settlements WHERE debt_settlements.debt_id=Debt.id)");
    var $belongsTo = array(
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
            'fields' => array('ExpenseCategory.id, ExpenseCategory.name'),
            'order' => ''
        )
    );
    
    public function checkDuplicity($category, $month, $year, $id=0)
    {
        $this->recursive = -1;
        $res = $this->find('first', array(
            'conditions' => array(
                'Budget.expense_category_id ' => $category,
                'Budget.pyear' => $year,
                'Budget.pmonth' => $month,
                'Budget.id <> ' => $id
            )
        ));
        
        return isset($res['Budget']['id'])? $res['Budget']['id'] : false;
    }
    
}

?>