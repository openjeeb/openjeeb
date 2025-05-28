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
            <?php echo $this->Form->select('Budget.date.month', $months, $persianDate->pdate('m'), array('empty'=>false)); ?>&nbsp;&nbsp;
            <?php echo $this->Form->select('Budget.date.year', $years, $persianDate->pdate('Y'), array('empty'=>false)); ?>
            <br />
            
            <?php echo $this->Form->label('Budget.amount','مبلغ'); ?><br/>
            <?php echo $this->Form->error('Budget.amount'); ?>
            <?php echo $this->Form->text('Budget.amount',array('value'=>'')); ?>
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
<br>

<div class="col-xs-16 col-md-16 ">
    <div class="box">
        <h2><?php echo $this->Html->link('جستجو', '#',array('id'=>'toggle-filter','class'=>'hidden')); ?></h2>
        <div id="filter" class="form">
            <?php echo $this->Form->create('Budget'); ?>
            <fieldset>
                <?php
                echo $this->Form->input('Budget.search',array('type'=>'hidden','value'=>true));

                //echo $this->Form->select('Budget.date.month', $bcMonth, $persianDate->pdate('m'), array('empty'=>false)),'&nbsp;&nbsp';
                echo $this->Form->select('Budget.date.ym', $cmbBC, null, array('empty'=>false));

                ?>
                <div class="clear">&nbsp;</div>
            </fieldset>
            <?php echo $this->Form->end('جستجو');?>
        </div>
    </div>

</div>
<div class="clear"></div>


<h2 class="col-xs-16 col-md-3" id="page-heading">لیست هزینه‌ها  <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
<div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>

<div class="col-md-16 col-xs-16">
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
            <?php $tableHeaders = $html->tableHeaders(array(
                $paginator->sort('نوع هزینه','Budget.expense_category_id',array('url'=>array('#'=>'#dataTable'))),
                $paginator->sort('بازه زمانی','Budget.start_date',array('url'=>array('#'=>'#dataTable'))),
                $paginator->sort('بودجه تعریف شده','Budget.amount',array('url'=>array('#'=>'#dataTable'))),
                $paginator->sort('بودجه مصرفی','Budget.amount_used',array('url'=>array('#'=>'#dataTable'))),
                'میزان مصرف',
                'عملیات'));
            echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>

            <?php foreach ($budgets as $budget):	?>
                <tr>
                    <td><?php echo $budget['ExpenseCategory']['name']; ?></td>
                    <td><?php echo __('month_'.$budget['Budget']['pmonth'],true).' '.$budget['Budget']['pyear'] ?></td>
                    <td style="color:green;direction:ltr;"><?php echo number_format($budget['Budget']['amount']); ?></td>
                    <td style="color:#C62121;direction:ltr;">-<?php echo number_format($budget['Budget']['amount_used']); ?></td>
                    <td>
                        <div class="progressbar">
                            <?php
                            $percent = round(($budget['Budget']['amount_used']/$budget['Budget']['amount'])*100, 1);
                            if($percent <=80 ) {
                                $style = 'green';
                            } elseif( $percent < 100 ) {
                                $style = 'yellow';
                            } else {
                                $style = 'red';
                            }
                            ?>
                            <div class="<? echo $style?>" style="width:<?php echo $percent; ?>%"></div>
                            <div style="font-family:Tahoma;font-size: 12px;"><?php echo ($percent).'%' ?></div>
                        </div>
                    </td>
                    <td style="width: 80px">
                        <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $budget['Budget']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $budget['Budget']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $budget['Budget']['id'])); ?>&nbsp;
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td><b>مجوع بودجه تعریف شده</b></td>
                <td colspan="6" style="direction:ltr;"><b>+<?php echo number_format($total_amount); ?></b></td>
            </tr>
            <tr>
                <td><b>مجموع بودجه مصرفی</b></td>
                <td colspan="6" style="color:red;direction:ltr;"><b>+<?php echo number_format($total_amount_used); ?></b></td>
            </tr>
            <tr>
                <td><b>برآیند کل</b></td>
                <td colspan="6" style="color:<?php echo (($total>0)?'green':'red') ?> ;direction:ltr;"><b><?php echo (($total>0)?'+':'').number_format($total); ?></b></td>
            </tr>

            <?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>    </table></div>
    <?php echo $this->element('pagination/bottom'); ?>
</div>
<div class="clear"></div>

<?php echo $this->Chart->barChart( 'ExpensePieChart' , $chartname , $chart); ?>
<script type="text/javascript">
//<![CDATA[

$(function(){   
    jeeb.FormatPrice($('#BudgetAmount'));
});

//]]>
</script>