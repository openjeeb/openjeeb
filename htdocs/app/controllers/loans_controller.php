<?php

uses( 'sanitize' );

class LoansController extends AppController {

    var $name = 'Loans';
    var $uses = array( 'Loan', 'Account', 'Transaction', 'Income', 'Reminder', 'Config', 'Tag', 'LoanTag', 'TransactionTag' );
    
    function index() {
        
        $this->set( 'title_for_layout', 'وام‌ها' );
        
        $this->loadTags();
        
        $this->Loan->recursive = 0;        
        $this->paginate['order'] = 'Loan.id DESC';
        $this->set( 'loans', $this->paginate() );
        //pie data
        $this->Loan->recursive = 0;
        $pie = $this->Loan->Installment->find( 'all', array(
            'fields' => array(
                "Installment.status AS k",
                'SUM(Installment.amount) AS value'
            ),
            'group' => array( 'Installment.status' ),
            'order' => 'Installment.status DESC'
            ) );
        $this->set( 'pieData', $this->Chart->formatPieData( $pie, 'Installment' ) );
        
        // list of accounts
        list($accounts, $accountsbalance) = $this->Account->listAccounts();
        $this->set( compact( 'accounts' , 'accountsbalance' ) );

        //get banks list
        $banks = $this->Loan->Bank->find( 'list', array( 'order' => 'Bank.name ASC' ) );
        $this->set( compact( 'banks' ) );

        //add
        if ( !empty( $this->data ) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean( $this->data );

            //check data
            if ( intval( $this->data['Loan']['installments_period'] ) < 1 OR intval( $this->data['Loan']['start_month'] ) < 1 OR intval( $this->data['Loan']['start_year'] ) < 1 ) {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            if ( intval( $this->data['Loan']['installments_remaining'] ) < 1 ) {
                $this->Loan->invalidate( 'installments_remaining', 'تعداد اقساط باقیمانده صحیح نیست.' );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            if ( $this->data['Loan']['installments_remaining'] > 400 OR $this->data['Loan']['installments_payed'] > 400 OR ($this->data['Loan']['installments_remaining'] + $this->data['Loan']['installments_payed']) > 400 ) {
                $this->Loan->invalidate( 'installments', 'تعداد اقساط نامعتبر است.' );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            if ( $this->data['Loan']['installments_due_day'] > 29 ) {
                $this->Loan->invalidate( 'installments_due_day', 'روز سررسید اقساط بایستی بین ۱ الی ۲۹ باشد.' );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            if ( intval( $this->data['Loan']['installments_payed'] ) < 0 ) {
                $this->Loan->invalidate( 'installments_payed', 'تعداد اقساط پرداختی صحیح نیست.' );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            if ( intval( $this->data['Loan']['installments_remaining'] ) < 1 ) {
                $this->Loan->invalidate( 'installments_payed', 'تعداد اقساط باقیمانده صحیح نیست.' );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //save
            $this->data['Loan']['amount'] = floatval( str_replace( ',', '', $this->data['Loan']['amount'] ) );
            $dataSource = $this->Loan->getDataSource();

            //begin transaction
            $dataSource->begin( $this );

            $this->Loan->create();
            if ( !$this->Loan->save( $this->data ) ) {
                $dataSource->rollback( $this );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            $loanId = $this->Loan->getInsertID();
            
            $this->LoanTag->replaceTags($loanId, empty($this->data['LoanTag']['tag_id'])? array() : $this->data['LoanTag']['tag_id'] );
            
            $persianDate = new PersianDate();
            
            if ($this->data['Loan']['add']=='yes') {
                //add income
                $data['Transaction']['amount'] = $this->data['Loan']['amount'];
                $data['Transaction']['date'] = $persianDate->pdate('Y/m/d', 'now');
                $data['Transaction']['type'] = 'credit';
                $data['Transaction']['account_id'] = $this->data['Transaction']['account_id'];
                $data['Income']['income_type_id'] = $this->Income->IncomeType->getIncomeTypeIdByName('وام');
                $data['Income']['description'] = 'وام '.$this->data['Loan']['name'];
                $data['TransactionTag']['tag_id'] = $this->data['LoanTag']['tag_id'];
                if (!$transactionId = $this->Income->saveIncome($data)) {
                    $dataSource->rollback($this);
                    $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                    $this->redirect(array('action' => 'index'));
                    return false;
                }
                //save transaction id back to debt
                $this->Loan->id = $loanId;
                if(!$this->Loan->saveField('transaction_id',$transactionId)) {
                    $dataSource->rollback($this);
                    $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                    $this->redirect(array('action' => 'index'));
                    return false;
                }
            } 

            //convert dates
            $startTimestamp = $persianDate->pmktime( 1, 1, 1, $this->data['Loan']['start_month'], $this->data['Loan']['installments_due_day'], $this->data['Loan']['start_year'] );

            //save installments
            $dates = $this->Loan->Installment->generateInstallmentDates(
                intval( $this->data['Loan']['installments_remaining'] ) + intval( $this->data['Loan']['installments_payed'] ), intval( $this->data['Loan']['installments_period'] ), intval( $this->data['Loan']['start_year'] ), intval( $this->data['Loan']['start_month'] ), intval( $this->data['Loan']['installments_due_day'] )
            );
            $loanId = $this->Loan->getInsertID();
            $this->Loan->Installment->setNotification = ($this->data['Loan']['notify']=='yes');
            if ( !$this->Loan->Installment->saveInstallments(
                    $loanId, intval( str_replace( ',', '', $this->data['Loan']['installments_amount'] ) ), $dates, intval( $this->data['Loan']['installments_payed'] )
                ) ) {
                $dataSource->rollback( $this );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            $editurl = Router::url(array('controller'=>'reminders','action'=>'view','loan'=>$loanId));
            if($num=$this->Loan->Installment->saved_reminders){
                $rmdtxt = str_replace('%NUM%',$num,'تعداد %NUM% یادآور نیز برای این مورد در سیستم ثبت شد که میتوانید آنها را در <a href="'.$editurl.'">این بخش</a> مدیریت نمائید');
            } else {
                $rmdtxt = 'با توجه به تاریخ مورد و تنظیمات یادآور شما برای این مورد یادآوری ذخیره نشد که میتوانید برای مدیریت یادآورهای این مورد از <a href="'.$editurl.'">این بخش</a> اقدام نمائید';
            }

            //commit
            $dataSource->commit( $this );
            $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.<br />'.$rmdtxt, 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
    }

    function view( $id = null ) {
        $this->set( 'title_for_layout', 'نمایش وام' );
        
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }

        //get accounts
        $this->Account->recursive = -1;
        list($accounts,$accountsbalance) = $this->Account->listAccounts();

        //get loan and it's installments
        $this->Loan->recursive = 0;
        $this->Loan->outputConvertDate = true;
        $this->Loan->convertDateFormat = 'Y/m/d';
        $loan = $this->Loan->read( null, intval( $id ) );
        $installments = $this->Loan->Installment->find( 'all', array( 'conditions' => array( 'Installment.loan_id' => intval( $id ) ), 'order' => 'Installment.due_date ASC' ) );
        $settledInstallments = $this->Loan->Installment->find( 'first', array(
            'fields' => array(
                'SUM(Installment.amount) as sum'
            ),
            'conditions' => array(
                'Installment.loan_id' => intval( $id ),
                'Installment.status' => 'done'
            )
            ) );
        $unsettledInstallments = $this->Loan->Installment->find( 'first', array(
            'fields' => array(
                'SUM(Installment.amount) as sum'
            ),
            'conditions' => array(
                'Installment.loan_id' => intval( $id ),
                'Installment.status' => 'due'
            )
            ) );
        $this->set( compact( 'loan', 'installments', 'accounts', 'accountsbalance', 'settledInstallments', 'unsettledInstallments' ) );
    }

    function edit( $id = null )
    {
        $this->set( 'title_for_layout', 'ویرایش وام' );
        if ( !$id && empty( $this->data ) ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        
        $loan = $this->Loan->read( null, $id );
        $this->LoanTag->recursive = -1;
        $loan['Loan']['LoanTag'] = Set::extract("{n}.0.tag_id", $this->LoanTag->find('all', array(
            'fields' => 'CONCAT("t",LoanTag.tag_id) AS tag_id',
            'conditions' => array(
                'loan_id' => $loan['Loan']['id']
                )
        )));
        $this->loadTags();
        
        if ( !empty( $this->data ) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->data['Loan']['amount'] = floatval( str_replace( ',', '', $this->data['Loan']['amount'] ) );
            $this->data = $san->clean( $this->data );
            if ( $this->Loan->save( $this->data ) ) {
                $this->LoanTag->replaceTags($id, empty($this->data['LoanTag']['tag_id'])? array() : $this->data['LoanTag']['tag_id'] );
                
                $this->Transaction->id = $loan['Loan']['transaction_id'];
                if($this->Transaction->exists()) {
                    if(!$this->Transaction->saveField('amount',abs($this->data['Loan']['amount']))) {
                        $dataSource->rollback($this->Debt);
                        $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                        return false;
                    }
                    //update balance
                    $amount=abs($this->data['Loan']['amount'])-abs($loan['Loan']['amount']);
                    $this->TransactionTag->replaceTags($loan['Loan']['transaction_id'], empty($this->data['LoanTag']['tag_id'])? array() : $this->data['LoanTag']['tag_id'] );
                    $this->Transaction->Account->updateBalance( $this->Transaction->field('account_id',array('Transaction.id'=>$debt['Debt']['transaction_id'])) ,$amount);
                }
                
                $editurl = Router::url(array('controller'=>'reminders','action'=>'view','loan'=>$this->data['Loan']['id']));
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.<br />برای تغییر یادآورها از <a href="'.$editurl.'">این بخش</a> اقدام نمائید', 'default', array('class' => 'success'));
                $this->redirect( array( 'action' => 'index' ) );
            } else {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            }
        }
        if ( empty( $this->data ) ) {
            $this->data = $loan;
        }
        $banks = $this->Loan->Bank->find( 'list' );
        $this->set( compact( 'banks' ) );
    }

    function delete( $id = null ) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }

        //check user
        $this->Loan->recursive = -1;
        if ( $this->Loan->field( 'user_id', array( 'id' => $id ) ) != $this->Auth->user( 'id' ) ) {
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return false;
        }

        //start transaction
        $dataSource = $this->Loan->getDataSource();
        $dataSource->begin( $this->Loan );

        //find all the installment clearing transaction ids
        $installments = $this->Loan->Installment->find( 'all', array(
            'fields' => array( 'clear_transaction_id', 'Installment.id' ),
            'conditions' => array(
                'Installment.loan_id' => intval( $id ),
                #'Installment.clear_transaction_id IS NOT NULL',
            )
            ) );
        $transactions = array_unique(Set::extract( '/Installment/clear_transaction_id', $installments ));
        $installments = Set::extract( '/Installment/id', $installments );
        /* Removing null value */
        if(($k=array_search(NULL, $transactions))!==false){
            unset($transactions[$k]);
            $transactions = array_values($transactions);
        }

        //get sum of clearing transactions
        //find all the installment clearing transaction ids
        $this->Transaction->recursive = -1;
        $sum = $this->Transaction->find( 'all', array(
            'fields' => array( 'account_id', 'SUM(Transaction.amount) AS sum' ),
            'conditions' => array(
                'Transaction.id' => $transactions,
            ),
            'group' => 'Transaction.account_id'
            ) );

        //delete
        if ( !$this->Loan->delete( $id ) ) {
            $dataSource->rollback( $this->Loan );
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return false;
        }
        
        foreach($installments as $ins) {
            $this->Reminder->deleteRegarding('installment', $ins);
        }

        //delete transactions
        if ( !$this->Transaction->deleteAll( array( 'Transaction.id' => $transactions ) ) ) {
            $dataSource->rollback( $this->Loan );
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return false;
        }

        //update the balance for accounts involved
        foreach ( $sum as $entry ) {
            if ( !$this->Transaction->Account->updateBalance( $entry['Transaction']['account_id'], $entry['0']['sum'] ) ) {
                $dataSource->rollback( $this->Loan );
                $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( array( 'action' => 'index' ) );
                return false;
            }
        }

        //commit
        $dataSource->commit( $this->Loan );
        $this->Session->setFlash( 'داده مورد نظر با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
        $this->redirect( array( 'action' => 'index' ) );
        return true;
    }

    function export( $limit = null ) {
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion( 8 );
        $workbook->send( 'loans.xls' );
        $worksheet = & $workbook->addWorksheet( 'loans' );
        $worksheet->setInputEncoding( 'utf-8' );

        //get the data
        $this->Loan->recursive = 0;
        $this->Loan->outputConvertDate = true;
        $this->Loan->convertDateFormat = 'Y/m/d';
        $options = array( );
        $options['fields'] = array( 'Loan.name', 'Loan.amount', 'Loan.status', 'Bank.name', 'Loan.description' );
        $options['order'] = "Loan.id";
        //apply the conditions
        if ( $this->Session->check( 'Loan.conditions' ) AND !empty( $this->params['named'] ) ) {
            $options['conditions'] = $this->Session->read( 'Loan.conditions' );
        }
        if ( !is_null( $limit ) ) {
            $options['limit'] = $limit;
        }
        $loans = $this->Loan->find( 'all', $options );
        $data = array( );
        $data[] = array( 'عنوان وام', 'مبلغ', 'بانک', 'توضیحات' );
        $i = 1;
        foreach ( $loans as $entry ) {
            $data[$i][] = $entry['Loan']['name'];
            $data[$i][] = $entry['Loan']['amount'];
            $data[$i][] = $entry['Bank']['name'];
            $data[$i][] = __($entry['Loan']['status'],true);
            $data[$i][] = $entry['Loan']['description'];
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
    
    private function loadTags()
    {
        $this->set( 'tags' , $c=$this->Tag->prepareList( $this->Tag->loadTags() ) );
    }
    
    private function bindTagModel($force=false)
    {
        $this->Loan->bindModel( array(
            'hasOne' => array(
                'LoanTag' => array(
                        'foreignKey' => false,
                        'conditions' => array( 'LoanTag.loan_id = Loan.id' )
                    )
            )
            ), false );
        if($force) {
            $this->Loan->Behaviors->attach( 'Containable' );
            $this->Loan->contain( 'LoanTag' );
        }
    }
    
}

?>