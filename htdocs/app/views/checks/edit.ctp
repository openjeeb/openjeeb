<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('چک‌ها', array('action' => 'index'));?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>ویرایش چک <?php __($this->data['Check']['type'],false); ?></h2>
    <div class="checks form">
        <?php echo $this->Form->create('Check');?>
        <fieldset>
            <?php echo $this->Form->input('id',array('label'=>'id')); ?>
            <?php 
                if($this->data['Check']['type']=='received') {
                    echo $this->Form->input('bank_id',array('label'=>'بانک','empty'=>false));
                    echo $this->Form->input('individual_id',array('label'=>'صادر شده توسط','empty'=>true)); 
                }
            ?>
            <?php 
                if($this->data['Check']['type']=='drawed' AND !$this->data['Check']['clear_transaction_id']) {
                    echo $this->Form->input('Check.account_id',array('label'=>'صادر شده از حساب جاری'));
                }
                if($this->data['Check']['type']=='drawed') {
                    echo $this->Form->input('individual_id',array('label'=>'شخص دریافت کننده چک','empty'=>true)); 
                }
            ?>
            <?php     
		echo $this->Form->input('Check.amount',array('label'=>'مبلغ','maxlength'=>15,'style'=>'direction:ltr;'));
		echo $this->Form->input('Check.due_date',array('label'=>'موعد چک','type'=>'text','class'=>'datepicker',));
		echo $this->Form->input('Check.serial',array('label'=>'سریال چک','maxlength'=>12));
		echo $this->Form->input('Check.created',array('label'=>'تاریخ ایجاد','type'=>'text','class'=>'datepicker','value'=>end(explode(' ',$this->data['Check']['created']))));
		echo $this->Form->input('Check.notify',array('label'=>'آگاه سازی','type'=>'select','options'=>array('yes'=>'بله','no'=>'خیر')));
       ?>
        <div class="clear">&nbsp;</div>

        <div class="input text">
            <label for="CheckCheckCategoryId">برچسب: </label>
            <div>
                <?php echo $this->Form->text('CheckTag.tag_id',array('value'=>'','autocomplete'=>'off')); ?>
                <?php echo $this->Html->image('info.png', array('id'=>'TagCategoryTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                <div id="groupListHolder"></div>
            </div>
        </div>

        <div class="clear">&nbsp;</div>
       <?php
		echo $this->Form->input('Check.description',array('label'=>'توضیحات'));
            ?>
        <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت برچسب‌ها',array('controller'=>'tags','action'=>'index'));?></span>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>

<script type="text/javascript">
//<![CDATA[
$(function(){
    jeeb.tip($('#TagCategoryTip'),'200','شما میتوانید با وارد کردن برچسبی که مورد نظرتان است و یا انتخاب آن از لیست این مورد را برچسب دار کنید. شما میتوانید بیش از یک برچسب به یک مورد متصل کنید.');
    //show/hide things!
    if($('input[name="data[Check][type]"]:checked').val()=='drawed') {
        $("#BankWrapper").hide();
        $("#AccountWrapper").show();
    }
    if($('input[name="data[Check][type]"]:checked').val()=='received') {
        $("#AccountWrapper").hide();
        $("#BankWrapper").show();
    }
    $('input[name="data[Check][type]"]', '#CheckEditForm').change(function(){
        if($('input[name="data[Check][type]"]:checked').val()=='drawed') {
            $("#BankWrapper").hide();
            $("#AccountWrapper").show();
        }
        if($('input[name="data[Check][type]"]:checked').val()=='received') {
            $("#AccountWrapper").hide();
            $("#BankWrapper").show();
        }
    });
    //format price
    jeeb.FormatPrice($('#CheckAmount'));    
    
    dflt = <?php echo json_encode(empty($this->data['Check']['CheckTag'])? array() : $this->data['Check']['CheckTag']); ?>;
    categories = <?php echo json_encode(empty($tags)? array() : $tags); ?>;
    new FloatingList({
        input: '#CheckTagTagId', 
        listholder: '#groupListHolder', 
        data: categories,
        preload: dflt,
        allowNew: true,
        empty: false
    });
    
    
});
//]]>
</script>
