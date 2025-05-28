<div class="col-xs-16 col-md-16 box" style="width: 920px;">
    <h2><?php echo $this->Html->link('ایجاد گروه کاربری', '#',array('id'=>'toggle-new','class'=>'hidden')); ?></h2>
    <div id="new" class="userGroups form">
    <?php echo $this->Form->create('UserGroup'); ?>
    <fieldset>
    	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name',array('label'=>'نام'));
	?>
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>

</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">گروه‌های کاربری</h2>

    
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array(
        $paginator->sort('ردیف','id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('نام','name',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('ایجاد','created',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('ویرایش','modified',array('url'=>array('#'=>'#dataTable')))));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php
	$i = 0;
	foreach ($userGroups as $userGroup):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $userGroup['UserGroup']['id']; ?>&nbsp;</td>
		<td><?php echo $userGroup['UserGroup']['name']; ?>&nbsp;</td>
		<td><?php echo $userGroup['UserGroup']['created']; ?>&nbsp;</td>
		<td><?php echo $userGroup['UserGroup']['modified']; ?>&nbsp;</td>
	</tr>
	<tr class="actions">
		<td></td>
                <td colspan="3">
			<?php echo $this->Html->link('نمایش', array('action' => 'view', $userGroup['UserGroup']['id'])); ?>
			<?php echo $this->Html->link('ویرایش', array('action' => 'edit', $userGroup['UserGroup']['id'])); ?>
			<?php echo $this->Html->link('پاک کردن', array('action' => 'delete', $userGroup['UserGroup']['id']), null, sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $userGroup['UserGroup']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
    
          
	<?php echo $this->element('pagination/bottom'); ?>
		
</div>
<div class="clear"></div>
