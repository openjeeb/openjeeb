<div class="col-xs-16 col-md-4">
    <div class="box">
        <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link( 'برچسب ها', array( 'action' => 'index' ) ); ?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
        <h2>ویرایش برچسب</h2>
        <div class="notes form">
            <?php echo $this->Form->create( 'Tag' ); ?>
            <fieldset>
                <?php
                echo $this->Form->input( 'id' );
                echo $this->Form->input( 'name', array( 'label' => 'عنوان' ) );
                //echo $this->Form->input( 'status', array( 'label' => 'وضعیت', 'type'=>'select','options'=>array('active'=>'فعال','inactive'=>'غیرفعال') ) );
                ?>
            </fieldset>
            <?php echo $this->Form->end( 'ثبت' ); ?>
        </div>

    </div>

</div>
<div class="clear"></div>
