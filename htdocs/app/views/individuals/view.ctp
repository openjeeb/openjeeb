<div class="col-xs-16 col-md-4">
    <div class="box">
        <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link( 'اشخاص', array( 'action' => 'index' ) ); ?></li>
                <li><?php echo $this->Html->link( 'ویرایش', array( 'action' => 'edit' ) ); ?></li>
                <li><?php echo $this->Html->link( 'پاک کردن', array( 'action' => 'delete' ) ); ?></li>
            </ul>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
            <tr>
                <th colspan="2" class="centered">
                    <h2>آمار <?php echo $individual['Individual']['name']; ?></h2>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>نام</th>
                <td><?php echo $individual['Individual']['name']; ?> </td>
            </tr>
            <tr>
                <th>توضیحات</th>
                <td> <?php echo $individual['Individual']['description']; ?></td>
            </tr>
            <tr>
                <th>تاریخ ایجاد</th>
                <td>  <?php echo $individual['Individual']['created']; ?></td>
            </tr>
            <tr>
                <th>تاریخ ویرایش</th>
                <td><?php echo $individual['Individual']['modified']; ?> </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16" id="IndividualTransactionColumnChart"></div>
<div class="clear"></div>

<br/>
<div class="col-xs-16 col-md-16">
    <h2 id="page-heading"> تراکنشهای <?php echo $individual['Individual']['name']; ?></h2>
    
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
            <td colspan="2" style="text-align:left;"><b>جمع هزینه‌ها</b></td>
            <td style="color:#C62121;direction:ltr;"><b>-<?php echo number_format($debtSum); ?></b></td>
            <td colspan="2" style="text-align:left;"><b>جمع درآمدها</b></td>
            <td style="color:green;direction:ltr;"><b><?php echo number_format($creditSum); ?></b></td>
            <td></td>
            <td><b>باقیمانده</b></td>
            <td style="direction:ltr;color:<?php $total=$creditSum - $debtSum; if($total>0){echo 'green';} else{echo '#C62121';} ?>"><b><?php echo number_format($total) ; ?></b></td>
            <td></td>
        </tr>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
	
    <?php echo $this->element('pagination/bottom'); ?>
    
</div>
<div class="clear"></div>

<?php echo $this->Chart->doubleColumn( 'IndividualTransactionColumnChart', 'تراکنشهای ماهانه', 'واریز '.$individual['Individual']['name'], $creditTransactionsColumn, 'برداشت', $debtTransactionsColumn, 6, 580, 500 ); ?>
