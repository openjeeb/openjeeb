<div class="row">
    <div class="col-xs-16 col-md-3">
        <div class="box rounded">
            <h2>دسترسی سریع</h2>
            <div class="block" id="shortlinks">
                <ul class="menu">
                    <li><?php echo $this->Html->link('تنظیمات یادآوری', array('action' => 'index'));?></li>
                </ul>
                <br/>
            </div>
        </div>
    </div>
    <div class="col-xs-16 col-md-12">

        <h2 id="page-heading">یادآور های ارسال شده<?php $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export'),array('escape' => false,'id'=>'excelExport')); ?></h2>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
                <?php
                $pdate = new PersianDate();
                $tableHeaders = $html->tableHeaders( array(
                    'عنوان',
                    $paginator->sort( 'تاریخ', 'time' ,array('url'=>array('#'=>'#dataTable'))),
                    'طریقه ارسال',
                    'پست الکترونیکی',
                    'پیامک'
                ));
                echo '<thead class="table-primary" >' . $tableHeaders . '</thead>'; ?>

                <?php if(isset($reminderlogs)): ?>
                    <?php foreach ( $reminderlogs as $item ): ?>
                        <tr>
                            <td><?php echo $item['ReminderLog']['subject']; ?>&nbsp;</td>
                            <td><?php echo PersianLib::FA_($pdate->pdate( "j F Y, H:i", strtotime($item['ReminderLog']['senddate']) )); ?>&nbsp;</td>
                            <td><?php
                                $medium = explode(",",$item['ReminderLog']['medium']);
                                foreach ( $medium as &$md ) {
                                    $md = __($md,true);
                                }
                                echo implode(" / ",$medium);
                                ?>&nbsp;
                            </td>
                            <td><?php __($item['ReminderLog']['email_status']); ?>&nbsp;</td>
                            <td><?php __($item['ReminderLog']['sms_status']); ?>&nbsp;</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align:center"> موردی برای نمایش وجود ندارد </td>
                    </tr>
                <?php endif; ?>
                <?php echo '<tfoot class=\'dark\'>' . $tableHeaders . '</tfoot>'; ?>    </table>
        </div>
        <?php echo $this->element('pagination/bottom'); ?>
        
    </div>
</div>
<div class="clear"></div>