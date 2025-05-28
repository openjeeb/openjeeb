<br/>
<div class="col-xs-16 col-md-8 col-md-offset-4 ">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>تغییر آدرس ایمیل</h2>
        </div>
        <div class="panel-body">
            <div class="users form">
                <?php echo $this->Form->create('User');?>
                <fieldset>
                    <?php
                    echo $this->Form->input('id');
                    echo $this->Form->input('email',array('value'=>'','label'=>'آدرس ایمیل جدید', 'style'=>'direction:ltr;'));
                    echo $this->Form->input('password',array('type'=>'password','label'=>'رمز کنونی','value'=>''));
                    ?>
                </fieldset>
                <?php echo $this->Form->end(array('label'=>'ثبت','style'=>'width:auto;','div'=>array('style'=>'float:none;','align'=>'center')));?>
            </div>
        </div>
    </div>
</div>
