<div class="col-xs-16 col-md-4">
    <div class="box">
        <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link( 'یادداشت‌ها', array( 'action' => 'index' ) ); ?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
        <h2>ویرایش یادداشت</h2>
        <div class="notes form">
            <?php echo $this->Form->create( 'Note' ); ?>
            <fieldset>
                <?php
                echo $this->Form->input( 'id' );
                echo $this->Form->input( 'subject', array( 'label' => 'عنوان' ) );
                echo $this->Form->input( 'date', array( 'label' => 'تاریخ', 'class'=>'datepicker', 'value' => $this->data['Note']['date'],'type'=>'text' ) );
                echo $this->Form->input( 'time', array( 'label' => 'ساعت', 'value' => $this->data['Note']['time'],'type'=>'text' ) );
                echo $this->Form->input( 'content', array( 'label' => 'متن', 'value' => str_replace( '\n', "\n", $this->data['Note']['content'] ) ) );
                echo $this->Form->input( 'status', array( 'label' => 'وضعیت', 'type'=>'select','options'=>array('due'=>'انجام نشده','done'=>'انجام شده') ) );
                ?>
            </fieldset>
            <?php echo $this->Form->end( 'ثبت' ); ?>
        </div>

    </div>

</div>
<div class="clear"></div>
    
<script type="text/javascript">
//<![CDATA[
$(function(){
    //tips
    $("#NoteTime").mask("99:99");
    
});
//]]>
</script>