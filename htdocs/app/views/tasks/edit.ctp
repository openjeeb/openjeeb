<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('Tasks', array('action' => 'index'));?></li>
            </ul>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>Edit Task</h2>
    <div class="tasks form">
        <?php echo $this->Form->create('Task');?>
        <fieldset>
		<?php
		echo $this->Form->input('id',array('label'=>'id'));
		echo $this->Form->input('title',array('label'=>'title'));
		echo $this->Form->input('user_id',array('label'=>'user_id'));
		echo $this->Form->input('sdate',array('label'=>'sdate'));
		echo $this->Form->input('edate',array('label'=>'edate'));
	?>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>
