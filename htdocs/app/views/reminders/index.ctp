<br/>
<div class="row">
    <div class="col-xs-16 col-md-3">
        <div class="box rounded">
            <h2>باقی مانده</h2>
            <div class="block" id="shortlinks">
                <div class="padding-10">
                    باقی مانده پیام کوتاه:<br />
                    <?php echo $remaining_sms ?>

                    <br /><br />
                </div>
            </div>
        </div>

        <div class="box rounded">
            <h2>یادآورهای ارسالی</h2>
            <div class="block" id="shortlinks">
                <div class="padding-10">
                    یادآورهای ارسال شده:<br />
                    <?php echo $sentlogcount ?> عدد

                    <br /><br />

                    <div style="margin: auto;display: inline-block;text-align: center;margin-bottom: 10px">
                        <button  onclick="window.location='<?php echo $this->Html->url(array('controller'=>'reminders', 'action'=>'logview')) ?>';">مشاهده ارسال شده‌ها</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-16 col-md-12 box rounded">
        <h2>تنظیمات یادآور</h2>
        <div class="users form">

            <?php echo $this->Form->create();?>
            <?php $lbwd = 155; $ipwd=300; ?>
            <fieldset>

                <?php if($user['User']['blocked']): ?>
                    <div style="color:#F00">
                        <br />کاربر گرامی. شما پیامهای تبلیغاتی تلفن همراه خود را غیر فعال نموده اید که این باعث عدم امکان ارسال پیامهای سایت جیب به تلفن همراه شما میشود.
                        &nbsp;
                        برای رفع این مشکل میتوانید از <?php echo $this->Html->link('راهنما',array('action'=>'help')); ?> استفاده کنید و رفع بلاک را به ما گزارش دهید.
                        <br />
                        <button style="width: 100%; margin:2px 0;" onclick="window.location='<?php echo $this->Html->url(array('controller'=>'reminders', 'action'=>'unblock')) ?>'; return false;">پیامکهای تبلیغاتی خود را فعال کرده ام</button>
                    </div>
                    <br /><br />
                <?php endif; ?>

                <?php if(!$remaining_sms): ?>
                    <span style="color:#F70000;">
                    کاربر عزیز، شما اعتبار پیامک ندارید به همین دلیل هیچ یادآور پیامکی برای شما ارسال نخواهد شد.
                </span>
                    <br /><br />
                <?php endif; ?>

                <?php echo $this->Form->label('User.cell','شماره همراه', array('style'=>' display:inline-block;' )); ?>
                <?php echo $this->Form->error('User.cell'); ?>
                <?php echo $this->Form->text('User.cell',array('maxlength'=>13,'style'=>' direction:ltr;', 'value'=>$user['User']['cell'])); ?>
                <?php echo $this->Html->image('info.png', array('id'=>'UserCellTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                <br/><br/>

                <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  class="padding-td" style="width:500px; margin:auto;">
                        <thead class="table-primary" >
                        <th class="padding-td"></th>
                        <th class="text-center padding-td">
                            <span class="fa fa-at">&nbsp;</span>&nbsp;
                            <span style="display:inline-block;">پست الکترونیکی</span>
                        </th>
                        <th class="text-center padding-td">
                            <span class="fa fa-mobile-phone">&nbsp;</span>
                            <span style="display:inline-block;">پیام کوتاه</span>
                        </th>
                        </thead>
                        <tr>
                            <td>یادآور چک ها</td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.check.medium.email', array('style'=>'width:auto;', 'value'=>'email', 'hiddenField'=> false, 'checked'=> isset( $settings['check_medium']['email']) )); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.check.medium.sms', array('style'=>'width:auto;', 'value'=>'sms', 'hiddenField'=> false, 'checked'=> isset( $settings['check_medium']['sms']))); ?></td>
                        </tr>
                        <tr>
                            <td>یادآور اقساط</td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.installment.medium.email', array('style'=>'width:auto;', 'value'=>'email', 'hiddenField'=> false, 'checked'=> isset( $settings['installment_medium']['email']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.installment.medium.sms', array('style'=>'width:auto;', 'value'=>'sms', 'hiddenField'=> false, 'checked'=> isset( $settings['installment_medium']['sms']))); ?></td>
                        </tr>
                        <tr>
                            <td>یادآور بدهی و طلب‌ها</td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.debt.medium.email', array('style'=>'width:auto;', 'value'=>'email', 'hiddenField'=> false, 'checked'=> isset( $settings['debt_medium']['email']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.debt.medium.sms', array('style'=>'width:auto;', 'value'=>'sms', 'hiddenField'=> false, 'checked'=> isset( $settings['debt_medium']['sms']))); ?></td>
                        </tr>
                        <tr>
                            <td>یادآور یادداشت‌ها</td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.note.medium.email', array('style'=>'width:auto;', 'value'=>'email', 'hiddenField'=> false, 'checked'=> isset( $settings['note_medium']['email']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.note.medium.sms', array('style'=>'width:auto;', 'value'=>'sms', 'hiddenField'=> false, 'checked'=> isset( $settings['note_medium']['sms']))); ?></td>
                        </tr>
                        <tr>
                            <td>یادآور عدم ورود</td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.login.medium.email', array('style'=>'width:auto;', 'value'=>'email', 'hiddenField'=> false, 'checked'=> isset( $settings['loginreminder_medium']['email']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.login.medium.sms', array('style'=>'width:auto;', 'value'=>'sms', 'hiddenField'=> false, 'checked'=> isset( $settings['loginreminder_medium']['sms']))); ?></td>
                        </tr>
                    </table></div>

                <br /><br />

                <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  class="padding-td" style="width:500px; margin:auto;">
                        <thead class="table-primary" >
                        <th class="padding-td"></th>
                        <th class="text-center padding-td">روز سررسید</th>
                        <th class="text-center padding-td">یک روز قبل</th>
                        <th class="text-center padding-td">سه روز قبل</th>
                        <th class="text-center padding-td">هفت روز قبل</th>
                        </thead>
                        <tr>
                            <td>یادآور چک ها</td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.check.frequency.theday', array('style'=>'width:auto;', 'value'=>'theday', 'hiddenField'=> false, 'checked'=> isset( $settings['check_freq']['theday']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.check.frequency.1before', array('style'=>'width:auto;', 'value'=>'1', 'hiddenField'=> false, 'checked'=> isset( $settings['check_freq']['1']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.check.frequency.3before', array('style'=>'width:auto;', 'value'=>'3', 'hiddenField'=> false, 'checked'=> isset( $settings['check_freq']['3']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.check.frequency.7before', array('style'=>'width:auto;', 'value'=>'7', 'hiddenField'=> false, 'checked'=> isset( $settings['check_freq']['7']))); ?></td>
                        </tr>
                        <tr>
                            <td>یادآور اقساط</td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.installment.frequency.theday', array('style'=>'width:auto;', 'value'=>'theday', 'hiddenField'=> false, 'checked'=> isset( $settings['installment_freq']['theday']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.installment.frequency.1before', array('style'=>'width:auto;', 'value'=>'1', 'hiddenField'=> false, 'checked'=> isset( $settings['installment_freq']['1']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.installment.frequency.3before', array('style'=>'width:auto;', 'value'=>'3', 'hiddenField'=> false, 'checked'=> isset( $settings['installment_freq']['3']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.installment.frequency.7before', array('style'=>'width:auto;', 'value'=>'7', 'hiddenField'=> false, 'checked'=> isset( $settings['installment_freq']['7']))); ?></td>
                        </tr>
                        <tr>
                            <td>یادآور بدهی و طلب‌ها</td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.debt.frequency.theday', array('style'=>'width:auto;', 'value'=>'theday', 'hiddenField'=> false, 'checked'=> isset( $settings['debt_freq']['theday']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.debt.frequency.1before', array('style'=>'width:auto;', 'value'=>'1', 'hiddenField'=> false, 'checked'=> isset( $settings['debt_freq']['1']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.debt.frequency.3before', array('style'=>'width:auto;', 'value'=>'3', 'hiddenField'=> false, 'checked'=> isset( $settings['debt_freq']['3']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.debt.frequency.7before', array('style'=>'width:auto;', 'value'=>'7', 'hiddenField'=> false, 'checked'=> isset( $settings['debt_freq']['7']))); ?></td>
                        </tr>
                        <tr>
                            <td>یادآور یادداشت‌ها</td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.note.frequency.theday', array('style'=>'width:auto;', 'value'=>'theday', 'hiddenField'=> false, 'checked'=> isset( $settings['note_freq']['theday']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.note.frequency.1before', array('style'=>'width:auto;', 'value'=>'1', 'hiddenField'=> false, 'checked'=> isset( $settings['note_freq']['1']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.note.frequency.3before', array('style'=>'width:auto;', 'value'=>'3', 'hiddenField'=> false, 'checked'=> isset( $settings['note_freq']['3']))); ?></td>
                            <td class="text-center"><?php echo $this->Form->checkbox('Reminder.note.frequency.7before', array('style'=>'width:auto;', 'value'=>'7', 'hiddenField'=> false, 'checked'=> isset( $settings['note_freq']['7']))); ?></td>
                        </tr>
                    </table></div>
                <br/><br/>

            </fieldset>
            <?php echo $this->Form->end(array('label'=>'ثبت','style'=>'width:auto;','div'=>array('style'=>'float:none;','align'=>'center')));?>
        </div>
    </div>
</div>


<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">یادآور ها<?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export'),array('escape' => false,'id'=>'excelExport')); ?></h2>

    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
        <?php $tableHeaders = $html->tableHeaders( array( 
            'عنوان',
            $paginator->sort( 'تاریخ', 'time' ,array('url'=>array('#'=>'#dataTable'))),
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
                    <td colspan="10" style="text-align:center"> موردی برای نمایش وجود ندارد </td>
                </tr>
        <?php endif; ?>
        <?php echo '<tfoot class=\'dark\'>' . $tableHeaders . '</tfoot>'; ?>    </table></div>


    <?php echo $this->element('pagination/bottom'); ?>

</div>
<div class="clear"></div>

<script type="text/javascript">
//<![CDATA[
$(function(){
    //tips
    jeeb.tip($('#excelExport'),'70','خروجی اکسل');
    jeeb.tip($('.view'),'50','نمایش');
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.showtext'),'80','نمایش متن');
    jeeb.tip($('.delete'),'70','پاک کردن');
    jeeb.tip($('.do'),'145','علامت زدن به عنوان انجام شده');
    jeeb.tip($('#UserCellTip'),'171','شماره تلفن همراه خود را برای دریافت پیامک های سیستم یادآور وارد نمائید');
    
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