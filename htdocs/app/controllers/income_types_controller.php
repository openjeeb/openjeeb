<?php

uses('sanitize');

class IncomeTypesController extends AppController {

    var $name = 'IncomeTypes';

    function index() {
        $this->set('title_for_layout','انواع درآمد');
        $this->IncomeType->recursive = -1;
        $this->IncomeType->contain('IncomeSubType');
        //$this->IncomeType->outputConvertDate=true;
        $this->set('incomeTypes', $this->IncomeType->find('all', array(
                'order' => 'IncomeType.sort ASC',
            )));
        //pie data
        $this->IncomeType->Income->recursive = 0;
        $pie=$this->IncomeType->Income->find('all',array(
            'fields'=>array(
                'IncomeType.name AS k',
//                '((SUM(Income.amount)/(SELECT SUM(amount) FROM incomes where user_id='.$this->Auth->user('id').'))*100) AS value'
                'SUM(Transaction.amount) AS value'
            ),
            'group'=>array('Income.income_type_id'),
        ));
        $this->set('pieData',$this->Chart->formatPieData($pie,'IncomeType'));        
        //add
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            $sort = $this->IncomeType->find( 'first' , array( 'fields' => array( 'GREATEST(MAX(sort)+1,COUNT(*)) AS sort' )) );
            $this->data['IncomeType']['sort'] = intval($sort[0]['sort']);
            $this->IncomeType->create();
            if ($this->IncomeType->save($this->data)) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
    }

    function edit($id = null) {
        $this->set('title_for_layout','ویرایش نوع درآمد');
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        //get income type
        $this->IncomeType->recursive=-1;
        $incomeType=$this->IncomeType->read(null,intval($id));
        //check for system income type
        if($incomeType['IncomeType']['delete']=='no') {
            $this->Session->setFlash('این نوع درآمد قابل ویرایش و حذف نیست.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        } 
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            if ($this->IncomeType->save($this->data)) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->IncomeType->read(null, $id);
            $expenseCategory['IncomeType']['name'] = html_entity_decode( str_replace( '\n', "\n", $expenseCategory['IncomeType']['name'] ), ENT_QUOTES, 'UTF-8' );
        }
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        //check user
        $this->IncomeType->recursive=-1;
        $incomeType=$this->IncomeType->read(null,intval($id));
        if($incomeType['IncomeType']['user_id']!=$this->Auth->user('id')) {
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        } 
        if($incomeType['IncomeType']['delete']=='no') {
            $this->Session->setFlash('این نوع درآمد قابل پاک کردن نیست.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }
        //check for incomes with this category
        if($this->IncomeType->Income->hasAny(array('income_type_id'=>intval($id)))) {
            $this->Session->setFlash('این نوع درآمد قابل پاک کردن نیست. درآمدهایی با این نوع درآمد وجود دارد، ابتدا باید آنها را پاک کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;            
        }
        //delete
        if ($this->IncomeType->delete($id)) {
            $this->Session->setFlash('داده مورد نظر با موفقیت پاک شد.', 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
            return true;
        }
        $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
        $this->redirect(array('action' => 'index'));
        return false;
    }    
    
    function sort()
    {
    	if ( isset($this->passedArgs['up']) || isset($this->passedArgs['down']) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->passedArgs = $san->clean( $this->passedArgs );
            $orientation = isset($this->passedArgs['up'])? 'up' : 'down';

            if ( $this->IncomeType->moveSortItem( $this->passedArgs[$orientation], $orientation ) ) {
                
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
        if( $status = $this->IncomeType->toggleStatus( intval( $this->passedArgs[0] ) ) ){
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