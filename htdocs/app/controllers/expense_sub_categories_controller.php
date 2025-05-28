<?php

uses('sanitize');

class ExpenseSubCategoriesController extends AppController {

    var $name = 'ExpenseSubCategories';

    function add($parentId=null) {
        $this->set('title_for_layout','زیر شاخه نوع هزینه');
        if (!$parentId && empty($this->data)) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'ExpenseCategories','action' => 'index'));
        }        
        //get user id
        $userId = $this->Auth->user('id');
        //check for user
        $this->ExpenseSubCategory->ExpenseCategory->recursive=-1;
        $expenseCategory = $this->ExpenseSubCategory->ExpenseCategory->read(null, intval($parentId));
        if($expenseCategory['ExpenseCategory']['user_id']!=$userId) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'ExpenseCategories','action' => 'index'));
        }
        //
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            //
            $this->data['ExpenseSubCategory']['user_id'] = $userId;
            $this->data['ExpenseSubCategory']['expense_category_id'] = intval($parentId);
            //save
            $this->ExpenseSubCategory->create();
            if ($this->ExpenseSubCategory->save($this->data)) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('controller'=>'ExpenseCategories','action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
        $this->set('expense_category_id',$expenseCategory['ExpenseCategory']['id']);
    }

    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'ExpenseCategories','action' => 'index'));
        }
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            //get user id
            $userId = $this->Auth->user('id');
            $this->data['ExpenseSubCategory']['user_id'] = $userId;
            //save
            if ($this->ExpenseSubCategory->save($this->data)) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('controller'=>'ExpenseCategories','action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->ExpenseSubCategory->read(null, $id);
            $this->data['ExpenseSubCategory']['name'] = html_entity_decode( str_replace( '\n', "\n", $this->data['ExpenseSubCategory']['name'] ), ENT_QUOTES, 'UTF-8' );
        }
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'ExpenseCategories','action' => 'index'));
        }
        //check user
        $this->ExpenseSubCategory->recursive=-1;
        if($this->ExpenseSubCategory->field('user_id',array('id'=>$id))!=$this->Auth->user('id')) {
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'ExpenseCategories','action' => 'index'));
        }
        //check for expenses with this category
        if($this->ExpenseSubCategory->Expense->hasAny(array('expense_sub_category_id'=>intval($id)))) {
            $this->Session->setFlash('این نوع هزینه قابل پاک کردن نیست. هزینه‌هایی با این نوع هزینه وجود دارد، ابتدا باید آنها را پاک کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'ExpenseCategories','action' => 'index'));
            return false;            
        }      
        //delete
        if ($this->ExpenseSubCategory->delete($id)) {
            $this->Session->setFlash('داده مورد نظر با موفقیت پاک شد.', 'default', array('class' => 'success'));
            $this->redirect(array('controller'=>'ExpenseCategories','action' => 'index'));
        }
        $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
        $this->redirect(array('controller'=>'ExpenseCategories','action' => 'index'));
    }

}

?>