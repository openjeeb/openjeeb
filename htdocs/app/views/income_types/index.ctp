<div class="col-xs-16 col-md-6 rounded box">
    <h2>نوع درآمد جدید</h2>
    <div id="new" class="incomeTypes form">
    <?php echo $this->Form->create('IncomeType'); ?>
    <fieldset>
    	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name',array('label'=>'عنوان درآمد'));
	?>
        <span>با مشخص نمودن انواع درآمدها میتوانید هنگام افزودن یک درآمد نوع آن را مشخص کنید. مشخص نمودن نوع درآمد به شما این امکان را میدهد تا گزارشهای مختلفی از درآمدهای خود بگیرید. یک نوع درآمد میتواند حقوق، سود بانکی، فروش، سود سرمایه گذاری، هدیه و ... باشد.</span>
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>
</div>

<div class="col-xs-16 col-md-9" id="incomeTypePieChart" style="direction: ltr;">
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">انواع درآمد</h2>

    
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array('','عنوان درآمد','وضعیت','عملیات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php foreach ($incomeTypes as $incomeType): ?>
	<tr class="topcat">
            <td style="width:55px;">
                <?php echo $this->Html->link('<i class="fa fa-sort-up"></i>', array('action' => 'sort', 'up'=>$incomeType['IncomeType']['sort'],'#'=>'#dataTable'),array('escape'=>false,'class'=>'up')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php echo $this->Html->link('<i class="fa fa-sort-down"></i>', array('action' => 'sort', 'down'=>$incomeType['IncomeType']['sort'],'#'=>'#dataTable'),array('escape'=>false,'class'=>'down')); ?>
            </td>
            <td><b><?php echo $incomeType['IncomeType']['name']; ?></b></td>
            <td style="width:80px;">
                <?php if($incomeType['IncomeType']['delete']=='yes'): ?>
                    <?php echo $this->Html->link(
                            $this->Html->image(
                                ($incomeType['IncomeType']['status']=='inactive')? 'off.png' : 'on.png',
                                array('alt'=>($incomeType['IncomeType']['status']=='inactive')? 'عدم نمایش در لیست' : 'نمایش در لیست')).'&nbsp;'.__($incomeType['IncomeType']['status'],true),
                            array('action' => 'toggleshow', $incomeType['IncomeType']['id']),array('escape'=>false,'class'=>'showinlist')
                            ); ?>
                <?php else: ?>
                    <?php echo '<i class="fa fa-lightbulb-o"></i>'.'&nbsp;'.__($incomeType['IncomeType']['status'],true); ?>
                <?php endif; ?>
            </td>
            <td>
                <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $incomeType['IncomeType']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $incomeType['IncomeType']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $incomeType['IncomeType']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php echo $this->Html->link('<i class="fa fa-plus"></i>',array('controller'=>'incomeSubTypes','action'=>'add', $incomeType['IncomeType']['id']),array('escape'=>false,'class'=>'add')); ?>&nbsp;
            </td>
	</tr>
        <?php foreach ($incomeType['IncomeSubType'] as $incomeSubType):	?>
            <tr>
                <td>&nbsp;</td>
                <td colspan="2" style="padding-right: 30px;"><?php echo $this->Html->image('sub.png',array('alt'=>'-')); ?><?php echo $incomeSubType['name']; ?></td>
                <td>
                    <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('controller'=>'incomeSubTypes','action' => 'edit', $incomeSubType['id']),array('escape'=>false)); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('controller'=>'incomeSubTypes','action' => 'delete', $incomeSubType['id']),array('escape'=>false,'alt'=>'حذف'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $incomeSubType['id'])); ?>&nbsp;
                </td>
            </tr>
        <?php endforeach; ?>
<?php endforeach; ?>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
</div>
<div class="clear"></div>
<?php echo $this->Chart->pie('incomeTypePieChart','تقسیم درآمد',$pieData,580,500); ?>

<script type="text/javascript">
//<![CDATA[
$(function(){
    //tips
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
    jeeb.tip($('.add'),'100','اضافه نمودن زیرشاخه');    
});
//]]>
</script>