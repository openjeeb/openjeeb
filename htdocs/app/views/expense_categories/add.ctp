<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('Expense Categories', array('action' => 'index'));?></li>
            </ul>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>Edit Expense Category</h2>
    <div class="expenseCategories form">
        <?php echo $this->Form->create('ExpenseCategory');?>
        <fieldset>
		<?php
		echo $this->Form->input('name',array('label'=>'name'));
		echo $this->Form->input('user_id',array('label'=>'user_id'));
	?>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>
