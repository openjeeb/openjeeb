<?php
/**
 * Bake Template for Controller action generation.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.console.libs.template.objects
 * @since         CakePHP(tm) v 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<?php $compact = array(); ?>
	function <?php echo $admin ?>index() {
		$this-><?php echo $currentModelName ?>->recursive = 0;
		$this->set('<?php echo $pluralName ?>', $this->paginate());
                //add
		if (!empty($this->data)) {
                        //sanitize the data
                        $san=new Sanitize();
                        $this->data=$san->clean($this->data);
			$this-><?php echo $currentModelName; ?>->create();
			if ($this-><?php echo $currentModelName; ?>->save($this->data)) {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.','default',array('class'=>'success'));
				$this->redirect(array('action' => 'index'));
<?php else: ?>
				$this->flash('داده‌های موردنظر با موفقیت وارد شد.', array('action' => 'index'));
<?php endif; ?>
			} else {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.','default',array('class'=>'error-message'));
<?php endif; ?>
			}
		}
<?php
	foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
		foreach ($modelObj->{$assoc} as $associationName => $relation):
			if (!empty($associationName)):
				$otherModelName = $this->_modelName($associationName);
				$otherPluralName = $this->_pluralName($associationName);
				echo "\t\t\${$otherPluralName} = \$this->{$currentModelName}->{$otherModelName}->find('list');\n";
				$compact[] = "'{$otherPluralName}'";
			endif;
		endforeach;
	endforeach;
	if (!empty($compact)):
		echo "\t\t\$this->set(compact(".join(', ', $compact)."));\n";
	endif;
?>
	}

	function <?php echo $admin ?>view($id = null) {
		if (!$id) {
<?php if ($wannaUseSession): ?>
			$this->Session->setFlash('شماره نامعتبر است.','default',array('class'=>'error-message'));
			$this->redirect(array('action' => 'index'));
<?php else: ?>
			$this->flash('شماره نامعتبر است.', array('action' => 'index'));
<?php endif; ?>
		}
		$this->set('<?php echo $singularName; ?>', $this-><?php echo $currentModelName; ?>->read(null, $id));
	}

<?php $compact = array(); ?>
	function <?php echo $admin; ?>edit($id = null) {
		if (!$id && empty($this->data)) {
<?php if ($wannaUseSession): ?>
			$this->Session->setFlash('شماره نامعتبر است.','default',array('class'=>'error-message'));
			$this->redirect(array('action' => 'index'));
<?php else: ?>
			$this->flash('شماره نامعتبر است.', array('action' => 'index'));
<?php endif; ?>
		}
		if (!empty($this->data)) {
                        //sanitize the data
                        $san=new Sanitize();
                        $this->data=$san->clean($this->data);
			if ($this-><?php echo $currentModelName; ?>->save($this->data)) {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.','default',array('class'=>'success'));
				$this->redirect(array('action' => 'index'));
<?php else: ?>
				$this->flash('داده‌های موردنظر با موفقیت وارد شد.', array('action' => 'index'));
<?php endif; ?>
			} else {
<?php if ($wannaUseSession): ?>
				$this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.','default',array('class'=>'error-message'));
<?php endif; ?>
			}
		}
		if (empty($this->data)) {
			$this->data = $this-><?php echo $currentModelName; ?>->read(null, $id);
		}
<?php
		foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
			foreach ($modelObj->{$assoc} as $associationName => $relation):
				if (!empty($associationName)):
					$otherModelName = $this->_modelName($associationName);
					$otherPluralName = $this->_pluralName($associationName);
					echo "\t\t\${$otherPluralName} = \$this->{$currentModelName}->{$otherModelName}->find('list');\n";
					$compact[] = "'{$otherPluralName}'";
				endif;
			endforeach;
		endforeach;
		if (!empty($compact)):
			echo "\t\t\$this->set(compact(".join(', ', $compact)."));\n";
		endif;
	?>
	}

	function <?php echo $admin; ?>delete($id = null) {
		if (!$id) {
<?php if ($wannaUseSession): ?>
			$this->Session->setFlash('شماره نامعتبر است.','default',array('class'=>'error-message'));
			$this->redirect(array('action'=>'index'));
<?php else: ?>
			$this->flash('شماره نامعتبر است.', array('action' => 'index'));
<?php endif; ?>
		}
        //check user
        $this-><?php echo $currentModelName; ?>->recursive=-1;
        if($this-><?php echo $currentModelName; ?>->field('user_id',array('id'=>$id))!=$this->Auth->user('id')) {
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        } 
        //delete
        elseif ($this-><?php echo $currentModelName; ?>->delete($id)) {
<?php if ($wannaUseSession): ?>
			$this->Session->setFlash('داده مورد نظر با موفقیت پاک شد.','default',array('class'=>'success'));
			$this->redirect(array('action'=>'index'));
<?php else: ?>
			$this->flash('داده مورد نظر با موفقیت پاک شد.', array('action' => 'index'));
<?php endif; ?>
		}
<?php if ($wannaUseSession): ?>
		$this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.','default',array('class'=>'error-message'));
<?php else: ?>
		$this->flash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', array('action' => 'index'));
<?php endif; ?>
		$this->redirect(array('action' => 'index'));
	}