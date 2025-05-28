<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('درآمد‌ها', array('action' => 'index'));?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>ویرایش درآمد</h2>
    <div class="incomes form">
        <?php echo $this->Form->create('Income');?>
        <fieldset>
		<?php
            echo $this->Form->input('id',array('label'=>'id'));
            echo $this->Form->hidden('Transaction.id',array('value'=>$this->data['Transaction']['id']));
            echo $this->Form->input('Transaction.amount',array('label'=>'مبلغ (ریال)','maxlength'=>15,'style'=>'direction:ltr;'));
            echo $this->Form->label('Income.income_type_id','نوع درآمد ',array('style'=>'margin-left:50px'));
            echo $this->Form->select('Income.income_type_id',$incomeTypes);
            echo $this->Form->input('Transaction.account_id',array('label'=>'واریز به','empty'=>false,
                            'after'=> '&nbsp;&nbsp;'.$this->Html->tag('span','موجودی: <span></span>', array('id'=>'AccountBalance', 'class'=>'smallfont'))
                            ));
            echo $this->Form->input('Income.individual_id',array('label'=>'شخص','empty'=>true));
            echo $this->Form->input('Transaction.date',array('label'=>'تاریخ','class'=>'datepicker','type'=>'text'));
            echo $this->Form->input( 'Income.description', array( 'label' => 'توضیحات', 'value' => str_replace( '\n', "\n", $this->data['Income']['description'] ) ) );
       ?>
            <div class="clear">&nbsp;</div>

            <div class="input text">
                <label for="IncomeIncomeCategoryId">برچسب: </label>
                <div>
                    <?php echo $this->Form->text('TransactionTag.tag_id',array('value'=>'','autocomplete'=>'off')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'TagCategoryTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                    <div id="groupListHolder"></div>
                </div>
            </div>

            <div class="clear">&nbsp;</div>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت انواع درآمد',array('controller'=>'incomeTypes','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت برچسب‌ها',array('controller'=>'tags','action'=>'index'));?></span>
            <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('راهنمای ثبت دخل و خرج با پیامک',array('controller'=>'pages','action'=>'smstonote'));?></span>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>

<script type="text/javascript">
//<![CDATA[
balances = <?php echo json_encode($accountsbalance); ?>;
$(function(){
    jeeb.tip($('#TagCategoryTip'),'200','شما میتوانید با وارد کردن برچسبی که مورد نظرتان است و یا انتخاب آن از لیست این مورد را برچسب دار کنید. شما میتوانید بیش از یک برچسب به یک مورد متصل کنید.');
    //number format
    jeeb.FormatPrice($('#TransactionAmount'));
    //bind the sub categories
    jeeb.bindIncomeSubTypes($('#IncomeIncomeTypeId'),<?php echo $this->Javascript->object($incomeTypesData); ?>,<?php if(!$this->data['Income']['income_sub_type_id']) { echo 0; } else { echo $this->data['Income']['income_sub_type_id']; } ?>,'IncomeIncomeSubTypeId');
    jeeb.accountBalance(balances, $('#TransactionAccountId'), $('#AccountBalance > span'));
    $('#TransactionAccountId').change(function(ev){ jeeb.accountBalance(balances, ev.target, '#AccountBalance > span'); });
    
    dflt = <?php echo json_encode(empty($this->data['Transaction']['TransactionTag'])? array() : $this->data['Transaction']['TransactionTag']); ?>;
    categories = <?php echo json_encode(empty($tags)? array() : $tags); ?>;
    new FloatingList({
        input: '#TransactionTagTagId', 
        listholder: '#groupListHolder', 
        data: categories,
        preload: dflt,
        allowNew: true,
        empty: false
    });
});
//]]>
</script>