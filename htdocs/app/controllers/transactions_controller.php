<?php

uses('sanitize');

class TransactionsController extends AppController {

    var $name = 'Transactions';
    var $uses = array('Transaction','Transfer', 'Tag', 'TransactionTag' );
    
    function index()
    {
        $this->set('title_for_layout', 'تراکنش‌ها');

        //search
        $conditions = array();
        if (isset($this->data['Transaction']['search'])) {
            if (!empty($this->data['Transaction']['transaction_type']) AND $this->data['Transaction']['transaction_type']!='all') {
                if($this->data['Transaction']['transaction_type']=='transfer') {
                    $conditions['Transaction.expense_id'] = NULL;
                    $conditions['Transaction.income_id'] = NULL;
                } else {
                    $conditions['Transaction.type'] = $this->data['Transaction']['transaction_type'];
                }
            }
            if (!empty($this->data['Transaction']['account_id'])) {
                $conditions['Transaction.account_id'] = $this->data['Transaction']['account_id'];
            }
            if (!empty($this->data['Transaction']['amount'])) {
                $conditions['Transaction.amount'] = floatval( str_replace( ',', '', $this->data['Transaction']['amount'] ) );
            }
            if (!empty($this->data['Transaction']['individual_id'])) {
                $conditions['OR'] = array(
                    'Expense.individual_id'=>$this->data['Transaction']['individual_id'],
                    'Income.individual_id'=>$this->data['Transaction']['individual_id'],
                );
            }
            if (!empty($this->data['Transaction']['start_date'])) {
                $persianDate = new PersianDate();
                $this->data['Transaction']['start_date'] = $persianDate->pdate_format_reverse($this->data['Transaction']['start_date']);
                $conditions['Transaction.date >='] = $this->data['Transaction']['start_date'];
            }
            if (!empty($this->data['Transaction']['end_date'])) {
                $persianDate = new PersianDate();
                $this->data['Transaction']['end_date'] = $persianDate->pdate_format_reverse($this->data['Transaction']['end_date']);
                $conditions['Transaction.date <='] = $this->data['Transaction']['end_date'];
            }
            
            $catlist = array();
            if( empty($this->data['TransactionTagSearch']['tag_id']) || (count($this->data['TransactionTagSearch']['tag_id'])==1 && $this->data['TransactionTagSearch']['tag_id'][0]=='0') ){
                //$catlist = $subcatlist = null;
            } else {
                foreach($this->data['TransactionTagSearch']['tag_id'] as $k=>$v) {
                    switch( substr($v, 0,1) ) {
                        case 't':
                            $catlist[] = substr($v, 1);
                            break;
                        default:
                            unset($this->data['TransactionTagSearch']['tag_id'][$k]);
                            break;
                    }
                }
                if ( $catlist ) {
                    $conditions['TransactionTag.tag_id'] = $catlist;
                }
                $this->set( 'report_catlist' , $this->data['TransactionTagSearch']['tag_id'] );
            }
            
            //save it into session
            $this->Session->delete('Transaction.conditions');
            $this->Session->write('Transaction.conditions', $conditions);
        }
        //reset the conditions
        if (empty($this->params['named']) AND empty($this->data)) {
            $this->Session->delete('Transaction.conditions');
        }
        //apply the conditions
        if ($this->Session->check('Transaction.conditions') AND !empty($this->params['named'])) {
            $conditions = $this->Session->read('Transaction.conditions');
        }
        
        $this->loadTags();
        
        //get transactions
        $this->Transaction->convertDateFormat = 'Y/m/d';
        $this->Transaction->recursive = 0;
        $this->Transaction->bindModel(array(
            'hasOne' => array(
                'ExpenseCategory' => array(
                    'foreignKey' => false,
                    'conditions' => array('Expense.expense_category_id = ExpenseCategory.id')
                ),
                'ExpenseSubCategory' => array(
                    'foreignKey' => false,
                    'conditions' => array('Expense.expense_sub_category_id=ExpenseSubCategory.id')
                ),
                'IncomeType' => array(
                    'foreignKey' => false,
                    'conditions' => array('Income.income_type_id=IncomeType.id')
                ),
                'IncomeSubType' => array(
                    'foreignKey' => false,
                    'conditions' => array('Income.income_sub_type_id=IncomeSubType.id')
                ),
                'IndividualExpense' => array(
                    'className' => 'Individual',
                    'foreignKey' => false,
                    'conditions' => array('Income.individual_id=IndividualExpense.id')
                ),
                'IndividualIncome' => array(
                    'className' => 'Individual',
                    'foreignKey' => false,
                    'conditions' => array('Expense.individual_id=IndividualIncome.id')
                ),
                'TransactionTag' => array(
                        'fields' => 'CONCAT_WS(",", TransactionTag.tag_id )',
                        'foreignKey' => false,
                        'conditions' => array( 'TransactionTag.transaction_id = Transaction.id' )
                    )
            )), false);
        $this->paginate['fields'] = 'Transaction.id, Transaction.amount, Transaction.date, Transaction.pyear, Transaction.pmonth, Transaction.pday, Transaction.type, Transaction.account_id, Transaction.expense_id, Transaction.income_id, Transaction.user_id, Transaction.created, Transaction.modified, Account.id, Account.name, Account.bank_id, Account.type, User.id, Expense.id, Expense.transaction_id, Expense.description, Income.id, Income.transaction_id, Income.description, ExpenseCategory.id, ExpenseCategory.name, ExpenseCategory.user_id, ExpenseCategory.delete, ExpenseCategory.sort, ExpenseCategory.status, ExpenseCategory.created, ExpenseCategory.modified, ExpenseSubCategory.id, ExpenseSubCategory.name, ExpenseSubCategory.expense_category_id, ExpenseSubCategory.user_id, ExpenseSubCategory.created, ExpenseSubCategory.modified, IncomeType.id, IncomeType.name, IncomeType.user_id, IncomeType.delete, IncomeType.sort, IncomeType.status, IncomeType.created, IncomeType.modified, IncomeSubType.id, IncomeSubType.name, IncomeSubType.income_type_id, IncomeSubType.user_id, IncomeSubType.created, IncomeSubType.modified, IndividualExpense.id, IndividualExpense.name, IndividualExpense.description, IndividualExpense.user_id, IndividualExpense.sort, IndividualExpense.status, IndividualExpense.created, IndividualExpense.modified, IndividualIncome.id, IndividualIncome.name, IndividualIncome.description, IndividualIncome.user_id, IndividualIncome.sort, IndividualIncome.status, IndividualIncome.created, IndividualIncome.modified, CONCAT_WS(",", TransactionTag.tag_id )';
        $this->paginate['order'] = 'Transaction.date DESC, Transaction.id DESC';
        $this->paginate['conditions'] = $conditions;
        $this->paginate['group'] = 'Transaction.id';
        $data = $this->paginate();
        
        $where = $this->Transaction->getDataSource()->conditions($conditions+array('Transaction.user_id'=>$this->Auth->user( 'id' )), true, true, $this->Transaction);
       
        foreach($data as &$item){
            if(empty($item['Transaction']['income_id']) && empty($item['Transaction']['expense_id'])){
                $item['Transfer'] = Set::extract('Transfer',$this->Transfer->find('first', array(
                    'conditions'=> array(
                        'OR'=>array(
                            'transaction_debt_id' => $item['Transaction']['id'],
                            'transaction_credit_id' => $item['Transaction']['id']
                        )
                        ),
                    'limit'=>1
                )));
                $accid = ($item['Transfer']['transaction_debt_id']==$item['Transaction']['id'])? $item['Transfer']['transaction_credit_id'] : $item['Transfer']['transaction_debt_id'];
                $this->Transaction->recursive = 0;
                $item['Transfer']['Account'] = Set::extract('Account',$this->Transaction->find('first',array( 'conditions' => array( 'Transaction.id'=>$accid )  )));
            }
        }
        $this->set('transactions', $data);
        
        $sql = "SELECT 
                    SUM(IF(type='debt',amount,0)) AS sum_debt,
                    SUM(IF(type='credit',amount,0)) AS sum_credit
                    FROM 
                        (SELECT `Transaction`.`type`, `Transaction`.`amount`
                        FROM `transactions` AS `Transaction`
                        LEFT JOIN `accounts` AS `Account` ON (`Transaction`.`account_id` = `Account`.`id`)
                        LEFT JOIN `users` AS `User` ON (`Transaction`.`user_id` = `User`.`id`)
                        LEFT JOIN `expenses` AS `Expense` ON (`Expense`.`transaction_id` = `Transaction`.`id`)
                        LEFT JOIN `incomes` AS `Income` ON (`Income`.`transaction_id` = `Transaction`.`id`)
                        LEFT JOIN `expense_categories` AS `ExpenseCategory` ON (`Expense`.`expense_category_id` = `ExpenseCategory`.`id`)
                        LEFT JOIN `expense_sub_categories` AS `ExpenseSubCategory` ON (`Expense`.`expense_sub_category_id`=`ExpenseSubCategory`.`id`)
                        LEFT JOIN `income_types` AS `IncomeType` ON (`Income`.`income_type_id`=`IncomeType`.`id`)
                        LEFT JOIN `income_sub_types` AS `IncomeSubType` ON (`Income`.`income_sub_type_id`=`IncomeSubType`.`id`)
                        LEFT JOIN `individuals` AS `IndividualExpense` ON (`Income`.`individual_id`=`IndividualExpense`.`id`)
                        LEFT JOIN `individuals` AS `IndividualIncome` ON (`Expense`.`individual_id`=`IndividualIncome`.`id`)
                        LEFT JOIN `transaction_tags` AS `TransactionTag` ON (`TransactionTag`.`transaction_id` = `Transaction`.`id`)
                        $where
                        GROUP BY Transaction.id)t";
        $sum = $this->Transaction->query($sql);
        
        $this->set('debtSum',$sum[0][0]['sum_debt']);
        $this->set('creditSum',$sum[0][0]['sum_credit']);
        
        //get accounts
        list($accounts,$accountsbalance) = $this->Transaction->Account->listAccounts();
        $this->set(compact('accounts','accountsbalance'));
        
        //get individuals
        $individuals = $this->Transaction->Income->Individual->fetchList();
        $this->set(compact('individuals'));

        //get expense categories
        $expenseCategories = $this->Transaction->Expense->ExpenseCategory->fetchList();
        $this->set(compact('expenseCategories'));

        //get the whole category subcategory data
        $this->Transaction->Expense->ExpenseCategory->recursive = -1;
        $expenseCategories = $this->Transaction->Expense->ExpenseCategory->find('all', array(
            'contain' => array('ExpenseSubCategory' => array('order' => 'name ASC')),
            'order' => 'ExpenseCategory.sort ASC'
            ));
        $expenseCategoriesData = array();
        $i = 0;
        foreach ($expenseCategories as $entry) {
            $expenseCategoriesData[$i]['id'] = $entry['ExpenseCategory']['id'];
            $expenseCategoriesData[$i]['name'] = $entry['ExpenseCategory']['name'];
            $expenseCategoriesData[$i]['subs'] = array();
            foreach ($entry['ExpenseSubCategory'] as $entry2) {
                $expenseCategoriesData[$i]['subs'][] = array(
                    'id' => $entry2['id'],
                    'name' => $entry2['name'],
                );
            }
            $i++;
        }
        $this->set(compact('expenseCategoriesData'));

        //get income types
        $incomeTypes = $this->Transaction->Income->IncomeType->fetchList();
        $this->set(compact('incomeTypes'));

        //get the whole type subtype data
        $this->Transaction->Income->IncomeType->recursive = -1;
        $incomeTypes = $this->Transaction->Income->IncomeType->find('all', array(
            'contain' => array('IncomeSubType' => array('order' => 'name ASC')),
            'order' => 'IncomeType.sort ASC'
            ));
        $incomeTypesData = array();
        $i = 0;
        foreach ($incomeTypes as $entry) {
            $incomeTypesData[$i]['id'] = $entry['IncomeType']['id'];
            $incomeTypesData[$i]['name'] = $entry['IncomeType']['name'];
            $incomeTypesData[$i]['subs'] = array();
            foreach ($entry['IncomeSubType'] as $entry2) {
                $incomeTypesData[$i]['subs'][] = array(
                    'id' => $entry2['id'],
                    'name' => $entry2['name'],
                );
            }
            $i++;
        }
        $this->set(compact('incomeTypesData'));
        
        // chart of debts
        $sql = "SELECT
                    CONCAT(pyear,'/',pmonth) AS k,
                    SUM(ABS(amount)) AS value
                    FROM 
                        (SELECT Transaction.id, Transaction.amount AS amount, Transaction.pyear, Transaction.pmonth
                        FROM `transactions` AS `Transaction`
                        LEFT JOIN `accounts` AS `Account` ON (`Transaction`.`account_id` = `Account`.`id`)
                        LEFT JOIN `users` AS `User` ON (`Transaction`.`user_id` = `User`.`id`)
                        LEFT JOIN `expenses` AS `Expense` ON (`Expense`.`transaction_id` = `Transaction`.`id`)
                        LEFT JOIN `incomes` AS `Income` ON (`Income`.`transaction_id` = `Transaction`.`id`)
                        LEFT JOIN `expense_categories` AS `ExpenseCategory` ON (`Expense`.`expense_category_id` = `ExpenseCategory`.`id`)
                        LEFT JOIN `expense_sub_categories` AS `ExpenseSubCategory` ON (`Expense`.`expense_sub_category_id`=`ExpenseSubCategory`.`id`)
                        LEFT JOIN `income_types` AS `IncomeType` ON (`Income`.`income_type_id`=`IncomeType`.`id`)
                        LEFT JOIN `income_sub_types` AS `IncomeSubType` ON (`Income`.`income_sub_type_id`=`IncomeSubType`.`id`)
                        LEFT JOIN `individuals` AS `IndividualExpense` ON (`Income`.`individual_id`=`IndividualExpense`.`id`)
                        LEFT JOIN `individuals` AS `IndividualIncome` ON (`Expense`.`individual_id`=`IndividualIncome`.`id`)
                        LEFT JOIN `transaction_tags` AS `TransactionTag` ON (`TransactionTag`.`transaction_id` = `Transaction`.`id`)
                        $where AND `Transaction`.`type` = 'debt'
                        GROUP BY Transaction.id)t
                GROUP BY k
                ORDER BY pyear ASC, pmonth ASC";
        $debtTransactionsColumn = $this->Transaction->query($sql);
        $this->set('debtTransactionsColumn', Set::classicExtract($debtTransactionsColumn, '{n}.0'));
        
        // chart of credits
        $sql = "SELECT
                    CONCAT(pyear,'/',pmonth) AS k,
                    SUM(ABS(amount)) AS value
                    FROM 
                        (SELECT Transaction.id, Transaction.amount AS amount, Transaction.pyear, Transaction.pmonth
                        FROM `transactions` AS `Transaction`
                        LEFT JOIN `accounts` AS `Account` ON (`Transaction`.`account_id` = `Account`.`id`)
                        LEFT JOIN `users` AS `User` ON (`Transaction`.`user_id` = `User`.`id`)
                        LEFT JOIN `expenses` AS `Expense` ON (`Expense`.`transaction_id` = `Transaction`.`id`)
                        LEFT JOIN `incomes` AS `Income` ON (`Income`.`transaction_id` = `Transaction`.`id`)
                        LEFT JOIN `expense_categories` AS `ExpenseCategory` ON (`Expense`.`expense_category_id` = `ExpenseCategory`.`id`)
                        LEFT JOIN `expense_sub_categories` AS `ExpenseSubCategory` ON (`Expense`.`expense_sub_category_id`=`ExpenseSubCategory`.`id`)
                        LEFT JOIN `income_types` AS `IncomeType` ON (`Income`.`income_type_id`=`IncomeType`.`id`)
                        LEFT JOIN `income_sub_types` AS `IncomeSubType` ON (`Income`.`income_sub_type_id`=`IncomeSubType`.`id`)
                        LEFT JOIN `individuals` AS `IndividualExpense` ON (`Income`.`individual_id`=`IndividualExpense`.`id`)
                        LEFT JOIN `individuals` AS `IndividualIncome` ON (`Expense`.`individual_id`=`IndividualIncome`.`id`)
                        LEFT JOIN `transaction_tags` AS `TransactionTag` ON (`TransactionTag`.`transaction_id` = `Transaction`.`id`)
                        $where AND `Transaction`.`type` = 'credit'
                        GROUP BY Transaction.id)t
                GROUP BY k
                ORDER BY pyear ASC, pmonth ASC";
        $creditTransactionsColumn = $this->Transaction->query($sql);
        $this->set('creditTransactionsColumn', Set::classicExtract($creditTransactionsColumn, '{n}.0'));
        
        //add
        if (!empty($this->data) AND !isset($this->data['Transaction']['search'])) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            
            switch ($this->data['Transaction']['type']) {

                case 'transfer':
                    //fix posted data
                    $this->data['Transaction']['amount'] = floatval(str_replace(',', '', $this->data['Transaction']['transfer_amount']));
                    $this->data['Transaction']['date'] = $this->data['Transaction']['transfer_date'];
                    //add transfer
                    if (!$this->_addTransfer($this->data)) {
                        return false;
                    }
                    break;

                case 'expense':
                    //fix posted data
                    $this->data['Transaction']['amount'] = floatval(str_replace(',', '', $this->data['Transaction']['expense_amount']));
                    $this->data['Transaction']['date'] = $this->data['Transaction']['expense_date'];
                    $this->data['Transaction']['account_id'] = $this->data['Transaction']['expense_account_id'];
                    //add expense
                    if (!$this->_addExpense($this->data)) {
                        return false;
                    }
                    break;

                case 'income':
                    //fix posted data
                    $this->data['Transaction']['amount'] = floatval(str_replace(',', '', $this->data['Transaction']['income_amount']));
                    $this->data['Transaction']['date'] = $this->data['Transaction']['income_date'];
                    $this->data['Transaction']['account_id'] = $this->data['Transaction']['income_account_id'];
                    //add income
                    if (!$this->_addIncome($this->data)) {
                        return false;
                    }
                    break;
            }
            $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
            return true;
        }
    }

    function delete($id = null) {
        
        if (!$id) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        //get transaction
        $this->Transaction->recursive = -1;
        $transaction = $this->Transaction->read(null, intval($id));
        if (!is_null($transaction['Transaction']['expense_id']) AND !is_null($transaction['Transaction']['income_id'])) {
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        
        if(!$transaction['Transaction']['expense_id'] && !$transaction['Transaction']['income_id']){            
            $this->Transfer->recursive = -1;
            $transfer = $this->Transfer->find( 'first' , array(
                'conditions' => array( 'transaction_'.$transaction['Transaction']['type'].'_id' => $transaction['Transaction']['id'] )
            ));
        }
        
        //start transaction
        $dataSource = $this->Transaction->getDataSource();
        $dataSource->begin($this->Transaction);
        
        if(!$this->_deleteTransaction($transaction)){
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $dataSource->rollback($this->Transaction);
        }
        
        if($transfer){            
            $second = ($transaction['Transaction']['type']=='debt')? $transfer['Transfer']['transaction_credit_id'] : $transfer['Transfer']['transaction_debt_id'];
            $transaction = $this->Transaction->read(null, intval($second));
            if(!$this->_deleteTransaction($transaction)){
                $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                $dataSource->rollback($this->Transaction);
            }
        }
        
        //commit
        $dataSource->commit($this->Transaction);
        
        $this->Session->setFlash('داده مورد نظر با موفقیت پاک شد.', 'default', array('class' => 'success'));
        $this->redirect(Controller::referer());
        return true;
    }

    function export($limit=null) {
        //set memory limit
        ini_set( 'memory_limit', '1024M' );
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion(8);
        $workbook->send('transactions.xls');
        $worksheet = & $workbook->addWorksheet('transactions');
        $worksheet->setInputEncoding('utf-8');

        //get the data
        $this->Transaction->recursive = 0;
        $this->Transaction->outputConvertDate = true;
        $this->Transaction->convertDateFormat = 'Y/m/d';
        $this->Transaction->recursive = 0;
        $this->Transaction->bindModel(array(
            'hasOne' => array(
                'ExpenseCategory' => array(
                    'foreignKey' => false,
                    'conditions' => array('Expense.expense_category_id = ExpenseCategory.id')
                ),
                'ExpenseSubCategory' => array(
                    'foreignKey' => false,
                    'conditions' => array('Expense.expense_sub_category_id=ExpenseSubCategory.id')
                ),
                'IncomeType' => array(
                    'foreignKey' => false,
                    'conditions' => array('Income.income_type_id=IncomeType.id')
                ),
                'IncomeSubType' => array(
                    'foreignKey' => false,
                    'conditions' => array('Income.income_sub_type_id=IncomeSubType.id')
                ),
                'IndividualExpense' => array(
                    'className' => 'Individual',
                    'foreignKey' => false,
                    'conditions' => array('Income.individual_id=IndividualExpense.id')
                ),
                'IndividualIncome' => array(
                    'className' => 'Individual',
                    'foreignKey' => false,
                    'conditions' => array('Expense.individual_id=IndividualIncome.id')
                ),
                'TransactionTag' => array(
                        'fields' => 'CONCAT_WS(",", TransactionTag.tag_id )',
                        'foreignKey' => false,
                        'conditions' => array( 'TransactionTag.transaction_id = Transaction.id' )
                    )
            )), false);
        $options = array();
        $options['fields'] = array('Transaction.id, Transaction.amount', 'Transaction.date', 'Transaction.type', 'Transaction.expense_id', 'Transaction.income_id', 'Transaction.created', 'Expense.description', 'ExpenseCategory.name', 'ExpenseSubCategory.name', 'Income.description', 'IncomeType.name', 'Account.name');
        $options['order'] = 'Transaction.date DESC, Expense.id DESC, Income.id DESC';
        if ( $this->Session->check( 'Transaction.conditions' ) ) {
            $options['conditions'] = $this->Session->read( 'Transaction.conditions' );
        }
        if (!is_null($limit)) {
            $options['limit'] = $limit;
        }
        $transactions = $this->Transaction->find('all', $options);

        $data = array();
        $data[] = array('نوع', 'تاریخ', 'هزینه', 'درآمد', 'نوع هزینه/درآمد', 'توضیحات', 'حساب', 'تاریخ ایجاد');
        $i = 1;
        foreach ($transactions as $entry) {
            if ($entry['Transaction']['expense_id']) {
                $data[$i][] = 'هزینه';
            } elseif ($entry['Transaction']['income_id']) {
                $data[$i][] = 'درآمد';
            } else {
                $entry['Transfer'] = Set::extract('Transfer',$this->Transfer->find('first', array(
                    'conditions'=> array(
                            'transaction_'.$entry['Transaction']['type'].'_id' => $entry['Transaction']['id']
                        ),
                    'limit'=>1
                )));
                $data[$i][] = 'انتقال وجه';
            }
            $data[$i][] = $entry['Transaction']['date'];
            if ($entry['Transaction']['type'] == 'debt') {
                $data[$i][] = $entry['Transaction']['amount'];
            } else {
                $data[$i][] = ' ';
            }
            if ($entry['Transaction']['type'] == 'credit') {
                $data[$i][] = $entry['Transaction']['amount'];
            } else {
                $data[$i][] = ' ';
            }
            if ($entry['Transaction']['expense_id']) {
                if (!empty($entry['ExpenseSubCategory']['name'])) {
                    $data[$i][] = $entry['ExpenseCategory']['name'] . '>>' . $entry['ExpenseSubCategory']['name'];
                } else {
                    $data[$i][] = $entry['ExpenseCategory']['name'];
                }
            } elseif ($entry['Transaction']['income_id']) {
                $data[$i][] = $entry['IncomeType']['name'];
            } else {
                $data[$i][] = '';
            }
            if ($entry['Transaction']['expense_id']) {
                $data[$i][] = $entry['Expense']['description'];
            } elseif ($entry['Transaction']['income_id']) {
                $data[$i][] = $entry['Income']['description'];
            } elseif(isset($entry['Transfer']['id'])) {
                # fetch data of the other transaction in transfer
                $accid = ($entry['Transfer']['transaction_debt_id']==$entry['Transaction']['id'])? $entry['Transfer']['transaction_credit_id'] : $entry['Transfer']['transaction_debt_id'];                
                $this->Transaction->recursive = 0;
                $entry['Transfer']['Account'] = Set::extract('Account',$this->Transaction->find('first',array( 'conditions' => array( 'Transaction.id'=>$accid )  )));
                
                $description = (($entry['Transaction']['type']=='debt')? "انتقال به حساب" : "انتقال از حساب").' '.$entry['Transfer']['Account']['name'];
                $description.= $entry['Transfer']['description']? "<br /> توضیحات: ".$entry['Transfer']['description'] : "";
                $data[$i][] = $description;                
            } else {
                $description = "تراکنش متناظر این انتقال حذف شده است";
                $data[$i][] = $description;
            }
            
            $data[$i][] = $entry['Account']['name'];
            $data[$i][] = $entry['Transaction']['created'];
            $i++;
        }

        // write to excel
        $i = 0;
        foreach ($data as $row) {
            $worksheet->writeRow($i, 0, $row);
            $i++;
        }

        // send the file
        $workbook->close();
        return;
    }
    
    private function _deleteTransaction($transaction)
    {
        //delete
        if (!$r=$this->Transaction->delete($transaction['Transaction']['id'])) {
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }
        //update balance
        if($transaction['Transaction']['type']=='debt') {
            $this->Transaction->Account->updateBalance($transaction['Transaction']['account_id'],$transaction['Transaction']['amount']);
        } elseif($transaction['Transaction']['type']=='credit') {
            $this->Transaction->Account->updateBalance($transaction['Transaction']['account_id'],'-'.$transaction['Transaction']['amount']);
        }
        return true;
    }

    function _addExpense($data) {
        
        $this->data['Expense']['expense_category_id'] = intval($this->data['Expense']['expense_category_id']);
        $this->data['Expense']['expense_sub_category_id'] = intval($this->data['Expense']['expense_sub_category_id']);
        //remove price formating strings and 
        $this->data['Transaction']['amount'] = floatval(str_replace(',', '', $this->data['Transaction']['amount']));
        //check for categories and sub categories
        if ($this->data['Expense']['expense_category_id'] == 0) {
            $this->Session->setFlash('لطفا یک نوع هزینه را انتخاب کنید.', 'default', array('class' => 'error-message'));
            return false;
        }
        if ($this->data['Expense']['expense_sub_category_id'] == 0) {
            $this->data['Expense']['expense_sub_category_id'] = null;
        }
        
        if(!empty($this->data['ExpenseTransactionTag'])) {
            $this->data['TransactionTag'] = $this->data['ExpenseTransactionTag'];
            unset($this->data['ExpenseTransactionTag']);
        }
        
        //save expense
        if (!$this->Transaction->Expense->saveExpense($this->data)) {
            $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            return false;
        }
        return true;
    }

    function _addIncome($data) {
        $this->data['Income']['income_type_id'] = intval($this->data['Income']['income_type_id']);
        //remove price formating strings and 
        $this->data['Transaction']['amount'] = str_replace(',', '', $this->data['Transaction']['amount']);
        //check for income type
        if ($this->data['Income']['income_type_id'] == 0) {
            $this->Session->setFlash('لطفا یک نوع درآمد را انتخاب کنید.', 'default', array('class' => 'error-message'));
            return false;
        }
        if ($this->data['Income']['income_sub_type_id'] == 0) {
            $this->data['Income']['income_sub_type_id'] = null;
        }
        
        if(!empty($this->data['IncomeTransactionTag'])) {
            $this->data['TransactionTag'] = $this->data['IncomeTransactionTag'];
            unset($this->data['IncomeTransactionTag']);
        }
        
        //save income
        if (!$this->Transaction->Income->saveIncome($this->data)) {
            $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            return false;
        }
        return true;
    }

    function _addTransfer($data) {
        //check for empty accounts
        if(intval($data['Transaction']['from_account_id']) <=0 OR intval($data['Transaction']['to_account_id']) <=0) {
            $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            return false;
        }
        
        //check for the same accounts
        if(intval($data['Transaction']['from_account_id'])==intval($data['Transaction']['to_account_id'])) {
            $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            return false;
        }
        
        //start transaction
        $dataSource = $this->Transaction->getDataSource();
        $dataSource->begin($this->Transaction); 
        
        //save transactions and update balance
        $transaction1Data=array();
        $transaction1Data['Transaction']['account_id']=intval($data['Transaction']['from_account_id']);
        $transaction1Data['Transaction']['amount']=$data['Transaction']['amount'];
        $transaction1Data['Transaction']['date']=$data['Transaction']['date'];
        $transaction1Data['Transaction']['type']='debt';
        $this->Transaction->create();
        if(!$this->Transaction->save($transaction1Data)) {
            $dataSource->rollback($this->Transaction);
            $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            return false;
        }else{
            $transaction1 = $this->Transaction->getLastInsertID();
        }
        
        //update balance
        if(!$this->Transaction->Account->updateBalance($transaction1Data['Transaction']['account_id'],'-'.$transaction1Data['Transaction']['amount'])) {
            $dataSource->rollback($this->Transaction);
            $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            return false;
        }
        
        $transaction2Data=array();
        $transaction2Data['Transaction']['account_id']=intval($data['Transaction']['to_account_id']);
        $transaction2Data['Transaction']['amount']=$data['Transaction']['amount'];
        $transaction2Data['Transaction']['date']=$data['Transaction']['date'];
        $transaction2Data['Transaction']['type']='credit';
        $this->Transaction->create();
        if(!$this->Transaction->save($transaction2Data)) {
            $dataSource->rollback($this->Transaction);
            $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            return false;
        //commit
        $dataSource->commit($this->Transaction);
        }else{
            $transaction2 = $this->Transaction->getLastInsertID();
        }
        
        //update balance
        if(!$this->Transaction->Account->updateBalance($transaction2Data['Transaction']['account_id'],$transaction2Data['Transaction']['amount'])) {
            $dataSource->rollback($this->Transaction);
            $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            return false;
        }
        
        $this->Transfer->create();
        $data = array(
            'transaction_debt_id'   => $transaction1,
            'transaction_credit_id'   => $transaction2,
            'description'       => $data['Transfer']['description']
                );
        $this->Transfer->save($data);
        
        //commit
        $dataSource->commit($this->Transaction);
        return true;
    }
    
    private function loadTags()
    {
        $this->set( 'tags' , $this->Tag->prepareList( $this->Tag->loadTags() ) );
    }

}

?>