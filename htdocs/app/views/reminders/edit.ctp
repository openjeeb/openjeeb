<div class="row">
    <div class="col-xs-16 col-md-3 ">
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
                </div>
            </div>
        </div>        
    </div>
    <div class="col-xs-16 col-md-12">
        <div class="box rounded">
            <h2>ویرایش یادآور</h2>
            <div class="reminder form">
                <?php echo $this->Form->create('Reminder');?>
                    <fieldset>
                         <?php
                            echo $this->Form->input('id',array('label'=>'id'));
                            echo $this->Form->input('Reminder.time',array('label'=>'تاریخ ارسال','class'=>'datepicker','type'=>'text'));
                            echo $this->Form->input('Reminder.exacttime',array('label'=>'زمان ارسال','type'=>'text')); // VALUE
                            echo $this->Form->label('Reminder.medium','شیوه ارسال',array('style'=>'margin-left:50px'));
                            echo $this->Form->label('Reminder.medium.sms','پیامک',array('style'=>'margin-left:10px'));
                            echo $this->Form->checkbox('Reminder.medium.sms', array('style'=>'width:auto;', 'value'=>'sms', 'hiddenField'=> false, 'checked'=>isset($this->data['Reminder']['medium']['sms']) ));
                            echo $this->Form->label('Reminder.medium.email','پست الکترونیکی',array('style'=>'margin-right:50px; margin-left:10px' ));
                            echo $this->Form->checkbox('Reminder.medium.email', array('style'=>'width:auto;', 'value'=>'email', 'hiddenField'=> false, 'checked'=>isset($this->data['Reminder']['medium']['email'])));
                        ?>
                    </fieldset>
                <?php echo $this->Form->end('ثبت');?>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>