<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('Transactions', array('action' => 'index'));?></li>
            </ul>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>Edit Transaction</h2>
    <div class="transactions form">
        <?php echo $this->Form->create('Transaction');?>
        <fieldset>
		<?php
		echo $this->Form->input('id',array('label'=>'id'));
		echo $this->Form->input('amount',array('label'=>'amount'));
		echo $this->Form->input('date',array('label'=>'date'));
		echo $this->Form->input('pyear',array('label'=>'pyear'));
		echo $this->Form->input('pmonth',array('label'=>'pmonth'));
		echo $this->Form->input('pday',array('label'=>'pday'));
		echo $this->Form->input('type',array('label'=>'type'));
		echo $this->Form->input('account_id',array('label'=>'account_id'));
		echo $this->Form->input('expense_id',array('label'=>'expense_id'));
		echo $this->Form->input('income_id',array('label'=>'income_id'));
		echo $this->Form->input('user_id',array('label'=>'user_id'));
	?>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>
