<div class="col-xs-16 col-md-16 box" style="width: 920px;">
    <h2>New Income Sub Type</h2>
    <div id="new" class="incomeSubTypes form">
    <?php echo $this->Form->create('IncomeSubType'); ?>
    <fieldset>
    	<?php
		echo $this->Form->input('id',array('label'=>'id'));
		echo $this->Form->input('name',array('label'=>'name'));
		echo $this->Form->input('income_type_id',array('label'=>'income_type_id'));
		echo $this->Form->input('user_id',array('label'=>'user_id'));
	?>
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>

</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">Income Sub Types</h2>

    
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array(
        $paginator->sort('id','id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('name','name',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('income_type_id','income_type_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('user_id','user_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('created','created',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('modified','modified',array('url'=>array('#'=>'#dataTable'))),
        'Actions'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php
	$i = 0;
	foreach ($incomeSubTypes as $incomeSubType):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $incomeSubType['IncomeSubType']['id']; ?>&nbsp;</td>
		<td><?php echo $incomeSubType['IncomeSubType']['name']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($incomeSubType['IncomeType']['name'], array('controller' => 'income_types', 'action' => 'view', $incomeSubType['IncomeType']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($incomeSubType['User']['email'], array('controller' => 'users', 'action' => 'view', $incomeSubType['User']['id'])); ?>
		</td>
		<td><?php echo $incomeSubType['IncomeSubType']['created']; ?>&nbsp;</td>
		<td><?php echo $incomeSubType['IncomeSubType']['modified']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link('نمایش', array('action' => 'view', $incomeSubType['IncomeSubType']['id'])); ?>
		
			<?php echo $this->Html->link('ویرایش', array('action' => 'edit', $incomeSubType['IncomeSubType']['id'])); ?>
		
			<?php echo $this->Html->link('پاک کردن', array('action' => 'delete', $incomeSubType['IncomeSubType']['id']), null, sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $incomeSubType['IncomeSubType']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
    
          
	<?php echo $this->element('pagination/bottom'); ?>
		
</div>
<div class="clear"></div>
