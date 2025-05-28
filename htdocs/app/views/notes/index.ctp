<?php
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
?>
<div class="col-xs-16 col-md-10">
    <div class="box rounded">
        <h2>یادداشت جدید</h2>
        <div id="new" class="notes form">
            <?php echo $this->Form->create( 'Note' ); ?>
            <fieldset>
                <?php echo $this->Form->label('Note.subject','عنوان'); ?><br/>
                <?php echo $this->Form->error('Note.subject'); ?>
                <?php echo $this->Form->text('Note.subject'); ?>
                <?php echo $this->Html->image('info.png', array('id'=>'NoteSubjectTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                <br/>

                <?php echo $this->Form->label('Note.date','تاریخ'); ?><br/>
                <?php echo $this->Form->error('Note.date'); ?>
                <?php echo $this->Form->text('Note.date',array('class'=>'datepicker','value'=>$persianDate->pdate('Y/m/d'))); ?>
                <br />

                <?php echo $this->Form->label('Note.time','ساعت'); ?><br/>
                <?php echo $this->Form->error('Note.time'); ?>
                <?php echo $this->Form->text('Note.time',array('value'=>'08:00')); ?>

                <br/>

                <?php echo $this->Form->label('Note.notify','یادآور'); ?><br/>
                <?php echo $this->Form->error('Note.notify'); ?>
                <?php echo $this->Form->select('Note.notify',array('1'=>'فعال','0'=>'غیر فعال'), 1, array('empty'=>false, 'separator'=>'&nbsp;&nbsp;&nbsp;', 'value'=>'1', 'style'=>'margin-top:10px;')); ?>

                <br />

                <?php echo $this->Form->label('Note.content','متن'); ?><br/>
                <?php echo $this->Form->error('Note.content'); ?>
                <?php echo $this->Form->textArea('Note.content',array('style'=>'width:97%;','rows'=>5)); ?><br/>

            </fieldset>
            <?php echo $this->Form->end( 'ثبت' ); ?>
        </div>
    </div>

</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16 ">
    <div class="box">
        <h2><?php echo $this->Html->link('جستجو', '#',array('id'=>'toggle-filter','class'=>'hidden')); ?></h2>
        <div id="filter" class="form">
            <?php echo $this->Form->create('Note'); ?>
            <fieldset>
                <?php
                echo $this->Form->input('Note.search',array('type'=>'hidden','value'=>true));
                echo $this->Form->input('Note.subject_search',array('label'=>'عنوان','type'=>'text','value'=>'')); ?>
                <div class="input text">
                    <label>از تاریخ</label>
                    <div style='float:right;'><?php echo $this->Form->text('Note.start_date',array('class'=>'datepicker','value'=>'')); ?></div>
                    <?php echo $this->element('filldate', array( 'start_date' => '#NoteStartDate', 'end_date' => '#NoteEndDate', 'oneline'=>true )); ?>
                </div>
                <?php
                echo $this->Form->input('Note.end_date',array('label'=>'تا تاریخ','class'=>'datepicker','value'=>''));
                echo $this->Form->input('Note.content_search',array('label'=>'در متن','type'=>'text','value'=>''));
                ?>
            </fieldset>
            <?php echo $this->Form->end('جستجو');?>
        </div>
    </div>

</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 class="col-xs-16 col-md-3" id="page-heading">یادداشت‌ها <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
        <?php $tableHeaders = $html->tableHeaders( array( 
            $paginator->sort( 'وضعیت', 'status' ,array('url'=>array('#'=>'#dataTable'))),
            $paginator->sort( 'عنوان', 'subject' ,array('url'=>array('#'=>'#dataTable'))),
            $paginator->sort( 'تاریخ', 'date' ,array('url'=>array('#'=>'#dataTable'))),
            $paginator->sort( 'ایجاد', 'created' ,array('url'=>array('#'=>'#dataTable'))),
            $paginator->sort( 'ویرایش', 'modified' ,array('url'=>array('#'=>'#dataTable'))),
            'عملیات' ) );
        echo '<thead class="table-primary" >' . $tableHeaders . '</thead>'; ?>

        <?php foreach ( $notes as $note ): ?>
            <tr>
                <td><?php if($note['Note']['status']=='done'){ echo '<i class="fa fa-check-square-o done"></i>'; }else{ echo '<i class="fa fa-square-o due"></i>'; } ?></td>
                <td><?php echo $note['Note']['subject']; ?>&nbsp;</td>
                <td><?php echo $note['Note']['date']; ?>&nbsp;</td>
                <td><?php echo $note['Note']['created']; ?>&nbsp;</td>
                <td><?php echo $note['Note']['modified']; ?>&nbsp;</td>
                <td style="width: 180px">
                    <?php 
                        if($note['Note']['status']=='done') {
                            echo $this->Html->image('blank.png',array('alt'=>'')); 
                        } else { 
                            echo $this->Html->link($this->Html->image('do.png',array('alt'=>'انجام')), array('action' => 'markDone', $note['Note']['id']),array('escape'=>false,'class'=>'do '));
                        } 
                    ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $note['Note']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $note['Note']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $note['Note']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-desktop"></i>', array('action' => 'view', $note['Note']['id']),array('escape'=>false,'class'=>'view','alt'=>'نمایش')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-bell"></i>', array('controller'=>'reminders','action' => 'view', 'note'=>$note['Note']['id']),array('escape'=>false,'class'=>'reminder','alt'=>'یادآور')); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php echo '<tfoot class=\'dark\'>' . $tableHeaders . '</tfoot>'; ?>    </table></div>


    <?php echo $this->element('pagination/bottom'); ?>

</div>
<div class="clear"></div>

<script type="text/javascript">
//<![CDATA[
$(function(){
    //tips
    jeeb.tip($('#excelExport'),'70','خروجی اکسل');
    jeeb.tip($('.view'),'45','نمایش');
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
    jeeb.tip($('.do'),'145','علامت زدن به عنوان انجام شده');
    jeeb.tip($('.reminder'),'50','یادآورها');
    jeeb.tip($('#NoteSubjectTip'),'171','یک عنوان برای یادداشت خود برگزینید.');
    
    $("#NoteTime").mask("99:99");
    
});
//]]>
</script>