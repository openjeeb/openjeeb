<div class="row">
    <?php echo $this->element('../reports/menu'); ?>
    <div class="col-xs-16 col-md-16">
        <div class="box rounded" >
            <h2>گزارش درآمد</h2>
            <div id="filter" class="form">
                <?php echo $this->Form->create('Income',array('url'=>array('controller'=>'reports','action'=>'incomes_new'))); ?>
                <fieldset>
                    <div class="input text" style="position: relative;">
                        <label for="IncomeCategoryId">نوع درآمد</label>
                        <div style="float: right; width:570px;">
                            <?php echo $this->Form->text('Income.category_id',array('value'=>'','autocomplete'=>'off')); ?>
                            <div id="groupListHolder"></div>
                        </div>
                    </div>
                    <div class="clear">&nbsp;</div>
                    <?php
                    echo $this->Form->input('Income.search',array('type'=>'hidden','value'=>true));
                    echo $this->Form->input('Transaction.account_id',array('label'=>'حساب','type'=>'select','empty'=>true));
                    echo $this->Form->input('Income.individual_id',array('label'=>'شخص','type'=>'select','empty'=>true));
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
        <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
                <thead class="table-primary" >
                    <tr>
                        <th>نوع درآمد</th>
                        <th><?php echo $this->Html->link('مجموع درآمد', array('categorysort'=>(@$this->passedArgs['categorysort']!='asc')? 'asc' : 'desc'), array('class'=>@$this->passedArgs['categorysort'])) ?></th>
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
            </table></div>
    </div>
</div>    
<div class="clear"></div>
<br/>

<div class="row">
    <h2 class="col-xs-16 col-md-3" id="page-heading">لیست درآمدها  <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="col-xs-16 col-md-16">
        <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
                <?php $tableHeaders = $html->tableHeaders(array(
                    $paginator->sort('نوع درآمد','Income.income_type_id',array('url'=>array('#'=>'#dataTable'))),
                    $paginator->sort('مبلغ (ریال)','Transaction.amount',array('url'=>array('#'=>'#dataTable'))),
                    $paginator->sort('تاریخ','Transaction.date',array('url'=>array('#'=>'#dataTable'))),
                    $paginator->sort('حساب','Transaction.account_id',array('url'=>array('#'=>'#dataTable'))),
                    $paginator->sort('شخص','Expense.individual_id',array('url'=>array('#'=>'#dataTable'))),
                    $paginator->sort('توضیحات','Income.description',array('url'=>array('#'=>'#dataTable')))));
                echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

                <?php foreach ($incomes as $income): ?>
                    <tr>
                        <td><?php echo $income['IncomeType']['name']; if(!is_null($income['IncomeSubType']['name'])) echo ' >> '.$income['IncomeSubType']['name'];?></td>
                        <td style="color:green;direction:ltr;">+<?php echo number_format($income['Transaction']['amount']); ?></td>
                        <td><?php echo $income['Transaction']['date']; ?>&nbsp;</td>
                        <td><?php echo $income['Account']['name']; ?>&nbsp;</td>
                        <td><?php echo $income['Individual']['name']; ?>&nbsp;</td>
                        <td><?php echo str_replace('\n', "<br />", $income['Income']['description']); ?>&nbsp;</td>
                    </tr>
                <?php endforeach; ?>
                <?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
        <?php echo $this->element('pagination/bottom'); ?>
    </div>
</div>
<div class="clear"></div>
<br/>

<div class="col-xs-16 col-md-16">
    <div align="center" id="IncomePieChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <div id="IncomeColumnChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<?php echo $this->Chart->pie('IncomePieChart','تقسیم درآمد',$pieData,700); ?>
<?php echo $this->Chart->column('IncomeColumnChart','درآمد ماهانه','تقسیم درآمد',$columnData,940); ?>

<script type="text/javascript">
//<![CDATA[
$(function(){
    jeeb.bindIncomeSubTypes($('#IncomeIncomeTypeId'),<?php echo $this->Javascript->object($incomeTypesData); ?>,'','IncomeIncomeSubTypeId');
    categories = <?php echo json_encode($catlist? $catlist : array()); ?>;
    values = <?php echo json_encode(!empty($report_catlist)? $report_catlist : array()); ?>;
    dflt = [];
    for(i in  values) {
        dflt[dflt.length] = values[i];
    }
    new FloatingList({
        input: '#IncomeCategoryId', 
        listholder: '#groupListHolder', 
        data: categories,
        preload: dflt
    });
});
//]]>
</script>