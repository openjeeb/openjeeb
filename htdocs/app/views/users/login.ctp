
<div class="col-xs-16 col-md-8 col-md-offset-4" id="login">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>ورود</h2>
        </div>
        <div class="panel-body">
            <?php echo $this->Session->flash('auth'); ?>
            <div class="users form">
                <?php echo $this->Form->create('User', array('action' => 'login'));?>
                <div class="form-group">
                    <?php echo $this->Form->label('User.email','نام کاربری (آدرس ایمیل)'); ?><br/>
                    <?php echo $this->Form->text('User.email',array('class'=>'form-control','value'=>@$demo? 'demo@jeeb.ir' : '')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->label('User.password','رمز عبور'); ?><br/>
                    <?php echo $this->Form->text('password',array('type'=>'password','class'=>'form-control','value'=>@$demo? 'demo' : '')); ?>
                </div>
                <div style="float:right;display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('بازیابی رمز عبور', array('controller' => 'users', 'action' => 'forgotPassword')); ?></div>
                <div class="clear"></div>
                <div style="float:right;display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('ثبت نام', array('controller' => 'users', 'action' => 'join')); ?></div>
                <?php echo $this->Form->end(array('label'=>'ورود','class'=>'btn btn-success','style'=>'float:left;width:auto;','align'=>'left'));?>
            </div>
        </div>

    </div>
</div>
<div class="clear"></div>

<script type="text/javascript">
//<![CDATA[
$(function(){
    $("#UserEmail").focus();
    <?php if($demo) : ?> $("#login .submit").css('width','100px'); <?php endif; ?>
});
//]]>
</script>