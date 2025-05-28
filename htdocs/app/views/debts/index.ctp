<?php
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
?>
<div class="col-xs-16 col-md-6 box rounded">
    <h2>ثبت بدهی / طلب</h2>
    <div class="debts form">
    <?php echo $this->Form->create('Debt',array('id'=>'DebtAddForm')); ?>
    <fieldset>
            <?php echo $this->Form->error('Debt.type'); ?>
            <?php echo $this->Form->radio('Debt.type',array('debt'=>'بدهی (قرض گرفتن)','credit'=>'طلب (قرض دادن)'),array('value'=>'debt','legend'=>false,'separator'=>'&nbsp;')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'DebtTypeTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br style="margin-bottom: 10px;"/>
            
            <?php echo $this->Form->label('Debt.amount','مبلغ (ریال)'); ?><br/>
            <?php echo $this->Form->error('Debt.amount'); ?>
            <?php echo $this->Form->text('Debt.amount',array('maxlength'=>15,'style'=>'direction:ltr;')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'DebtAmountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Debt.name','عنوان '); ?><br/>
            <?php echo $this->Form->error('Debt.name'); ?>
            <?php echo $this->Form->text('Debt.name'); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'DebtNameTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Debt.due_date','تاریخ موعد'); ?><br/>
            <?php echo $this->Form->error('Debt.due_date'); ?>
            <?php echo $this->Form->text('Debt.due_date',array('class'=>'datepicker','value'=>$persianDate->pdate('Y/m/d'))); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'DebtDueDateTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/><br/>
            
            <?php echo $this->Form->error('Debt.add'); ?>
            <?php echo $this->Form->checkbox('Debt.add',array('value'=>'yes','checked'=>'checked','style'=>'width:auto;')); ?>
            <?php echo $this->Form->label('Debt.add','اضافه نمودن درآمد متناظر'); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'DebtAddTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Transaction.account_id','واریز به حساب'); ?>
            <?php echo $this->Form->error('Transaction.account_id'); ?>
            <?php echo $this->Form->select('Transaction.account_id',array($accounts),null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'TransactionAccountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br />
            <?php echo $this->Html->tag('span','موجودی: <span></span>', array('id'=>'AccountBalanceTo')); ?>
            <br/>
            
            <?php echo $this->Form->label('Debt.individual_id','دریافت از'); ?>
            <?php echo $this->Form->error('Debt.individual_id'); ?>
            <?php echo $this->Form->select('Debt.individual_id',array($individuals),null,array('empty'=>true)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'DebtIndividualTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Debt.notify','یادآور'); ?>
            <?php echo $this->Form->error('Debt.notify'); ?>
            <?php echo $this->Form->select('Debt.notify',array('yes'=>'فعال','no'=>'غیر فعال'),null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'DebtNotifyTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <div class="clear">&nbsp;</div>
            
            <div class="input text">
                <label for="DebtDebtCategoryId" style="width:60px;">برچسب: </label>
                <div>
                    <?php echo $this->Form->text('DebtTag.tag_id',array('value'=>'','autocomplete'=>'off', 'style'=>'width:210px')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'TagCategoryTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                    <div id="groupListHolder"></div>
                </div>
            </div>
            
            <div class="clear">&nbsp;</div>
            
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت حساب‌ها',array('controller'=>'accounts','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت اشخاص',array('controller'=>'individuals','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت برچسب‌ها',array('controller'=>'tags','action'=>'index'));?></span>
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>
</div>


<div class="col-xs-16 col-md-10">
    <div id="DebtColumnChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16 ">
    <div class="box">
        <h2><?php echo $this->Html->link('جستجو', '#',array('id'=>'toggle-filter','class'=>'hidden')); ?></h2>
        <div id="filter" class="form">
            <?php echo $this->Form->create('Debt',array('id'=>'DebtSearchForm')); ?>
            <fieldset>
                <?php
                echo $this->Form->input('Debt.search',array('type'=>'hidden','value'=>true));
                echo $this->Form->input('Debt.type',array('label'=>'نوع','type'=>'select','options'=>array('all'=>'همه','debt'=>'بدهی','credit'=>'طلب')));
                echo $this->Form->input('Debt.status',array('label'=>'وضعیت','type'=>'select','options'=>array('all'=>'همه','due'=>'تسویه نشده','done'=>'تسویه شده')));
                echo $this->Form->input('Debt.individual_id',array('label'=>'شخص','id'=>'DebtSearchIndividual','type'=>'select','options'=>$individuals,'empty'=>true));
                echo $this->Form->input('Debt.name',array('label'=>'عنوان','type'=>'text', 'id'=> 'DebtNameSearch'));
                echo $this->Form->input('Debt.amount',array('label'=>'مبلغ','type'=>'text','maxlength'=>15, 'id'=> 'DebtAmountSearch', 'style'=>'direction:ltr;')); ?>
                <div class="input text">
                    <label>از تاریخ</label>
                    <div style='float:right;'><?php echo $this->Form->text('Debt.start_date',array('class'=>'datepicker','value'=>'')); ?></div>
                    <?php echo $this->element('filldate', array( 'start_date' => '#DebtStartDate', 'end_date' => '#DebtEndDate', 'oneline'=>true )); ?>
                </div>
                <?php
                echo $this->Form->input('Debt.end_date',array('label'=>'تا تاریخ','class'=>'datepicker','value'=>'')); ?>

                <div class="clear">&nbsp;</div>

                <div class="input text">
                    <label for="DebtDebtCategoryId">برچسب: </label>
                    <div>
                        <?php echo $this->Form->text('DebtTagSearch.tag_id',array('value'=>'','autocomplete'=>'off', 'id'=> 'DebtTagIdSearch')); ?>
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
    <h2 class="col-xs-16 col-md-4" id="page-heading">لیست بدهی‌ها و طلب‌ها  <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-9" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array(
        $paginator->sort('#','status',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('نوع','type',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('عنوان','name',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('مبلغ (ریال)','amount',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('تسویه شده','settled',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('باقیمانده','settled',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('تاریخ موعد','due_date',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('شخص','individual_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('آگاه‌سازی','notify',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('تاریخ ایجاد','created',array('url'=>array('#'=>'#dataTable')))
        ,'عملیات'
        ));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php foreach ($debts as $debt): ?>
	<tr>
        <td><?php if($debt['Debt']['status']=='done'){ echo '<i class="fa fa-check-square-o done"></i>'; }else{ echo '<i class="fa fa-square-o due"></i>'; } ?></td>
		<td><?php __($debt['Debt']['type']); ?>&nbsp;</td>
		<td><?php echo $debt['Debt']['name']; ?>&nbsp;</td>
                <td style="direction: ltr;<?php if($debt['Debt']['type']=='debt'): ?>color:#C62121;<?php else: ?>color:green;<?php endif; ?>"><?php if($debt['Debt']['amount']>0){echo '+';} echo number_format($debt['Debt']['amount']); ?></td>
                <td style="direction: ltr;<?php if($debt['Debt']['type']=='credit'): ?>color:#C62121;<?php else: ?>color:green;<?php endif; ?>"><?php echo number_format( $debt['Debt']['settled'] ); ?></td>
                <td style="direction: ltr;<?php if($debt['Debt']['type']=='debt'): ?>color:#C62121;<?php else: ?>color:green;<?php endif; ?>"><?php echo number_format( abs( $debt['Debt']['amount'] ) - $debt['Debt']['settled'] ); ?></td>
		<td><?php echo $debt['Debt']['due_date']; ?>&nbsp;</td>
		<td><?php echo $debt['Individual']['name']; ?>&nbsp;</td>
		<td><?php __($debt['Debt']['notify']); ?>&nbsp;</td>
        <td><?php echo end(explode(' ',$debt['Debt']['created'])); ?></td>
		<td style="width: 150px;">
        <?php 
            if($debt['Debt']['status']=='done') {
                echo $this->Html->image('blank.png',array('alt'=>'','style'=>'margin-left:8px;')); 
            } else { 
                echo $this->Html->link('<i class="fa fa-check"></i>', array('action' => 'ajaxDebtDone', $debt['Debt']['id']),array('escape'=>false,'class'=>'do '.$debt['Debt']['type'],'style'=>'margin-left:8px;','onclick'=>'return false'));
            } 
        ?>
        <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $debt['Debt']['id']),array('escape'=>false,'class'=>'edit','style'=>'margin-left:8px;')); ?>
        <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $debt['Debt']['id']),array('escape'=>false,'class'=>'delete','style'=>'margin-left:8px;'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟ با پاک کردن این بدهی/طلب تراکنشهای مربوط به آن هم حذف میشوند.', $debt['Debt']['id'])); ?>
        <?php echo $this->Html->link('<i class="fa fa-desktop"></i>', array('action' => 'view', $debt['Debt']['id']),array('escape'=>false,'class'=>'view','style'=>'margin-left:8px;','alt'=>'نمایش')); ?>
        <?php echo $this->Html->link('<i class="fa fa-bell"></i>', array('controller'=>'reminders','action' => 'view', 'debt'=>$debt['Debt']['id']),array('escape'=>false,'class'=>'reminder','alt'=>'یادآور')); ?>
		</td>
	</tr>
<?php endforeach; ?>
        <tr>
            <td></td>
            <td colspan="3" style="text-align:left;"><b>جمع کل بدهی‌ها</b></td>
            <td style="direction:ltr;color:#C62121;"><b><?php echo number_format($debtsSum); ?></b></td>
            <td></td>
            <td colspan="3" style="text-align:left;"><b>جمع کل طلب‌ها</b></td>
            <td colspan="2" style="direction:ltr;color:green;"><b>+<?php echo number_format($creditsSum); ?></b></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3" style="text-align:left;"><b>جمع بدهی‌های تسویه نشده</b></td>
            <td style="direction:ltr;color:#C62121;"><b><?php echo number_format($debtsDueSum); ?></b></td>
            <td></td>
            <td colspan="3" style="text-align:left;"><b>جمع طلب‌های تسویه نشده</b></td>
            <td colspan="2" style="direction:ltr;color:#C62121;"><b><?php echo number_format($creditsDueSum); ?></b></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3" style="text-align:left;"><b>جمع بدهی‌های تسویه شده</b></td>
            <td style="direction:ltr;color:green;"><b><?php echo number_format(abs($debtsDoneSum)); ?></b></td>
            <td></td>
            <td colspan="3" style="text-align:left;"><b>جمع طلب‌های تسویه شده</b></td>
            <td colspan="2" style="direction:ltr;color:green;"><b><?php echo number_format($creditsDoneSum); ?></b></td>
        </tr>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
    
          
	<?php echo $this->element('pagination/bottom'); ?>
		
</div>
<div class="clear"></div>
<?php echo $this->Chart->doubleColumn('DebtColumnChart','بدهی / طلب','طلب‌',$creditsColumn,'بدهی',$debtsColumn,6,580,500); ?>

<!-- Modal -->
<div id="DebtDoneConfirm" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close pull-left" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">تسویه بدهی</h4>
            </div>
            <div class="modal-body">
                <?php echo $this->Form->create('Debt',array('id'=>'DebtDoForm')); ?>
                <fieldset>
                    <legend>نحوه تسویه</legend>
                    <span>آیا کل بدهی تسویه شده یا بخشی از آن تسویه شده؟</span><br/>
                    <?php echo $this->Form->radio('Debt.state',array('all'=>'کل بدهی','part'=>'بخشی از بدهی'),array('legend'=>false,'separator'=>'  ','value'=>'all')); ?>
                    <br/>
                    <?php echo $this->Form->label('DebtDoneAmount','میزان تسویه شده'); ?>
                    <?php echo $this->Form->text('Debt.done_amount',array('disabled'=>'disabled')); ?> ریال
                    <br/>
                </fieldset>
                <br/>

                <fieldset>
                    <legend>افزودن مبلغ به هزینه</legend>
                    <span>آیا مایلید مبلغ تسویه بدهی به هزینه‌های شما اضافه گردد؟ در اینصورت حسابی که از آن برای تسویه بدهی برداشت کرده‌اید را مشخص کنید.</span>    <br/><br/>
                    <?php echo $this->Form->radio('Debt.addExpense',array('yes'=>'بله','no'=>'خیر'),array('legend'=>false,'separator'=>'  ','value'=>'yes')); ?>
                    <br/>
                    <?php echo $this->Form->label('TransactionAccountIdDebt','برداشت از حساب'); ?>
                    <?php echo $this->Form->select('Transaction.account_id',$accounts,null,array('id'=>'TransactionAccountIdDebt','empty'=>false )); ?>
                    <br />
                    <?php echo $this->Form->label('AccountIdBalanceDebt','موجودی:'); ?>&nbsp;
                    <?php echo $this->Html->tag('span','<span></span>', array('id'=>'AccountBalanceDebt')); ?>
                </fieldset>
                <?php echo $this->Form->end();?>
            </div>
            <div class="modal-footer">
                <button type="button" id="DebtDoneConfirm-submit" class="btn btn-success" data-dismiss="modal">ثبت</button>
                <button type="button" id="DebtDoneConfirm-cancel" class="btn btn-warning" data-dismiss="modal">لغو</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="CreditDoneConfirm" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close pull-left" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">تسویه طلب</h4>
            </div>
            <div class="modal-body">
                <?php echo $this->Form->create('Debt',array('id'=>'CreditDoForm')); ?>
                <fieldset>
                    <legend>نحوه تسویه</legend>
                    <span>آیا کل طلب تسویه شده یا بخشی از آن تسویه شده؟</span><br/>
                    <?php echo $this->Form->radio('Credit.state',array('all'=>'کل طلب','part'=>'بخشی از طلب'),array('legend'=>false,'separator'=>'  ','value'=>'all')); ?>
                    <br/>
                    <?php echo $this->Form->label('CreditDoneAmount','میزان تسویه شده'); ?>
                    <?php echo $this->Form->text('Credit.done_amount',array('disabled'=>'disabled')); ?> ریال
                    <br/>
                </fieldset>
                <br/>

                <fieldset>
                    <legend>افزودن مبلغ به درآمد</legend>
                    <span>آیا مایلید مبلغ تسویه طلب به درآمدهای شما اضافه گردد؟ در اینصورت حسابی که مبلغ طلب را به آن واریز کرده‌اید مشخص کنید.</span>    <br/><br/>
                    <?php echo $this->Form->radio('Debt.addIncome',array('yes'=>'بله','no'=>'خیر'),array('legend'=>false,'separator'=>'  ','value'=>'yes')); ?>
                    <br/>
                    <?php echo $this->Form->label('TransactionAccountIdCredit','واریز به حساب'); ?>
                    <?php echo $this->Form->select('Transaction.account_id',$accounts,null,array('id'=>'TransactionAccountIdCredit','empty'=>false)); ?>
                    <br />
                    <?php echo $this->Form->label('AccountCreditIdBalance','موجودی:'); ?>&nbsp;
                    <?php echo $this->Html->tag('span','<span></span>', array('id'=>'AccountBalanceCredit')); ?>
                </fieldset>
                <?php echo $this->Form->end();?>
            </div>
            <div class="modal-footer">
                <button type="button" id="CreditDoneConfirm-submit" class="btn btn-success" data-dismiss="modal">ثبت</button>
                <button type="button" id="CreditDoneConfirm-cancel" class="btn btn-warning" data-dismiss="modal">لغو</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
var balances = <?php echo json_encode($accountsbalance); ?>;
$(function(){   
    jeeb.tip($('#TagCategoryTip'),'200','شما میتوانید با وارد کردن برچسبی که مورد نظرتان است و یا انتخاب آن از لیست این مورد را برچسب دار کنید. شما میتوانید بیش از یک برچسب به یک مورد متصل کنید.');
    jeeb.accountBalance(balances, $('#TransactionAccountId'), $('#AccountBalanceTo > span'));
    $('#TransactionAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalanceTo > span'); });
    jeeb.accountBalance(balances, $('#TransactionAccountIdDebt'), $('#AccountBalanceDebt > span'));
    $('#TransactionAccountIdDebt').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalanceDebt > span'); });
    jeeb.accountBalance(balances, $('#TransactionAccountIdCredit'), $('#AccountBalanceCredit > span'));
    $('#TransactionAccountIdCredit').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalanceCredit > span'); });
    //number format
    jeeb.FormatPrice($('#DebtAmount'));
    jeeb.FormatPrice($('#DebtDoneAmount'));
    jeeb.FormatPrice($('#CreditDoneAmount'));
    jeeb.FormatPrice($('#DebtAmountSearch'));
    //tips
    jeeb.tip($('#excelExport'),'70','خروجی اکسل');
    jeeb.tip($('.due'),'58','تسویه نشده');
    jeeb.tip($('.done'),'57','تسویه شده');
    jeeb.tip($('.do'),'45','تسویه');
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
    jeeb.tip($('.view'),'45','نمایش');
    jeeb.tip($('.reminder'),'60','یادآورها');
    jeeb.tip($('#DebtTypeTip'),'300','<b>طلب</b><br/>طلب را انتخاب کنید اگر شما از کسی طلب دارید. و او باید آن طلب را به شما پرداخت کند.<br/><br/><b>بدهی</b><br/>بدهی را انتخاب کنید اگر شما به کسی بدهکارید. و شما باید آن بدهی را پرداخت کنید.');
    jeeb.tip($('#DebtAmountTip'),'180','مبلغ بدهی یا طلب خود را وارد کنید.');
    jeeb.tip($('#DebtNameTip'),'200','یک عنوان برای بدهی یا طلب خود برگزینید.');
    jeeb.tip($('#DebtDueDateTip'),'200','تاریخی که بدهی یا طلب خود را بایستی تسویه کنید انتخاب نمایید.');
    jeeb.tip($('#DebtAddTip'),'300','<b>طلب</b><br/>در صورتی که این گزینه انتخاب شده باشد، یک هزینه به همین مبلغ به لیست هزینه‌های شما اضافه خواهد شد زیرا شما مبلغی پرداخت کرده‌اید.<br/>سناریو ۱: شما مبلغی را به کسی قرض داده‌اید، در این صورت این گزینه بایستی انتخاب شود تا یک هزینه با همین مبلغ به لیست هزینه‌های شما اضافه شود.<br/>سناریو ۲: شما برای مثال خدماتی را برای کسی انجام داده‌اید و از آن شخص طلب دارید، در این صورت این گزینه نبایستی انتخاب گردد زیرا پولی رد و بدل نشده است.<br/><b>بدهی</b><br/>در صورتی که این گزینه انتخاب شده باشد، یک درآمد به همین مبلغ به لیست درآمدهای شما اضافه خواهد شد زیرا شما مبلغی دریافت کرده‌اید.<br/>سناریو ۱: شما مبلغی را از کسی قرض گرفته‌اید، در این صورت این گزینه بایستی انتخاب شود تا یک درآمد با همین مبلغ به لیست درآمدهای شما اضافه گردد.<br/>سناریو ۲: شما خدماتی را از کسی دریافت کرده‌اید و به آن شخص بدهکارید، در این صورت این گزینه نبایستی انتخاب گردد زیرا پولی رد و بدل نشده است.');
    jeeb.tip($('#TransactionAccountTip'),'200','<b>طلب</b><br/>انتخاب کنید که مبلغی که قرض داده‌اید از کدام حساب برداشت شده است؟<br/><b>بدهی</b><br/>انتخاب کنید که مبلغی که قرض گرفته‌اید به کدام حساب پرداخت شده است؟');
    jeeb.tip($('#DebtIndividualTip'),'200','<b>طلب</b><br/>انتخاب کنید که مبلغی که قرض داده‌اید به چه کسی پرداخت شده است؟<br/><b>بدهی</b><br/>انتخاب کنید که مبلغی که قرض گرفته‌اید از چه کسی دریافت شده است؟');
    jeeb.tip($('#DebtNotifyTip'),'200','در صورتی که بله را انتخاب کنید، ایمیل‌های یادآوری را قبل از تاریخ موعد دریافت خواهید کرد.');
    //show/hide things!
    if($('input[name="data[Debt][type]"]:checked').val()=='credit') {
        $("label[for='TransactionAccountId']").html('برداشت از حساب');
        $("label[for='DebtIndividualId']").html('پرداخت به');
        $("label[for='DebtAdd']").html('اضافه نمودن هزینه متناظر');
    }
    if($('input[name="data[Debt][type]"]:checked').val()=='debt') {
        $("label[for='TransactionAccountId']").html('واریز به حساب');
        $("label[for='DebtIndividualId']").html('دریافت از');
        $("label[for='DebtAdd']").html('اضافه نمودن درآمد متناظر');
    }
    $('input[name="data[Debt][type]"]', '#DebtAddForm').change(function(){
        if($('input[name="data[Debt][type]"]:checked').val()=='credit') {
            $("label[for='TransactionAccountId']").html('برداشت از حساب');
            $("label[for='DebtIndividualId']").html('پرداخت به');
            $("label[for='DebtAdd']").html('اضافه نمودن هزینه متناظر');
        }
        if($('input[name="data[Debt][type]"]:checked').val()=='debt') {
            $("label[for='TransactionAccountId']").html('واریز به حساب');
            $("label[for='DebtIndividualId']").html('دریافت از');
            $("label[for='DebtAdd']").html('اضافه نمودن درآمد متناظر');
        }
    });
    //
    $('#DebtAdd').click(function() {
        $("#TransactionAccountId").toggle(this.checked);
        $("#TransactionAccountTip").toggle(this.checked);
        $("label[for='TransactionAccountId']").toggle(this.checked);
        $("#AccountBalanceTo").toggle(this.checked);
    });
    //prevent enter submit
    $('#DebtDoneConfirm').keypress(function(e){
        if ( e.which == 13 ) e.preventDefault();
    });
    $('#CreditDoneConfirm').keypress(function(e){
        if ( e.which == 13 ) e.preventDefault();
    });
    //mark debt as done
    $('.do.debt').click(function () {
        that=$(this);
        $('#DebtDoForm')[0].reset();
        $("#DebtDoneConfirm-submit").unbind('click');
        $("#DebtDoneConfirm-submit").on('click',function (e) {
            if($('input[name="data[Debt][addExpense]"]:checked').val()=='yes') {
                jeeb.DebtDone(that.attr('href'),true,$('#DebtDoForm #TransactionAccountIdDebt').val(),$('input[name="data[Debt][state]"]:checked').val(),$('#DebtDoForm #DebtDoneAmount').val());
            } else {
                jeeb.DebtDone(that.attr('href'),'',0,$('input[name="data[Debt][state]"]:checked').val(),$('#DebtDoForm #DebtDoneAmount').val());
            }
            location.reload();
        });
        $("#DebtDoneConfirm").modal();

    });
    $('#DebtStateAll').click( function() {
        $('#DebtDoneAmount').attr('disabled', 'disabled');
    });
    $('#DebtStatePart').click( function() {
        $('#DebtDoneAmount').attr('disabled', false);
    });
    $('#DebtAddExpenseYes').click( function() {
        $('#TransactionAccountIdDebt').attr('disabled', false);
    });
    $('#DebtAddExpenseNo').click( function() {
        $('#TransactionAccountIdDebt').attr('disabled', 'disabled');
    });
    //mark credit as done
    $('.do.credit').click(function () {
        that=$(this);
        $('#CreditDoForm')[0].reset();
        $("#CreditDoneConfirm-submit").unbind('click');
        $("#CreditDoneConfirm-submit").on('click',function (e) {
            if($('input[name="data[Debt][addIncome]"]:checked').val()=='yes') {
                jeeb.DebtDone(that.attr('href'),true,$('#CreditDoForm #TransactionAccountIdCredit').val(),$('input[name="data[Credit][state]"]:checked').val(),$('#CreditDoForm #CreditDoneAmount').val());
            } else {
                jeeb.DebtDone(that.attr('href'),'',0,$('input[name="data[Credit][state]"]:checked').val(),$('#CreditDoForm #CreditDoneAmount').val());
            }
            location.reload();
        });
        $("#CreditDoneConfirm").modal();

    });
    $('#CreditStateAll').click( function() {
        $('#CreditDoneAmount').attr('disabled', 'disabled');
    });
    $('#CreditStatePart').click( function() {
        $('#CreditDoneAmount').attr('disabled', false);
    });
    $('#DebtAddIncomeYes').click( function() {
        $('#TransactionAccountIdCredit').attr('disabled', false);
    });
    $('#DebtAddIncomeNo').click( function() {
        $('#TransactionAccountIdCredit').attr('disabled', 'disabled');
    });
    
    
    categories = <?php echo json_encode(empty($tags)? array() : $tags); ?>;
    report_catlist = <?php echo json_encode(empty($report_catlist)? array() : $report_catlist); ?>;
    floatingList = new FloatingList({
        input: '#DebtTagTagId', 
        listholder: '#groupListHolder', 
        data: categories,
        allowNew: true,
        empty: false
    });
    floatingList2 = new FloatingList({
        holderId: 'CategoryListHolderSearch',
        listId:'CategoryListSearch',
        input: '#DebtTagIdSearch', 
        listholder: '#groupListHolderSearch',
        preload: report_catlist,
        data: categories
    });
    
});
//]]>
</script>