<div class="row">
    <?php echo $this->element('../reports/menu') ?>
    <div class="col-xs-16 col-md-16 ">
        <div class=" box rounded">
            <h2>گزارش اشخاص</h2>
            <div id="filter" class="form">
                <?php echo $this->Form->create('Individual',array('url'=>array('controller'=>'reports','action'=>'individuals'))); ?>
                <fieldset>
                    <?php
                    echo $this->Form->input('individual_id',array('label'=>'شخص','type'=>'select','empty'=>'همه'));
                    echo $this->Form->input('start_date',array('label'=>'از تاریخ','class'=>'datepicker'));
                    echo $this->Form->input('end_date',array('label'=>'تا تاریخ','class'=>'datepicker'));
                    ?>
                    <div class="input text"><label>&nbsp;</label><?php echo $this->element('filldate', array( 'start_date' => '#IndividualStartDate', 'end_date' => '#IndividualEndDate', 'showthisyear' => true )); ?></div>
                </fieldset>
                <?php echo $this->Form->end('گزارش گیری');?>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>

<div class="row">
    <h2 class="col-xs-16 col-md-3" id="page-heading">لیست اشخاص<?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('export'),array('escape' => false,'id'=>'excelExport')); ?></h2>

    <div class="col-xs-16 col-md-16">
        <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
                <?php $tableHeaders = $html->tableHeaders(array('شخص','هزینه','درآمد'));
                echo '<thead class="table-primary" >'.$tableHeaders.'</thead>';
                ?>
                <?php $income = $expense = 0; ?>
                <?php foreach ($individual_report as $item): ?>
                    <?php
                    $expense += @$item['expenses'];
                    $income += @$item['incomes'];
                    ?>
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td style="color:#C62121;direction:ltr;"><?php echo number_format(@$item['expenses']); ?></td>
                        <td style="color:green;direction:ltr;"><?php echo number_format(@$item['incomes']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td >مجموع</td>
                    <td style="color:#C62121;direction:ltr;"><?php echo number_format($expense); ?></td>
                    <td style="color:green;direction:ltr;"><?php echo number_format($income); ?></td>
                </tr>
                <?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?>
            </table></div>
    </div>
</div>    
<div class="clear"></div>
<br/>

<?php /*

<div class="col-xs-16 col-md-16">
    <h2 class="col-xs-16 col-md-3" id="page-heading">لیست تراکنشها<?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-9" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
    <?php $tableHeaders = $html->tableHeaders(array('حساب','هزینه','درآمد','تاریخ','توضیحات'));
echo '<thead class="table-primary" >'.$tableHeaders.'</thead>'; ?>
        
<?php foreach ($transactions as $transaction):	?>
	<tr>
                <td><?php echo $accounts[$transaction['Transaction']['account_id']]; ?></td>
                <td style="color:#C62121;direction:ltr;"><?php echo (($transaction['Transaction']['type']=='debt')? ('-'.number_format($transaction['Transaction']['amount'])) : '0'); ?></td>
                <td style="color:green;direction:ltr;"><?php echo (($transaction['Transaction']['type']=='credit')? (number_format($transaction['Transaction']['amount'])) : '0'); ?></td>
		<td><?php echo $transaction['Transaction']['date']; ?></td>
		<td>
                    <?php
                    if(!empty($transaction['Transaction']['expense_id'])){
                        $description = $transaction['Expense']['description'];
                    } elseif(!empty($transaction['Transaction']['income_id'])){
                        $description = $transaction['Income']['description'];
                    } else {
                        $description = (($transaction['Transaction']['type']=='debt')? "انتقال به حساب" : "انتقال از حساب").' '.$transaction['Transfer']['Account']['name'];
                        $description.= $transaction['Transfer']['description']? "<br /> توضیحات: ".$transaction['Transfer']['description'] : "";                        
                    }
?>
                    <?php echo str_replace('\n', "<br />", $description); ?>
                </td>
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
<br/> */ ?>