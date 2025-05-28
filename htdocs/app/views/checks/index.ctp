<?php
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
?>
<div class="col-xs-16 col-md-6 box rounded">
    <h2>ثبت چک</h2>
    <div class="checks form">
    <?php echo $this->Form->create('Check'); ?>
    <fieldset>
            <?php echo $this->Form->label('Check.type','نوع چک:'); ?>&nbsp;&nbsp;
            <?php echo $this->Form->radio('Check.type',array('drawed'=>'صادر شده','received'=>'دریافتی'),array('value'=>'drawed','legend'=>false,'separator'=>'&nbsp;')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'CheckTypeTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br style="margin-bottom: 10px;"/>
            
            <?php echo $this->Form->label('Check.individual_id','صادر شده توسط',array('id'=>'CheckReceivedIndividualLabel')); ?>
            <?php echo $this->Form->label('Check.individual_id','شخص دریافت کننده',array('id'=>'CheckDrawedIndividualLabel')); ?>
            &nbsp;
            <?php echo $this->Form->error('Check.individual_id'); ?>
            <?php echo $this->Form->select('Check.individual_id',$individuals,null,array('empty'=>true)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'CheckRecievedIndividualTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'CheckDrawedIndividualTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <span id="AccountWrapper">
            <?php echo $this->Form->label('Check.account_id','صادر شده از حساب جاری'); ?>&nbsp;
            <?php echo $this->Form->error('Check.account_id'); ?>
            <?php echo $this->Form->select('Check.account_id',$accounts,null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'CheckAccountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br />
            <?php echo $this->Form->label('AccountIdBalance','موجودی:'); ?>&nbsp;
            <?php echo $this->Html->tag('span','<span></span>', array('id'=>'AccountBalance')); ?>
            <br />
            </span>
                        
            <span id="BankWrapper" style="display: none;">
            <?php echo $this->Form->label('Check.bank_id','بانک'); ?>
            <?php echo $this->Form->error('Check.bank_id'); ?>
            <?php echo $this->Form->select('Check.bank_id',$banks,null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'CheckBankTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            </span>
            
            <br/>
            
            <?php echo $this->Form->label('Check.amount','مبلغ (ریال)'); ?><br/>
            <?php echo $this->Form->error('Check.amount'); ?>
            <?php echo $this->Form->text('Check.amount',array('maxlength'=>15,'style'=>'direction:ltr;')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'CheckAmountTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Check.due_date','موعد چک'); ?><br/>
            <?php echo $this->Form->error('Check.due_date'); ?>
            <?php echo $this->Form->text('Check.due_date',array('class'=>'datepicker','value'=>$persianDate->pdate('Y/m/d'))); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'CheckDueDateTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
                        
            <?php echo $this->Form->label('Check.serial','سریال چک'); ?><br/>
            <?php echo $this->Form->error('Check.serial'); ?>
            <?php echo $this->Form->text('Check.serial',array('maxlength'=>16)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'CheckSerialTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Check.notify','یادآور'); ?>
            <?php echo $this->Form->error('Check.notify'); ?>
            <?php echo $this->Form->select('Check.notify',array('yes'=>'فعال','no'=>'غیر فعال'),null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'CheckNotifyTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <div class="clear">&nbsp;</div>
            
            <div class="input text">
                <label for="CheckCheckCategoryId" style="width:60px;">برچسب: </label>
                <div>
                    <?php echo $this->Form->text('CheckTag.tag_id',array('value'=>'','autocomplete'=>'off', 'style'=>'width:210px')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'TagCategoryTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                    <div id="groupListHolder"></div>
                </div>
            </div>
            <div class="clear">&nbsp;</div>
            
            <?php echo $this->Form->label('Check.description','توضیحات'); ?><br/>
            <?php echo $this->Form->error('Check.description'); ?>
            <?php echo $this->Form->textArea('Check.description',array('style'=>'width:98%;')); ?><br/>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت حساب‌ها',array('controller'=>'accounts','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت اشخاص',array('controller'=>'individuals','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت برچسب‌ها',array('controller'=>'tags','action'=>'index'));?></span>
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>
</div>

<div class="col-xs-16 col-md-10">
    <div id="CheckColumnChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <div class="box">
        <h2><?php echo $this->Html->link('جستجو', '#',array('id'=>'toggle-filter','class'=>'hidden')); ?></h2>
        <div id="filter" class="form">
            <?php echo $this->Form->create('Check'); ?>
            <fieldset>
                <?php
                echo $this->Form->input('Check.search',array('type'=>'hidden','value'=>true));
                echo $this->Form->input('Check.type',array('label'=>'نوع چک','type'=>'select','options'=>array('all'=>'همه','received'=>'دریافتی','drawed'=>'صادر شده')));
                echo $this->Form->input('Check.status',array('label'=>'وضعیت چک','type'=>'select','options'=>array('all'=>'همه','due'=>'تسویه نشده','done'=>'تسویه شده')));
                echo $this->Form->input('Check.bank_id',array('label'=>'بانک','type'=>'select','options'=>$banks,'empty'=>true));
                echo $this->Form->input('Check.account_id',array('label'=>'حساب','type'=>'select','options'=>$accounts,'empty'=>true));
                echo $this->Form->input('Check.individual_id',array('label'=>'شخص','type'=>'select','options'=>$individuals,'empty'=>true));
                echo $this->Form->input('Check.amount',array('label'=>'مبلغ','type'=>'text','maxlength'=>15,'id'=>'CheckAmountSearch','style'=>'direction:ltr;')); ?>
                <div class="input text">
                    <label>از تاریخ</label>
                    <div style='float:right;'><?php echo $this->Form->text('Check.start_date',array('class'=>'datepicker','value'=>'')); ?></div>
                    <?php echo $this->element('filldate', array( 'start_date' => '#CheckStartDate', 'end_date' => '#CheckEndDate', 'oneline'=>true )); ?>
                </div>
                <?php
                echo $this->Form->input('Check.end_date',array('label'=>'تا تاریخ','class'=>'datepicker','value'=>'')); ?>

                <div class="clear">&nbsp;</div>

                <div class="input text">
                    <label for="CheckCheckCategoryId">برچسب: </label>
                    <div>
                        <?php echo $this->Form->text('CheckTagSearch.tag_id',array('value'=>'','autocomplete'=>'off', 'id'=> 'CheckTagIdSearch')); ?>
                        <div id="groupListHolderSearch"></div>
                    </div>
                </div>

                <div class="clear">&nbsp;</div>
                <?php
                echo $this->Form->input('Check.description_search',array('label'=>'توضیحات','type'=>'text','value'=>''));
                ?>
            </fieldset>
            <?php echo $this->Form->end('جستجو');?>
        </div>
    </div>

</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 class="col-xs-16 col-md-3" id="page-heading">لیست چک‌ها  <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array(
        $paginator->sort('وضعیت','status',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('نوع چک','type',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('مبلغ (ریال)','amount',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('حساب','account_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('شخص','individual_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('بانک','bank_id',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('موعد چک','due_date',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('سریال','serial',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('تاریخ ایجاد','created',array('url'=>array('#'=>'#dataTable'))),
        $paginator->sort('توضیحات','description',array('url'=>array('#'=>'#dataTable'))),
        'عملیات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php foreach ($checks as $check): ?>
	<tr>
		<td><?php if($check['Check']['status']=='due'){ echo '<i class="fa fa-square-o due"></i>'; }else{ echo '<i class="fa fa-check-square-o done"></i>'; } ?></td>
		<td><?php __($check['Check']['type']); ?>&nbsp;</td>
                <td style="direction: ltr;<?php if($check['Check']['type']=='drawed'): ?>color:#C62121;<?php else: ?>color:green;<?php endif; ?>"><?php if($check['Check']['amount']>0){echo '+';} echo number_format($check['Check']['amount']); ?></td>
		<td><?php if(!($check['Check']['type']=='received' AND $check['Check']['status']=='due')) {echo $check['Account']['name'];} ?></td>
		<td><?php echo $check['Individual']['name']; ?></td>
		<td><?php echo $check['Bank']['name']; ?></td>
		<td><?php echo $check['Check']['due_date']; ?>&nbsp;</td>
		<td><?php echo $check['Check']['serial']; ?>&nbsp;</td>
		<td><?php echo end(explode(' ',$check['Check']['created'])); ?></td>
		<td><?php echo str_replace('\n', "<br />", $check['Check']['description']); ?>&nbsp;</td>
		<td style="width: 150px">
            <?php 
                if($check['Check']['status']=='due') {
                    if($check['Check']['type']=='drawed') {
                        echo $this->Html->link('<i class="fa fa-check"></i>', array('action' => 'drawedcheckdDone', $check['Check']['id']),array('escape'=>false,'class'=>'do '.$check['Check']['type']));
                    } else {
                        echo $this->Html->link('<i class="fa fa-check"></i>', array('action' => 'ajaxCheckDone', $check['Check']['id']),array('escape'=>false,'class'=>'do '.$check['Check']['type'],'onclick'=>'return false'));
                    }
                } else { 
                    echo $this->Html->image('blank.png',array('alt'=>'')); 
                } 
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $check['Check']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $check['Check']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟ با پاک کردن این چک تراکنش‌های مربوط به آن هم حذف میشوند.', $check['Check']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $this->Html->link('<i class="fa fa-bell"></i>', array('controller'=>'reminders','action' => 'view', 'check'=>$check['Check']['id']),array('escape'=>false,'class'=>'reminder','alt'=>'یادآور')); ?>
		</td>
	</tr>
<?php endforeach; ?>
        <tr>
            <td colspan="3" style="text-align:left;"><b>جمع کل چکهای صادره</b></td>
            <td style="direction: ltr;color:#C62121;"><b><?php echo number_format($drawedSum); ?></b></td>
            <td></td>
            <td colspan="3" style="text-align:left;"><b>جمع کل چکهای دریافتی</b></td>
            <td colspan="3" style="direction: ltr;color:green;"><b>+<?php echo number_format($receivedSum); ?></b></td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:left;"><b>جمع چکهای صادره تسویه نشده</b></td>
            <td style="direction: ltr;color:#C62121;"><b><?php echo number_format($drawedDueSum); ?></b></td>
            <td></td>
            <td colspan="3" style="text-align:left;"><b>جمع چکهای دریافتی تسویه نشده</b></td>
            <td colspan="3" style="direction: ltr;color:green;"><b><?php echo number_format($receivedDueSum); ?></b></td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:left;"><b>جمع چکهای صادره تسویه شده</b></td>
            <td style="direction: ltr;color:green;"><?php echo number_format(abs($drawedDoneSum)); ?></td>
            <td></td>
            <td colspan="3" style="text-align:left;"><b>جمع چکهای دریافتی تسویه شده</b></td>
            <td colspan="3" style="direction: ltr;color:green;"><b><?php echo number_format($receivedDoneSum); ?></b></td>
        </tr>
<?php echo '<tfoot>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
    
    <?php echo $this->element('pagination/bottom'); ?>
		
</div>
<div class="clear"></div>

<?php echo $this->Chart->doubleColumn('CheckColumnChart','چکهای ماهانه','چکهای دریافتی ',$receivedChecksColumn,'چکهای صادره',$drawedChecksColumn,6,580,500); ?>
<!-- Modal -->
<div id="RecievedCheckDoneConfirm" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close pull-left" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">دریافت چک</h4>
            </div>
            <div class="modal-body">
                <?php echo $this->Form->create('Check',array('id'=>'CheckDoForm')); ?>
                <span>مبلغ این چک دریافتی به کدام حساب واریز شد؟</span>
                <br/><br/>
                <?php echo $this->Form->label('Transaction.account_id','واریز به حساب'); ?>
                <?php echo $this->Form->select('Transaction.account_id',$allAccounts,null,array('empty'=>false)); ?><br/>
                <?php echo $this->Form->label('AccountIdBalance','موجودی:'); ?>&nbsp;
                <?php echo $this->Html->tag('span','&nbsp;', array('id'=>'AccountBalanceTo')); ?>
                <?php echo $this->Form->end();?>
            </div>
            <div class="modal-footer">
                <button type="button" id="RecievedCheckDoneConfirm-submit" class="btn btn-success" data-dismiss="modal">ثبت</button>
                <button type="button" id="RecievedCheckDoneConfirm-cancel" class="btn btn-warning" data-dismiss="modal">لغو</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
//<![CDATA[
var balances = <?php echo json_encode($allAccountsbalance); ?>;
$(function(){
    jeeb.tip($('#CheckNotifyTip'),'200','در صورتی که بله را انتخاب کنید، ایمیل‌های یادآوری را قبل از تاریخ موعد دریافت خواهید کرد.');
    //show/hide things!
    jeeb.accountBalance(balances, $('#CheckAccountId'), $('#AccountBalance > span'));
    $('#CheckAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalance > span'); });
    jeeb.accountBalance(balances, $('#TransactionAccountId'), $('#AccountBalanceTo'));
    $('#TransactionAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalanceTo'); });
    
    if($('input[name="data[Check][type]"]:checked').val()=='drawed') {
        $("#BankWrapper").hide();
        $("#CheckReceivedIndividualLabel").hide();
        $("#CheckRecievedIndividualTip").hide();
        $("#AccountWrapper").show();
        $("#CheckDrawedIndividualLabel").show();
        $("#CheckDrawedIndividualTip").show();
    }
    if($('input[name="data[Check][type]"]:checked').val()=='received') {
        $("#AccountWrapper").hide();
        $("#CheckDrawedIndividualLabel").hide();
        $("#CheckDrawedIndividualTip").hide();
        $("#BankWrapper").show();
        $("#CheckReceivedIndividualLabel").show();
        $("#CheckRecievedIndividualTip").show();
    }
    $('input[name="data[Check][type]"]', '#CheckIndexForm').change(function(){
        if($('input[name="data[Check][type]"]:checked').val()=='drawed') {
            $("#BankWrapper").hide();
            $("#CheckReceivedIndividualLabel").hide();
            $("#CheckRecievedIndividualTip").hide();
            $("#AccountWrapper").show();
            $("#CheckDrawedIndividualLabel").show();
            $("#CheckDrawedIndividualTip").show();
        }
        if($('input[name="data[Check][type]"]:checked').val()=='received') {
            $("#AccountWrapper").hide();
            $("#CheckDrawedIndividualLabel").hide();
            $("#CheckDrawedIndividualTip").hide();
            $("#BankWrapper").show();
            $("#CheckReceivedIndividualLabel").show();
            $("#CheckRecievedIndividualTip").show();
        }
    });
    //number format
    jeeb.FormatPrice($('#CheckAmount'));
    jeeb.FormatPrice($('#CheckAmountSearch'));
    //tips
    jeeb.tip($('#excelExport'),'70','خروجی اکسل');
    jeeb.tip($('.due'),'58','تسویه نشده');
    jeeb.tip($('.done'),'57','تسویه شده');
    jeeb.tip($('.do'),'80','تسویه چک');
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
    jeeb.tip($('.reminder'),'60','یادآورها');
    jeeb.tip($('#CheckTypeTip'),'110','نوع چک را انتخاب کنید.');
    jeeb.tip($('#CheckAmountTip'),'160','مبلغ چک را در این بخش وارد کنید.');
    jeeb.tip($('#CheckAmountSearch'),'160','مبلغ چک را در این بخش وارد کنید.');
    jeeb.tip($('#CheckDueDateTip'),'180','تاریخ موعد چک را در این بخش وارد کنید.');
    jeeb.tip($('#CheckAccountTip'),'200','حسابی را که این چک مربوط به آن است انتخاب کنید.<br/>دقت کنید که در این قسمت تنها حسابهای از نوع جاری نمایش داده میشوند.<br/>در صورتی که حساب جاری مربوط را ایجاد نکرده‌اید از منوی حساب‌ها میتوانید آن را ایجاد کنید.');
    jeeb.tip($('#CheckSerialTip'),'200','شماره سریال چک را در این بخش وارد کنید.');
    jeeb.tip($('#CheckRecievedIndividualTip'),'200','میتوانید شخص صادر کننده این چک را مشخص کنید.<br/>برای مدیریت اشخاص از منوی اشخاص استفاده کنید.');
    jeeb.tip($('#CheckDrawedIndividualTip'),'200','میتوانید شخص دریافت کننده چک را مشخص کنید.<br/>برای مدیریت اشخاص از منوی اشخاص استفاده کنید.');
    jeeb.tip($('#TagCategoryTip'),'200','شما میتوانید با وارد کردن برچسبی که مورد نظرتان است و یا انتخاب آن از لیست این مورد را برچسب دار کنید. شما میتوانید بیش از یک برچسب به یک مورد متصل کنید.');
    //mark received check as done
    $('.do.received').click(function () {
        that=$(this);
        $("#RecievedCheckDoneConfirm-submit").unbind('click');
        $("#RecievedCheckDoneConfirm-submit").on('click',function (e) {
            jeeb.CheckDone(that.attr('href'),$('#CheckDoForm #TransactionAccountId').val());
            location.reload(true);
        });
        $("#RecievedCheckDoneConfirm").modal();
    });
    
    categories = <?php echo json_encode(empty($tags)? array() : $tags); ?>;
    report_catlist = <?php echo json_encode(empty($report_catlist)? array() : $report_catlist); ?>;
    floatingList = new FloatingList({
        input: '#CheckTagTagId', 
        listholder: '#groupListHolder', 
        data: categories,
        allowNew: true,
        empty: false
    });
    floatingList2 = new FloatingList({
        holderId: 'CategoryListHolderSearch',
        listId:'CategoryListSearch',
        input: '#CheckTagIdSearch', 
        listholder: '#groupListHolderSearch',
        preload: report_catlist,
        data: categories
    });
    
});
//]]>
</script>