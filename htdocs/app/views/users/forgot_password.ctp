
<div class="col-xs-16 col-md-8 col-md-offset-4">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>بازیابی رمز عبور</h2>
        </div>
        <div class="panel-body">
            <div class="users form">
                <?php echo $this->Form->create('User', array('action' => 'forgotPassword'));?>
                <div class="form-group">
                    <?php echo $this->Form->label('User.email','آدرس ایمیل',array('for'=>'email')); ?><br/>
                    <?php echo $form->text('email', array('class'=>'form-control')); ?><br/>
                </div>
                <?php echo $this->Form->end(array('label'=>'ارسال ایمیل','class'=>'btn btn-success','div'=>array('style'=>'float:none;','align'=>'left')));?>
            </div>
        </div>
    </div>

</div>
