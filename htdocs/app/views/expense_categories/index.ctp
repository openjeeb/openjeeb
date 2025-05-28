<div class="col-xs-16 col-md-6 rounded box">
    <h2>ثبت نوع هزینه</h2>
    <div id="new" class="expenseCategories form">
    <?php echo $this->Form->create('ExpenseCategory'); ?>
    <fieldset>
    	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name',array('label'=>'عنوان'));
	?>
        <span>با مشخص نمودن انواع هزینه‌ها میتوانید هنگام افزودن یک هزینه نوع آن را مشخص کنید. مشخص نمودن نوع هزینه به شما این امکان را میدهد تا گزارشهای مختلفی از هزینه‌های خود بگیرید. یک نوع هزینه میتواند هزینه حمل ونقل، اتومبیل، آموزش و ... باشد. </span>
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>
</div>

<div class="col-xs-16 col-md-9" id="ExpenseCategoryPieChart" style="direction: ltr;">
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">انواع هزینه</h2>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array('', 'عنوان','وضعیت','عملیات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php foreach ($expenseCategories as $expenseCategory):	?>
        <tr class="topcat">
                <td style="width:55px;">
                    <?php echo $this->Html->link('<i class="fa fa-sort-up"></i>', array('action' => 'sort', 'up'=>$expenseCategory['ExpenseCategory']['sort'],'#'=>'#dataTable'),array('escape'=>false,'class'=>'up')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-sort-down"></i>', array('action' => 'sort', 'down'=>$expenseCategory['ExpenseCategory']['sort'],'#'=>'#dataTable'),array('escape'=>false,'class'=>'down')); ?>
                </td>
                <td><b><?php echo $expenseCategory['ExpenseCategory']['name']; ?></b></td>
                
                <td style="width:80px;">
                    <?php if($expenseCategory['ExpenseCategory']['delete']=='yes'): ?>
                        <?php echo $this->Html->link(
                                '<i class="'.(($expenseCategory['ExpenseCategory']['status']=='inactive')? 'fa fa-lightbulb-o' : 'fa fa-lightbulb-o').'"></i>'.
                                '&nbsp;'.__($expenseCategory['ExpenseCategory']['status'],true),
                                array('action' => 'toggleshow', $expenseCategory['ExpenseCategory']['id']),array('escape'=>false,'class'=>'showinlist')
                                ); ?>
                    <?php else: ?>
                        <?php echo '<i class="fa fa-lightbulb-o"></i>'.'&nbsp;'.__($expenseCategory['ExpenseCategory']['status'],true); ?>
                    <?php endif; ?>
                </td>
		<td>
                    <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $expenseCategory['ExpenseCategory']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $expenseCategory['ExpenseCategory']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $expenseCategory['ExpenseCategory']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-plus"></i>',array('controller'=>'expenseSubCategories','action'=>'add',$expenseCategory['ExpenseCategory']['id']),array('escape'=>false,'class'=>'add')); ?>
		</td>
	</tr>
        <?php foreach ($expenseCategory['ExpenseSubCategory'] as $expenseSubCategory):	?>
            <tr>
                    <td></td>
                    <td colspan="2" style="padding-right: 30px;"><?php echo $this->Html->image('sub.png',array('alt'=>'-')); ?><?php echo $expenseSubCategory['name']; ?></td>
                    <td>
                        <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('controller'=>'expenseSubCategories','action' => 'edit', $expenseSubCategory['id']),array('escape'=>false)); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('controller'=>'expenseSubCategories','action' => 'delete', $expenseSubCategory['id']),array('escape'=>false,'alt'=>'حذف'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $expenseSubCategory['id'])); ?>&nbsp;
                    </td>
            </tr>
        <?php endforeach; ?>
<?php endforeach; ?>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
</div>
<div class="clear"></div>
<?php echo $this->Chart->pie('ExpenseCategoryPieChart','تقسیم هزینه',$pieData); ?>

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
