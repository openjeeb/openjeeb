<div class="col-xs-16 col-md-16">
    <div class="box rounded">
        <h2>برچسب جدید</h2>
        <div id="new" class="notes form">
            <?php echo $this->Form->create( 'Tag' ); ?>
            <fieldset>
                <?php echo $this->Form->label('Tag.name','عنوان'); ?>
                <?php echo $this->Form->error('Tag.name'); ?>
                <?php echo $this->Form->text('Tag.name'); ?>

            </fieldset>
            <?php echo $this->Form->end( 'ثبت' ); ?>
        </div>
    </div>
</div>
<div class="clear"></div>

<div class="col-xs-16 col-md-16">
    <h2 id="page-heading">برچسب‌ها</h2>


    <div class="table-responsive"><table class="table table-striped table-hover table-bordered"  id="dataTable" cellpadding="0" cellspacing="0">
        <?php $tableHeaders = $html->tableHeaders( array( 
            $paginator->sort( 'عنوان', 'name' ,array('url'=>array('#'=>'#dataTable'))),
            $paginator->sort( 'ایجاد', 'created' ,array('url'=>array('#'=>'#dataTable'))),
            //$paginator->sort( 'وضعیت', 'status' ),
            'عملیات' ) );
        echo '<thead class="table-primary" >' . $tableHeaders . '</thead>'; ?>

        <?php foreach ( $tags as $tag ): ?>
            <tr>
                <td><?php echo $tag['Tag']['name']; ?>&nbsp;</td>
                <td><?php echo $tag['Tag']['created']; ?>&nbsp;</td>
                <?php /*<td style="width: 165px">
                    <?php echo $this->Html->link(
                            $this->Html->image(
                                ($tag['Tag']['status']=='inactive')? 'off.png' : 'on.png',
                                array('alt'=>($tag['Tag']['status']=='inactive')? 'عدم نمایش در لیست' : 'نمایش در لیست')).'&nbsp;'.__($tag['Tag']['status'],true),
                            array('action' => 'toggleshow', $tag['Tag']['id']),array('escape'=>false,'class'=>'showinlist')
                            ); ?>
                </td>*/ ?>
                <td>
                    <?php echo $this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $tag['Tag']['id']),array('escape'=>false,'class'=>'edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php echo $this->Html->link('<i class="fa fa-times"></i>', array('action' => 'delete', $tag['Tag']['id']),array('escape'=>false,'class'=>'delete'), sprintf('آیا مطمئنید که میخواهید این اطلاعات را پاک کنید؟', $tag['Tag']['id'])); ?>&nbsp;&nbsp;&nbsp;&nbsp;
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
    jeeb.tip($('.edit'),'50','ویرایش');
    jeeb.tip($('.delete'),'70','پاک کردن');
});
//]]>
</script>