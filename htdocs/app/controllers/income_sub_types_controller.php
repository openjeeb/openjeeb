<?php

uses('sanitize');

class IncomeSubTypesController extends AppController {

    var $name = 'IncomeSubTypes';

    function add($parentId=null) {
        $this->set('title_for_layout','زیر شاخه نوع درآمد');
        
        if (!$parentId && empty($this->data)) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'IncomeTypes','action' => 'index'));
        }

        //get user id
        $userId = $this->Auth->user('id');

        //check for user
        $this->IncomeSubType->IncomeType->recursive=-1;
        $incomeType = $this->IncomeSubType->IncomeType->read(null, intval($parentId));
        $this->set('income_type_id',$incomeType['IncomeType']['id']);
        if($incomeType['IncomeType']['user_id']!=$userId) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'IncomeTypes','action' => 'index'));
        }
        
        //
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
        
            //gather data
            $this->data['IncomeSubType']['user_id'] = $userId;
            $this->data['IncomeSubType']['income_type_id'] = intval($parentId);
            
            //save
            $this->IncomeSubType->create();
            if ($this->IncomeSubType->save($this->data)) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('controller'=>'IncomeTypes','action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
            
        }
    }

    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'incomeTypes','action' => 'index'));
        }
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            //get user id
            $userId = $this->Auth->user('id');
            $this->data['IncomeSubType']['user_id'] = $userId;
            //save
            if ($this->IncomeSubType->save($this->data)) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('controller'=>'incomeTypes','action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->IncomeSubType->read(null, $id);
            $this->data['IncomeSubType']['name'] = html_entity_decode( str_replace( '\n', "\n", $this->data['IncomeSubType']['name'] ), ENT_QUOTES, 'UTF-8' );
        }
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'IncomeTypes','action' => 'index'));
        }
        //check user
        $this->IncomeSubType->recursive = -1;
        if ($this->IncomeSubType->field('user_id', array('id' => $id)) != $this->Auth->user('id')) {
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'IncomeTypes','action' => 'index'));
            return false;
        }
        //check for expenses with this category
        if($this->IncomeSubType->IncomeType->Income->hasAny(array('income_sub_type_id'=>intval($id)))) {
            $this->Session->setFlash('این نوع درآمد قابل پاک کردن نیست. درآمدهایی با این نوع درآمد وجود دارد، ابتدا باید آنها را پاک کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'IncomeTypes','action' => 'index'));
            return false;            
        }      
        
        //delete
        if ($this->IncomeSubType->delete($id)) {
            $this->Session->setFlash('داده مورد نظر با موفقیت پاک شد.', 'default', array('class' => 'success'));
            $this->redirect(array('controller'=>'IncomeTypes','action' => 'index'));
            return true;
        }
        $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
        $this->redirect(array('controller'=>'IncomeTypes','action' => 'index'));
        return false;
    }

}

?>