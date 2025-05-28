<?php
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
?>

<div class="col-xs-16 col-md-6 box rounded">
    <h2>ثبت هزینه جدید</h2>
    <div class="expenses form">
        <?php echo $this->Form->create('Expense'); ?>
        <fieldset>
            <?php echo $this->Form->label('Transaction.amount','مبلغ (ریال)'); ?><br/>
            <?php echo $this->Form->error('Transaction.amount'); ?>
            <?php echo $this->Form->text('Transaction.amount',array('maxlength'=>15,'style'=>'direction:ltr;')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransactionAmountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>

            <?php echo $this->Form->label('Transaction.date','تاریخ'); ?><br/>
            <?php echo $this->Form->error('Transaction.date'); ?>
            <?php echo $this->Form->text('Transaction.date',array('class'=>'datepicker pdate','value'=>$persianDate->pdate('Y/m/d'))); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransactionDateTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>

            <?php echo $this->Form->label('Expense.expense_category_id','نوع هزینه'); ?>
            <?php echo $this->Form->select('Expense.expense_category_id',$expenseCategories); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'ExpenseCategoryTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>

            <div class="clear">&nbsp;</div>

            <div class="input text">
                <label for="ExpenseExpenseCategoryId" style="width:60px;">برچسب: </label>
                <div>
                    <?php echo $this->Form->text('TransactionTag.tag_id',array('value'=>'','autocomplete'=>'off', 'style'=>'width:210px')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'TagCategoryTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                    <div id="groupListHolder"></div>
                </div>
            </div>

            <div class="clear">&nbsp;</div>

            <?php echo $this->Form->label('Transaction.account_id','برداشت از'); ?>
            <?php echo $this->Form->select('Transaction.account_id',$accounts,null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransactionAccountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br />

            <?php echo $this->Form->label('AccountIdBalance','موجودی:'); ?>&nbsp;
            <?php echo $this->Html->tag('span','<span></span>', array('id'=>'AccountBalance')); ?>
            <br/>

            <?php echo $this->Form->label('Expense.individual_id','شخص'); ?>
            <?php echo $this->Form->select('Expense.individual_id',$individuals,null,array('empty'=>true)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'ExpenseIndividualTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>

            <?php echo $this->Form->label('Expense.description','توضیحات'); ?><br/>
            <?php echo $this->Form->error('Expense.description'); ?>
            <?php echo $this->Form->textArea('Expense.description',array('style'=>'width:97%;')); ?><br/>

            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('ثبت هزینه ها بصورت گروهی',array('controller'=>'expenses','action'=>'batch'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('تعریف گروه هزینه',array('controller'=>'expenseCategories','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت حساب‌ها',array('controller'=>'accounts','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت اشخاص',array('controller'=>'individuals','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت برچسب‌ها',array('controller'=>'tags','action'=>'index'));?></span>
        </fieldset>
        <?php echo $this->Form->end('ثبت');?>
    </div>
</div>

<div class="col-xs-16 col-md-10" >
    <div align="center" id="ExpensePieChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16 ">
    <div class=" box">
        <h2><?php echo $this->Html->link('جستجو', '#',array('id'=>'toggle-filter','class'=>'hidden')); ?></h2>
        <div id="filter" class="form">
            <?php echo $this->Form->create('Expense'); ?>
            <fieldset>
                <?php
                echo $this->Form->input('Expense.search',array('type'=>'hidden','value'=>true));
                echo $this->Form->input('Expense.expense_category_id',array('label'=>'نوع هزینه','type'=>'select','id'=>'FilterExpenseCategoryId','empty'=>true));
                ?>
                <div class="clear">&nbsp;</div>

                <div class="input text">
                    <label for="ExpenseExpenseCategoryId">برچسب: </label>
                    <div>
                        <?php echo $this->Form->text('TransactionTagSearch.tag_id',array('value'=>'','autocomplete'=>'off', 'style'=>'width:210px', 'id'=> 'TransactionTagIdSearch')); ?>
                        <div id="groupListHolderSearch"></div>
                    </div>
                </div>

                <div class="clear">&nbsp;</div>
                <?php
                echo $this->Form->input('Transaction.account_id',array('label'=>'حساب','type'=>'select','empty'=>true));
                echo $this->Form->input('Expense.individual_id',array('label'=>'شخص','type'=>'select','empty'=>true));
                echo $this->Form->input('Transaction.amount',array('label'=>'مبلغ','type'=>'text','maxlength'=>15,'id'=>'TransactionAmountSearch','style'=>'direction:ltr;'));
                ?>
                <div class="input text">
                    <label>از تاریخ</label>
                    <div style='float:right;'><?php echo $this->Form->text('Expense.start_date',array('class'=>'datepicker','value'=>'')); ?></div>
                    <?php echo $this->element('filldate', array( 'start_date' => '#ExpenseStartDate', 'end_date' => '#ExpenseEndDate', 'oneline'=>true )); ?>
                </div>
                <?php
                echo $this->Form->input('Expense.end_date',array('label'=>'تا تاریخ','class'=>'datepicker','value'=>''));
                echo $this->Form->input('Expense.description_search',array('label'=>'توضیحات','type'=>'text','value'=>''));
                ?>
            </fieldset>
            <?php echo $this->Form->end('جستجو');?>
        </div>

    </div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 class="col-xs-12 col-md-3" id="page-heading">لیست هزینه‌ها  <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-12 col-md-3 col-md-offset-10" style="margin-top:15px;">
        <?php echo $this->element('pagination/top'); ?>
    </div>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array(
        $paginator->sort('نوع هزینه','Expense.expense_category_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('مبلغ (ریال)','Transaction.amount',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('تاریخ','Transaction.date',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('حساب','Transaction.account_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('شخص','Expense.individual_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('توضیحات','Expense.description',array('url'=>array('#'=>'#dataTable'))),
        'عملیات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php foreach ($expenses as $expense):	?>
	<tr>
        <td><?php echo $expense['ExpenseCategory']['name']; if(!is_null($expense['ExpenseSubCategory']['name'])) echo ' >> '.$expense['ExpenseSubCategory']['name'];?></td>
        <td style="color:#C62121;direction:ltr;">-<?php echo number_format($expense['Transaction']['amount']); ?></td>
		<td><?php echo $expense['Transaction']['date']; ?></td>
		<td><?php echo $expense['Account']['name']; ?></td>
		<td><?php echo $expense['Individual']['name']; ?></td>
        <td><?php echo str_replace('\n', "<br />", $expense['Expense']['description']); ?></td>
		<td style="width: 80px">
            <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $expense['Expense']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $expense['Expense']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $expense['Expense']['id'])); ?>&nbsp;
		</td>
	</tr>
<?php endforeach; ?>
        <tr>
            <td><b>جمع کل</b></td>
            <td colspan="6" style="color:#C62121;direction:ltr;"><b>-<?php echo number_format($sum); ?></b></td>
        </tr>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>
    </table></div>
    
	<?php echo $this->element('pagination/bottom'); ?>
		
</div>
<div class="clear"></div>

<?php echo $this->Chart->pie('ExpensePieChart','تقسیم هزینه',$pieData); ?>
<script type="text/javascript">
//<![CDATA[
var balances = <?php echo json_encode($accountsbalance) ?>;
$(function(){
    jeeb.tip($('#TagCategoryTip'),'200','شما میتوانید با وارد کردن برچسبی که مورد نظرتان است و یا انتخاب آن از لیست این مورد را برچسب دار کنید. شما میتوانید بیش از یک برچسب به یک مورد متصل کنید.');
    //bind the sub categories
    jeeb.bindExpenseSubCategories($('#ExpenseExpenseCategoryId'),<?php echo $this->Javascript->object($expenseCategoriesData); ?>,'','ExpenseExpenseSubCategoryId');
    jeeb.bindExpenseSubCategories($('#FilterExpenseCategoryId'),<?php echo $this->Javascript->object($expenseCategoriesData); ?>,'','FilterExpenseSubCategoryId');
    //number format
    jeeb.FormatPrice($('#TransactionAmount'));
    jeeb.FormatPrice($('#TransactionAmountSearch'));
    //tips
    jeeb.tip($('#excelExport'),'70','خروجی اکسل');
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
    jeeb.tip($('#TransactionAmountTip,#TransactionAmountSearch'),'104','مبلغ هزینه را وارد کنید.');    
    jeeb.tip($('#TransactionDateTip'),'105','تاریخ هزینه را وارد کنید.');    
    jeeb.tip($('#ExpenseCategoryTip'),'200','نوع هزینه را انتخاب کنید.<br/> از طریق صفحه مدیریت انواع هزینه میتوانید این انواع را مدیریت کنید.');    
    jeeb.tip($('#TransactionAccountTip'),'200','حسابی را که برای این هزینه از آن برداشت کرده‌اید انتخاب کنید.<br/>در صورتی که این مبلغ از حسابی برداشت نکرده‌اید و نقد پرداخت کرده‌اید جیب را انتخاب کنید.');
    jeeb.tip($('#ExpenseIndividualTip'),'200','میتوانید یک شخص را بعنوان دریافت کننده این هزینه انتخاب کنید.');
    jeeb.accountBalance(balances, $('#TransactionAccountId'), $('#AccountBalance > span'));
    $('#TransactionAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalance > span'); });
    
    categories = <?php echo json_encode(empty($tags)? array() : $tags); ?>;
    report_catlist = <?php echo json_encode(empty($report_catlist)? array() : $report_catlist); ?>;
    floatingList = new FloatingList({
        input: '#TransactionTagTagId', 
        listholder: '#groupListHolder', 
        data: categories,
        allowNew: true,
        empty: false
    });
    floatingList2 = new FloatingList({
        holderId: 'CategoryListHolderSearch',
        listId:'CategoryListSearch',
        input: '#TransactionTagIdSearch', 
        listholder: '#groupListHolderSearch',
        preload: report_catlist,
        data: categories
    });
});
//]]>
</script>