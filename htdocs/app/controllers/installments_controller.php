<?php

uses( 'sanitize' );

class InstallmentsController extends AppController {

    var $name = 'Installments';
    var $uses = array( 'Installment', 'Expense', 'Transaction', 'Reminder' );
    var $components = array( 'Security' );

    function add( $loanId = null ) {
        $this->set( 'title_for_layout', 'افزودن قسط' );
        if ( !$loanId ) {
            $this->Session->setFlash( 'مشکلی در ثبت داده‌ها بوجود آمد.', 'default', array( 'class' => 'error-message' ) );
            return false;
        }

        //get Loan
        $this->Installment->Loan->recursive = -1;
        $loan = $this->Installment->Loan->read( null, intval( $loanId ) );
        $this->set( compact( 'loan' ) );

        if ( empty( $loan ) ) {
            $this->Session->setFlash( 'مشکلی در ثبت داده‌ها بوجود آمد.', 'default', array( 'class' => 'error-message' ) );
            return false;
        }

        if(!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean( $this->data );

            //remove price formatting
            $this->data['Installment']['amount'] = str_replace( ',', '', $this->data['Installment']['amount'] );

            //add in tranaction safe mode
            $dataSource = $this->Installment->getDataSource();
            $dataSource->begin( $this->Installment );
            $this->data['Installment']['loan_id'] = $loanId;
            $this->Installment->create();
            if ( !$this->Installment->save( $this->data ) ) {
                $dataSource->rollback( $this->Installment );
                $this->Session->setFlash( 'مشکلی در ثبت داده‌ها بوجود آمد.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            //set the loan status as due
            $this->Installment->Loan->id = $loanId;
            if( !$this->Installment->Loan->saveField( 'status' , 'due' )) {
                $dataSource->rollback( $this->Installment );
                $this->Session->setFlash( 'مشکdsfsdfلی در ثبت داده‌ها بوجود آمد.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            //commit transaction
            $dataSource->commit( $this->Installment );
            
            $refId = $this->Installment->getLastInsertID();
            $editurl = Router::url(array('controller'=>'reminders','action'=>'view','installment'=>$refId));
            if(($this->data['Installment']['notify']=='yes') && ($num = $this->Reminder->addReminder('installment', $this->data['Installment']['due_date'], $refId) ) ) {
                $rmdtxt = str_replace('%NUM%',$num,'تعداد %NUM% یادآور نیز برای این مورد در سیستم ثبت شد که میتوانید آنها را در <a href="'.$editurl.'">این بخش</a> مدیریت نمائید');
            }else {
                $rmdtxt = 'با توجه به تاریخ مورد و تنظیمات یادآور شما برای این مورد یادآوری ذخیره نشد که میتوانید برای مدیریت یادآورهای این مورد از <a href="'.$editurl.'">این بخش</a> اقدام نمائید';
            }
            
            $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.<br />'.$rmdtxt, 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'controller' => 'loans', 'action' => 'view', $loanId ) );
            return true;
        }
    }

    function edit( $id = null ) {
        $this->set( 'title_for_layout', 'ویرایش قسط' );

        if ( !$id && empty( $this->data ) ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( $this->referer() );
            return false;
        }

        //get installment
        $this->Installment->outputConvertDate = true;
        $this->Installment->convertDateFormat = 'Y/m/d';
        $this->Installment->recursive = -1;
        $installment = $this->Installment->read( null, intval( $id ) );

        //save
        if ( !empty( $this->data ) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean( $this->data );
            
            //remove price formatting
            $this->data['Installment']['amount'] = str_replace( ',', '', $this->data['Installment']['amount'] );

            //start transaction
            $dataSource = $this->Installment->getDataSource();
            $dataSource->begin( $this->Installment );

            //save
            if ( !$this->Installment->save( $this->data ) ) {
                $dataSource->rollback( $this->Installment );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            //check for amount and installment clearing transaction
            if ( $installment['Installment']['amount'] != $this->data['Installment']['amount'] AND intval( $installment['Installment']['clear_transaction_id'] ) > 0 ) {
                //fix clearing transaction
                $this->Transaction->id = intval( $installment['Installment']['clear_transaction_id'] );
                $this->Transaction->saveField( 'amount', intval( $this->data['Installment']['amount'] ) );
            }

            //commit
            $dataSource->commit( $this->Installment );
            $editurl = Router::url(array('controller'=>'reminders','action'=>'view','installment'=>$this->data['Installment']['id']));
            $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.<br />برای تغییر یادآورها از <a href="'.$editurl.'">این بخش</a> اقدام نمائید', 'default', array('class' => 'success'));
            $this->redirect( array( 'controller' => 'loans', 'action' => 'view', $installment['Installment']['loan_id'] ) );
            return true;
        }

        //assign edit data
        if ( empty( $this->data ) ) {
            $this->data = $installment;
        }
    }

    function delete( $id = null ) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( $this->referer() );
            return false;
        }

        //get installment
        $this->Installment->recursive = -1;
        $installment = $this->Installment->read( null, intval( $id ) );

        //check user
        if ( $installment['Installment']['user_id'] != $this->Auth->user( 'id' ) ) {
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( $this->referer() );
            return false;
        }

        //start transaction
        $dataSource = $this->Installment->getDataSource();
        $dataSource->begin( $this->Installment );

        //delete
        if ( !$this->Installment->delete( $id ) ) {
            $dataSource->rollback( $this->Installment );
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( $this->referer() );
            return false;
        }
        $this->Reminder->deleteRegarding('installment',$id);

        //check for clearing transaction and delete associated transaction
        if ( intval( $installment['Installment']['clear_transaction_id'] ) > 0 ) {
            //delete transaction and update the balance
            $this->Transaction->deleteAndUpdateBalance( intval( $installment['Installment']['clear_transaction_id'] ) );
        }

        //commit
        $dataSource->commit( $this->Installment );
        $this->Session->setFlash( 'داده مورد نظر با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
        $this->redirect( $this->referer() );
        return true;
    }

    /*
     * mark installment as done
     */

    function ajaxInstallmentDone( $id ) {
        if ( $this->RequestHandler->isAjax() ) {
            Configure::write( 'debug', 0 );
            //sanitize the data
            $san = new Sanitize();
            $this->params = $san->clean( $this->params );
            //get the installments expense category
            $this->Expense->ExpenseCategory->recursive = -1;
            $expenseCategory = $this->Expense->ExpenseCategory->find( 'first', array( 'conditions' => array( 'name' => 'اقساط' ) ) );
            if ( empty( $expenseCategory ) ) {
                $this->set( 'response', false );
                $this->render( 'ajax', 'json' );
                return false;
            }
            //get the installment
            $this->Installment->outputConvertDate = false;
            $installment = $this->Installment->read( null, intval( $id ) );
            //gather data
            $data['Transaction']['account_id'] = intval( $this->params['form']['addExpense'] );
            $data['Transaction']['amount'] = $installment['Installment']['amount'];
            $data['Transaction']['date'] = date( 'Y-m-d' );
            $data['Expense']['description'] = 'پرداخت قسط ' . $installment['Loan']['name'];
            $data['Expense']['expense_category_id'] = $expenseCategory['ExpenseCategory']['id'];
            //start transaction
            $dataSource = $this->Installment->getDataSource();
            $dataSource->begin( $this->Installment );
            //update the installment
            $this->Installment->id = intval( $id );
            if ( !$this->Installment->saveField( 'status', 'done', true ) ) {
                $dataSource->rollback( $this->Installment );
                $this->set( 'response', false );
                $this->render( 'ajax', 'json' );
                return false;
            }
            //save the expense
            $this->Expense->inputConvertDate = false;
            if ( !$transactionId = $this->Expense->saveExpense( $data ) ) {
                $dataSource->rollback( $this->Installment );
                $this->set( 'response', false );
                $this->render( 'ajax', 'json' );
                return false;
            }
            //save clearing transaction id back to installment
            if ( !$this->Installment->saveField( 'clear_transaction_id', $transactionId ) ) {
                $dataSource->rollback( $this->Installment );
                $this->set( 'response', false );
                $this->render( 'ajax', 'json' );
                return false;
            }
            //check if the loan is cleared
            if ( !$this->Installment->hasAny( array( 'loan_id' => $installment['Installment']['loan_id'], 'status' => 'due' ) ) ) {
                $this->Installment->Loan->id = $installment['Installment']['loan_id'];
                $this->Installment->Loan->saveField( 'status', 'done' );
            }
            
            $this->Reminder->deleteRegarding('installment',$id);

            //commit
            $this->Session->setFlash('قسط مورد نظر با موفقیت پرداخت شد.<br />یادآورهای این مورد نیز حذف شده اند. در صورت تمایل به ساختن یادآور برای این موضوع از <a href="'.Router::url( array( 'controller'=>'reminders' , 'action'=>'view', 'installment'=>$id ) ).'">اینجا</a> اقدام کنید', 'default', array('class' => 'success'));
            $dataSource->commit( $this->Installment );
            $this->set( 'response', true );
            $this->render( 'ajax', 'json' );
            return true;
        }
        $this->redirect( array( 'controller' => 'reports', 'action' => 'dashboard' ) );
    }

    function export( $installmentId, $limit = null ) {
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion( 8 );
        $workbook->send( 'installments.xls' );
        $worksheet = & $workbook->addWorksheet( 'expenses' );
        $worksheet->setInputEncoding( 'utf-8' );

        //get the data
        $this->Installment->recursive = 0;
        $this->Installment->outputConvertDate = true;
        $this->Installment->convertDateFormat = 'Y/m/d';
        $options = array( );
        $options['fields'] = array( 'Installment.amount', 'Installment.due_date', 'Installment.description', 'Installment.status' );
        $options['order'] = "Installment.due_date";
        //apply the conditions
        if ( $this->Session->check( 'Installment.conditions' ) AND !empty( $this->params['named'] ) ) {
            $options['conditions'] = $this->Session->read( 'Installment.conditions' );
        }
        $options['conditions']['loan_id'] = intval( $installmentId );
        if ( !is_null( $limit ) ) {
            $options['limit'] = $limit;
        }
        $installments = $this->Installment->find( 'all', $options );
        $data = array( );
        $data[] = array( 'مبلغ قسط', 'تاریخ سر رسید', 'توضیحات', 'وضعیت' );
        $i = 1;
        foreach ( $installments as $entry ) {
            $data[$i][] = $entry['Installment']['amount'];
            $data[$i][] = $entry['Installment']['due_date'];
            $data[$i][] = $entry['Installment']['description'];
            $data[$i][] = __( $entry['Installment']['status'], true );
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

}

?>