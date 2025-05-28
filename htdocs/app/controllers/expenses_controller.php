<?php

uses( 'sanitize' );

class ExpensesController extends AppController {

    var $name = 'Expenses';
    var $components = array( 'Security' );
    var $uses = array( 'Expense' , 'Tag' );

    function beforeFilter() {
        parent::beforeFilter();
        $this->Security->requireAuth( 'index', 'edit' );
        $this->Security->blackHoleCallback = 'fail';
    }

    function index() {
        $this->set( 'title_for_layout', 'هزینه‌ها' );
        //sanitize the data
        $san = new Sanitize();
        $this->data = $san->clean( $this->data );

        //paginate
        $conditions = array( );
        if ( isset( $this->data['Expense']['search'] ) ) {
            if ( !empty( $this->data['Expense']['expense_category_id'] ) ) {
                $conditions['Expense.expense_category_id'] = $this->data['Expense']['expense_category_id'];
            }
            if ( !empty( $this->data['Expense']['expense_sub_category_id'] ) ) {
                $conditions['Expense.expense_sub_category_id'] = $this->data['Expense']['expense_sub_category_id'];
            }
            if ( !empty( $this->data['Transaction']['account_id'] ) ) {
                $conditions['Transaction.account_id'] = $this->data['Transaction']['account_id'];
            }
            if ( !empty( $this->data['Transaction']['amount'] ) ) {
                $conditions['Transaction.amount'] = floatval( str_replace( ',', '', $this->data['Transaction']['amount'] ) );
            }
            if ( !empty( $this->data['Expense']['individual_id'] ) ) {
                $conditions['Expense.individual_id'] = $this->data['Expense']['individual_id'];
            }
            if ( !empty( $this->data['Expense']['start_date'] ) ) {
                $persianDate = new PersianDate();
                $this->data['Expense']['start_date'] = $persianDate->pdate_format_reverse( $this->data['Expense']['start_date'] );
                $conditions['Transaction.date >='] = $this->data['Expense']['start_date'];
            }
            if ( !empty( $this->data['Expense']['end_date'] ) ) {
                $persianDate = new PersianDate();
                $this->data['Expense']['end_date'] = $persianDate->pdate_format_reverse( $this->data['Expense']['end_date'] );
                $conditions['Transaction.date <='] = $this->data['Expense']['end_date'];
            }
            if ( !empty( $this->data['Expense']['description_search'] ) ) {
                $conditions['Expense.description LIKE'] = "%" . $this->data['Expense']['description_search'] . "%";
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
            $this->Session->delete( 'Expense.conditions' );
            $this->Session->write( 'Expense.conditions', $conditions );
        }
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) ) {
            $this->Session->delete( 'Expense.conditions' );
        }
        //apply the conditions
        if ( $this->Session->check( 'Expense.conditions' ) AND !empty( $this->params['named'] ) ) {
            $conditions = $this->Session->read( 'Expense.conditions' );
        }
        
        $this->_setCombos();
        
        $this->Expense->recursive = 0;
        $this->Expense->Transaction->outputConvertDate = true;
        $this->Expense->Transaction->convertDateFormat = 'Y/m/d';
        $this->Expense->convertDateFormat = 'Y/m/d';
        $this->Expense->bindModel( array(
            'hasOne' => array(
                'Account' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Account.id = Transaction.account_id' )
                ),
                'TransactionTag' => array(
                        'fields' => 'CONCAT_WS(",", TransactionTag.tag_id )',
                        'foreignKey' => false,
                        'conditions' => array( 'TransactionTag.transaction_id = Transaction.id' )
                    )
            )
            ), false );
        $this->paginate['fields'] = array( 'Expense.id', 'Expense.transaction_id', 'Expense.description', 'Expense.expense_category_id', 'Expense.expense_sub_category_id', 'Transaction.id', 'Transaction.amount', 'Transaction.date', 'Transaction.pyear', 'Transaction.pmonth', 'Transaction.pday', 'Transaction.account_id', 'ExpenseCategory.name', 'ExpenseSubCategory.name', 'Account.name', 'Individual.name' );
        $this->paginate['order'] = 'Transaction.date DESC, Expense.id DESC';
        $this->paginate['conditions'] = $conditions;
        $this->paginate['group'] = 'Expense.id';
        $this->set( 'expenses', $this->paginate() );

        //find the sum
        /*$this->Expense->recursive = 1;
        $this->Expense->Behaviors->attach( 'Containable' );
        $this->Expense->bindModel( array(
            'hasOne' => array(
                'TransactionTag' => array(
                        'fields' => 'CONCAT_WS(",", TransactionTag.tag_id )',
                        'foreignKey' => false,
                        'conditions' => array( 'TransactionTag.transaction_id = Transaction.id' )
                    )
            )
            ), false );
        $this->Expense->contain( 'TransactionTag', 'Transaction' );
        $sum = $this->Expense->find( 'first', array( 'fields' => array( 'SUM(Transaction.amount) AS sum' ), 'conditions' => $conditions ) );
        $this->set( 'sum', $sum[0]['sum'] ); */
        
        /*$lQuery = $this->Expense->getDataSource()->getLog(false, false);
        $where = explode('WHERE',$lQuery['log'][count($lQuery['log'])-1]['query']); $where = explode('ORDER',$where[count($where)-1]); $where = explode('LIMIT',$where[0]); $where = explode('GROUP',$where[0]); $where = $where[0];*/
        
        $where = $this->Expense->getDataSource()->conditions($conditions+array('Expense.user_id'=>$this->Auth->user( 'id' )), true, true, $this->Expense);
        $sql = "SELECT SUM(amount) AS sum
                    FROM 
                        (SELECT Expense.id, Transaction.amount AS amount
                        FROM `expenses` AS `Expense`
                        LEFT JOIN `transactions` AS `Transaction` ON (`Expense`.`transaction_id` = `Transaction`.`id`)
                        LEFT JOIN `transaction_tags` AS `TransactionTag` ON (`TransactionTag`.`transaction_id` = `Transaction`.`id`)
                        $where
                        GROUP BY Expense.id)t";
        $sum = $this->Expense->query($sql);
        
        $this->set( 'sum', $sum[0][0]['sum'] );

        //pie data
        $this->Expense->recursive = 0;
        $this->Expense->unbindModelAll();
        $this->Expense->bindModel(array(
            'belongsTo' => array(
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
                )
            )
        ));
        $pie = $this->Expense->find( 'all', array(
            'fields' => array(
                'ExpenseCategory.name as k',
                'SUM(Transaction.amount) AS value'
            ),
            'group' => array( 'Expense.expense_category_id' ),
            ) );
        $this->set( 'pieData', $this->Chart->formatPieData( $pie, 'ExpenseCategory' ) );

        //column chart
//        $this->Expense->recursive = -1;
//        $this->Expense->contain('Transaction');
//        $column=$this->Expense->find('all',array(
//            'fields'=>array(
//                            "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k",
//                            'SUM(Transaction.amount) AS value'
//                        ),
//            'group'=>array('k'),
//            'order'=>'Transaction.date ASC',
//        ));
//        $this->set('columnData',Set::classicExtract($column,'{n}.0'));
        //add
        if ( !empty( $this->data ) AND !isset( $this->data['Expense']['search'] ) ) {
            //sanitize expense_category_id and expense_sub_category_id
            $this->data['Expense']['expense_category_id'] = intval( $this->data['Expense']['expense_category_id'] );
            $this->data['Expense']['expense_sub_category_id'] = intval( $this->data['Expense']['expense_sub_category_id'] );
            
            //remove price formating strings and 
            $this->data['Transaction']['amount'] = floatval( str_replace( ',', '', $this->data['Transaction']['amount'] ) );

            //check for categories and sub categories
            if ( $this->data['Expense']['expense_category_id'] == 0 ) {
                $this->Session->setFlash( 'لطفا یک نوع هزینه را انتخاب کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            if ( $this->data['Expense']['expense_sub_category_id'] == 0 ) {
                $this->data['Expense']['expense_sub_category_id'] = null;
            }

            //save expense
            if ( !$this->Expense->saveExpense( $this->data ) ) {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return true;
        }
    }
    
    function batch()
    {
        $this->set( 'title_for_layout', 'ثبت هزینه دسته ای' );
        $this->_setCombos();
        
        if(!empty($this->data)) {
            
            foreach($this->data['data'] as $k=>&$data) {
                $data['Transaction']['date'] = $this->data['Transaction']['date'];
                
                switch($this->_saveData($data)) {
                    case 0:                        
                        unset($this->data['data'][$k]);
                        break;
                    case 1:
                        $data['Transaction']['error_message'] = 'لطفا یک نوع هزینه را انتخاب کنید.';
                        $data = array_merge($data['Expense'],$data['Transaction'],$data['TransactionTag']);
                        break;
                    case 2:
                    default:
                        $data['Transaction']['error_message'] = 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.';
                        $data = array_merge($data['Expense'],$data['Transaction'],$data['TransactionTag']);
                        break;
                }
                
            }
            
            if(empty($this->data['data'])) {
                $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
                $this->redirect( array( 'action' => 'index' ) );
            } else {
                $this->set('formdate' , $this->data['Transaction']['date']);
            }
        }
        
    }

    function edit( $id = null ) {
        $this->set( 'title_for_layout', 'ویرایش هزینه' );

        if ( !$id && empty( $this->data ) ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }

        //get expense
        $this->Expense->recursive = 0;
        $this->Expense->outputConvertDate = true;
        $this->Expense->convertDateFormat = 'Y/m/d';
        $expense = $this->Expense->read( array( 'Expense.id', 'Expense.transaction_id', 'Expense.description', 'Expense.expense_category_id', 'Expense.expense_sub_category_id', 'Expense.individual_id', 'Transaction.id', 'Transaction.amount', 'Transaction.date', 'Transaction.account_id', 'ExpenseCategory.name', 'ExpenseSubCategory.name' ), $id );
        $expense['Expense']['description'] = html_entity_decode( str_replace( '\n', "\n", $expense['Expense']['description'] ), ENT_QUOTES, 'UTF-8' );
        
        // reading tags
        $this->Expense->Transaction->TransactionTag->recursive = -1;
        $expense['Transaction']['TransactionTag'] = Set::extract("{n}.0.tag_id", $this->Expense->Transaction->TransactionTag->find('all', array(
            'fields' => 'CONCAT("t",TransactionTag.tag_id) AS tag_id',
            'conditions' => array(
                'transaction_id' => $expense['Transaction']['id']
                )
        )));
        
        //$expense['Transaction']['TransactionTag'] = $expense['Transaction']['TransactionTag'];
        $this->_setCombos();

        //set the data
        if ( empty( $this->data ) ) {
            $this->data = $expense;
        }

        $this->data['Expense']['transaction_id'] = $expense['Expense']['transaction_id'];

        //saving the posted data
        if ( !empty( $this->data ) && !empty( $_POST ) ) {

            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean( $this->data );

            //remove price formating strings and 
            $this->data['Transaction']['amount'] = str_replace( ',', '', $this->data['Transaction']['amount'] );

            //check sub category
            if ( $this->data['Expense']['expense_sub_category_id'] == 0 ) {
                $this->data['Expense']['expense_sub_category_id'] = null;
            }

            //save
            $dataSource = $this->Expense->getDataSource();
            $dataSource->begin( $this->Expense );

            //save expense
            if ( !$this->Expense->save( $this->data, true, array( 'expense_category_id', 'expense_sub_category_id', 'individual_id', 'description' ) ) ) {
                $dataSource->rollback( $this->Expense );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //save transaction
            if ( !$this->Expense->Transaction->save( $this->data, true, array( 'amount', 'date', 'pyear', 'pmonth', 'pday', 'account_id' ) ) ) {
                $dataSource->rollback( $this->Expense );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            $this->Expense->Transaction->TransactionTag->replaceTags($this->data['Transaction']['id'], $this->data['TransactionTag']['tag_id']);

            //check for account change
            if ( $expense['Transaction']['account_id'] != intval( $this->data['Transaction']['account_id'] ) ) {

                //update balance for original account
                if ( !$this->Expense->Transaction->Account->updateBalance( $expense['Transaction']['account_id'], $expense['Transaction']['amount'] ) ) {
                    $dataSource->rollback( $this->Expense );
                    $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                    return false;
                }

                //update balance for new account
                if ( !$this->Expense->Transaction->Account->updateBalance( $this->data['Transaction']['account_id'], '-' . intval( $this->data['Transaction']['amount'] ) ) ) {
                    $dataSource->rollback( $this->Expense );
                    $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                    return false;
                }
            } else {

                //update account balance
                $amount = -1 * ($this->data['Transaction']['amount'] - $expense['Transaction']['amount']);
                if ( !$this->Expense->Transaction->Account->updateBalance( $expense['Transaction']['account_id'], $amount ) ) {
                    $dataSource->rollback( $this->Expense );
                    $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                    return false;
                }
            }

            //commit
            $dataSource->commit( $this->Expense );
            $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
            $this->redirect(array('action' => 'index'));
            return true;
        }
    }

    function delete( $id = null ) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        //begin transaction
        $dataSource = $this->Expense->getDataSource();
        $dataSource->begin( $this->Expense );
        //get expense
        $this->Expense->recursive = -1;
        $this->Expense->Behaviors->attach( 'Containable' );
        $this->Expense->contain( 'Transaction' );
        $expense = $this->Expense->read( array( 'Expense.id', 'Expense.user_id', 'Transaction.id', 'Transaction.account_id', 'Transaction.amount' ), intval( $id ) );
        //check user
        if ( $expense['Expense']['user_id'] != $this->Auth->user( 'id' ) ) {
            $dataSource->rollback( $this->Expense );
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        //delete
        if ( !$this->Expense->delete( $id ) ) {
            $dataSource->rollback( $this->Expense );
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        //update balance
        if ( !$this->Expense->Transaction->Account->updateBalance( $expense['Transaction']['account_id'], $expense['Transaction']['amount'] ) ) {
            $dataSource->rollback( $this->Expense );
            $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        //commit
        $dataSource->commit( $this->Expense );
        $this->Session->setFlash( 'داده مورد نظر با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
        $this->redirect( Controller::referer() );
        return true;
    }

    function export( $limit = null ) {
        //set memory limit
        ini_set( 'memory_limit', '64M' );
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion( 8 );
        $workbook->send( 'expenses.xls' );
        $worksheet = & $workbook->addWorksheet( 'expenses' );
        $worksheet->setInputEncoding( 'utf-8' );

        //get the data
        $this->Expense->recursive = 0;
        $this->Expense->Transaction->outputConvertDate = true;
        $this->Expense->convertDateFormat = 'Y/m/d';
        $this->Expense->bindModel( array(
            'hasOne' => array(
                'Account' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Account.id = Transaction.account_id' )
                ),
            )
            ), false );
        $options = array( );
        $options['fields'] = array( 'Transaction.amount', 'Transaction.date', 'Expense.description', 'ExpenseCategory.name', 'ExpenseSubCategory.name', 'Account.name', 'Individual.name' );
        $options['order'] = "Transaction.date DESC";
        //apply the conditions
        if ( $this->Session->check( 'Expense.conditions' ) ) {
            $options['conditions'] = $this->Session->read( 'Expense.conditions' );
        }
        if ( !is_null( $limit ) ) {
            $options['limit'] = $limit;
        }
        $expenses = $this->Expense->find( 'all', $options );
        $data = array( );
        $data[] = array( 'مبلغ', 'تاریخ', 'نوع هزینه اصلی', 'نوع هزینه فرعی', 'حساب', 'شخص', 'توضیحات' );
        $i = 1;
        foreach ( $expenses as $entry ) {
            $data[$i][] = $entry['Transaction']['amount'];
            $data[$i][] = $entry['Transaction']['date'];
            $data[$i][] = $entry['ExpenseCategory']['name'];
            $data[$i][] = $entry['ExpenseSubCategory']['name'];
            $data[$i][] = $entry['Account']['name'];
            $data[$i][] = $entry['Individual']['name'];
            $data[$i][] = $entry['Expense']['description'];
            $i++;
        }

        // write to excel
        $i = 0;
        foreach ( $data as $row ) {
            $worksheet->writeRow( $i, 0, $row );
            $i++;
        }

        // send the file
        $workbook->close();
        return;
    }
    
    private function _setCombos()
    {
        $this->loadTags();

        //get accounts
        list($accounts, $accountsbalance) = $this->Expense->Transaction->Account->listAccounts();
        $this->set( compact( 'accounts' ) );
        $this->set( compact( 'accountsbalance' ) );

        //get individuals
        $individuals = $this->Expense->Individual->fetchList();
        $this->set( compact( 'individuals' ) );

        //get expense categories
        $expenseCategories = $this->Expense->ExpenseCategory->fetchList();
        $this->set( compact( 'expenseCategories' ) );

        //get the whole category subcategory data
        $this->Expense->ExpenseCategory->recursive = -1;
        $expenseCategories = $this->Expense->ExpenseCategory->find( 'all', array(
            'contain' => array( 'ExpenseSubCategory' => array( 'fields' => array( 'ExpenseSubCategory.id', 'ExpenseSubCategory.name' ), 'order' => 'name ASC' ) ),
            'fields' => array( 'ExpenseCategory.id', 'ExpenseCategory.name' ),
            'order' => 'ExpenseCategory.sort ASC'
            ) );
        $expenseCategoriesData = array( );
        $i = 0;
        foreach ( $expenseCategories as $entry ) {
            $i = $entry['ExpenseCategory']['id'];
            $expenseCategoriesData[$i]['id'] = $entry['ExpenseCategory']['id'];
            $expenseCategoriesData[$i]['name'] = $entry['ExpenseCategory']['name'];
            $expenseCategoriesData[$i]['subs'] = array( );
            foreach ( $entry['ExpenseSubCategory'] as $entry2 ) {
                $expenseCategoriesData[$i]['subs'][] = array(
                    'id' => $entry2['id'],
                    'name' => $entry2['name'],
                );
            }
            $i++;
        }
        $this->set( compact( 'expenseCategoriesData' ) );
        
    }
    
    private function _saveData($data)
    {
        $data['Expense']['expense_category_id'] = intval( $data['Expense']['expense_category_id'] );
        $data['Expense']['expense_sub_category_id'] = intval( @$data['Expense']['expense_sub_category_id'] );

        //remove price formating strings and 
        $data['Transaction']['amount'] = floatval( str_replace( ',', '', $data['Transaction']['amount'] ) );

        //check for categories and sub categories
        if ( $data['Expense']['expense_category_id'] == 0 ) {
            return 1;
        }

        if ( $data['Expense']['expense_sub_category_id'] == 0 ) {
            $data['Expense']['expense_sub_category_id'] = null;
        }

        //save expense
        if ( !$this->Expense->saveExpense( $data ) ) {
            return 2;
        }
        
        return 0;
    }


    private function loadTags()
    {
        $this->set( 'tags' , $this->Tag->prepareList( $this->Tag->loadTags() ) );
    }

}

?>
