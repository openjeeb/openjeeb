<br/>
<div class="col-xs-16 col-md-10 col-md-offset-3">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>تغییر رمز</h2>
        </div>
        <div class="panel-body">
            <div class="users form">
                <?php echo $this->Form->create('User');?>
                <fieldset>
                    <?php
                    echo $this->Form->input('id');
                    echo $this->Form->input('old_password',array('type'=>'password','label'=>'رمز قبلی','value'=>''));
                    echo $this->Form->input('password',array('value'=>'','label'=>'رمز جدید'));
                    echo $this->Form->input('password_repeat',array('type'=>'password','value'=>'','label'=>'تکرار رمز جدید'));
                    ?>
                </fieldset>
                <?php echo $this->Form->end(array('label'=>'ثبت','style'=>'width:auto;','div'=>array('style'=>'float:none;','align'=>'center')));?>
            </div>
        </div>
    </div>
</div>

