<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('دسترسی‌ها', array('action' => 'index'));?></li>
                <li><?php echo $this->Html->link('نمایش', array('action' => 'view',$this->data['ArosAco']['id']));?></li>
            </ul>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>ویرایش دسترسی</h2>
    <div class="arosAcos form">
        <?php echo $this->Form->create('ArosAco');?>
        <fieldset>
		<?php
		echo $this->Form->input('id');
		echo $this->Form->input('aro_id',array('label'=>'گروه کاربری'));
		echo $this->Form->input('aco_id',array('label'=>'دسترسی به'));
		echo $this->Form->input('_create',array('label'=>'ایجاد'));
		echo $this->Form->input('_read',array('label'=>'خواندن'));
		echo $this->Form->input('_update',array('label'=>'بروزرسانی'));
		echo $this->Form->input('_delete',array('label'=>'پاک کردن'));
	?>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>
