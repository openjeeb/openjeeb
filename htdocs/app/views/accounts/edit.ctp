<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('حساب‌ها', array('action' => 'index'));?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>ویرایش حساب</h2>
    <div class="accounts form">
        <?php echo $this->Form->create('Account');?>
        <fieldset>
		<?php
                echo $this->Form->input('id',array('label'=>'id'));
                echo $this->Form->label('name',' عنوان حساب',array('style'=>'margin-left:40px'));
                echo $this->Form->error('name');
                echo $this->Form->text('name').'<br/>';
                echo $this->Form->label('init_balance',' موجودی اولیه',array('style'=>'margin-left:40px'));
                echo $this->Form->error('init_balance');
                echo $this->Form->text('init_balance' , array('style' => 'direction:ltr;')).'<br/>';
                echo $this->Form->label('type',' نوع حساب',array('style'=>'margin-left:50px'));
                echo $this->Form->error('type');
                echo $this->Form->select('type',array('deposit'=>'پس انداز','check'=>'جاری','cash'=>'نقدی','other'=>'سایر'),null,array('empty'=>false)).'<br/>';
                echo $this->Form->label('bank_id','بانک',array('style'=>'margin-left:83px')); 
                echo $this->Form->error('bank_id'); 
                echo $this->Form->select('bank_id',$banks,null,array('empty'=>true)).'<br/>';
	?>
            <div>
                <div style="float:right; width:107px;"><?php echo $this->Form->label('description','توضیحات'); ?></div>
                <div style="float:right"><?php echo $this->Form->textArea('description',array('style'=>'width:300px;')); ?></div>
                <div style="clear:both;">&nbsp;</div>
            </div>
        <br/><br/>
        <div>نکته: شما نمیتوانید موجودی یک حساب را ویرایش کنید، در صورتی که مایل به افزایش موجودی هستید باید یک درآمد ثبت کنید و در صورتی که مایل به کاهش موجودی هستید میتوانید یک هزینه برای این حساب ثبت کنید.</div>
        <div>همچنین میتوانید با استفاده از منوی تراکنش‌ها از طریق فرم انتقال وجه موجودی را از سایر حسابها به این حساب منتقل کنید.</div>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>
<br/><br/><br/><br/><br/><br/><br/>
<br/><br/><br/><br/><br/><br/><br/>
<script type="text/javascript">
//<![CDATA[
$(function(){
    //number format
    jeeb.FormatPrice($('#AccountInitBalance'));
});
//]]>
</script>