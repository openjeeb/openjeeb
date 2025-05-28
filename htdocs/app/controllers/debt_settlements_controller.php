<?php

uses( 'sanitize' );

class DebtSettlementsController extends AppController {

    var $name = 'DebtSettlements';
    var $uses = array('DebtSettlement', 'Transaction');

    function delete( $id = null ) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        
        //get DebtSettlement
        $this->DebtSettlement->recursive=0;
        $debtSettlement=$this->DebtSettlement->read(null,intval($id));
        
        //check user
        if ( $debtSettlement['DebtSettlement']['user_id'] != $this->Auth->user( 'id' ) ) {
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمdsfdsfdد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        
        //start transaction
        $dataSource = $this->DebtSettlement->getDataSource();
        $dataSource->begin($this->DebtSettlement);
        
        //delete
        if ( !$this->DebtSettlement->delete( $id ) ) {
            $dataSource->rollback($this->DebtSettlement);
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        
        //set the debt as due or part
        $status='part';
        if ( $debtSettlement['DebtSettlement']['amount'] == $debtSettlement['Debt']['settled'] ) {
            $status = 'due';
        }
        
        $this->DebtSettlement->Debt->id = $debtSettlement['DebtSettlement']['debt_id'];
        if ( !$this->DebtSettlement->Debt->saveField( 'status', $status ) ) {
            $dataSource->rollback($this->DebtSettlement);
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }

        //delete the transaction too
        if ( !is_null( $debtSettlement['DebtSettlement']['transaction_id'] ) ) {
            if ( !$this->DebtSettlement->Transaction->deleteAndUpdateBalance( $debtSettlement['DebtSettlement']['transaction_id'] ) ) {
                $dataSource->rollback( $this->DebtSettlement );
                $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( Controller::referer() );
                return false;
            }
        }
        
        //commit
        $dataSource->commit($this->Debt);
        $this->Session->setFlash( 'داده مورد نظر با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
        $this->redirect( Controller::referer() );
        return true;
    }
    
//    function edit( $id = null ) {
//        if ( !$id && empty( $this->data ) ) {
//            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
//            $this->redirect( array( 'action' => 'index' ) );
//        }
//        if ( empty( $this->data ) ) {
//            $this->data = $this->DebtSettlement->read( null, $id );
//        }
//        if ( !empty( $this->data ) ) {
//            //sanitize the data
//            $san = new Sanitize();
//            $this->data = $san->clean( $this->data );
//            $this->DebtSettlement->recursive = 0;
//            $debtSettlement = $this->DebtSettlement->read( null, intval( $id ) );
//            $rest = $debtSettlement['Debt']['settled'] - $debtSettlement['DebtSettlement']['amount'];
//            if ( abs( $debtSettlement['Debt']['amount'] ) < $rest + $this->data['DebtSettlement']['amount'] ) {
//                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
//                return false;
//            }
//            if ( $this->DebtSettlement->save( $this->data ) ) {
//                $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
//                $this->redirect( array( 'action' => 'index' ) );
//            } else {
//                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
//            }
//        }
//    }
}

?>