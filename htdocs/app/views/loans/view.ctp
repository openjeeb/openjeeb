<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('وام‌ها', array('action' => 'index')); ?></li>
                <li><?php echo $this->Html->link('ویرایش', array('action' => 'edit',$loan['Loan']['id'])); ?></li>
                <li><?php echo $this->Html->link('پاک کردن', array('action' => 'delete',$loan['Loan']['id']), array('escape'=>false,'alt'=>'حذف'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟ در صورت پاک کردن این وام تمامی تراکنشهای مربوط به اقساط آن هم حذف خواهند شد.', $loan['Loan']['id'])); ?></li>
                <li><?php echo $this->Html->link('افزودن قسط به این وام', array('controller'=>'installments','action' => 'add',$loan['Loan']['id'])); ?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
            <tr>
                <th colspan="2" class="centered">
                    <h2>جزئیات وام</h2>
                </th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <th>عنوان وام</th>
                    <td><?php echo $loan['Loan']['name']; ?></td>
                </tr>
                <tr>
                    <th>توضیحات</th>
                    <td><?php echo $loan['Loan']['description']; ?>&nbsp;</td>
                </tr>
                <tr>
                    <th>موسسه</th>
                    <td> <?php echo $loan['Bank']['name']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">لیست اقساط <?php echo $loan['Loan']['name']; ?>&nbsp;<?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('controller'=>'installments','action'=>'export',$installments[0]['Installment']['loan_id']),array('escape' => false,'id'=>'excelExport')); ?></h2>

    
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array('ردیف','وضعیت','مبلغ','تاریخ سر رسید','توضیحات','آگاه سازی','عملیات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php $i=1;foreach ($installments as $installment): ?>
	<tr>
		<td><?php echo $i; ?>&nbsp;</td>
                <td><?php if($installment['Installment']['status']=='due'){ echo $this->Html->image('due.png',array('class'=>'due','alt'=>__($installment['Installment']['status'],true))); }else{ echo $this->Html->image('done.png',array('class'=>'done','alt'=>__($installment['Installment']['status'],true))); } ?>&nbsp;<?php __($installment['Installment']['status']); ?></td>
		<td><?php echo number_format($installment['Installment']['amount']); ?>&nbsp;</td>
		<td><?php echo $installment['Installment']['due_date']; ?>&nbsp;</td>
                <td><?php echo $installment['Installment']['description']; ?>&nbsp;</td>
		<td><?php __($installment['Installment']['notify']); ?>&nbsp;</td>
		<td style="width: 160px">
            <?php if($installment['Installment']['status']=='due'){echo $this->Html->link('<i class="fa fa-check"></i>', array('controller'=>'installments', 'action' => 'ajaxInstallmentDone', $installment['Installment']['id']),array('escape'=>false,'class'=>'InstallmentDone','onclick'=>'return false','class'=>'do'));}else{ echo $this->Html->image('blank.png',array('alt'=>'')); } ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('controller'=>'installments', 'action' => 'edit', $installment['Installment']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('controller'=>'installments', 'action' => 'delete', $installment['Installment']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟ در صورت پاک کردن این قسط تراکنش‌های مربوط به آن نیز حذف خواهد شد.', $installment['Installment']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo $this->Html->link('<i class="fa fa-bell"></i>', array('controller'=>'reminders','action' => 'view', 'installment'=>$installment['Installment']['id']),array('escape'=>false,'class'=>'reminder','alt'=>'یادآور')); ?>
		</td>
	</tr>
<?php $i++;endforeach; ?>
	<tr>
            <td colspan="2" style="text-align:left"><b>جمع وام‌های تسویه شده</b></td>
            <td style="color:green"><?php echo number_format($settledInstallments[0]['sum']); ?></td>
            <td colspan="2" style="text-align:left"><b>جمع وام‌های تسویه نشده</b></td>
            <td style="color:#C62121;"><?php echo number_format($unsettledInstallments[0]['sum']); ?></td>
            <td></td>
	</tr>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
</div>
<div class="clear"></div>
<div id="InstallmentDoneConfirm" title="پرداخت قسط">
    <?php echo $this->Form->create('Installment',array('id'=>'InstallmentDoForm')); ?>
    <span>مبلغ این قسط را از کدام حساب پرداخت کردید؟</span>
    <br/><br/>
    <?php echo $this->Form->label('Transaction.account_id','برداشت از حساب'); ?>
    <?php echo $this->Form->select('Transaction.account_id',$accounts,null,array('empty'=>false)); ?><br/>
    <?php echo $this->Form->label('AccountIdBalance','موجودی:'); ?>&nbsp;
    <?php echo $this->Html->tag('span','&nbsp;', array('id'=>'AccountBalance')); ?><br /><br />
    <?php echo $this->Form->end();?>
</div>
<script type="text/javascript">
//<![CDATA[
balances = <?php echo json_encode($accountsbalance); ?>;
$(function(){
    jeeb.accountBalance(balances, $('#TransactionAccountId'), $('#AccountBalance'));
    $('#TransactionAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalance'); });
    //tips
    jeeb.tip($('#excelExport'),'70','خروجی اکسل');
    jeeb.tip($('.due'),'58','تسویه نشده');
    jeeb.tip($('.done'),'57','تسویه شده');
    jeeb.tip($('.do'),'60','تسویه قسط');
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.reminder'),'60','یادآورها');
    jeeb.tip($('.delete'),'70','پاک کردن');
    //mark installment as done
    $('.do').click(function () {
        that=$(this);
        $( "#InstallmentDoneConfirm" ).dialog({
                resizable: false,
                position: 'center',
                height:180,
                modal: true,             
                buttons: {
                        "ثبت": function() {
                                jeeb.InstallmentDone(that.attr('href'),$('#InstallmentDoForm #TransactionAccountId').val());
                                $( this ).dialog( "close" );
                                location.reload();
                        },
                        "لغو": function() {
                                $( this ).dialog( "close" );
                                location.reload();
                        }
                }
        });
    });
});
//]]>
</script>