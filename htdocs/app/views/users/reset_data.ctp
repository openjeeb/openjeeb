<br/>
<br/>
<div class="clear"></div>
<div class="col-xs-16 col-md-8 col-md-offset-4 " id="login">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 >پاک کردن تمام اطلاعات</h2>
        </div>
        <div class="panel-body">
            <div class="users form">
                <?php echo $this->Form->create('User', array('action' => 'resetData'));?>
                <fieldset>
                    <div><h3 style="color:red;line-height: 2.5rem">شما در حال پاک کردن تمام اطلاعاتی هستید که در جیب وارد کرده‌اید، در صورت پاک کردن این اطلاعات بازگشت اطلاعات غیرممکن است.</h3></div>
                    <?php echo $this->Form->label('User.user_password','رمز عبور'); ?><br/>
                    <?php echo $this->Form->text('user_password',array('type'=>'password','style'=>'direction:ltr')); ?><br/><br/>
                    <?php echo $this->Form->label('User.password_repeat','تکرار رمز عبور'); ?><br/>
                    <?php echo $this->Form->text('password_repeat',array('type'=>'password','style'=>'direction:ltr')); ?><br/><br/>
                </fieldset>
                <?php echo $this->Form->end(array('label' => 'پاک کردن تمام اطلاعات', 'name' => 'delete', 'style'=>'width:150px','div' => array('align' => 'center','style'=>'width:inherit;')));?>
            </div>
        </div>
    </div>
</div>
