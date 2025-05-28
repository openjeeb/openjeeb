<div class="col-xs-16 col-md-16 box" style="width: 920px;">
    <h2>New Task</h2>
    <div id="new" class="tasks form">
    <?php echo $this->Form->create('Task'); ?>
    <fieldset>
    	<?php
		echo $this->Form->input('id',array('label'=>'id'));
		echo $this->Form->input('title',array('label'=>'title'));
		echo $this->Form->input('user_id',array('label'=>'user_id'));
		echo $this->Form->input('sdate',array('label'=>'sdate'));
		echo $this->Form->input('edate',array('label'=>'edate'));
	?>
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>

</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">Tasks</h2>

    
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array($paginator->sort('id','id'),$paginator->sort('title','title'),$paginator->sort('user_id','user_id'),$paginator->sort('sdate','sdate'),$paginator->sort('edate','edate'),'Actions'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php
	$i = 0;
	foreach ($tasks as $task):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $task['Task']['id']; ?>&nbsp;</td>
		<td><?php echo $task['Task']['title']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($task['User']['email'], array('controller' => 'users', 'action' => 'view', $task['User']['id'])); ?>
		</td>
		<td><?php echo $task['Task']['sdate']; ?>&nbsp;</td>
		<td><?php echo $task['Task']['edate']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link('نمایش', array('action' => 'view', $task['Task']['id'])); ?>
		
			<?php echo $this->Html->link('ویرایش', array('action' => 'edit', $task['Task']['id'])); ?>
		
			<?php echo $this->Html->link('پاک کردن', array('action' => 'delete', $task['Task']['id']), null, sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $task['Task']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
    
          
	<p align="center">
	<?php
	echo $this->Paginator->counter(array(
                    'format' => 'صفحه %page% از %pages%, در حال نمایش %current% مورد از %count%, از %start% تا %end%'
                ));
	?>	</p>

	<div  align="center" class="paging">
		<?php echo $this->Paginator->prev('<< قبلی', array(), null, array('class' => 'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
&nbsp;|
		<?php echo $this->Paginator->next('بعدی >>', array(), null, array('class' => 'disabled'));?>
	</div>
		
</div>
<div class="clear"></div>
