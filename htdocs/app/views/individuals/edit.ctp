<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('اشخاص', array('action' => 'index'));?></li>
            </ul>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>ویرایش شخص</h2>
    <div class="individuals form">
        <?php echo $this->Form->create('Individual');?>
        <fieldset>
		<?php
		echo $this->Form->input('id',array('label'=>'id'));
		echo $this->Form->input('name',array('label'=>'نام'));
		echo $this->Form->input('description',array('label'=>'توضیحات'));
	?>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>
