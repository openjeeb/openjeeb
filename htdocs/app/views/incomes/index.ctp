<?php
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
?>
<div class="col-xs-16 col-md-6 rounded box">
    <h2>ثبت درآمد جدید</h2>
    <div class="users form">
        <?php echo $this->Form->create('Income');?>
        <fieldset>
            <?php echo $this->Form->label('Transaction.amount','مبلغ (ریال)'); ?><br/>
            <?php echo $this->Form->error('Transaction.amount'); ?>
            <?php echo $this->Form->text('Transaction.amount',array('maxlength'=>15,'style'=>'direction:ltr;')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransactionAmountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Transaction.date','تاریخ'); ?><br/>
            <?php echo $this->Form->error('Transaction.date'); ?>
            <?php echo $this->Form->text('Transaction.date',array('class'=>'datepicker','value'=>$persianDate->pdate('Y/m/d'))); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransactionDateTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Income.income_type_id','نوع درآمد'); ?>
            <?php echo $this->Form->select('Income.income_type_id',$incomeTypes); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'IncomeTypeTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Transaction.account_id','واریز به'); ?>&nbsp;&nbsp;&nbsp;
            <?php echo $this->Form->select('Transaction.account_id',$accounts,null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransactionAccountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br />
            <?php echo $this->Form->label('AccountIdBalance','موجودی:'); ?>&nbsp;
            <?php echo $this->Html->tag('span','<span></span>', array('id'=>'AccountBalance')); ?>
            <br/>
            
            <?php echo $this->Form->label('Income.individual_id','شخص'); ?>&nbsp;&nbsp;&nbsp;
            <?php echo $this->Form->select('Income.individual_id',$individuals,null,array('empty'=>true)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'IncomeIndividualtTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <div class="clear">&nbsp;</div>
            
            <div class="input text">
                <label for="ExpenseExpenseCategoryId" style="width:80px;">برچسب: </label>
                <div>
                    <?php echo $this->Form->text('TransactionTag.tag_id',array('value'=>'','autocomplete'=>'off', 'style'=>'width:210px')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'TagCategoryTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                    <div id="groupListHolder"></div>
                </div>
            </div>
            
            <div class="clear">&nbsp;</div>
            
            <?php echo $this->Form->label('Income.description','توضیحات'); ?><br/>
            <?php echo $this->Form->error('Income.description'); ?>
            <?php echo $this->Form->textarea('Income.description',array('style'=>'width:98%;')); ?><br/>
            
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت انواع درآمد',array('controller'=>'incomeTypes','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت حساب‌ها',array('controller'=>'accounts','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت اشخاص',array('controller'=>'individuals','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت برچسب‌ها',array('controller'=>'tags','action'=>'index'));?></span>
        </fieldset>
        <?php echo $this->Form->end('ثبت');?>
    </div>
</div>
<div class="col-xs-16 col-md-10">
    <div id="IncomePieChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16 ">
    <div class="box">
        <h2><?php echo $this->Html->link('جستجو', '#',array('id'=>'toggle-filter','class'=>'hidden')); ?></h2>
        <div id="filter" class="form">
            <?php echo $this->Form->create('Income'); ?>
            <fieldset>
                <?php
                echo $this->Form->input('Income.search',array('type'=>'hidden','value'=>true));
                echo $this->Form->input('Income.income_type_id',array('label'=>'نوع درآمد','type'=>'select','id'=>'FilterIncomeTypeId','empty'=>true,'options'=>$incomeTypes));
                echo $this->Form->input('Transaction.account_id',array('label'=>'حساب','type'=>'select','empty'=>true));
                echo $this->Form->input('Income.individual_id',array('label'=>'شخص','type'=>'select','empty'=>true));
                echo $this->Form->input('Transaction.amount',array('label'=>'مبلغ','type'=>'text','maxlength'=>15,'id'=>'TransactionAmountSearch','style'=>'direction:ltr;'));
                ?>
                <div class="input text">
                    <label>از تاریخ</label>
                    <div style='float:right;'><?php echo $this->Form->text('Income.start_date',array('class'=>'datepicker','value'=>'')); ?></div>
                    <?php echo $this->element('filldate', array( 'start_date' => '#IncomeStartDate', 'end_date' => '#IncomeEndDate', 'oneline'=>true )); ?>
                </div>
                <?php echo $this->Form->input('Income.end_date',array('label'=>'تا تاریخ','class'=>'datepicker','value'=>'')); ?>
                <?php echo $this->Form->input('Income.description_search',array('label'=>'توضیحات','type'=>'text','value'=>'')); ?>
                <div class="clear">&nbsp;</div>

                <div class="input text">
                    <label for="IncomeIncomeCategoryId">برچسب: </label>
                    <div>
                        <?php echo $this->Form->text('TransactionTagSearch.tag_id',array('value'=>'','autocomplete'=>'off', 'id'=> 'TransactionTagIdSearch')); ?>
                        <div id="groupListHolderSearch"></div>
                    </div>
                </div>

                <div class="clear">&nbsp;</div>
            </fieldset>
            <?php echo $this->Form->end('جستجو');?>
        </div>
    </div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 class="col-xs-16 col-md-3" id="page-heading">لیست درآمدها  <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array(
        $paginator->sort('نوع درآمد','Income.income_type_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('مبلغ (ریال)','Transaction.amount',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('تاریخ','Transaction.date',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('حساب','Transaction.account_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('شخص','Income.individual_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('توضیحات','Income.description',array('url'=>array('#'=>'#dataTable'))),
        'عملیات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php foreach ($incomes as $income): ?>
	<tr>
		<td><?php echo $income['IncomeType']['name']; if(!is_null($income['IncomeSubType']['name'])) echo ' >> '.$income['IncomeSubType']['name'];?></td>
                <td style="color:green;direction:ltr;">+<?php echo number_format($income['Transaction']['amount']); ?></td>
                <td><?php echo $income['Transaction']['date']; ?>&nbsp;</td>
                <td><?php echo $income['Account']['name']; ?>&nbsp;</td>
                <td><?php echo $income['Individual']['name']; ?>&nbsp;</td>
                <td><?php echo str_replace('\n', "<br />", $income['Income']['description']); ?>&nbsp;</td>
		<td style="width: 80px">
                    <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $income['Income']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $income['Income']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $income['Income']['id'])); ?>&nbsp;
                </td>
	</tr>
<?php endforeach; ?>
        <tr>
            <td><b>جمع کل</b></td>
            <td colspan="6" style="color:green;direction:ltr;"><b>+<?php echo number_format($sum); ?></b></td>
        </tr>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
    
          
	<?php echo $this->element('pagination/bottom'); ?>
		
</div>
<div class="clear"></div>

<?php echo $this->Chart->pie('IncomePieChart','تقسیم درآمد',$pieData,580,500); ?>
<script type="text/javascript">
balances = <?php echo json_encode($accountsbalance); ?>;
$(function(){        
    jeeb.tip($('#TagCategoryTip'),'200','شما میتوانید با وارد کردن برچسبی که مورد نظرتان است و یا انتخاب آن از لیست این مورد را برچسب دار کنید. شما میتوانید بیش از یک برچسب به یک مورد متصل کنید.');
    //number format
    jeeb.FormatPrice($('#TransactionAmount'));
    jeeb.FormatPrice($('#TransactionAmountSearch'));
    //bind the sub categories
    jeeb.bindIncomeSubTypes($('#IncomeIncomeTypeId'),<?php echo $this->Javascript->object($incomeTypesData); ?>,'','IncomeIncomeSubTypeId');
    jeeb.bindIncomeSubTypes($('#FilterIncomeTypeId'),<?php echo $this->Javascript->object($incomeTypesData); ?>,'','FilterIncomeSubTypeId');
    //tips
    jeeb.tip($('#excelExport'),'70','خروجی اکسل');
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
    jeeb.tip($('#TransactionAmountTip'),'104','مبلغ درآمد را وارد کنید.');    
    jeeb.tip($('#TransactionAmountSearch'),'104','مبلغ درآمد را وارد کنید.');    
    jeeb.tip($('#TransactionDateTip'),'105','تاریخ درآمد را وارد کنید.');    
    jeeb.tip($('#IncomeTypeTip'),'200','نوع درآمد را انتخاب کنید.<br/> از طریق صفحه مدیریت انواع درآمد میتوانید این انواع را مدیریت کنید.');    
    jeeb.tip($('#TransactionAccountTip'),'200','حسابی را که این درآمد به آن واریز شده است انتخاب کنید.<br/>در صورتی که این مبلغ را به حسابی واریز نکرده‌اید و نقد دریافت کرده‌اید جیب را انتخاب کنید.');
    jeeb.tip($('#IncomeIndividualtTip'),'200','میتوانید یک شخص را بعنوان پرداخت کننده این درآمد انتخاب کنید.');
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
</script>
