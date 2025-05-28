<?php
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
?>
<div class="col-xs-16 col-md-6" >
    <div class="box rounded">
        <h2>ثبت سرمایه</h2>
        <div id="new" class="investments form">
            <?php echo $this->Form->create('Investment'); ?>
            <fieldset>
                <?php echo $this->Form->label('Investment.name', 'عنوان سرمایه'); ?><br/>
                <?php echo $this->Form->error('Investment.name'); ?>
                <?php echo $this->Form->text('Investment.name', array('maxlength' => 75)); ?>
                <?php echo $this->Html->image('info.png', array('id' => 'InvestmentNameTip', 'alt' => 'راهنما', 'border' => '0')); ?>
                <br/>

                <?php echo $this->Form->label('Investment.amount', 'ارزش'); ?><br/>
                <?php echo $this->Form->error('Investment.amount'); ?>
                <?php echo $this->Form->text('Investment.amount', array('style' => 'direction:ltr')); ?>
                <?php echo $this->Html->image('info.png', array('id' => 'InvestmentAmountTip', 'alt' => 'راهنما', 'border' => '0')); ?>
                <br/>

                <?php echo $this->Form->label('Investment.date','تاریخ خرید'); ?><br/>
                <?php echo $this->Form->error('Investment.date'); ?>
                <?php echo $this->Form->text('Investment.date',array('class'=>'datepicker','value'=>$persianDate->pdate('Y/m/d'))); ?>
                <?php echo $this->Html->image('info.png', array('id'=>'InvestmentDateTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                <br/>

            </fieldset>
            <?php echo $this->Form->end('ثبت'); ?>
        </div>
    </div>
</div>
<div class="col-xs-16 col-md-9" >
    <div align="center" id="InvestmentPieChart" style="direction: ltr;"></div>
</div>
<div class="clear"></div>

<h2 class="col-xs-16 col-md-3" id="page-heading" >سرمایه‌ها</h2>
<div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
<div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
        <colgroup>
            <col/>
            <col />
            <col />
            <col  style="width:80px" />
        </colgroup>
        <?php $tableHeaders = $html->tableHeaders(array(
            $paginator->sort('عنوان', 'name',array('url'=>array('#'=>'#dataTable'))),
            $paginator->sort('میزان', 'amount',array('url'=>array('#'=>'#dataTable'))),
            $paginator->sort('تاریخ خرید', 'date',array('url'=>array('#'=>'#dataTable'))),
            'عملیات'));
        echo '<thead class="table-primary" >' . $tableHeaders . '</thead>'; ?>

        <?php foreach ($investments as $investment): ?>
            <tr>
                <td><?php echo $investment['Investment']['name']; ?>&nbsp;</td>
                <td><?php echo number_format($investment['Investment']['amount']); ?>&nbsp;</td>
                <td><?php echo $investment['Investment']['date']; ?>&nbsp;</td>
                <td>
                    <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $investment['Investment']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $investment['Investment']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟ با پاک کردن این حساب تمام تراکنشها، هزینه‌ها و درآمدهای مربوط به آن حذف میشوند.', $investment['Investment']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php //echo $this->Html->link('<i class="fa fa-bar-chart"></i>', array('action' => 'view', $investment['Investment']['id']),array('escape'=>false,'class'=>'view')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        <?php endforeach; ?>
        <?php echo '<tfoot class=\'dark\'>' . $tableHeaders . '</tfoot>'; ?>    </table></div>

<?php echo $this->element('pagination/bottom'); ?>
<div class="clear"></div>

<br/>

<?php echo $this->Chart->pie('InvestmentPieChart','تقسیم سرمایه',$pieData); ?>
<script type="text/javascript">
//<![CDATA[
$(function(){
    //tips
    jeeb.FormatPrice($('#InvestmentAmount'));
    
    jeeb.tip($('#excelExport'),'70','خروجی اکسل');
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
    jeeb.tip($('#InvestmentNameTip'),'166','یک عنوان برای  سرمایه خود برگزینید');    
    jeeb.tip($('#InvestmentAmountTip'),'220','ارزش یا قیمت واقعی سرمایه را وارد نمایید');    
    jeeb.tip($('#InvestmentDateTip'),'250','تاریخی را که این سرمایه‌گذاری را انجام داده‌اید برگزینید');
});
//]]>
</script>