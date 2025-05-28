<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('وام‌ها', array('action' => 'index'));?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>ویرایش وام</h2>
    <div class="loans form">
        <?php echo $this->Form->create('Loan');?>
        <fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name',array('label'=>'عنوان','maxlength'=>75));
        echo $this->Form->input('amount',array('label'=>'مبلغ','maxlength'=>15,'style'=>'direction:ltr;'));
		echo $this->Form->input('description',array('label'=>'توضیحات'));
		echo $this->Form->input('bank_id',array('label'=>'بانک'));
       ?>
        <div class="clear">&nbsp;</div>

        <div class="input text">
            <label for="LoanLoanCategoryId">برچسب: </label>
            <div>
                <?php echo $this->Form->text('LoanTag.tag_id',array('value'=>'','autocomplete'=>'off')); ?>
                <?php echo $this->Html->image('info.png', array('id'=>'TagCategoryTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                <div id="groupListHolder"></div>
            </div>
        </div>

        <div class="clear">&nbsp;</div>
        <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت برچسب‌ها',array('controller'=>'tags','action'=>'index'));?></span>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>
<script>
//<![CDATA[
    jeeb.tip($('#TagCategoryTip'),'200','شما میتوانید با وارد کردن برچسبی که مورد نظرتان است و یا انتخاب آن از لیست این مورد را برچسب دار کنید. شما میتوانید بیش از یک برچسب به یک مورد متصل کنید.');
    jeeb.FormatPrice($('#LoanAmount'));    
    
    dflt = <?php echo json_encode(empty($this->data['Loan']['LoanTag'])? array() : $this->data['Loan']['LoanTag']); ?>;
    categories = <?php echo json_encode(empty($tags)? array() : $tags); ?>;
    new FloatingList({
        input: '#LoanTagTagId', 
        listholder: '#groupListHolder', 
        data: categories,
        preload: dflt,
        allowNew: true,
        empty: false
    });
//]]>
</script>