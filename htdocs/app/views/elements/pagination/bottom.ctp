

<p align="center" class="font-smaller">
    <?php echo $this->Paginator->counter(array(
        'format' => 'صفحه %page% از %pages%, در حال نمایش %current% مورد از %count%, از %start% تا %end%'
    )); ?>
</p>

<div aria-label="Page navigation" align="center">
    <ul class="pagination">
        <li>
            <?php echo $this->Paginator->prev('&laquo; قبلی', array('class'=>'','url'=>array('#'=>'#dataTable'),'escape'=>false), null, array('class' => 'disabled','escape'=>false));?>
        </li>
        <li>
            <?php echo $this->Paginator->numbers(array('class'=>'','separator'=>'</li><li>','url'=>array('controller'=>$this->params['controller'], 'action'=>$this->params['action'])+$this->params['pass']+$this->params['named']+array('#'=>'#dataTable')));?>
        </li>
        <li>
            <?php echo $this->Paginator->next('بعدی &raquo;', array('class'=>'','url'=>array('#'=>'#dataTable'),'escape'=>false), null, array('class' => 'disabled','escape'=>false));?>
        </li>
    </ul>
</div>

