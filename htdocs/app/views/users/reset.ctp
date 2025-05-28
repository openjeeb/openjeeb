<br/>
<br/>
<div class="clear"></div>
<div class="col-xs-16 col-md-6 prefix_5 suffix_5 rounded">
    <div class="box">
        <h2>تغییر رمز عبور</h2>
        <?php echo $this->Session->flash('auth'); ?>
        <br/>
        <div class="users form">
            <?php echo $this->Form->create('User', array('action' => 'reset'));?>
            <fieldset>
                <?php echo $form->input('forgot_password_code',array('type'=>'hidden','value'=>$forgot_password_code)); ?>
                <?php echo $this->Form->label('User.password','رمز عبور جدید'); ?><br/>
                <?php echo $this->Form->text('password',array('type'=>'password','style'=>'direction:ltr')); ?><br/>
                <?php echo $this->Form->label('User.verify_password','تکرار رمز عبور جدید'); ?><br/>
                <?php echo $this->Form->text('verify_password',array('type'=>'password','style'=>'direction:ltr')); ?><br/><br/>
            </fieldset>
            <?php echo $this->Form->end('ثبت');?>
        </div>
    </div>
</div>
<div class="clear"></div>
