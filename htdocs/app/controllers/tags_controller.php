<?php

uses('sanitize');

class TagsController extends AppController {

    var $name = 'Tags';

    function index() {
        $this->set('title_for_layout','برچسب ها');
        
        $userId = $this->Auth->user('id');
        
        $this->paginate['recursive'] = -1;
        $this->set('tags', $this->paginate());
        
        //add
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            
            if ( $this->Tag->newTag($this->data['Tag']['name']) ) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                $this->redirect(array('action' => 'index'));
            }
        }
    }

    function edit($id = null) {
        $this->set('title_for_layout','ویرایش برچسب');
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        //get user id
        $userId = $this->Auth->user('id');
        //get expense category
        $this->Tag->recursive = -1;
        $this->Tag->convertDateTimeFormat = 'Y/m/d';
        $tag = $this->Tag->read(null, intval($id));
        $tag['Tag']['name'] = html_entity_decode( str_replace( '\n', "\n", $tag['Tag']['name'] ), ENT_QUOTES, 'UTF-8' );
        
        //check if this category belongs to user
        if ($tag['Tag']['user_id'] != $userId) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        
        //
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            
            $tags = $this->Tag->loadTags();
            
            $k=array_search($this->data['Tag']['name'], $tags);
            if($k && $k!=$id) {
                $this->Session->setFlash('برچسبی با این عنوان قبلا ثبت شده است', 'default', array('class' => 'error-message'));
                $this->redirect(array('action' => 'index'));
            }
            //add user id
            $this->data['Tag']['user_id'] = $userId;
            //save
            $this->Tag->id = $this->data['Tag']['id'];
            if ($this->Tag->save($this->data)) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
        if (empty($this->data)) {
            $this->data = $tag;
        }
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        //check user
        $this->Tag->recursive=-1;
        $tag=$this->Tag->read(null,intval($id));
        if($tag['Tag']['user_id']!=$this->Auth->user('id')) {
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }
        
        //delete
        if ($this->Tag->delete($id)) {
            $this->Session->setFlash('داده مورد نظر با موفقیت پاک شد.', 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
            return true;
        }
        $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
        $this->redirect(array('action' => 'index'));
        return false;
    }
    
    function toggleshow($id)
    {
        
        if( $status = $this->Tag->toggleStatus( intval( $id ) ) ){
            if($status == 'active') {
                $this->Session->setFlash( 'برچسب مورد نظر شما فعال شد و در لیستها نمایش داده خواهد شد.', 'default', array( 'class' => 'success' ) );
            } else {
                $this->Session->setFlash( 'برچسب مورد نظر شما غیر فعال شد و در لیستها نمایش داده نخواهد شد.', 'default', array( 'class' => 'success' ) );
            }
        } else {
            $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );    			
        }
        
        $this->redirect( $this->referer(array('action'=>'index')), true );        
        return true;
    }
    
}

?>