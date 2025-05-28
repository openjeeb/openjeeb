<div class="col-xs-16 col-md-10 box rounded">
    <h2>اشخاص</h2>
    <div id="new" class="individuals form">
        <?php echo $this->Form->create( 'Individual' ); ?>
        <fieldset>
            <?php echo $this->Form->label('Individual.name','نام '); ?><br/>
            <?php echo $this->Form->error('Individual.name'); ?>
            <?php echo $this->Form->text('Individual.name'); ?>
            <?php echo $this->Html->image('info.png', array('id'=>'IndividualNameTip', 'alt'=>'راهنما', 'border' => '0')); ?>
            <br/>
            
            <?php echo $this->Form->label('Individual.description','توضیحات'); ?><br/>
            <?php echo $this->Form->error('Individual.description'); ?>
            <?php echo $this->Form->textArea('Individual.description',array('style'=>'width:97%;')); ?><br/>
            <br/>
        </fieldset>
        <?php echo $this->Form->end( 'ثبت' ); ?>
    </div>

</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16 ">
    <div class="box">
        <h2><?php echo $this->Html->link('جستجو', '#',array('id'=>'toggle-filter','class'=>'hidden')); ?></h2>
        <div id="filter" class="form">
            <?php echo $this->Form->create('Individual'); ?>
            <fieldset>
                <?php
                echo $this->Form->input('Individual.search',array('type'=>'hidden','value'=>true));
                ?>
                <?php echo $this->Form->input('Individual.name',array('label'=>'نام','type'=>'text')); ?>
                <?php echo $this->Form->input('Individual.status',array('label'=>'وضعیت','type'=>'select','id'=>'FilterStatusId','empty'=>true,'options'=>array('active'=>'فعال','inactive'=>'غیرفعال'))); ?>
                <div class="input text">
                    <label>از تاریخ</label>
                    <div style='float:right;'><?php echo $this->Form->text('Individual.start_date',array('class'=>'datepicker','value'=>'')); ?></div>
                    <?php echo $this->element('filldate', array( 'start_date' => '#IndividualStartDate', 'end_date' => '#IndividualEndDate', 'oneline'=>true )); ?>
                </div>
                <?php echo $this->Form->input('Individual.end_date',array('label'=>'تا تاریخ','class'=>'datepicker','value'=>'')); ?>
                <?php echo $this->Form->input('Individual.description_search',array('label'=>'توضیحات','type'=>'text','value'=>'')); ?>

                <div class="clear">&nbsp;</div>
            </fieldset>
            <?php echo $this->Form->end('جستجو');?>
        </div>
    </div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 class="col-xs-16 col-md-3" id="page-heading">اشخاص <?php echo $this->Html->link('<i class="fa fa-file-excel-o"></i>',array('action'=>'export'),array('escape' => false,'id'=>'excelExport')); ?></h2>
    <div class="col-xs-16 col-md-3 col-md-offset-10" style="margin-top:15px;"><?php echo $this->element('pagination/top'); ?></div>
    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
        <?php
        $tableHeaders = $html->tableHeaders(
                array(
                    '',
                    $paginator->sort('نام','Individual.name',array('url'=>array('#'=>'#dataTable'))),
                    $paginator->sort('توضیحات','Individual.description',array('url'=>array('#'=>'#dataTable'))),
                    $paginator->sort('تاریخ ایجاد','Individual.created',array('url'=>array('#'=>'#dataTable'))),
                    $paginator->sort('وضعیت','Individual.status',array('url'=>array('#'=>'#dataTable'))),
                    'عملیات'
                    )
                );
        echo '<thead class="table-primary" >' . $tableHeaders . '</thead>';
        ?>

<?php foreach ( $individuals as $individual ): ?>
            <tr>
                 <td style="width:55px;">
                    <?php echo $this->Html->link('<i class="fa fa-sort-up"></i>', array('action' => 'sort', 'up'=>$individual['Individual']['sort'],'#'=>'#dataTable'),array('escape'=>false,'class'=>'up')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-sort-down"></i>', array('action' => 'sort', 'down'=>$individual['Individual']['sort'],'#'=>'#dataTable'),array('escape'=>false,'class'=>'down')); ?>
                </td>
                <td><?php echo $individual['Individual']['name']; ?>&nbsp;</td>
                <td><?php echo $individual['Individual']['description']; ?>&nbsp;</td>
                <td><?php echo $individual['Individual']['created']; ?>&nbsp;</td>
                <td style="width:80px;">
                    <?php if($individual['Individual']['status']=='inactive'): ?>
                        <?php echo $this->Html->link($this->Html->image('off.png ',array('alt'=>'نمایش در لیست')).'&nbsp;'.__($individual['Individual']['status'],true), array('action' => 'toggleshow', $individual['Individual']['id']),array('escape'=>false,'class'=>'showinlist')); ?>
                    <?php else: ?>
                        <?php echo $this->Html->link('<i class="fa fa-lightbulb-o"></i>'.'&nbsp;'.__($individual['Individual']['status'],true), array('action' => 'toggleshow', $individual['Individual']['id']),array('escape'=>false,'class'=>'hidefromlist')); ?>
                    <?php endif; ?>
                </td>
		<td style="width: 120px">
                    <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $individual['Individual']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $individual['Individual']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $individual['Individual']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-bar-chart"></i>', array('action' => 'view', $individual['Individual']['id']),array('escape'=>false,'class'=>'view')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
<?php endforeach; ?>
        <?php echo '<tfoot class=\'dark\'>' . $tableHeaders . '</tfoot>'; ?>    </table></div>

    <?php echo $this->element('pagination/bottom'); ?>

</div>
<div class="clear"></div>
<br/>

<script type="text/javascript">
//<![CDATA[
$(function(){
    //tips
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
    jeeb.tip($('.view'),'60','نمودارها');
    jeeb.tip($('#IndividualNameTip'),'145','نام شخص مورد نظر را وارد کنید.');    
});
//]]>
</script>
