<br/>
<div class="row">
    <div class="col-xs-16 col-md-3">
        <div class="box rounded">
            <h2>دسترسی سریع</h2>
            <div class="block" id="shortlinks">
                <div class="padding-10">
                    <div style="margin: auto;width: 120px;display: inline-block;text-align: center;">
                        <button style="width: 100%;" onclick="window.location='<?php echo $referer; ?>'">بازگشت</button>
                    </div>
                    
                    <br /><br />
                    
                    باقی مانده پیام کوتاه:<br />
                    <?php echo $remaining_sms ?>
                    
                    <br /><br />
                    <div style="margin: auto;width: 120px;display: inline-block;text-align: center;">
                        <button style="width: 100%;" onclick="window.location='<?php echo $this->Html->url(array('controller'=>'reminders', 'action'=>'add', $type=>$refId)) ?>'">افزودن یادآور</button>
                    </div>
                    
                </div>
            </div>
        </div>        
    </div>
    <div class="col-xs-16 col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th colspan="2" class="centered">
                            <h2><?php echo __('reminder-'.$type) ?></h2>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php if($type=='check'): ?>
                    <tr>
                        <th>
                            <?php if($reference['Check']['type']=='drawed'): ?>
     دریافت کننده:
                            <?php else: ?>
  صادر کننده:
                            <?php endif; ?>
                        </th>
                        <td><?php echo $reference['Individual']['name']; ?></td>
                    </tr>
                    <tr>
                        <th>بانک</th>
                        <td><?php echo $reference['Bank']['name']; ?></td>
                    </tr>
                    <?php if($reference['Check']['type']=='drawed'): ?>
                        <tr>
                            <th>از حساب</th>
                            <td><?php echo $reference['Account']['name']; ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <th>مبلغ (ریال)</th>
                        <td><?php echo PersianLib::currency(abs($reference['Check']['amount'])); ?></td>
                    </tr>
                    <tr>
                        <th>موعد چک</th>
                        <td> <?php echo PersianLib::FA_($reference['Check']['due_date']); ?></td>
                    </tr>
                    <tr>
                        <th>سریال چک</th>
                        <td> <?php echo PersianLib::FA_($reference['Check']['serial']); ?>&nbsp;</td>
                    </tr>
                    <tr>
                        <th>توضیحات</th>
                        <td><?php echo PersianLib::FA_($reference['Check']['description']); ?></td>
                    </tr>
                <?php endif; ?>

                <?php if($type=='loan'): ?>
                    <tr>
                        <th>عنوان وام</th>
                        <td> <?php echo $reference['Loan']['name']; ?></td>
                    </tr>
                    <tr>
                        <th>مبلغ</th>
                        <td> <?php echo PersianLib::currency($reference['Loan']['amount']);  ?></td>
                    </tr>
                    <tr>
                        <th>توضیحات</th>
                        <td> <?php echo $reference['Loan']['description']; ?></td>
                    </tr>
                    <tr>
                        <th>موسسه</th>
                        <td> <?php echo $reference['Bank']['name']; ?></td>
                    </tr>
                <?php endif; ?>

                <?php if($type=='installment'): ?>
                    <tr>
                        <th>عنوان وام</th>
                        <td> <?php echo $reference['Loan']['name']; ?></td>
                    </tr>
                    <tr>
                        <th>موعد</th>
                        <td> <?php echo PersianLib::FA_($reference['Installment']['due_date']); ?></td>
                    </tr>
                    <tr>
                        <th>مبلغ</th>
                        <td> <?php echo PersianLib::currency($reference['Installment']['amount']); ?></td>
                    </tr>
                    <tr>
                        <th>توضیحات</th>
                        <td> <?php echo $reference['Installment']['description']; ?></td>
                    </tr>
                <?php endif; ?>

                <?php if($type=='debt'): ?>
                    <tr>
                        <th>عنوان</th>
                        <td><?php echo __($reference['Debt']['type'],true)." : ".$reference['Debt']['name']; ?></td>
                    </tr>
                    <tr>
                        <th>مبلغ</th>
                        <td><?php echo PersianLib::currency(abs($reference['Debt']['amount'])); ?></td>
                    </tr>
                    <tr>
                        <th>
                            <?php if($reference['Debt']['type']=='debt'): ?>
                                دریافت از
                            <?php else: ?>
   پرداخت به
                            <?php endif; ?>
                        </th>
                        <td><?php echo $reference['Individual']['name']? $reference['Individual']['name'] : " - "; ?></td>
                    </tr>
                    <tr>
                        <th>موعد</th>
                        <td><?php echo PersianLib::FA_($reference['Debt']['due_date']); ?></td>
                    </tr>

                <?php endif; ?>

                <?php if($type=='note'): ?>
                    <tr>
                        <th>عنوان</th>
                        <td><?php echo $reference['Note']['subject']; ?></td>
                    </tr>
                    <tr>
                        <th>موعد</th>
                        <td><?php echo PersianLib::FA_($reference['Note']['date']); ?></td>
                    </tr>
                    <tr>
                        <th>توضیحات</th>
                        <td><?php echo PersianLib::FA_($reference['Note']['content']); ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">یادآور ها</h2>

    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
        <?php $tableHeaders = $html->tableHeaders( array( 
            'عنوان',
            'تاریخ',
            'طریقه ارسال',
            ''
            ));
        echo '<thead class="table-primary" >' . $tableHeaders . '</thead>'; ?>

        <?php if(isset($reminders)): ?>
            <?php foreach ( $reminders as $item ): ?>
                <tr>
                    <td><?php echo $item['Reminder']['name']; ?>&nbsp;</td>
                    <td><?php echo $item['Reminder']['time']; ?>&nbsp;</td>
                    <td><?php
                            $medium = explode(",",$item['Reminder']['medium']);
                            foreach ( $medium as &$md ) {
                                $md = __($md,true);
                            }
                            echo implode(" / ",$medium);
                        ?>&nbsp;
                    </td>
                    <td style="width: 130px">
                        <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $item['Reminder']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $item['Reminder']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $item['Reminder']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php echo $this->Html->link('<i class="fa fa-mobile-phone"></i>', '#',array('escape'=>false,'class'=>'showtext', 'id'=>'show'.$item['Reminder']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align:center"> یادآوری برای نمایش وجود ندارد </td>
                </tr>
        <?php endif; ?>
        <?php echo '<tfoot class=\'dark\'>' . $tableHeaders . '</tfoot>'; ?>    </table></div>


</div>
<div class="clear"></div>

<script type="text/javascript">
//<![CDATA[
$(function(){
    //tips
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.showtext'),'35','نمایش متن');
    jeeb.tip($('.delete'),'70','پاک کردن');
    
    showText = function(obj) {
        var objid = obj.currentTarget.id.match(/(\d+)/)[0];
        $.ajax({
            type: "POST",
            url: '<?php echo $this->Html->url(array('controller'=>'reminders','action'=>'ajaxShowText'))?>/'+objid,
            data: {
            },
            async: false,
            error:function (XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
            },
            success: function(data, stat, xhr) {
                alert(data);
            }
        });
        return false;
    }
    $('.showtext').click(showText);
});
//]]>
</script>