<div class="col-xs-16 col-md-16 box">
    <h2>افزودن خبرنامه</h2>
    <div id="new" class="newsletters form">
    <?php echo $this->Form->create('Newsletter'); ?>
    <fieldset>
    	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('subject',array('label'=>array('text'=>'عنوان خبرنامه','style'=>'width:120px;')));
		echo $this->Form->input('query',array('label'=>array('text'=>'Query','style'=>'width:120px;'),'style'=>'direction:ltr;','value'=>'SELECT * FROM users LIMIT 10'));
		echo $this->Form->input('head_title',array('label'=>array('text'=>'عنوان سرمقاله','style'=>'width:120px;')));
		echo $this->Form->input('head_text',array('label'=>array('text'=>'متن سرمقاله','style'=>'width:120px;')));
		echo $this->Form->input('head_image',array('label'=>array('text'=>'تصویر سرمقاله','style'=>'width:120px;'),'value'=>'pie_chart.png'));
		echo $this->Form->input('title1',array('label'=>array('text'=>'عنوان ۱','style'=>'width:120px;')));
		echo $this->Form->input('content1',array('label'=>array('text'=>'متن ۱','style'=>'width:120px;')));
		echo $this->Form->input('title2',array('label'=>array('text'=>'عنوان ۲','style'=>'width:120px;')));
		echo $this->Form->input('content2',array('label'=>array('text'=>'متن ۲','style'=>'width:120px;')));
		echo $this->Form->input('title3',array('label'=>array('text'=>'عنوان ۳','style'=>'width:120px;')));
		echo $this->Form->input('content3',array('label'=>array('text'=>'متن ۳','style'=>'width:120px;')));
		echo $this->Form->input('sent',array('type'=>'select','options'=>array('no'=>'خیر','yes'=>'بلی'),'empty'=>false,'label'=>array('text'=>'ارسال شده؟','style'=>'width:120px;')));
	?>
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>

</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">خبرنامه‌ها</h2>

    
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array(
        $paginator->sort('id','id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('عنوان','subject',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('ارسال شده؟','sent',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('ایجاد','created',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('ویرایش','modified',array('url'=>array('#'=>'#dataTable'))),
        'عملیات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php
	$i = 0;
	foreach ($newsletters as $newsletter):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $newsletter['Newsletter']['id']; ?>&nbsp;</td>
		<td><?php echo $newsletter['Newsletter']['subject']; ?>&nbsp;</td>
		<td><?php __($newsletter['Newsletter']['sent']); ?>&nbsp;</td>
		<td><?php echo $newsletter['Newsletter']['created']; ?>&nbsp;</td>
		<td><?php echo $newsletter['Newsletter']['modified']; ?>&nbsp;</td>
		<td style="width: 120px">
                    <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $newsletter['Newsletter']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $newsletter['Newsletter']['id']),array('escape'=>false,'class'=>'delete','alt'=>'حذف'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $newsletter['Newsletter']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-desktop"></i>', array('action' => 'view', $newsletter['Newsletter']['id']),array('escape'=>false,'class'=>'view','alt'=>'نمایش','target'=>'_blank')); ?>
		</td>
	</tr>
<?php endforeach; ?>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
    
          
	<?php echo $this->element('pagination/bottom'); ?>
		
</div>
<div class="clear"></div>
