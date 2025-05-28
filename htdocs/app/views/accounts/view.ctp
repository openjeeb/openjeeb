<div class="col-xs-16 col-md-4">
    <div class="box">
        <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('حساب‌ها', array('action' => 'index')); ?></li>
                <li><?php echo $this->Html->link('ویرایش', array('action' => 'edit',$account['Account']['id'])); ?></li>
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
                    <h2>حساب</h2>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>عنوان حساب</th>
                <td> <?php echo $account['Account']['name']; ?></td>
            </tr>
            <tr>
                <th>توضیحات</th>
                <td> <?php echo $account['Account']['description']; ?></td>
            </tr>
            <tr>
                <th style="direction: ltr;text-align: right;color:<?php if($account['Account']['balance']>0){echo 'green';}else{echo '#C62121';} ?>">موجودی</th>
                <td>
                    <?php echo number_format($account['Account']['balance']); ?>
                </td>
            </tr>
            <tr>
                <th>موجودی اولیه</th>
                <td><?php echo number_format($account['Account']['init_balance']); ?> </td>
            </tr>
            <tr>
                <th>نوع حساب</th>
                <td><?php __($account['Account']['type']); ?> </td>
            </tr>
            <tr>
                <th>تاریخ ایجاد</th>
                <td>  <?php echo $account['Account']['created']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="clear"></div>

<div style="margin-right:20px;">
    مشاهده حساب: <?php echo $this->Form->select('Account.current', $accounts, $account['Account']['id'], array( 'empty'=>false, 'style'=>'width:350px', 'id'=>'gotoaccount' )); ?>
</div>

<div class="clear"></div>
<div class="col-xs-16 col-md-16">
    <div align="center" id="AccountLineChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<br/>
<div class="col-xs-16 col-md-16">
    <h2 id="page-heading"> تراکنشهای حساب <?php echo $account['Account']['name']; ?> <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export',$account['Account']['id']),array('escape' => false,'id'=>'excelExport')); ?></h2>
    
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array(
        'نوع',
        $paginator->sort('تاریخ','date',array('url'=>array('#'=>'#dataTable'))),
        'هزینه',
        'درآمد',
        'نوع هزینه/درآمد',
        'توضیحات',
        $paginator->sort('حساب','account_id',array('url'=>array('#'=>'#dataTable'))),
        'شخص',
        $paginator->sort('تاریخ ایجاد','created',array('url'=>array('#'=>'#dataTable'))),
        'عملیات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

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
		<td style="color:#C62121;direction:ltr;">
                    <?php
                        if($transaction['Transaction']['type']=='debt') {
                            echo '-'.number_format($transaction['Transaction']['amount']);
                        }
                    ?>&nbsp;
                </td>
		<td style="color:green;direction:ltr;">
                    <?php
                        if($transaction['Transaction']['type']=='credit') {
                            echo '+'.number_format($transaction['Transaction']['amount']);
                        }
                    ?>&nbsp;
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
                    <?php
                        if($transaction['Transaction']['expense_id']) {
                            echo $transaction['Expense']['description'];
                        } elseif($transaction['Transaction']['income_id']) {
                            echo $transaction['Income']['description'];
                        } elseif (isset($transaction['Transfer']['id'])) {
                            echo (($transaction['Transaction']['type']=='debt')? "انتقال به حساب " : "انتقال از حساب ")." ".$transaction['Transfer']['Account']['name'];
                            echo $transaction['Transfer']['description']? "<br />توضیحات: ".$transaction['Transfer']['description'] : "";
                        } else {
                            echo "تراکنش متناظر انتقال حذف شده است";
                        }
                    ?>&nbsp;
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
            <td colspan="2" style="text-align:left;"><b>جمع</b></td>
            <td style="color:#C62121;direction:ltr;"><b>-<?php echo number_format($debtSum); ?></b></td>
            <td style="color:green;direction:ltr;"><b>+<?php echo number_format($creditSum); ?></b></td>
            <td colspan="7"></td>
        </tr>    
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
	
<p align="center" class="font-smaller">
    <?php echo $this->Paginator->counter(array(
                    'format' => 'صفحه %page% از %pages%, در حال نمایش %current% مورد از %count%, از %start% تا %end%'
                )); ?>
</p>

<div  align="center" class="paging">
    <?php echo $this->Paginator->prev('<< قبلی', array('class'=>'white_button','url'=>array('#'=>'#dataTable')), null, array('class' => 'white_button disabled'));?>
    &nbsp;
    <?php echo $this->Paginator->numbers(array('class'=>'white_button','separator'=>'&nbsp;&nbsp;'));?>
    &nbsp;
    <?php echo $this->Paginator->next('بعدی >>', array('class'=>'white_button','url'=>array('#'=>'#dataTable')), null, array('class' => 'white_button disabled'));?>
</div>
    
</div>
<div class="clear"></div>

<?php echo $this->Chart->line('AccountLineChart','موجودی ماهانه حساب','موجودی',$chart,'',array(),900)?>
<script>
function showBalance(obj)
{
    obj = $(obj);
    var id = obj.attr('id').match(/(\d+)/i)[0];
    var balance = jeeb.showBalance('<?php echo $this->Html->url(array('action'=>'showbalance')); ?>',id);
    //$('#showBalanceCell'+id).html(  );
}
$('.showBalanceButton').click(function(evt){
    showBalance(evt.target);
});
    $('#gotoaccount').change(function(ev){
        var baseurl = '<?php echo Router::url( array('action'=>'view'), false ) ?>';
        window.location = baseurl+'/'+ev.target.value;
    });
</script>