<div class="row">
    <?php echo $this->element('../reports/menu') ?>
    <div class="col-xs-16 col-md-16 ">
        <div class="box rounded" >
            <h2>گزارش هزینه</h2>
            <div id="filter" class="form">
                <?php echo $this->Form->create('Account',array('url'=>array('controller'=>'reports','action'=>'accounts'))); ?>
                <fieldset>
                    <?php
                    echo $this->Form->input('account_id',array('label'=>'حساب','type'=>'select','empty'=>true));
                    echo $this->Form->input('Account.start_date',array('label'=>'از تاریخ','class'=>'datepicker'));
                    echo $this->Form->input('Account.end_date',array('label'=>'تا تاریخ','class'=>'datepicker'));
                    ?>
                    <div class="input text"><label>&nbsp;</label><?php echo $this->element('filldate', array( 'start_date' => '#AccountStartDate', 'end_date' => '#AccountEndDate', 'showthisyear' => true )); ?></div>
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
                    <?php $tableHeaders = $html->tableHeaders(array('لیست حسابها','موجودی اولیه','موجودی فعلی','مبلغ هزینه‌ها','مبلغ درآمدها'));
                echo '<thead class="table-primary" >'.$tableHeaders.'</thead>';
                ?>
                <?php $balance = $income = $expense = 0; ?>
                <?php foreach ($account_report as $account): ?>
                <?php
                $balance += $account['Account']['balance'];
                $expense += $account['Transaction']['sum_expense'];
                $income += $account['Transaction']['sum_income'];
                ?>
                    <tr>
                        <td><?php echo $account['Account']['name']; ?></td>
                        <td style="color:<?php echo (($account['Account']['init_balance']>0)?'green':'#C62121') ?>;direction:ltr;"><?php echo (($account['Account']['init_balance']>0)?'+':'') ?><?php echo number_format($account['Account']['init_balance']); ?></td>
                        <td style="color:<?php echo (($account['Account']['balance']>0)?'green':'#C62121') ?>;direction:ltr;"><?php echo (($account['Account']['balance']>0)?'+':'') ?><?php echo number_format($account['Account']['balance']); ?></td>
                        <td style="color:#C62121; direction:ltr;">- <?php echo number_format(intval($account['Transaction']['sum_expense'])); ?></td>
                        <td style="color:green;"><?php echo number_format(intval($account['Transaction']['sum_income'])); ?></td>
                    </tr>
                <?php endforeach; ?>
                    <tr>
                       <td colspan="2">مجموع</td>
                       <td style="color:<?php echo (($balance>0)?'green':'#C62121') ?>;direction:ltr;"><?php echo (($balance>0)?'+':'') ?><?php echo number_format($balance); ?></td>
                       <td style="color:#C62121; direction:ltr;"><?php echo number_format(intval($expense? '-'.$expense : '')); ?></td>
                       <td style="color:green;"><?php echo number_format(intval($income? $income : '')); ?></td>
                    </tr>
                <?php echo '<tfoot class=\'dark\'>'.$tableHeaders.'</tfoot>'; ?> 
            </table></div>
    </div>
</div>    
<div class="clear"></div>
<br/>

<div class="row">
    <h2 class="col-xs-16 col-md-3" id="page-heading">لیست تراکنشها<?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="col-xs-16 col-md-16">
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
        <?php echo $this->element('pagination/bottom'); ?>
    </div>
</div>
<div class="clear"></div>
<br/>