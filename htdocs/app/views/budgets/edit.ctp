<?php
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
?>
<div class="col-xs-16 col-md-6 box rounded">
    <h2>بودجه بندی جدید</h2>
    <div id="new" class="notes form">
        <?php echo $this->Form->create( 'Budget' ); ?>
        <fieldset>
            
            <?php echo $this->Form->label('Budget.date','تاریخ'); ?><br/>
            <?php echo $this->Form->error('Budget.date'); ?>
            <?php echo $this->Form->select('Budget.date.month', $months, null, array('empty'=>false)); ?>&nbsp;&nbsp;
            <?php echo $this->Form->select('Budget.date.year', $years, null, array('empty'=>false)); ?>
            <br />
            
            <?php echo $this->Form->label('Budget.amount','مبلغ'); ?><br/>
            <?php echo $this->Form->error('Budget.amount'); ?>
            <?php echo $this->Form->text('Budget.amount'); ?>
            <br />
            
            <?php echo $this->Form->label('Budget.expense_category_id','گروه هزینه مرتبط'); ?><br/>
            <?php echo $this->Form->error('Budget.expense_category_id'); ?>
            <?php echo $this->Form->select('Budget.expense_category_id', $expenseCategories, null, array('empty'=>false)); ?>
            <br />
            
        </fieldset>
        <?php echo $this->Form->end( 'ثبت' ); ?>
    </div>

</div>

<div class="col-xs-16 col-md-10" >
    <div align="center" id="ExpensePieChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<?php echo $this->Chart->barChart( 'ExpensePieChart' , 'بودجه بندی '.' '.$this->data['ExpenseCategory']['name'].' '.__('month_'.$this->data['Budget']['pmonth'],true).' '.PersianLib::FA_($this->data['Budget']['pyear']) , array( $this->data['ExpenseCategory']['name'] => array(
    'بودجه تعریف شده' => $this->data['Budget']['amount'],
    'هزینه انجام شده' => $amount_used
) ) ) ?>

<script type="text/javascript">
//<![CDATA[

$(function(){   
    jeeb.FormatPrice($('#BudgetAmount'));
});

//]]>
</script>