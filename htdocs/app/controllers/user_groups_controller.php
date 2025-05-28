<?php
class UserGroupsController extends AppController {

	var $name = 'UserGroups';
        var $uses = array('UserGroup','Aro','ArosAco');

	function index() {
		$this->UserGroup->recursive = 0;
		$this->set('userGroups', $this->paginate());
                //add
		if (!empty($this->data)) {
			$this->UserGroup->create();
			if ($this->UserGroup->save($this->data)) {
                                $this->Aro->recursive = 0;
                                $aro=$this->Aro->find('first',array('conditions' => array('Aro.model'=>'UserGroup','Aro.foreign_key'=>$this->UserGroup->getInsertID())));
                                $this->ArosAco->save(array('ArosAco'=>array('aro_id'=>$aro['Aro']['id'],'aco_id'=>'1','_create'=>'-1','_read'=>'-1','_update'=>'-1','_delete'=>'-1')));
				$this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.','default',array('class'=>'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.','default',array('class'=>'error-message'));
			}
		}
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('شماره نامعتبر است.','default',array('class'=>'error-message'));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('userGroup', $this->UserGroup->read(null, $id));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('شماره نامعتبر است.','default',array('class'=>'error-message'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->UserGroup->save($this->data)) {
				$this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.','default',array('class'=>'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.','default',array('class'=>'error-message'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->UserGroup->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('شماره نامعتبر است.','default',array('class'=>'error-message'));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->UserGroup->delete($id)) {
			$this->Session->setFlash('داده مورد نظر با موفقیت پاک شد.','default',array('class'=>'success'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.','default',array('class'=>'error-message'));
		$this->redirect(array('action' => 'index'));
	}
}
?>