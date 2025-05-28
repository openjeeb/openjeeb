<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('گروه‌های کاربری', array('action' => 'index'));?></li>
                <li><?php echo $this->Html->link('نمایش', array('action' => 'view',$this->data['UserGroup']['id']));?></li>
            </ul>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>ویرایش گروه کاربری</h2>
    <div class="userGroups form">
        <?php echo $this->Form->create('UserGroup');?>
        <fieldset>
		<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name',array('label'=>'نام'));
	?>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>
