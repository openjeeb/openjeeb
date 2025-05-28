<?php
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
?>
<div class="col-xs-16 col-md-6 rounded box">
    <h2>ثبت وام</h2>
    <div class="loans form">
    <?php echo $this->Form->create('Loan'); ?>
    <fieldset>
            <?php echo $this->Form->label('Loan.name','عنوان وام'); ?><br/>
            <?php echo $this->Form->error('Loan.name'); ?>
            <?php echo $this->Form->text('Loan.name',array('maxlength'=>75)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'LoanNameTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Loan.amount','مبلغ'); ?><br/>
            <?php echo $this->Form->error('Loan.amount'); ?>
            <?php echo $this->Form->text('Loan.amount',array('maxlength'=>15,'style'=>'direction:ltr;')); ?>
            <br/>
            
            <?php echo $this->Form->label('Loan.bank_id','موسسه'); ?><br/>
            <?php echo $this->Form->error('Loan.bank_id'); ?>
            <?php echo $this->Form->select('Loan.bank_id',$banks,null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'LoanBankIdTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->error('Loan.add'); ?>
            <?php echo $this->Form->checkbox('Loan.add',array('value'=>'yes','checked'=>'checked','style'=>'width:auto;')); ?>
            <?php echo $this->Form->label('Loan.add','اضافه نمودن درآمد متناظر'); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'LoanAddTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Transaction.account_id','واریز به حساب'); ?>
            <?php echo $this->Form->error('Transaction.account_id'); ?>
            <?php echo $this->Form->select('Transaction.account_id',array($accounts),null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransactionAccountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br />
            <?php echo $this->Html->tag('span','موجودی: <span></span>', array('id'=>'AccountBalanceTo')); ?>
            <br/>
            
            <?php echo $this->Form->label('Loan.start','تاریخ شروع وام'); ?>
            <?php echo $this->Form->label('Loan.start_month','ماه'); ?>
            <?php echo $this->Form->select('Loan.start_month',array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12),null,array('empty'=>false)); ?>
            <?php for($i=1370;$i<=$persianDate->pdate('Y')+1;$i++){$years[$i]=$i;} ?>
            <?php echo $this->Form->label('Loan.start_year','سال'); ?>
            <?php echo $this->Form->select('Loan.start_year',$years,end($years),array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'LoanStartDateTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->error('Loan.installments_remaining'); ?>
            <?php echo $this->Form->error('Loan.installments_payed'); ?>
            <?php echo $this->Form->label('Loan.installments_payed','تعداد اقساط پرداخت شده'); ?>
            <?php echo $this->Form->text('Loan.installments_payed',array('style'=>'width:20%;','maxlength'=>3)); ?>
            <?php echo $this->Form->label('Loan.installments_remaining','اقساط باقیمانده'); ?>
            <?php echo $this->Form->text('Loan.installments_remaining',array('style'=>'width:20%;','maxlength'=>3)); ?>
            <br/>
            
            <span>سر رسید اقساط در روز </span>
            <?php echo $this->Form->select('Loan.installments_due_day',array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20,21=>21,22=>22,23=>23,24=>24,25=>25,26=>26,27=>27,28=>28,29=>29),1,array('empty'=>false)); ?>
            <span> هر </span>
            <?php echo $this->Form->select('Loan.installments_period',array(
                    '1'=>'ماه',
                    '2'=>'دو ماه',
                    '3'=>'سه ماه',
                    '4'=>'چهار ماه',
                    '5'=>'پنج ماه',
                    '6'=>'شش ماه',
                    '12'=>'سال',
                ),
                null,array('empty'=>false)); ?>
            <br/>
            
            <?php echo $this->Form->label('Loan.installments_amount','مبلغ هر قسط (ریال)'); ?><br/>
            <?php echo $this->Form->error('Loan.installments_amount'); ?>
            <?php echo $this->Form->text('Loan.installments_amount',array('maxlength'=>15, 'style'=>'direction:ltr;')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'LoanInstallmentsAmountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Loan.notify','یادآور'); ?>
            <?php echo $this->Form->error('Loan.notify'); ?>
            <?php echo $this->Form->select('Loan.notify',array('yes'=>'فعال','no'=>'غیر فعال'),null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'LoanNotifyTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <div class="clear">&nbsp;</div>

            <div class="input text">
                <label for="LoanLoanCategoryId" style="width:60px;">برچسب: </label>
                <div>
                    <?php echo $this->Form->text('LoanTag.tag_id',array('value'=>'','autocomplete'=>'off', 'style'=>'width:210px')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'TagCategoryTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                    <div id="groupListHolder"></div>
                </div>
            </div>

            <div class="clear">&nbsp;</div>
            
            <?php echo $this->Form->label('Loan.description','توضیحات'); ?><br/>
            <?php echo $this->Form->error('Loan.description'); ?>
            <?php echo $this->Form->textArea('Loan.description',array('style'=>'width:98%;')); ?><br/>
            
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت برچسب‌ها',array('controller'=>'tags','action'=>'index'));?></span>
            
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>

</div>
<div class="col-xs-16 col-md-9">
    <div id="LoanPieChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 class="col-xs-16 col-md-3" id="page-heading">لیست وام‌ها <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?>
</div>
<div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
<?php $tableHeaders = $html->tableHeaders(array(
    $paginator->sort('عنوان وام','name',array('url'=>array('#'=>'#dataTable'))),
    $paginator->sort('مبلغ','amount',array('url'=>array('#'=>'#dataTable'))),
    $paginator->sort('توضیحات','description',array('url'=>array('#'=>'#dataTable'))),
    $paginator->sort('وضعیت','status',array('url'=>array('#'=>'#dataTable'))),
    $paginator->sort('اطلاع رسانی','notify',array('url'=>array('#'=>'#dataTable'))),
    $paginator->sort('بانک','bank_id',array('url'=>array('#'=>'#dataTable'))),
    'تاریخ ایجاد',
    'عملیات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php foreach ($loans as $loan): ?>
<tr>
    <td><?php echo $loan['Loan']['name']; ?>&nbsp;</td>
            <td><?php echo number_format($loan['Loan']['amount']); ?> ریال&nbsp;</td>
    <td><?php echo str_replace('\n', "<br />", $loan['Loan']['description']); ?>&nbsp;</td>
    <td><?php if($loan['Loan']['status']=='due'){ echo $this->Html->image('due.png',array('class'=>'due','alt'=>__($loan['Loan']['status'],true))); }else{ echo $this->Html->image('done.png',array('class'=>'done','alt'=>__($loan['Loan']['status'],true))); } ?>&nbsp;<?php __($loan['Loan']['status']) ?></td>
    <td><?php __($loan['Loan']['notify']); ?>&nbsp;</td>
    <td><?php echo $loan['Bank']['name']; ?></td>
    <td><?php echo $loan['Loan']['created']; ?></td>
    <td style="width: 150px">
        <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $loan['Loan']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $loan['Loan']['id']),array('escape'=>false,'class'=>'delete','alt'=>'حذف'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟ در صورت پاک کردن این وام تمامی تراکنشهای مربوط به اقساط آن هم حذف خواهند شد.', $loan['Loan']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Html->link('<i class="fa fa-desktop"></i>', array('action' => 'view', $loan['Loan']['id']),array('escape'=>false,'class'=>'view','alt'=>'نمایش')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo $this->Html->link('<i class="fa fa-bell"></i>', array('controller'=>'reminders','action' => 'view', 'loan'=>$loan['Loan']['id']),array('escape'=>false,'class'=>'reminder','alt'=>'یادآور')); ?>
    </td>
</tr>
<?php endforeach; ?>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
    
          
	<?php echo $this->element('pagination/bottom'); ?>
		
</div>
<div class="clear"></div>
<?php
foreach ($pieData as $key=>$value) {
    $pieData[$key]['key']=__($value['key'],true);
}
?>
<?php echo $this->Chart->pie('LoanPieChart','وضعیت پرداخت وام‌ها',$pieData,580,500); ?>
<script type="text/javascript">
//<![CDATA[
var balances = <?php echo json_encode($accountsbalance); ?>;
$(function(){   
    jeeb.tip($('#TagCategoryTip'),'200','شما میتوانید با وارد کردن برچسبی که مورد نظرتان است و یا انتخاب آن از لیست این مورد را برچسب دار کنید. شما میتوانید بیش از یک برچسب به یک مورد متصل کنید.');
    jeeb.accountBalance(balances, $('#TransactionAccountId'), $('#AccountBalanceTo > span'));
    $('#TransactionAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalanceTo > span'); });
    //number format
    jeeb.FormatPrice($('#LoanInstallmentsAmount'));
    jeeb.FormatPrice($('#LoanInstallmentsPayed'));
    jeeb.FormatPrice($('#LoanInstallmentsRemaining'));
    jeeb.FormatPrice($('#LoanAmount'));
    //tips
    jeeb.tip($('#excelExport'),'70','خروجی اکسل');
    jeeb.tip($('.view'),'80','لیست اقساط');
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
    jeeb.tip($('.reminder'),'60','یادآورها');
    jeeb.tip($('#LoanNameTip'),'170','عنوانی برای وام جدید خود وارد کنید.');
    jeeb.tip($('#LoanBankIdTip'),'200','بانک یا موسسه مالی که وام خود را از آن دریافت کرده‌اید انتخاب کنید.<br/> در صورتی که وام خود را از هیچکدام از موسسات موجود در لیست دریافت نکرده‌اید میتوانید گزینه سایر را انتخاب کنید.');
    jeeb.tip($('#LoanStartDateTip'),'200','سال و ماه شروع باز پرداخت وام را وارد کنید.');
    jeeb.tip($('#LoanInstallmentsAmountTip'),'200','مبلغ هز قسط را در این قسمت وارد کنید.<br/>در صورتی که مبلغ اقساط با هم متفاوت میباشند میتوانید مبلغ اقساط را پس از ثبت وام ویرایش کنید.');
    jeeb.tip($('#LoanAddTip'),'300','در صورتی که این گزینه انتخاب شده باشد، یک درآمد به همین مبلغ به لیست درآمدهای شما اضافه خواهد شد زیرا شما مبلغی دریافت کرده‌اید.');
    jeeb.tip($('#TransactionAccountTip'),'200','در صورتی که این گزینه انتخاب شده باشد، یک درآمد به همین مبلغ به لیست درآمدهای شما اضافه خواهد شد زیرا شما مبلغی دریافت کرده‌اید.');
    jeeb.tip($('#LoanNotifyTip'),'200','در صورتی که بله را انتخاب کنید، ایمیل‌های یادآوری را قبل از تاریخ موعد دریافت خواهید کرد.');
    
    //due date check
    $('#LoanInstallmentsDueDay').blur(function(){
        if($(this).val()>29) {
            $(this).val('');
            alert('تاریخ سررسید میتواند بین ۱ تا ۲۹ باشد');
        }
    });
    
    $('#LoanAdd').click(function() {
        $("#TransactionAccountId").toggle(this.checked);
        $("#TransactionAccountTip").toggle(this.checked);
        $("label[for='TransactionAccountId']").toggle(this.checked);
        $("#AccountBalanceTo").toggle(this.checked);
    });
    
    categories = <?php echo json_encode(empty($tags)? array() : $tags); ?>;
    console.log(categories)
    report_catlist = <?php echo json_encode(empty($report_catlist)? array() : $report_catlist); ?>;
    floatingList = new FloatingList({
        input: '#LoanTagTagId', 
        listholder: '#groupListHolder', 
        data: categories,
        allowNew: true,
        empty: false
    });
    
});
//]]>
</script>