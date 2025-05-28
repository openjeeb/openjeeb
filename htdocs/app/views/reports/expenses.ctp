<div class="row">
    <?php echo $this->element('../reports/menu') ?>
    <div class="col-xs-16 col-md-16 ">
        <div class="box rounded">
            <h2>گزارش هزینه</h2>
            <div id="filter" class="form">
                <?php echo $this->Form->create('Expense',array('url'=>array('controller'=>'reports','action'=>'expenses'))); ?>
                <fieldset>
                    <?php
                    echo $this->Form->input('Expense.search',array('type'=>'hidden','value'=>true));
                    echo $this->Form->input('Expense.expense_category_id',array('label'=>'نوع هزینه','type'=>'select','empty'=>true));
                    echo $this->Form->input('Transaction.account_id',array('label'=>'حساب','type'=>'select','empty'=>true));
                    echo $this->Form->input('Expense.individual_id',array('label'=>'شخص','type'=>'select','empty'=>true));
                    echo $this->Form->input('Transaction.start_date',array('label'=>'از تاریخ','class'=>'datepicker'));
                    echo $this->Form->input('Transaction.end_date',array('label'=>'تا تاریخ','class'=>'datepicker'));
                    ?>
                    <div class="input text"><label>&nbsp;</label><?php echo $this->element('filldate', array( 'start_date' => '#TransactionStartDate', 'end_date' => '#TransactionEndDate', 'showthisyear' => true )); ?></div>
                </fieldset>
                <?php echo $this->Form->end('گزارش گیری');?>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>

<div class="row">
    <div class="col-xs-16 col-md-16">
        <div>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered"  id="dataTable2" cellpadding="0" cellspacing="0">
                    <thead class="table-primary">
                        <tr>
                            <th>نوع هزینه</th>
                            <th><?php echo $this->Html->link('مجموع هزینه', array('categorysort'=>(@$this->passedArgs['categorysort']!='asc')? 'asc' : 'desc','#'=>'#dataTable2'), array('class'=>@$this->passedArgs['categorysort'])) ?></th>
                            <th style="width:25px;"><?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('exportsummary'),array('escape' => false,'id'=>'excelExport')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pieData as $entry): ?>
                        <tr>
                            <td><?php echo $entry['key']; ?></td>
                            <td colspan="2"><?php echo number_format($entry['value']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td><b>جمع</b></td>
                            <td colspan="2"><b><?php echo number_format($columnDataSum); ?></b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>    
<div class="clear"></div>
<br/>

<div class="row">
    <h2 class="col-xs-16 col-md-3" id="page-heading">لیست هزینه‌ها  <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="col-xs-16 col-md-16">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
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

        <?php echo $this->element('pagination/bottom'); ?>
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
<?php echo $this->Chart->column('ExpenseColumnChart','مقایسه هزینه','هزینه ماهانه',$columnData,940); ?>
<script type="text/javascript">
//<![CDATA[

$(function(){
    
    //bind the sub categories
    jeeb.bindExpenseSubCategories($('#ExpenseExpenseCategoryId'),<?php echo $this->Javascript->object($expenseCategoriesData); ?>,'','ExpenseExpenseSubCategoryId');   
});
//]]>
</script>