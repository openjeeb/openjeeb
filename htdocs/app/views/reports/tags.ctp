<div class="row">
    <?php echo $this->element('../reports/menu') ?>
    <div class="col-xs-16 col-md-16">
        <div class=" box rounded">
            <h2>گزارش برچسب ها</h2>
            <div id="filter" class="form">
                <?php echo $this->Form->create('Tag',array('url'=>array('controller'=>'reports','action'=>'tags'))); ?>
                <fieldset>
                    <div class="input text" style="position: relative;">
                        <label for="TagTagCategoryId">برچسب</label>
                        <div style="float: right; width:570px;">
                            <?php echo $this->Form->text('Tag.tag_id',array('value'=>'','autocomplete'=>'off')); ?>
                            <div id="groupListHolder"></div>
                        </div>
                    </div>
                    <div class="clear">&nbsp;</div>
                    <?php
                    echo $this->Form->input('Tag.search',array('type'=>'hidden','value'=>true));
                    echo $this->Form->input('Tag.start_date',array('label'=>'از تاریخ','class'=>'datepicker'));
                    echo $this->Form->input('Tag.end_date',array('label'=>'تا تاریخ','class'=>'datepicker'));
                    ?>
                    <div class="input text"><label>&nbsp;</label><?php echo $this->element('filldate', array( 'start_date' => '#TagStartDate', 'end_date' => '#TagEndDate', 'showthisyear' => true )); ?></div>
                </fieldset>
                <?php echo $this->Form->end('گزارش گیری');?>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>

<div class="row">
    <div class="col-xs-16 col-md-16">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
                <thead class="table-primary" >
                    <tr>
                        <th>برچسب</th>
                        <th>هزینه (ریال)</th>
                        <th>درآمد (ریال)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($taglist as $entry): ?>
                        <tr>
                            <td><?php echo PersianLib::FA_($entry['name']); ?></td>
                            <td style="color:#C62121;"><?php echo $entry['expense']? PersianLib::currency($entry['expense']).' -' : PersianLib::currency(0); ?></td>
                            <td style="color:green;" colspan="2"><?php echo $entry['income']? PersianLib::currency($entry['income']).' +' : PersianLib::currency(0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><b>جمع</b></td>
                        <td style="color:#C62121;"><b><?php echo $sum_expense? PersianLib::currency($sum_expense).' -' : PersianLib::currency(0); ?></b></td>
                        <td style="color:green;" colspan="2"><b><?php echo $sum_income? PersianLib::currency($sum_income).' -' : PersianLib::currency(0); ?></b></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>    
<div class="clear"></div>
<br />

<div class="row">
    <div class="col-xs-16 col-md-16">
        <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
                <thead class="table-primary" >
                    <tr>
                        <th>برچسب</th>
                        <th>چک کشیده (ریال)</th>
                        <th>چک دریافتی (ریال)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($taglist as $entry): ?>
                        <tr>
                            <td><?php echo PersianLib::FA_($entry['name']); ?></td>
                            <td style="color:#C62121;"><?php echo $entry['drawed']? PersianLib::currency($entry['drawed']).' -' : PersianLib::currency(0); ?></td>
                            <td style="color:green;" colspan="2"><?php echo $entry['received']? PersianLib::currency($entry['received']).' +' : PersianLib::currency(0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><b>جمع</b></td>
                        <td style="color:#C62121;"><b><?php echo $sum_drawed? PersianLib::currency($sum_drawed).' -' : PersianLib::currency(0); ?></b></td>
                        <td style="color:green;" colspan="2"><b><?php echo $sum_received? PersianLib::currency($sum_received).' +' : PersianLib::currency(0); ?></b></td>
                    </tr>
                </tbody>
            </table></div>
    </div>
</div>    
<div class="clear"></div>
<br/>

<div class="row">
    <div class="col-xs-16 col-md-16">

        <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
                <thead class="table-primary" >
                    <tr>
                        <th>برچسب</th>
                        <th>وام دریافتی</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $loan = 0; ?>
                    <?php foreach($taglist as $entry): ?>
                    <?php $loan += $entry['loan'] ?>
                        <tr>
                            <td><?php echo PersianLib::FA_($entry['name']); ?></td>
                            <td><?php echo PersianLib::currency($entry['loan']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><b>جمع</b></td>
                        <td><b><?php echo PersianLib::currency($loan); ?></b></td>
                    </tr>
                </tbody>
            </table></div>

    </div>
</div>    
<div class="clear"></div>
<br/>

<div class="row">
    <div class="col-xs-16 col-md-16" >
        <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
                <thead class="table-primary" >
                    <tr>
                        <th>برچسب</th>
                        <th>بدهی (ریال)</th>
                        <th>طلب (ریال)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($taglist as $entry): ?>
                        <tr>
                            <td><?php echo PersianLib::FA_($entry['name']); ?></td>
                            <td style="color:#C62121;"><?php echo $entry['debt']? PersianLib::currency($entry['debt']).' -' : PersianLib::currency(0); ?></td>
                            <td style="color:green" colspan="2"><?php echo $entry['credit']? PersianLib::currency($entry['credit']).' +' : PersianLib::currency(0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><b>جمع</b></td>
                        <td style="color:#C62121;"><b><?php echo $sum_debt? PersianLib::currency($sum_debt).' -' : PersianLib::currency(0); ?></b></td>
                        <td style="color:green"><b><?php echo $sum_credit? PersianLib::currency($sum_credit).' +' : PersianLib::currency(0); ?></b></td>
                    </tr>
                </tbody>
            </table></div>
    </div>
</div>    
<div class="clear"></div>
<br/>

<?php /* <div class="col-xs-16 col-md-16">
    <h2 class="col-xs-16 col-md-3" id="page-heading">لیست هزینه‌ها  <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-9" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array($paginator->sort('نوع هزینه','Expense.expense_category_id'),$paginator->sort('مبلغ (ریال)','Transaction.amount'),$paginator->sort('تاریخ','Transaction.date'),$paginator->sort('حساب','Expense.account_id'),$paginator->sort('شخص','Expense.individual_id'),$paginator->sort('توضیحات','Expense.description')));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php foreach ($expenses as $expense):	?>
	<tr>
                <td><?php echo $expense['ExpenseCategory']['name']; if(!is_null($expense['ExpenseSubCategory']['name'])) echo ' >> '.$expense['ExpenseSubCategory']['name'];?></td>
                <td style="color:#C62121;direction:ltr;">-<?php echo number_format($expense['Transaction']['amount']); ?></td>
		<td><?php echo $expense['Transaction']['date']; ?></td>
		<td><?php echo $expense['Account']['name']; ?></td>
		<td><?php echo $expense['Individual']['name']; ?></td>
                <td><?php echo str_replace('\n', "<br />", $expense['Expense']['description']); ?></td>
	</tr>
<?php endforeach; ?>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
    
          
	<p align="center">
	<?php
	echo $this->Paginator->counter(array(
                    'format' => 'صفحه %page% از %pages%, در حال نمایش %current% مورد از %count%, از %start% تا %end%'
                ));
	?>	</p>

	<div  align="center" class="paging">
		<?php echo $this->Paginator->prev('<< قبلی', array(), null, array('class' => 'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
&nbsp;|
		<?php echo $this->Paginator->next('بعدی >>', array(), null, array('class' => 'disabled'));?>
	</div>
		
</div>
<div class="clear"></div>
<br/>

<div class="col-xs-16 col-md-16">
    <div align="center" id="ExpensePieChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <div id="ExpenseColumnChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<?php echo $this->Chart->pie('ExpensePieChart','تقسیم هزینه',$pieData,700); ?>
<?php echo $this->Chart->column('ExpenseColumnChart','مقایسه هزینه','هزینه ماهانه',$columnData,940); ?> */ ?>
<script type="text/javascript">
//<![CDATA[

$(function(){
    //bind the sub categories
    categories = <?php echo json_encode($tags? $tags : array()); ?>;
    values = <?php echo json_encode(!empty($report_catlist)? $report_catlist : array()); ?>;
    dflt = [];
    for(i in  values) {
        dflt[dflt.length] = values[i];
    }    
    new FloatingList({
        input: '#TagTagId', 
        listholder: '#groupListHolder', 
        data: categories,
        preload: dflt
    });
});
//]]>
</script>