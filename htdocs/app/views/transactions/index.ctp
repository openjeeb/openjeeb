<?php
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
?>
<div class="col-xs-16 col-md-6 box rounded">
    <h2>تراکنش جدید</h2>
    <div class="transactions form">
    <?php echo $this->Form->create('Transaction'); ?>
    <fieldset>
        <?php echo $this->Form->error('Transaction.type'); ?>
        <?php $type='transfer'; if(isset($this->data['Transaction']['type'])) {$type=$this->data['Transaction']['type'];} ?>
        <?php echo $this->Form->radio('Transaction.type',array('transfer'=>' انتقال وجه ','expense'=>' ثبت هزینه ','income'=>' ثبت درآمد '),array('value'=>$type,'legend'=>false,'separator'=>'&nbsp;')); ?>
        <?php echo $this->Html->image('info.png', array('id'=>'TransferTypeTip', 'alt'=>'راهنما', 'border' => '0')); ?>
        <br/>
        <span id="TransferWrapper" style="display: none">
            <?php echo $this->Form->label('Transaction.transfer_amount','مبلغ (ریال)'); ?><br/>
            <?php echo $this->Form->error('Transaction.amount'); ?>
            <?php echo $this->Form->text('Transaction.transfer_amount',array('maxlength'=>15,'id'=>'TransferTransactionAmount','style'=>'direction:ltr;')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransferTransactionAmountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>

            <?php echo $this->Form->label('Transaction.transfer_date','تاریخ'); ?><br/>
            <?php echo $this->Form->error('Transaction.date'); ?>
            <?php echo $this->Form->text('Transaction.transfer_date',array('class'=>'datepicker','value'=>$persianDate->pdate('Y/m/d'))); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransferTransactionDateTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/><br/>

            <?php echo $this->Form->label('Transaction.from_account_id','برداشت از حساب',array('style'=>'margin-left:5px')); ?>
            <?php echo $this->Form->error('Transaction.from_account_id'); ?>
            <?php echo $this->Form->select('Transaction.from_account_id',array($accounts),null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransactionFromAccountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br />
            <?php echo $this->Form->label('AccountFromIdBalance','موجودی:'); ?>&nbsp;
            <?php echo $this->Html->tag('span','<span></span>', array('id'=>'AccountBalanceFrom')); ?>
            <br/>
            
            <?php echo $this->Form->label('Transaction.to_account_id','واریز به حساب',array('style'=>'margin-left:23px')); ?>
            <?php echo $this->Form->error('Transaction.to_account_id'); ?>
            <?php echo $this->Form->select('Transaction.to_account_id',array($accounts),null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransactionToAccountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br />
            <?php echo $this->Form->label('AccountToIdBalance','موجودی:'); ?>&nbsp;
            <?php echo $this->Html->tag('span','<span></span>', array('id'=>'AccountBalanceTo')); ?>
            <br/>
            
            <?php echo $this->Form->label('Transfer.description','توضیح'); ?><br/>
            <?php echo $this->Form->textArea('Transfer.description',array('style'=>'width:97%;')); ?>
            <br/>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت حساب‌ها',array('controller'=>'accounts','action'=>'index'));?></span>
            <br/>
        </span>
        
        
        <div id="ExpenseWrapper" style="display: none">
            <?php echo $this->Form->label('Transaction.expense_amount','مبلغ (ریال)'); ?><br/>
            <?php echo $this->Form->error('Transaction.amount'); ?>
            <?php echo $this->Form->text('Transaction.expense_amount',array('maxlength'=>15,'id'=>'ExpenseTransactionAmount')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'ExpenseTransactionAmountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Transaction.expense_date','تاریخ'); ?><br/>
            <?php echo $this->Form->error('Transaction.date'); ?>
            <?php echo $this->Form->text('Transaction.expense_date',array('class'=>'datepicker','value'=>$persianDate->pdate('Y/m/d'))); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'ExpenseTransactionDateTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Expense.expense_category_id','نوع هزینه',array('style'=>'margin-left:5px;')); ?>
            <?php echo $this->Form->select('Expense.expense_category_id',$expenseCategories); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'ExpenseCategoryTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Transaction.expense_account_id','برداشت از',array('style'=>'margin-left:2px;')); ?>
            <?php echo $this->Form->select('Transaction.expense_account_id',$accounts,null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'ExpenseTransactionAccountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br />
            <?php echo $this->Form->label('AccountExpenseIdBalance','موجودی:'); ?>&nbsp;
            <?php echo $this->Html->tag('span','<span></span>', array('id'=>'AccountBalanceExpense')); ?>
            <br/>

            <?php echo $this->Form->label('Expense.individual_id','شخص',array('style'=>'margin-left:19px;')); ?>
            <?php echo $this->Form->select('Expense.individual_id',$individuals,null,array('empty'=>true)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'ExpenseTransactionIndividualTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Expense.description','توضیحات'); ?><br/>
            <?php echo $this->Form->error('Expense.description'); ?>
            <?php echo $this->Form->textArea('Expense.description',array('style'=>'width:97%;')); ?>
            <br/>
            
            <div class="clear">&nbsp;</div>
            
            <div class="input text">
                <label for="ExpenseExpenseCategoryId" style="width:60px;">برچسب: </label>
                <div>
                    <?php echo $this->Form->text('ExpenseTransactionTag.tag_id',array('value'=>'','autocomplete'=>'off', 'style'=>'width:210px')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'TagCategoryExpenseTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                    <div id="groupListHolderExpense"></div>
                </div>
            </div>
            
            <div class="clear">&nbsp;</div>
            
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت انواع هزینه',array('controller'=>'expenseCategories','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت حساب‌ها',array('controller'=>'accounts','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت برچسب‌ها',array('controller'=>'tags','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت اشخاص',array('controller'=>'individuals','action'=>'index'));?></span>
        </div>

        
        <div id="IncomeWrapper" style="display: none">
            <?php echo $this->Form->label('Transaction.income_amount','مبلغ (ریال)'); ?><br/>
            <?php echo $this->Form->error('Transaction.amount'); ?>
            <?php echo $this->Form->text('Transaction.income_amount',array('maxlength'=>15,'id'=>'IncomeTransactionAmount','style'=>'direction:ltr;')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'IncomeTransactionAmountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Transaction.income_date','تاریخ'); ?><br/>
            <?php echo $this->Form->error('Transaction.date'); ?>
            <?php echo $this->Form->text('Transaction.income_date',array('class'=>'datepicker','value'=>$persianDate->pdate('Y/m/d'))); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'IncomeTransactionDateTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Income.income_type_id','نوع درآمد'); ?>
            <?php echo $this->Form->select('Income.income_type_id',$incomeTypes); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'IncomeTypeTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Transaction.income_account_id','واریز به'); ?>&nbsp;&nbsp;&nbsp;
            <?php echo $this->Form->select('Transaction.income_account_id',$accounts,null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'IncomeTransactionAccountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br />
            <?php echo $this->Form->label('AccountIncomeIdBalance','موجودی:'); ?>&nbsp;
            <?php echo $this->Html->tag('span','<span></span>', array('id'=>'AccountBalanceIncome')); ?>
            <br/>

            <?php echo $this->Form->label('Income.individual_id','شخص'); ?>&nbsp;&nbsp;&nbsp;
            <?php echo $this->Form->select('Income.individual_id',$individuals,null,array('empty'=>true)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'IncomeTransactionIndividualTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Income.description','توضیحات'); ?><br/>
            <?php echo $this->Form->error('Income.description'); ?>
            <?php echo $this->Form->textarea('Income.description',array('style'=>'width:98%;')); ?>
            <br/>
            
            <div class="clear">&nbsp;</div>
            
            <div class="input text">
                <label for="IncomeIncomeCategoryId" style="width:60px;">برچسب: </label>
                <div>
                    <?php echo $this->Form->text('IncomeTransactionTag.tag_id',array('value'=>'','autocomplete'=>'off', 'style'=>'width:210px')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'TagCategoryIncomeTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                    <div id="groupListHolderIncome"></div>
                </div>
            </div>
            
            <div class="clear">&nbsp;</div>
            
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت انواع درآمد',array('controller'=>'incomeTypes','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت حساب‌ها',array('controller'=>'accounts','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت برچسب‌ها',array('controller'=>'tags','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت اشخاص',array('controller'=>'individuals','action'=>'index'));?></span>
        </div>
        
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>

</div>

<div class="col-xs-16 col-md-10">
    <div id="TransactionColumnChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <div class="box">
        <h2><?php echo $this->Html->link('جستجو', '#',array('id'=>'toggle-filter','class'=>'hidden')); ?></h2>
        <div id="filter" class="form">
            <?php echo $this->Form->create('Transaction'); ?>
            <fieldset>
                <?php
                echo $this->Form->input('Transaction.search',array('type'=>'hidden','value'=>true));
                echo $this->Form->input('Transaction.transaction_type',array('label'=>'نوع تراکنش','type'=>'select','options'=>array('all'=>'همه','credit'=>'واریز','debt'=>'برداشت','transfer'=>'انتقال')));
                echo $this->Form->input('Transaction.account_id',array('label'=>'حساب','type'=>'select','options'=>$accounts,'empty'=>true));
                echo $this->Form->input('Transaction.individual_id',array('label'=>'شخص','type'=>'select','options'=>$individuals,'empty'=>true));
                echo $this->Form->input('Transaction.amount',array('label'=>'مبلغ','type'=>'text','maxlength'=>15,'id'=>'TransactionAmountSearch','style'=>'direction:ltr;'));?>
                <div class="input text">
                    <label>از تاریخ</label>
                    <div style='float:right;'><?php echo $this->Form->text('Transaction.start_date',array('class'=>'datepicker','value'=>'')); ?></div>
                    <?php echo $this->element('filldate', array( 'start_date' => '#TransactionStartDate', 'end_date' => '#TransactionEndDate', 'oneline'=>true )); ?>
                </div>
                <?php
                echo $this->Form->input('Transaction.end_date',array('label'=>'تا تاریخ','class'=>'datepicker','value'=>''));
                ?>
                <div class="clear">&nbsp;</div>

                <div class="input text">
                    <label for="ExpenseExpenseCategoryId">برچسب: </label>
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
    <h2  class="col-xs-16 col-md-3" id="page-heading">لیست تراکنش‌ها <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  class="table table-striped table-hover table-bordered table-responsive" id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array(
        'نوع',
        $paginator->sort('تاریخ','date',array('url'=>array('#'=>'#dataTable'))),
        'مبلغ',
        'نوع',
        'توضیحات',
        $paginator->sort('حساب','account_id',array('url'=>array('#'=>'#dataTable'))),
        'شخص',
        $paginator->sort('تاریخ ایجاد','created',array('url'=>array('#'=>'#dataTable'))),
        'عملیات'));
echo '<thead class="table-primary"  class="table-primary">'.$tableHeaders.'</thead>'; ?>

<?php foreach ($transactions as $transaction): ?>
	<tr>
		<td>
                    <?php if($transaction['Transaction']['expense_id']): ?>
                        <span style="color: #C62121">هزینه</span>
                    <?php elseif($transaction['Transaction']['income_id']): ?>
                        <span style="color: green">درآمد</span>
                    <?php else: ?>
                        <span style="color: #9ECBE5">انتقال</span>
                    <?php endif; ?>
                </td>
		<td><?php echo $transaction['Transaction']['date']; ?>&nbsp;</td>
		<td style="direction:ltr;text-align: right;">
                    <?php
                        if($transaction['Transaction']['type']=='debt') {
                            echo '<span style="color:#C62121;">-'.number_format($transaction['Transaction']['amount']).'</span>';
                        }
                        else if($transaction['Transaction']['type']=='credit') {
                            echo '<span style="color:green;">+'.number_format($transaction['Transaction']['amount']).'</span>';
                        }
                    ?>
                </td>
		<td>
                    <?php
                        if($transaction['Transaction']['expense_id']) {
                            echo $transaction['ExpenseCategory']['name'];
                            if(!empty($transaction['ExpenseSubCategory']['name'])) {
                                echo '>>'.$transaction['ExpenseSubCategory']['name'];
                            }
                        } elseif($transaction['Transaction']['income_id']) {
                            echo $transaction['IncomeType']['name'];
                            if(!empty($transaction['IncomeSubType']['name'])) {
                                echo '>>'.$transaction['IncomeSubType']['name'];
                            }
                        }
                    ?>&nbsp;
                </td>
                <td>
                    <?php if($transaction['Transaction']['expense_id']): ?>
                            <?php echo str_replace('\n', "<br />", $transaction['Expense']['description']); ?>
                    <?php elseif($transaction['Transaction']['income_id']): ?>
                            <?php echo str_replace('\n', "<br />", $transaction['Income']['description']); ?>
                    <?php elseif(!empty($transaction['Transfer']['Account'])): ?>
                            <?php if($transaction['Transaction']['type']=='credit'): ?>
                                انتقال از حساب <?php echo $transaction['Transfer']['Account']['name'] ?>
                                <?php echo $transaction['Transfer']['description']? "<br /> توضیحات: ".str_replace('\n', "<br />", $transaction['Transfer']['description']) : ""; ?>
                            <?php else: ?>
                                انتقال به حساب <?php echo $transaction['Transfer']['Account']['name'] ?>
                                <?php echo $transaction['Transfer']['description']? "<br /> توضیحات: ".str_replace('\n', "<br />", $transaction['Transfer']['description']) : ""; ?>
                            <?php endif; ?>
                    <?php else: ?>
                            تراکنش انتقال متناظر حذف شده است
                    <?php endif; ?>&nbsp;
                </td>
		<td><?php echo $transaction['Account']['name']; ?>&nbsp;</td>
		<td><?php 
                        echo $transaction['IndividualExpense']['name'];
                        echo $transaction['IndividualIncome']['name'];
                     ?>&nbsp;</td>
		<td><?php echo $transaction['Transaction']['created']; ?>&nbsp;</td>
		<td style="width: 80px">
                    <?php 
                        if($transaction['Transaction']['expense_id']) {
                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('controller'=>'expenses','action' => 'edit', $transaction['Expense']['id']),array('escape'=>false,'class'=>'edit'));
                        } elseif($transaction['Transaction']['income_id']) {
                            echo $this->Html->link('<i class="fa fa-pencil"></i>', array('controller'=>'incomes','action' => 'edit', $transaction['Income']['id']),array('escape'=>false,'class'=>'edit'));
                        } else {
                            echo '&nbsp;&nbsp;&nbsp;&nbsp;';
                        } 
                     ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php 
                        if($transaction['Transaction']['expense_id']) {
                            echo $this->Html->link('<i class="fa fa-times"></i>', array('controller'=>'expenses','action' => 'delete', $transaction['Expense']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $transaction['Expense']['id']));
                        } elseif($transaction['Transaction']['income_id']) {
                            echo $this->Html->link('<i class="fa fa-times"></i>', array('controller'=>'incomes','action' => 'delete', $transaction['Income']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $transaction['Income']['id']));
                        } else {
                            echo $this->Html->link('<i class="fa fa-times"></i>', array('controller'=>'transactions','action' => 'delete', $transaction['Transaction']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $transaction['Transaction']['id']));
                        } 
                     ?>&nbsp;
		</td>
	</tr>
<?php endforeach; ?>
        <tr>
            <td colspan=""></td>
            <td colspan="2" style="text-align:left;"><b>جمع هزینه‌ها</b></td>
            <td style="color:#C62121;direction:ltr;"><b>-<?php echo number_format($debtSum); ?></b></td>
            <td colspan="2" style="text-align:left;"><b>جمع درآمدها</b></td>
            <td style="color:green;direction:ltr;"><b>+<?php echo number_format($creditSum); ?></b></td>
            <td colspan="6"></td>
        </tr>    
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
	<?php echo $this->element('pagination/bottom'); ?>
</div>
<div class="clear"></div>

<?php echo $this->Chart->doubleColumn('TransactionColumnChart','تراکنشهای ماهانه','واریز',$creditTransactionsColumn,'برداشت',$debtTransactionsColumn,6,580,500); ?>

<script type="text/javascript">
//<![CDATA[
balances = <?php echo json_encode($accountsbalance); ?>;
$(function(){
    jeeb.tip($('#TagCategoryExpenseTip'),'200','شما میتوانید با وارد کردن برچسبی که مورد نظرتان است و یا انتخاب آن از لیست این مورد را برچسب دار کنید. شما میتوانید بیش از یک برچسب به یک مورد متصل کنید.');
    jeeb.tip($('#TagCategoryIncomeTip'),'200','شما میتوانید با وارد کردن برچسبی که مورد نظرتان است و یا انتخاب آن از لیست این مورد را برچسب دار کنید. شما میتوانید بیش از یک برچسب به یک مورد متصل کنید.');
    //format price
    jeeb.FormatPrice($('#TransferTransactionAmount'));
    jeeb.FormatPrice($('#ExpenseTransactionAmount'));
    jeeb.FormatPrice($('#IncomeTransactionAmount'));
    jeeb.FormatPrice($('#TransactionAmountSearch'));
    //tips
    jeeb.tip($('#TransactionAmountSearch'),'104','مبلغ هزینه را وارد کنید.');    
    jeeb.tip($('#excelExport'),'70','خروجی اکسل');
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
    $('#TransferTypeTip').bt('نوع تراکنشی را که میخواهید ثبت کنید انتخاب کنید.<br/><b>انتقال وجه</b><br/>برای ثبت انتقال وجه مابین حساب‌ها این گزینه را انتخاب کنید.<br/>+ زمانی که وجهی را از حساب خود بصورت نقد برداشت میکنید بایستی یک تراکنش انتقال وجه از حساب مورد نظر به حساب جیب ثبت کنید. (مانند برداشت وجه از عابر بانک یا بانک)<br/>+ زمانی که وجهی نقد را به حساب خود واریز میکنید نیز بایستی یک تراکنش انتقال وجه از حساب جیب به حساب مورد نظر ثبت کنید.<br/><b>هزینه</b><br/>این گزینه را برای ثبت هزینه انتخاب کنید.<br/><b>درآمد</b><br/>این گزینه را برای ثبت هزینه انتخاب کنید.',{positions:'left',width:300,fill:'#EFF2F5',strokeStyle:'#B7B7B7',spikeLength:10,spikeGirth:10,padding:10,cornerRadius:8,cssStyles:{fontSize:'11px'}});
    jeeb.tip($('#TransferTransactionAmountTip'),'130','مبلغ انتقال وجه را وارد کنید.');    
    jeeb.tip($('#TransferTransactionDateTip'),'130','تاریخ انتقال وجه را وارد کنید.');    
    jeeb.tip($('#TransactionFromAccountTip'),'200','حسابی را که وجه از آن برداشت شده است.');    
    jeeb.tip($('#TransactionToAccountTip'),'200','حسابی که وجه به آن واریز شده است.');    
    jeeb.tip($('#ExpenseTransactionAmountTip'),'104','مبلغ هزینه را وارد کنید.');    
    jeeb.tip($('#ExpenseTransactionDateTip'),'105','تاریخ هزینه را وارد کنید.');    
    jeeb.tip($('#ExpenseCategoryTip'),'200','نوع هزینه را انتخاب کنید.<br/> از طریق صفحه مدیریت انواع هزینه میتوانید این انواع را مدیریت کنید.');    
    jeeb.tip($('#ExpenseTransactionAccountTip'),'200','حسابی را که برای این هزینه از آن برداشت کرده‌اید انتخاب کنید.<br/>در صورتی که این مبلغ از حسابی برداشت نکرده‌اید و نقد پرداخت کرده‌اید جیب را انتخاب کنید.');
    jeeb.tip($('#ExpenseTransactionIndividualTip'),'200','میتوانید یک شخص را بعنوان دریافت کننده این هزینه انتخاب کنید.');
    jeeb.tip($('#IncomeTransactionAmountTip'),'104','مبلغ درآمد را وارد کنید.');    
    jeeb.tip($('#IncomeTransactionDateTip'),'105','تاریخ درآمد را وارد کنید.');    
    jeeb.tip($('#IncomeTypeTip'),'200','نوع درآمد را انتخاب کنید.<br/> از طریق صفحه مدیریت انواع درآمد میتوانید این انواع را مدیریت کنید.');    
    jeeb.tip($('#IncomeTransactionAccountTip'),'200','حسابی را که این درآمد به آن واریز شده است انتخاب کنید.<br/>در صورتی که این مبلغ را به حسابی واریز نکرده‌اید و نقد دریافت کرده‌اید جیب را انتخاب کنید.');
    jeeb.tip($('#IncomeTransactionIndividualTip'),'200','میتوانید یک شخص را بعنوان پرداخت کننده این درآمد انتخاب کنید.');
    //show/hide things!
    if($('input[name="data[Transaction][type]"]:checked').val()=='transfer') {
        $('#TransferWrapper').show();
    }
    if($('input[name="data[Transaction][type]"]:checked').val()=='expense') {
        $('#ExpenseWrapper').show();
    }
    if($('input[name="data[Transaction][type]"]:checked').val()=='income') {
        $('#IncomeWrapper').show();
    }
    $('input[name="data[Transaction][type]"]', '#TransactionIndexForm').change(function(){
        if($('input[name="data[Transaction][type]"]:checked').val()=='transfer') {
            $('#TransferWrapper').show();
            $('#ExpenseWrapper').hide();
            $('#IncomeWrapper').hide();
        }
        if($('input[name="data[Transaction][type]"]:checked').val()=='expense') {
            $('#TransferWrapper').hide();
            $('#ExpenseWrapper').show();
            $('#IncomeWrapper').hide();
        }
        if($('input[name="data[Transaction][type]"]:checked').val()=='income') {
            $('#TransferWrapper').hide();
            $('#ExpenseWrapper').hide();
            $('#IncomeWrapper').show();
        }
    });
    //bind the sub categories
    jeeb.bindExpenseSubCategories($('#ExpenseExpenseCategoryId'),<?php echo $this->Javascript->object($expenseCategoriesData); ?>,'','ExpenseExpenseSubCategoryId');
    jeeb.bindIncomeSubTypes($('#IncomeIncomeTypeId'),<?php echo $this->Javascript->object($incomeTypesData); ?>,'','IncomeIncomeSubTypeId');
    
    jeeb.accountBalance(balances, $('#TransactionFromAccountId'), $('#AccountBalanceFrom'));
    $('#TransactionFromAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalanceFrom'); });
    jeeb.accountBalance(balances, $('#TransactionToAccountId'), $('#AccountBalanceTo'));
    $('#TransactionToAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalanceTo'); });
    jeeb.accountBalance(balances, $('#TransactionExpenseAccountId'), $('#AccountBalanceExpense > span'));
    $('#TransactionExpenseAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalanceExpense > span'); });
    jeeb.accountBalance(balances, $('#TransactionIncomeAccountId'), $('#AccountBalanceIncome > span'));
    $('#TransactionIncomeAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalanceIncome > span'); });
    
    categories = <?php echo json_encode(empty($tags)? array() : $tags); ?>;
    report_catlist = <?php echo json_encode(empty($report_catlist)? array() : $report_catlist); ?>;
    floatingList = new FloatingList({
        input: '#IncomeTransactionTagTagId', 
        listholder: '#groupListHolderIncome', 
        data: categories,
        allowNew: true,
        empty: false
    });
    floatingList2 = new FloatingList({
        input: '#ExpenseTransactionTagTagId', 
        listholder: '#groupListHolderExpense', 
        data: categories,
        allowNew: true,
        empty: false
    });
    floatingList3 = new FloatingList({
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
