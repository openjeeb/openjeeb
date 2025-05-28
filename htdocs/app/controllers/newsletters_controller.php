<?php

uses( 'sanitize' );

class NewslettersController extends AppController {

    var $name = 'Newsletters';

    function index() {
        $this->Newsletter->recursive = 0;
        $this->Newsletter->order = 'id DESC';
        $this->set( 'newsletters', $this->paginate() );
        //add
        if ( !empty( $this->data ) ) {
            $this->Newsletter->create();
            if ( $this->Newsletter->save( $this->data ) ) {
                $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
                $this->redirect( array( 'action' => 'index' ) );
            } else {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            }
        }
    }

    function view( $id = null ) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        $this->layout = 'newsletter';
        $this->set( 'newsletter', $this->Newsletter->read( null, $id ) );
    }

    function edit( $id = null ) {
        if ( !$id && empty( $this->data ) ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        if ( !empty( $this->data ) ) {
            if ( $this->Newsletter->save( $this->data ) ) {
                $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
                $this->redirect( array( 'action' => 'index' ) );
            } else {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            }
        }
        if ( empty( $this->data ) ) {
            $this->data = $this->Newsletter->read( null, $id );
        }
    }

    function delete( $id = null ) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        //check user
        $this->Newsletter->recursive = -1;
        if ( $this->Newsletter->field( 'user_id', array( 'id' => $id ) ) != $this->Auth->user( 'id' ) ) {
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        //delete
        elseif ( $this->Newsletter->delete( $id ) ) {
            $this->Session->setFlash( 'داده مورد نظر با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
        $this->redirect( array( 'action' => 'index' ) );
    }

}

?>