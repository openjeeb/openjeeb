<?php

uses( 'sanitize' );

class AccountsController extends AppController {

    var $name = 'Accounts';
    var $components = array( 'Security' );
    var $uses = array( 'Account' , 'Transfer' );

    function beforeFilter() {
        parent::beforeFilter();
        $this->Security->requireAuth( 'index', 'edit' );
        $this->Security->blackHoleCallback = 'fail';
    }

    function index() {
        $this->set( 'title_for_layout', 'حساب‌ها' );
        //check for first login
        if ( $this->Auth->user( 'setup' ) ) {
            $this->set( 'setup', true );
            //get user's jeeb account
            $this->set( 'jeeb_account_id', $this->Account->field( 'id', array( 'name' => 'جیب', 'delete' => 'no' ) ) );
        }
        //paginate
        $this->Account->recursive = 0;
        $this->Account->convertDateFormat = 'Y/m/d';
        $this->Account->order = 'sort ASC';
        $this->set( 'accounts', $this->paginate() );
        //get banks list
        $banks = $this->Account->Bank->find( 'list' );
        $this->set( compact( 'banks' ) );
        //find the sum
        $this->Account->recursive = -1;
        $sum = $this->Account->find( 'first', array( 'fields' => array( 'SUM(Account.balance) AS sum' ) ) );
        $this->set( 'sum', $sum[0]['sum'] );
        //pie data
        $this->Account->recursive = -1;
        $pie = $this->Account->find( 'all', array(
            'fields' => array(
                'Account.name AS k',
                'Account.balance AS value'
            ) ) );
        $pieData = array( );
        $i = 0;
        foreach ( $pie as $entry ) {
            $pieData[$i]['key'] = $entry['Account']['k'];
            $pieData[$i]['value'] = $entry['Account']['value'];
            if ( $entry['Account']['value'] < 0 ) {
                $pieData[$i]['value'] = 0;
            }
            $i++;
        }
        $this->set( compact( 'pieData' ) );
        
        //add
        if ( !empty( $this->data ) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean( $this->data );
            //remove price formating strings
            $this->data['Account']['balance'] = floatval( str_replace( ',', '', $this->data['Account']['balance'] ) );
            $this->data['Account']['init_balance'] = $this->data['Account']['balance'];           
            $sort = $this->Account->find( 'first' , array( 'fields' => array( 'GREATEST(MAX(sort)+1,COUNT(*)) AS sort' )) );
            $this->data['Account']['sort'] = intval($sort[0]['sort']);
            $this->Account->create();
            if ( $this->Account->save( $this->data ) ) {
                $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
                $this->redirect( array( 'action' => 'index' ) );
            } else {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            }
        }
    }

    function view( $id = null ) {
        $this->set( 'title_for_layout', 'نمایش حساب' );
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return false;
        }

        //get and set account
        $account = $this->Account->read( null, intval( $id ) );
        $this->set( compact( 'account' ) );

        //line chart
        $this->Account->Transaction->recursive = -1;
        $credits = $this->Account->Transaction->find( 'all', array(
            'fields' => array(
                "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k",
                //"Transaction.pyear AS year",
                //"Transaction.pmonth AS month",
                'SUM(Transaction.amount) AS value'
            ),
            'conditions' => array(
                'Transaction.account_id' => intval( $id ),
                'Transaction.type' => 'credit'
            ),
            'group' => array( 'k' ),
            'order' => array( 'Transaction.pyear', 'Transaction.pmonth' )
            ) );
        $credits = Set::classicExtract( $credits, '{n}.0' );

        $this->Account->Transaction->recursive = -1;
        $this->Account->convertDateFormat = 'Y/m/d';
        $debts = $this->Account->Transaction->find( 'all', array(
            'fields' => array(
                "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k",
                //"Transaction.pyear AS year",
                //"Transaction.pmonth AS month",
                'SUM(Transaction.amount) AS value'
            ),
            'conditions' => array(
                'Transaction.account_id' => intval( $id ),
                'Transaction.type' => 'debt'
            ),
            'group' => array( 'k' ),
            'order' => array( 'Transaction.pyear', 'Transaction.pmonth' )
            ) );
        $debts = Set::classicExtract( $debts, '{n}.0' );

        //prepare the data for chart
        $keys = array_unique( array_merge( Set::classicExtract( $debts, '{n}.k' ), Set::classicExtract( $credits, '{n}.k' ) ) );
        $chart = array( );
        $i = 0;
        foreach ( $keys as $key ) {
            $chart[$i]['k'] = $key;
            $chart[$i]['value'] = $account['Account']['init_balance'];
            foreach ( $debts as $debt ) {
                if ( $debt['k'] == $key ) {
                    $chart[$i]['value']-=$debt['value'];
                }
            }
            foreach ( $credits as $credit ) {
                if ( $credit['k'] == $key ) {
                    $chart[$i]['value']+=$credit['value'];
                }
            }
            $i++;
        }
        $this->set( compact( 'chart' ) );

        //get transactions
        $this->Account->Transaction->convertDateFormat = 'Y/m/d';
        $this->Account->Transaction->Expense->convertDateFormat = 'Y/m/d';
        $this->Account->Transaction->Income->convertDateFormat = 'Y/m/d';
        $this->Account->Transaction->recursive = 0;
        $this->Account->Transaction->bindModel( array(
            'hasOne' => array(
                'ExpenseCategory' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Expense.expense_category_id = ExpenseCategory.id' )
                ),
                'ExpenseSubCategory' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Expense.expense_sub_category_id=ExpenseSubCategory.id' )
                ),
                'IncomeType' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Income.income_type_id=IncomeType.id' )
                ),
                'IncomeSubType' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Income.income_sub_type_id=IncomeSubType.id' )
                ),
                'IndividualExpense' => array(
                    'className' => 'Individual',
                    'foreignKey' => false,
                    'conditions' => array( 'Income.individual_id=IndividualExpense.id' )
                ),
                'IndividualIncome' => array(
                    'className' => 'Individual',
                    'foreignKey' => false,
                    'conditions' => array( 'Expense.individual_id=IndividualIncome.id' )
                ),
            ) ), false );
        $this->paginate['order'] = 'Transaction.date DESC, Expense.id DESC, Income.id DESC';
        $this->paginate['conditions'] = array( 'Transaction.account_id' => $account['Account']['id'] );
        $transactions = $this->paginate( 'Transaction' );
        foreach($transactions as &$t) {
            if(empty($transactions['Transaction']['income_id']) && empty($transactions['Transaction']['expense_id'])){
                $t['Transfer'] = Set::extract('Transfer',$this->Transfer->find('first', array(
                    'conditions'=> array(
                        'OR'=>array(
                            'transaction_debt_id' => $t['Transaction']['id'],
                            'transaction_credit_id' => $t['Transaction']['id']
                        )
                        ),
                    'limit'=>1
                )));
                $accid = ($t['Transfer']['transaction_debt_id']==$t['Transaction']['id'])? $t['Transfer']['transaction_credit_id'] : $t['Transfer']['transaction_debt_id'];                
                $this->Account->Transaction->recursive = 0;
                $t['Transfer']['Account'] = Set::extract('Account',$this->Account->Transaction->find('first',array( 'conditions' => array( 'Transaction.id'=>$accid )  )));
            }
        }
        $this->set( compact( 'transactions' ) );

        //get sum 
        $sum = $this->Account->Transaction->find( 'all', array(
            'fields' => array( 'Transaction.type', 'SUM(Transaction.amount) AS sum' ),
            'conditions' => array( 'Transaction.account_id' => intval( $id ) ),
            'group' => array( 'Transaction.type' )
            ) );
        //set debts and credits sum
        foreach ( $sum as $entry ) {
            if ( $entry['Transaction']['type'] == 'debt' ) {
                $debtSum =  $entry['0']['sum'];
            } elseif ( $entry['Transaction']['type'] == 'credit' ) {
                $creditSum = $entry['0']['sum'];
            }
        }
        $this->set( 'debtSum', isset($debtSum)? $debtSum : 0 );
        $this->set( 'creditSum', isset($creditSum)? $creditSum : 0 );
        
        
        $this->Account->recursive = -1;
        $this->set( 'accounts', $this->Account->find( 'list' ) );
        
    }

    function edit( $id = null ) {
        $this->set( 'title_for_layout', 'ویرایش حساب' );

        //validate id
        if ( !$id && empty( $this->data ) ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return false;
        }

        //get banks list
        $banks = $this->Account->Bank->find( 'list' );
        $this->set( compact( 'banks' ) );

        //get account
        $account = $this->Account->read( null, intval( $id ) );
        $account['Account']['name'] = html_entity_decode( str_replace( '\n', "\n", $account['Account']['name'] ), ENT_QUOTES, 'UTF-8' );
        $account['Account']['description'] = html_entity_decode( str_replace( '\n', "\n", $account['Account']['description'] ), ENT_QUOTES, 'UTF-8' );

        //
        if ( !empty( $this->data ) ) {

            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean( $this->data );

            //remove price formating strings and 
            $this->data['Account']['init_balance'] = floatval( str_replace( ',', '', $this->data['Account']['init_balance'] ) );

            //start transaction
            $dataSource = $this->Account->getDataSource();
            $dataSource->begin( $this->Account );

            //save
            if ( $this->Account->save( $this->data, true, array( 'name', 'description', 'balance', 'init_balance', 'type', 'bank_id' ) ) ) {

                //update balance
                if ( !$this->Account->updateBalance( intval( $id ), ($this->data['Account']['init_balance'] - $account['Account']['init_balance'] ) ) ) {
                    $dataSource->rollback( $this->Account );
                    $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                    return false;
                }

                //commit
                $dataSource->commit( $this->Account );
                $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
                $this->redirect( array( 'action' => 'index' ) );
                return true;
            } else {

                $dataSource->rollback( $this->Account );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
        }

        if ( empty( $this->data ) ) {
            $this->data = $account;
        }
    }

    function delete( $id = null ) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return false;
        }
        //get account
        $this->Account->recursive = -1;
        $account = $this->Account->read( array( 'id', 'user_id', 'delete' ), intval( $id ) );
        //check user
        if ( $account['Account']['user_id'] != $this->Auth->user( 'id' ) ) {
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return false;
        }
        //check if deletable
        if ( $account['Account']['delete'] == 'no' ) {
            $this->Session->setFlash( 'شما نمیتوانید حساب جیب را پاک کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return false;
        }
        //delete
        if ( $this->Account->delete( $id ) ) {
            $this->Session->setFlash( 'داده مورد نظر با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return true;
        }
        $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
        $this->redirect( array( 'action' => 'index' ) );
        return false;
    }

    function balance( $id = null ) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return false;
        }
        //get account
        $this->Account->recursive = -1;
        $account = $this->Account->read( null, intval( $id ) );
        //check user
        if ( $account['Account']['user_id'] != $this->Auth->user( 'id' ) ) {
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return false;
        }
        $debts = $this->Account->Transaction->field( 'SUM(Transaction.amount)', array( 'Transaction.account_id' => intval( $id ), 'Transaction.type' => 'debt' ) );
        $credits = $this->Account->Transaction->field( 'SUM(Transaction.amount)', array( 'Transaction.account_id' => intval( $id ), 'Transaction.type' => 'credit' ) );
        ;
        $this->Account->saveField( 'balance', ($credits - $debts) + $account['Account']['init_balance'] );
        $this->redirect( array( 'action' => 'index' ) );
        return true;
    }

    function ajaxSaveInitBalance( $id ) {
        if ( $this->RequestHandler->isAjax() ) {
            Configure::write( 'debug', 0 );
            //sanitize the data
            $san = new Sanitize();
            $this->params = $san->clean( $this->params );
            //gather data
            $data['Account']['init_balance'] = str_replace( ',', '', $this->params['form']['init_balance'] );
            $data['Account']['balance'] = $data['Account']['init_balance'];
            //save init balance
            if ( !$this->Account->save( $data, array( 'init_balance', 'balance' ) ) ) {
                $this->Account->invalidate( 'Account.init_balance', 'موجودی وارد شده صحیح نیست.' );
                $this->set( 'response', false );
                $this->render( 'ajax', 'json' );
                return false;
            }
            //remove setup page
            $this->Session->write( 'Auth.User.setup', false );
            //done
            $this->set( 'response', true );
            $this->render( 'ajax', 'json' );
            return true;
        }
        $this->redirect( Controller::referer() );
    }

    function export( $id = null )
    {
        
        if ( !$id ) {
            $this->Session->setFlash( 'این حساب وجود ندارد.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
        }

        //set memory limit
        ini_set( 'memory_limit', '32M' );
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion( 8 );
        $workbook->send( 'account_transactions.xls' );
        $worksheet = & $workbook->addWorksheet( 'transactions' );
        $worksheet->setInputEncoding( 'utf-8' );

        //get the data
        $this->Account->Transaction->convertDateFormat = 'Y/m/d';
        $this->Account->Transaction->Expense->convertDateFormat = 'Y/m/d';
        $this->Account->Transaction->Income->convertDateFormat = 'Y/m/d';
        $this->Account->Transaction->recursive = 0;
        $this->Account->Transaction->bindModel( array(
            'hasOne' => array(
                'ExpenseCategory' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Expense.expense_category_id = ExpenseCategory.id' )
                ),
                'ExpenseSubCategory' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Expense.expense_sub_category_id=ExpenseSubCategory.id' )
                ),
                'IncomeType' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Income.income_type_id=IncomeType.id' )
                ),
                'IncomeSubType' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Income.income_sub_type_id=IncomeSubType.id' )
                ),
                'IndividualExpense' => array(
                    'className' => 'Individual',
                    'foreignKey' => false,
                    'conditions' => array( 'Income.individual_id=IndividualExpense.id' )
                ),
                'IndividualIncome' => array(
                    'className' => 'Individual',
                    'foreignKey' => false,
                    'conditions' => array( 'Expense.individual_id=IndividualIncome.id' )
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
            ) ), false );
        $options = array( );
        $options['conditions'] = array( 'Transaction.account_id' => intval( $id ) );
        $options['fields'] = array('Transaction.id, Transaction.amount', 'Transaction.date', 'Transaction.type', 'Transaction.expense_id', 'Transaction.income_id', 'Transaction.created', 'Expense.description', 'ExpenseCategory.name', 'ExpenseSubCategory.name', 'Income.description', 'IncomeType.name', 'Account.name');
        $options['order'] = 'Transaction.date DESC, Expense.id DESC, Income.id DESC';
        $transactions = $this->Account->Transaction->find( 'all', $options );

        $data = array( );
        $data[] = array( 'نوع', 'تاریخ', 'هزینه', 'درآمد', 'نوع هزینه/درآمد', 'توضیحات', 'حساب', 'تاریخ ایجاد' );
        $i = 1;
        foreach ( $transactions as $entry ) {
            if ( $entry['Transaction']['expense_id'] ) {
                $data[$i][] = 'هزینه';
            } elseif ( $entry['Transaction']['income_id'] ) {
                $data[$i][] = 'درآمد';
            } else {
                $entry['Transfer'] = Set::extract('Transfer',$this->Transfer->find('first', array(
                    'conditions'=> array(
                            'OR'=>array(
                                'transaction_debt_id' => $entry['Transaction']['id'],
                                'transaction_credit_id' => $entry['Transaction']['id']
                                )
                        ),
                    'limit'=>1
                )));
                $data[$i][] = 'انتقال وجه';
            }
            $data[$i][] = $entry['Transaction']['date'];
            if ( $entry['Transaction']['type'] == 'debt' ) {
                $data[$i][] = $entry['Transaction']['amount'];
            } else {
                $data[$i][] = '';
            }
            if ( $entry['Transaction']['type'] == 'credit' ) {
                $data[$i][] = $entry['Transaction']['amount'];
            } else {
                $data[$i][] = '';
            }
            if ( $entry['Transaction']['expense_id'] ) {
                if ( !empty( $entry['ExpenseSubCategory']['name'] ) ) {
                    $data[$i][] = $entry['ExpenseCategory']['name'] . '>>' . $entry['ExpenseSubCategory']['name'];
                } else {
                    $data[$i][] = $entry['ExpenseCategory']['name'];
                }
            } elseif ( $entry['Transaction']['income_id'] ) {
                $data[$i][] = $entry['IncomeType']['name'];
            } else {
                $data[$i][] = '';
            }
            
            if ( $entry['Transaction']['expense_id'] ) {
                $data[$i][] = html_entity_decode( str_replace( '\n', "\n", $entry['Expense']['description'] ), ENT_QUOTES, 'UTF-8' );;
            } elseif ( $entry['Transaction']['income_id'] ) {
                $data[$i][] = html_entity_decode( str_replace( '\n', "\n", $entry['Income']['description'] ), ENT_QUOTES, 'UTF-8' );;
            } elseif(isset($entry['Transfer']['id'])) {
                # fetch data of the other transaction in transfer
                $accid = ($entry['Transfer']['transaction_debt_id']==$entry['Transaction']['id'])? $entry['Transfer']['transaction_credit_id'] : $entry['Transfer']['transaction_debt_id'];                
                $this->Account->Transaction->recursive = 0;
                $entry['Transfer']['Account'] = Set::extract('Account',$this->Account->Transaction->find('first',array( 'conditions' => array( 'Transaction.id'=>$accid )  )));
                
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
        foreach ( $data as $row ) {
            $worksheet->writeRow( $i, 0, $row );
            $i++;
        }

        // send the file
        $workbook->close();
        return;
    }
    
    function showbalance()
    {
    	$this->disableCache();
        $this->layout = 'ajax';
        
        $this->params['form']['id'] ;
        
        $id = intval($this->params['form']['id']);
        $this->Account->Transaction->recursive = 0;
        $this->Account->Transaction->outputConvertDate = false;
        //Transaction.date DESC, Expense.id DESC, Income.id DESC
        $data = $this->Account->Transaction->read('Account.id, Account.init_balance, Transaction.date, Transaction.id',$id);
        if(!$data){
            return false;
        }
        
        $this->Account->Transaction->recursive = -1;
        $init = $data['Account']['init_balance'];
        $data = $this->Account->Transaction->find( 'first', array(
            'fields'=>'SUM(IF(Transaction.type="credit",Transaction.amount,0)) AS credit, SUM(IF(Transaction.type="debt",Transaction.amount,0)) AS debt',
            'conditions' => array(
                'Transaction.account_id'=>$data['Account']['id'],
                'DATE(Transaction.date) <= TIMESTAMP("'.$data['Transaction']['date'].'")'
            ),
            'order'=>'Transaction.date DESC, Transaction.id DESC'
        ) );
        if(!$data){
            return $data = 0;
        }else{
            $data = $data[0];
        }
        
        debug(($data['credit']+$init)-$data['debt']);
        
    }
    
    function sort()
    {
    	if ( $this->Auth->user( 'setup' ) ) {
            $this->Session->setFlash( '.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
    	}
        
        if ( isset($this->passedArgs['up']) || isset($this->passedArgs['down']) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->passedArgs = $san->clean( $this->passedArgs );
            $orientation = isset($this->passedArgs['up'])? 'up' : 'down';

            if ( $this->Account->moveSortItem( $this->passedArgs[$orientation], $orientation ) ) {
                
            } else {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );    			
            }
    	}else{
            $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
    	}
        $this->redirect( array( 'action' => 'index' ) );
    	
    }
    
    function toggleshow()
    {
        if ( $this->Auth->user( 'setup' ) ) {
            $this->Session->setFlash( '.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
    	}
        
        if( $status = $this->Account->toggleStatus( intval( $this->passedArgs[0] ) ) ){
            if($status == 'active') {
                $this->Session->setFlash( 'حساب مورد نظر شمافعال شد و در لیستها نمایش داده خواهد شد.', 'default', array( 'class' => 'success' ) );
            } else {
                $this->Session->setFlash( 'حساب مورد نظر شما غیر فعال شد و در لیستها نمایش داده نخواهد شد.', 'default', array( 'class' => 'success' ) );
            }
        } else {
            $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );    			
        }
        
        $this->redirect( $this->referer(array('action'=>'index')), true );
        
        return true;
    }

    function exportaccounts($limit=null) {
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion(8);
        $workbook->send('accounts.xls');
        $worksheet = & $workbook->addWorksheet('accounts');
        $worksheet->setInputEncoding('utf-8');

        //get the data
        $this->Account->recursive = 0;
        $this->Account->outputConvertDate = true;
        $this->Account->convertDateFormat = 'Y/m/d';
        $options = array();
        $options['fields'] = array('Account.name', 'Account.balance', 'Account.type', 'Bank.name', 'Account.description', 'Account.created' );
        $options['order'] = "Account.sort DESC";
        //apply the conditions
        if (!is_null($limit)) {
            $options['limit'] = $limit;
        }
        $accounts = $this->Account->find('all', $options);
        $data = array();
        $data[] = array('عنوان حساب', 'موجودی', 'نوع حساب', 'بانک', 'توضیحات' ,'تاریخ ایجاد');
        $i = 1;
        foreach ($accounts as $entry) {
            $data[$i][] = $entry['Account']['name'];
            $data[$i][] = $entry['Account']['balance'];
            $data[$i][] = __($entry['Account']['type'], true);
            $data[$i][] = $entry['Bank']['name'];
            $data[$i][] = $entry['Account']['description'];
            $data[$i][] = $entry['Account']['created'];
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

}

?>
