<div class="col-xs-16 col-md-4">
    <div class="box">
        <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link( 'بدهی / طلب‌ها', array( 'action' => 'index' ) ); ?></li>
            </ul>
        </div>
        <br/>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
        <h2>ویرایش <?php __( $this->data['Debt']['type'] ); ?></h2>
        <div class="debts form">
            <?php echo $this->Form->create( 'Debt' ); ?>
            <fieldset>
                <?php echo $this->Form->input( 'id', array( 'label' => 'id' ) ); ?>
                <?php
                echo $this->Form->input( 'amount', array( 'label' => 'مبلغ (ریال)', 'maxlength' => 15 , 'style'=>'direction:ltr;' ) );
                echo $this->Form->input( 'name', array( 'label' => 'عنوان' ) );
                echo $this->Form->input( 'due_date', array( 'label' => 'تاریخ موعد', 'type' => 'text', 'class' => 'datepicker' ) );
                echo $this->Form->input( 'created', array( 'label' => 'تاریخ ایجاد', 'type' => 'text', 'class' => 'datepicker', 'value' => end( explode( ' ', $this->data['Debt']['created'] ) ) ) );
                echo $this->Form->input( 'individual_id', array( 'label' => 'شخص', 'empty' => true ) );
                echo $this->Form->input( 'notify', array( 'label' => 'آگاه سازی', 'type' => 'select', 'options' => array( 'yes' => 'بله', 'no' => 'خیر' ) ) );
                ?>
                 <div class="clear">&nbsp;</div>

                 <div class="input text">
                     <label for="DebtDebtCategoryId">برچسب: </label>
                     <div>
                         <?php echo $this->Form->text('DebtTag.tag_id',array('value'=>'','autocomplete'=>'off')); ?>
                         <?php echo $this->Html->image('info.png', array('id'=>'TagCategoryTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                         <div id="groupListHolder"></div>
                     </div>
                 </div>

                 <div class="clear">&nbsp;</div>
                <span style="display: block;margin: 5px 5px 0 0;"><?php echo '<i class="fa fa-cog"></i>' ?>&nbsp;<?php echo $this->Html->link('مدیریت برچسب‌ها',array('controller'=>'tags','action'=>'index'));?></span>
            </fieldset>
            <?php echo $this->Form->end( 'ثبت' ); ?>
        </div>

    </div>

</div>
<div class="clear"></div>
<script type="text/javascript">
//<![CDATA[
    $(function() {
        jeeb.tip($('#TagCategoryTip'),'200','شما میتوانید با وارد کردن برچسبی که مورد نظرتان است و یا انتخاب آن از لیست این مورد را برچسب دار کنید. شما میتوانید بیش از یک برچسب به یک مورد متصل کنید.');
        jeeb.FormatPrice($('#DebtAmount'));
        
        dflt = <?php echo json_encode(empty($this->data['Debt']['DebtTag'])? array() : $this->data['Debt']['DebtTag']); ?>;
        categories = <?php echo json_encode(empty($tags)? array() : $tags); ?>;
        new FloatingList({
            input: '#DebtTagTagId', 
            listholder: '#groupListHolder', 
            data: categories,
            preload: dflt,
            allowNew: true,
            empty: false
        });
    });
//]]>
</script>