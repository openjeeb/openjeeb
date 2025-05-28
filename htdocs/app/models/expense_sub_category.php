<?php

class ExpenseSubCategory extends AppModel {

    var $name = 'ExpenseSubCategory';
    var $displayField = 'name';
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'لطفا عنوان را وارد کنید.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
    );
    var $belongsTo = array(
        'ExpenseCategory' => array(
            'className' => 'ExpenseCategory',
            'foreignKey' => 'expense_category_id',
            'conditions' => '',
            'fields' => array('ExpenseCategory.id','ExpenseCategory.name'),
            'order' => ''
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => array('User.id'),
            'order' => ''
        )
    );
    var $hasMany = array(
        'Expense' => array(
            'className' => 'Expense',
            'foreignKey' => 'expense_sub_category_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => array('Expense.id','Expense.transaction_id','Expense.description'),
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );
}

?>