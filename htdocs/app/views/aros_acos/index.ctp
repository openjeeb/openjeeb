<div class="col-xs-16 col-md-16 box" style="width: 920px;">
    <h2><?php echo $this->Html->link('ایجاد دسترسی', '#',array('id'=>'toggle-new','class'=>'hidden')); ?></h2>
    <div id="new" class="arosAcos form">
    <?php echo $this->Form->create('ArosAco'); ?>
    <fieldset>
    	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('aro_id',array('label'=>'گروه کاربری'));
		echo $this->Form->input('aco_id',array('label'=>'دسترسی به','style'=>'direction:ltr;'));
                echo $form->input('permission',array('type'=>'select','options'=>array('allow'=>'Allow','deny'=>'Deny'),'label'=>'دسترسی'));
	?>
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>

</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">دسترسی‌ها</h2>   
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array(
        $paginator->sort('ردیف','id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('گروه','aro_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('دسترسی به','aco_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('ایجاد','_create',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('خواندن','_read',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('بروزرسانی','_update',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('پاک کردن','_delete',array('url'=>array('#'=>'#dataTable')))));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php
	$i = 0;
	foreach ($arosAcos as $arosAco):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $arosAco['ArosAco']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $arosAco['UserGroup']; ?>
		</td>
		<td>
			<?php echo $arosAco['Aco']['alias']; ?>
		</td>
		<td><?php echo $arosAco['ArosAco']['_create']; ?>&nbsp;</td>
		<td><?php echo $arosAco['ArosAco']['_read']; ?>&nbsp;</td>
		<td><?php echo $arosAco['ArosAco']['_update']; ?>&nbsp;</td>
		<td><?php echo $arosAco['ArosAco']['_delete']; ?>&nbsp;</td>
	</tr>
	<tr class="actions">
		<td>
			
		</td>
		<td>
			<?php echo $this->Html->link('نمایش', array('action' => 'view', $arosAco['ArosAco']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link('ویرایش', array('action' => 'edit', $arosAco['ArosAco']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link('پاک کردن', array('action' => 'delete', $arosAco['ArosAco']['id']), null, sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $arosAco['ArosAco']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
    
          
	<?php echo $this->element('pagination/bottom'); ?>
		
</div>
<div class="clear"></div>
