<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('وام‌ها', array('controller'=>'loans','action' => 'index'));?></li>
                <li><?php echo $this->Html->link('وام‌ '.$loan['Loan']['name'], array('controller'=>'loans','action' => 'view',$loan['Loan']['id']));?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>افزودن قسط به وام <?php echo $loan['Loan']['name']; ?></h2>
    <div class="installments form">
        <?php echo $this->Form->create('Installment',array('action'=>'add/'.$loan['Loan']['id']));?>
        <fieldset>
		<?php
		echo $this->Form->input('loan_id',array('type'=>'hidden','value'=>$loan['Loan']['id']));
		echo $this->Form->input('amount',array('label'=>'مبلغ','maxlength'=>15,'style'=>'direction:ltr;'));
		echo $this->Form->input('due_date',array('label'=>'تاریخ سر رسید','type'=>'text','class'=>'datepicker'));
		echo $this->Form->input('notify',array('label'=>'آگاه سازی','type'=>'select','options'=>array('yes'=>'بله','no'=>'خیر')));
                echo $this->Form->input('description',array('label'=>'توضیحات','style'=>'width:250px;'));
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
    //number format
    jeeb.FormatPrice($('#InstallmentAmount'));
});
//]]>
</script>