<div class="col-xs-16 col-md-4">
    <div class="box">
                <h2>
            <a href="#" id="toggle-shortlinks">دسترسی سریع</a>
        </h2>
        <div class="block" id="shortlinks">
            <ul class="menu">
                <li><?php echo $this->Html->link('خبرنامه‌ها', array('action' => 'index'));?></li>
            </ul>
            <br/>
        </div>
    </div>
</div>

<div class="col-xs-16 col-md-12">
    <div class="box">
    <h2>ویرایش خبرنامه</h2>
    <div class="newsletters form">
        <?php echo $this->Form->create('Newsletter');?>
        <fieldset>
		<?php
		echo $this->Form->input('id');
		echo $this->Form->input('subject',array('label'=>array('text'=>'عنوان خبرنامه','style'=>'width:120px;')));
		echo $this->Form->input('query',array('label'=>array('text'=>'Query','style'=>'width:120px;')));
		echo $this->Form->input('head_title',array('label'=>array('text'=>'عنوان سرمقاله','style'=>'width:120px;')));
		echo $this->Form->input('head_text',array('label'=>array('text'=>'متن سرمقاله','style'=>'width:120px;'),'value' => str_replace( '\n', "\n", $this->data['Newsletter']['head_text'] )));
		echo $this->Form->input('head_image',array('label'=>array('text'=>'تصویر سرمقاله','style'=>'width:120px;')));
		echo $this->Form->input('title1',array('label'=>array('text'=>'عنوان ۱','style'=>'width:120px;')));
		echo $this->Form->input('content1',array('label'=>array('text'=>'متن ۱','style'=>'width:120px;'),'value' => str_replace( '\n', "\n", $this->data['Newsletter']['content1'] )));
		echo $this->Form->input('title2',array('label'=>array('text'=>'عنوان ۲','style'=>'width:120px;')));
		echo $this->Form->input('content2',array('label'=>array('text'=>'متن ۲','style'=>'width:120px;'),'value' => str_replace( '\n', "\n", $this->data['Newsletter']['content2'] )));
		echo $this->Form->input('title3',array('label'=>array('text'=>'عنوان ۳','style'=>'width:120px;')));
		echo $this->Form->input('content3',array('label'=>array('text'=>'متن ۳','style'=>'width:120px;'),'value' => str_replace( '\n', "\n", $this->data['Newsletter']['content3'] )));
                echo $this->Form->input('sent',array('type'=>'select','options'=>array('no'=>'خیر','yes'=>'بلی'),'empty'=>false,'label'=>array('text'=>'ارسال شده؟','style'=>'width:120px;')));
	?>
        </fieldset>
	<?php echo $this->Form->end('ثبت');?>
    </div>

    </div>

</div>
<div class="clear"></div>
