<?php

uses('sanitize');

class InvestmentsController extends AppController {

    var $name = 'Investments';
    var $components = array('Security');

    function index() {
        $this->set('title_for_layout', 'سرمایه‌ها');
        $this->Investment->recursive = 0;       
        $this->Investment->outputConvertDate = true;
        $this->paginate['order'] = 'Investment.id DESC';
        $paginate = $this->paginate();

        $this->set( 'investments', $paginate );

        //pie data
        $this->Investment->recursive = 1;
        $pie = $this->Investment->find( 'all', array(
            'fields' => array(
                'Investment.name AS k',
                'Investment.amount AS value'
            ),
            //'limit' => ,
            ) );
        $pieData = array();
        if($pie && is_array($pie)){
            foreach ($pie as $key => $value) {
                $pieData[$key] = array('key'=>$value['Investment']['k'],'value'=>$value['Investment']['value']);
            }
        }
        $this->set( 'pieData',  $pieData);

        
        //add
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            $this->Investment->create();
            $this->data['Investment']['amount'] = floatval( str_replace( ',', '', $this->data['Investment']['amount'] ) );
            if ($this->Investment->save($this->data)) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('investment', $this->Investment->read(null, $id));
    }

    function edit($id = null) {
        $this->Investment->recursive = 0;
        $this->Investment->outputConvertDate = true;
        $this->Investment->convertDateFormat = 'Y/m/d';

        if (!$id && empty($this->data)) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            $this->data['Investment']['amount'] = floatval( str_replace( ',', '', $this->data['Investment']['amount'] ) );
            if ($this->Investment->save($this->data)) {
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Investment->read(null, $id);
        }
        //$currencies = $this->Investment->Currency->find('list');
        //$this->set(compact('currencies', 'users'));
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        //check user
        $this->Investment->recursive = -1;
        if ($this->Investment->field('user_id', array('id' => $id)) != $this->Auth->user('id')) {
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        //delete
        elseif ($this->Investment->delete($id)) {
            $this->Session->setFlash('داده مورد نظر با موفقیت پاک شد.', 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
        $this->redirect(array('action' => 'index'));
    }

}

?>