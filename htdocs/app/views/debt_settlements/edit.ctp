<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('بدهی / طلب‌ها', array('action' => 'index')); ?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>ویرایش تسویه بدهی/طلب</h2>
    <div class="debtSettlements form">
        <?php echo $this->Form->create('DebtSettlement');?>
        <fieldset>
		<?php
		echo $this->Form->input('id');
		echo $this->Form->input('amount',array('label'=>'مبلغ'));
	?>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>
