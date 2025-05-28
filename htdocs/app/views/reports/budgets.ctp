<?php
$pd = new PersianDate();
?>
<div class="col-xs-16 col-md-16">
    <?php echo $this->element('../reports/menu') ?>
    <div class="col-xs-16 col-md-12 box">
        <h2>گزارش بودجه بندی</h2>
        <div id="filter" class="form">
        <?php echo $this->Form->create('Budget',array('url'=>array('controller'=>'reports','action'=>'budgets'))); ?>
        <fieldset>
            <?php
                echo $this->Form->input('Budget.search',array('type'=>'hidden','value'=>true));
            ?>
            <div class="input select">
                <label for="ExpenseCategoryId">نوع هزینه</label>
                <?php echo $this->Form->select('Budget.start_date.year',$categories,0,array('label'=>false)); ?>
            </div>
            <div class="input select">
                <label for="BudgetStartDate">شروع از</label>
                <?php echo $this->Form->select('Budget.start_date.month', $months, 1, array('label'=>false,'empty'=>false) ); ?>
                &nbsp;&nbsp;
                <?php echo $this->Form->select('Budget.start_date.year', $years, $pd->pdate("Y"), array('label'=>false,'empty'=>false) ); ?>
            </div>
            <div class="input select">
                <label for="BudgetStartDate">تا پایان</label>
                <?php echo $this->Form->select('Budget.end_date.month', $months, 12, array('label'=>false,'empty'=>false) ); ?>
                &nbsp;&nbsp;
                <?php echo $this->Form->select('Budget.end_date.year', $years, $pd->pdate("Y"), array('label'=>false,'empty'=>false) ); ?>
            </div>
        </fieldset>
        <?php echo $this->Form->end('گزارش گیری');?>
        </div>
    </div>
</div>
<div class="clear"></div>
<br/>

<div class="col-xs-16 col-md-16">
    <h2 class="col-xs-16 col-md-3" id="page-heading">لیست بودجه بندی ها  <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-9" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array( 'گروه هزینه', 'بازه زمانی', 'مبلغ بودجه (ریال)', 'میزان مصرف شده (ریال)', 'درصد مصرفی', 'کسر بودجه (ریال)', 'بودجه مازاد (ریال)' ));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

<?php foreach ($budgets as $item):	?>
	<tr>
        <td><?php echo $item['ExpenseCategory']['name']; ?></td>
        <td><?php echo PersianLib::FA_(__('month_'.$item['Budget']['pmonth'],true).' '.$item['Budget']['pyear']); ?></td>
        <td style="color:green;direction:ltr;"><?php echo PersianLib::currency($item['Budget']['amount']); ?></td>
        <td style="color:#C62121;direction:ltr;"><?php echo PersianLib::currency($item['Budget']['amount_used']? '-'.$item['Budget']['amount_used'] : 0); ?></td>
        <td style="direction:ltr;"><?php echo PersianLib::FA_($item['Budget']['used_percent']); ?> %</td>
        <td style="color:#C62121;direction:ltr;"><?php echo PersianLib::FA_($item['Budget']['minus']); ?></td>
        <td style="color:green;direction:ltr;"><?php echo PersianLib::FA_($item['Budget']['plus']); ?></td>
	</tr>
<?php endforeach; ?>
<?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>
    </table></div>
    
		
</div>
<div class="clear"></div>
<br/>

<?php //echo $this->Chart->pie('ExpensePieChart','تقسیم هزینه',$pieData,700); ?>
<?php //echo $this->Chart->column('ExpenseColumnChart','مقایسه هزینه','هزینه ماهانه',$columnData,940); ?>
<script type="text/javascript">
//<![CDATA[

$(function(){
    
});
//]]>
</script>