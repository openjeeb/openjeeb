<?php

uses('sanitize');

class ExpenseCategoriesController extends AppController {

    var $name = 'ExpenseCategories';

    function index() {
        $this->set('title_for_layout','انواع هزینه');
        $userId = $this->Auth->user('id');
        $this->ExpenseCategory->recursive = -1;
        $this->ExpenseCategory->contain('ExpenseSubCategory');
        $this->set('expenseCategories', $this->ExpenseCategory->find('all', array(
                'order' => 'ExpenseCategory.sort ASC',
            )));
        //pie data
        $this->ExpenseCategory->Expense->recursive = 0;
        $pie=$this->ExpenseCategory->Expense->find('all',array(
            'fields'=>array(
                'ExpenseCategory.name as k',
//                '((SUM(Expense.amount)/(SELECT SUM(amount) FROM expenses where user_id='.$userId.'))*100) AS percent'
                'SUM(Transaction.amount) AS value'
            ),
            'group'=>array('Expense.expense_category_id'),
        ));
        $this->set('pieData',$this->Chart->formatPieData($pie,'ExpenseCategory'));        
        //add
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            $this->data['ExpenseCategory']['user_id'] = $userId;
            $sort = $this->ExpenseCategory->find( 'first' , array( 'fields' => array( 'GREATEST(MAX(sort)+1,COUNT(*)) AS sort' )) );
            $this->data['ExpenseCategory']['sort'] = intval($sort[0]['sort']);
            $this->ExpenseCategory->create();
            if ($this->ExpenseCategory->save($this->data)) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
    }

    function edit($id = null) {
        $this->set('title_for_layout','ویرایش نوع هزینه');
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        //get user id
        $userId = $this->Auth->user('id');
        //get expense category
        $this->ExpenseCategory->recursive = -1;
        $expenseCategory = $this->ExpenseCategory->read(null, intval($id));
        $expenseCategory['ExpenseCategory']['name'] = html_entity_decode( str_replace( '\n', "\n", $expenseCategory['ExpenseCategory']['name'] ), ENT_QUOTES, 'UTF-8' );
        
        //check if this category belongs to user
        if ($expenseCategory['ExpenseCategory']['user_id'] != $userId) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        //check for system category
        if($expenseCategory['ExpenseCategory']['delete']=='no') {
            $this->Session->setFlash('این نوع هزینه قابل ویرایش و حذف نیست.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        //
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            //add user id
            $this->data['ExpenseCategory']['user_id'] = $userId;
            //save
            if ($this->ExpenseCategory->save($this->data)) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
        if (empty($this->data)) {
            $this->data = $expenseCategory;
        }
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        //check user
        $this->ExpenseCategory->recursive=-1;
        $expenseCategory=$this->ExpenseCategory->read(null,intval($id));
        if($expenseCategory['ExpenseCategory']['user_id']!=$this->Auth->user('id')) {
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }
        if($expenseCategory['ExpenseCategory']['delete']=='no') {
            $this->Session->setFlash('این نوع هزینه قابل پاک کردن نیست.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }
        //check for expenses with this category
        if($this->ExpenseCategory->Expense->hasAny(array('expense_category_id'=>intval($id)))) {
            $this->Session->setFlash('این نوع هزینه قابل پاک کردن نیست. هزینه‌هایی با این نوع هزینه وجود دارد، ابتدا باید آنها را پاک کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;            
        }
        //delete
        if ($this->ExpenseCategory->delete($id)) {
            $this->Session->setFlash('داده مورد نظر با موفقیت پاک شد.', 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
            return true;
        }
        $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
        $this->redirect(array('action' => 'index'));
        return false;
    }
    
    function ajaxGetSubCategories() {
        if ($this->RequestHandler->isAjax()) {
            Configure::write('debug',0);
            //sanitize the data
            $san = new Sanitize();
            $this->params = $san->clean($this->params);
            //get sub categories
            $this->ExpenseCategory->ExpenseSubCategory->recursive=-1;
            $expenseSubCategories=$this->ExpenseCategory->ExpenseSubCategory->find('list',array(
                'conditions'=>array(
                    'ExpenseSubCategory.expense_category_id'=>intval($this->params['form']['expense_category_id']),
                    'ExpenseSubCategory.user_id'=>intval($this->Auth->user('id')),
                ),
            ));
            $this->set('response', $expenseSubCategories);
            $this->render('ajax', 'json');
            return true;
        }
        $this->redirect(array('action' => 'index'));
    }
    
    function sort()
    {
    	if ( isset($this->passedArgs['up']) || isset($this->passedArgs['down']) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->passedArgs = $san->clean( $this->passedArgs );
            $orientation = isset($this->passedArgs['up'])? 'up' : 'down';

            if ( $this->ExpenseCategory->moveSortItem( $this->passedArgs[$orientation], $orientation ) ) {
                
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
        if( $status = $this->ExpenseCategory->toggleStatus( intval( $this->passedArgs[0] ) ) ){
            if($status == 'active') {
                $this->Session->setFlash( 'گروه مورد نظر شما فعال شد و در لیستها نمایش داده خواهد شد.', 'default', array( 'class' => 'success' ) );
            } else {
                $this->Session->setFlash( 'گروه مورد نظر شما غیر فعال شد و در لیستها نمایش داده نخواهد شد.', 'default', array( 'class' => 'success' ) );
            }
        } else {
            $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );    			
        }
        
        $this->redirect( $this->referer(array('action'=>'index')), true );        
        return true;
    }
    
}

?>