<div class="col-xs-16 col-md-6 box rounded">
    <h2>حساب جدید</h2>
    <div id="new" class="accounts form">
    <?php echo $this->Form->create('Account'); ?>
    <fieldset>
            <?php echo $this->Form->label('name','عنوان حساب'); ?><br/>
            <?php echo $this->Form->error('name'); ?>
            <?php echo $this->Form->text('name'); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'AccountNameTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('balance','موجودی (ریال)'); ?><br/>
            <?php echo $this->Form->error('balance'); ?>
            <?php echo $this->Form->text('balance' , array( 'style' => 'direction:ltr;' )); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'AccountBalanceTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('type','نوع حساب'); ?><br/>
            <?php echo $this->Form->error('type'); ?>
            <?php echo $this->Form->select('type',array('deposit'=>'پس انداز','check'=>'جاری','cash'=>'نقدی','other'=>'سایر'),null,array('empty'=>false)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'AccountTypeTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('bank_id','بانک'); ?><br/>
            <?php echo $this->Form->error('bank_id'); ?>
            <?php echo $this->Form->select('bank_id',$banks,null,array('empty'=>true)); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'AccountBankTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('description','توضیحات'); ?><br/>
            <?php echo $this->Form->textArea('description',array('style'=>'width:97%;')); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'AccountTypeTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('انتقال وجه بین حساب‌ها',array('controller'=>'transactions','action'=>'index'));?></span>
    </fieldset>
    <?php echo $this->Form->end('ثبت');?>
    </div>

</div>

<div class="col-xs-16 col-md-10" >
    <div align="center" id="AccountPieChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 class="col-xs-16 col-md-3" id="page-heading">لیست حساب‌ها <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'exportaccounts'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array('','عنوان حساب','موجودی','نوع حساب','بانک','توضیحات','تاریخ ایجاد','وضعیت','عملیات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php
	$i = 0;
	foreach ($accounts as $account):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
                <td>
                    <?php echo $this->Html->link('<i class="fa fa-sort-up"></i>', array('action' => 'sort', 'up'=>$account['Account']['sort'],'#'=>'#dataTable'),array('escape'=>false,'class'=>'up')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-sort-down"></i>', array('action' => 'sort', 'down'=>$account['Account']['sort'],'#'=>'#dataTable'),array('escape'=>false,'class'=>'down')); ?>
                </td>
		<td><?php echo $account['Account']['name']; ?>&nbsp;</td>
                <td style="color:<?php if($account['Account']['balance']>0){echo 'green';}else{echo '#C62121';} ?>;direction:ltr;"><?php if($account['Account']['balance']>0){ echo '+';} echo number_format($account['Account']['balance']); ?>&nbsp;</td>
		<td><?php __($account['Account']['type']); ?>&nbsp;</td>
		<td><?php echo $account['Bank']['name']; ?>&nbsp;</td>
                <td><?php echo $account['Account']['description']; ?>&nbsp;</td>
		<td><?php echo $account['Account']['created']; ?>&nbsp;</td>
                <td style="<?php if($account['Account']['status']=='inactive'){echo 'color:#C62121;';} ?>; width:80px;">
                    <?php if($account['Account']['delete']=='yes'): ?>
                        <?php echo $this->Html->link(
                                '<i class="'.(($account['Account']['status']=='inactive')? 'fa fa-lightbulb-o' : 'fa fa-lightbulb-o').'"></i>'.'&nbsp;'.__($account['Account']['status'],true),
                                array('action' => 'toggleshow', $account['Account']['id']),array('escape'=>false,'class'=>'showinlist')
                                ); ?>
                    <?php else: ?>
                        <?php echo '<i class="fa fa-lightbulb-o"></i>'.'&nbsp;'.__($account['Account']['status'],true); ?>
                    <?php endif; ?>
                </td>
                <td style="width:160px">
                    <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $account['Account']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $account['Account']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟ با پاک کردن این حساب تمام تراکنشها، هزینه‌ها و درآمدهای مربوط به آن حذف میشوند.', $account['Account']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-bar-chart"></i>', array('action' => 'view', $account['Account']['id']),array('escape'=>false,'class'=>'view')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-refresh"></i>', array('action' => 'balance', $account['Account']['id']),array('escape'=>false,'class'=>'balance')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
		</td>
	</tr>
<?php endforeach; ?>
        <tr>
            <td><b>جمع کل</b></td>
            <td colspan="8" style="color:<?php if($sum>0){echo 'green';}else{echo '#C62121';} ?>;direction:ltr;"><b><?php if($sum>0){echo '+';} echo number_format($sum); ?></b></td>
        </tr>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>   </table></div>
    
	<?php echo $this->element('pagination/bottom'); ?>
		
</div>
<div class="clear"></div>
<?php echo $this->Chart->pie('AccountPieChart','تقسیم موجودی',$pieData); ?>

<script type="text/javascript">
//<![CDATA[
$(function(){
    //number format
    jeeb.FormatPrice($('#AccountBalance'));
    //tips
    jeeb.tip($('.showinlist'),'60','نمایش در لیست');
    jeeb.tip($('.hidefromlist'),'75','عدم نمایش در لیست');
    jeeb.tip($('.up'),'50','اولویت بالاتر');
    jeeb.tip($('.down'),'50','اولویت پایینتر');
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
    jeeb.tip($('.view'),'127','نمایش اطلاعات حساب');
    jeeb.tip($('.balance'),'90','بالانس حساب');
    jeeb.tip($('#AccountNameTip'),'200','یک عنوان برای حساب خود برگزینید.');
    jeeb.tip($('#AccountBalanceTip'),'200','موجودی حساب خود را وارد کنید.<br/>با افزودن یک هزینه به این حساب از موجودی کاسته و با افزودن درآمد به موجودی اضافه میگردد.<br/>زمانی که مبلغی به حساب واریز میکنید نیز میتوانید از منوی تراکنش‌ها و فرم انتقال وجه موجودی را بروز کنید.');
    jeeb.tip($('#AccountBankTip'),'200','اگر حساب شما نزد یک بانک است، بانک مورد نظر را انتخاب کنید در غیر اینصورت میتوانید این گزینه را خالی بگذارید.');
    jeeb.tip($('#AccountTypeTip'),'200','نوع حساب خود را برگزینید.<br/><b>حساب پس انداز</b><br/>حساب پس انداز حسابی است که پول در آن پس انداز میشود. اکثر حساب‌های بانکی به غیر از حساب‌های جاری، حساب پس انداز محسوب میشوند. حسابهای سرمایه گذاری کوتاه مدت و بلند مدت هم نوعی حساب پس انداز هستند.<br/><b>حساب جاری</b><br/>حساب جاری حسابی است که میتوان از طریق آن چک صادر نمود.<br/><b>حساب نقدی</b><br/>منظور از حساب نقدی مبالغی است که شما بصورت نقد نزد خود دارید. جیب شما یک حساب نقدی است. هر کاربر در جیب به صورت پیشفرض یک حساب به نام جیب دارد.');
});
//]]>
</script>
<?php if(isset($setup) AND $setup): ?>
<div id="firstsetup" title="به جیب خوش آمدید">
    <?php echo $this->Form->create('Account',array('id'=>'saveInitBalance')); ?>
    <br/>
    <div>به جیب خوش آمدید، برای شروع کارهای زیر را بایستی انجام دهید:</div>
    <br/>
    
    <ul>
        <li style="list-style-type:none;padding:5px;font-size:110%;background-color:#FCF8E3;border: 1px solid #FBEED5;color:#C09853;text-shadow:0 1px 0 rgba(255, 255, 255, 0.5);border-radius:4px;">
            <span style="font-size:150%;">۱.</span>
            <span>همین حالا موجودی جیب خود را بشمارید و وارد کنید.</span>
        </li>
        <br/>
        
        <li style="list-style-type:none;padding:5px;font-size:110%;background-color:#D9EDF7;border: 1px solid #BCE8F1;color:#3A87AD;text-shadow:0 1px 0 rgba(255, 255, 255, 0.5);border-radius:4px;">
            <span style="font-size:150%;">۲.</span>
            <span>تمام حسابهای خود را همراه با موجودی آنها وارد کنید.</span>
        </li>
        <br/>
        
        <li style="list-style-type:none;padding:5px;font-size:110%;background-color:#DFF0D8;border: 1px solid #D6E9C6;color:#468847;text-shadow:0 1px 0 rgba(255, 255, 255, 0.5);border-radius:4px;">
            <span style="font-size:150%;">۳.</span>
            <span>اکنون شما میتوانید هزینه‌ها و درآمدهای خود را ثبت کنید.</span>
        </li>
        <br/>
        
    </ul>
    <br/>
    
    <div style="list-style-type:none;padding:5px;background-color:#F7F7F9;border: 1px solid #E1E1E8;color:#333333;text-shadow:0 1px 0 rgba(255, 255, 255, 0.5);border-radius:4px;">
        <ul>
            <li>نکته: در این سیستم برای نگهداری میزان موجودی نقد شما یک حساب بنام «جیب» تعریف شده است.</li>
            <li>نکته: در صورتی که مایل به ورود حسابهای خود نیستید میتوانید کل موجودی خود را نقد و در داخل حساب جیب در نظر بگیرید.</li>
        </ul>
    </div>
    <br/>
    <?php echo $this->Form->label('init_balance','کل موجودی نقد شما در حال حاضر چند ریال است؟'); ?>
    <?php echo $this->Form->text('init_balance',array('value'=>'0')); ?> ریال<br/>
    <?php echo $this->Form->end();?>
</div>
<script type="text/javascript">
//<![CDATA[
$(function(){
    //mark received check as done
    jeeb.FormatPrice($('#AccountInitBalance'));
    //prevent enter submit
    $('#AccountInitBalance').keypress(function(e){
        if ( e.which == 13 ) e.preventDefault();
    });
    //modal
    $( "#firstsetup" ).dialog({
            resizable: false,
            open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
            position: 'top',
            width:650,
            height:450,
            modal: true,
            closeOnEscape: false,
            buttons: {
                    "ثبت": function() {
                            $.ajax({
                                type: "POST",
                                url: '<?php echo $this->Html->url(array('controller'=>'accounts','action'=>'ajaxSaveInitBalance',$jeeb_account_id))?>',
                                data: {
                                    'init_balance':$('#saveInitBalance #AccountInitBalance').val()
                                },
                                dataType: "json",
                                async: false,
                                error:function (XMLHttpRequest, textStatus, errorThrown){
                                    alert('مشکلی در ارتباط با سرور پیش آمد لطفا دوباره تلاش کنید.');
                                }
                            });
                            $( this ).dialog( "close" );
                            location.reload(true);
                    }
            }
    });
});
//]]>
</script>
<?php endif; ?>
