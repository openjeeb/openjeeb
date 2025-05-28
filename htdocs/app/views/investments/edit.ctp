<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('سرمایه', array('action' => 'index'));?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>ویرایش سرمایه</h2>
    <div class="investments form">
        <?php echo $this->Form->create('Investment');?>
        <fieldset>
		<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name',array('label'=>'عنوان'));
		echo $this->Form->input('amount',array('label'=>'ارزش'));
		//echo $this->Form->input('date',array('label'=>'تاریخ خرید'));
                echo $this->Form->input('Investment.date',array('label'=>'تاریخ خرید','class'=>'datepicker','type'=>'text'));

		//echo $this->Form->input('currency_id',array('label'=>'نوع'));
	?>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>

<script type="text/javascript">
//<![CDATA[

$(function(){   
    jeeb.FormatPrice($('#InvestmentAmount'));
});

//]]>
</script>