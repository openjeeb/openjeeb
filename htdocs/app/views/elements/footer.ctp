<?php
$pdate = new PersianDate();
$m = $pdate->pdate_format(date('Y-m-d'), 'm');
if( $m <=3 ) {
    $season = 'spring';
} elseif( $m <= 6 ) {
    $season = 'spring';
}elseif( $m <= 9 ){
    $season = 'fall';
}else{
    $season = 'winter';
}
?>
<div class="<?php echo $season ?>" id="footer">

    <div class="row" style="margin-top: 250px; margin-right: 0px !important; margin-left: 0px !important;padding-bottom: 50px;">
        <div class="col-xs-16 col-md-2 col-md-offset-1">
            <ul>
                <li><?php echo $this->Html->link('صفحه نخست', array('controller' => 'pages', 'action' => 'home')); ?></li>
                <li><?php echo $this->Html->link('جیب چیست؟', array('controller' => 'pages', 'action' => 'whatisjeeb')); ?></li>
                <li><?php echo $this->Html->link('امکانات جیب', array('controller' => 'pages', 'action' => 'features')); ?></li>
            </ul>
        </div>

        <div class="col-xs-16 col-md-2 ">
            <ul>
                <li><?php echo $this->Html->link('ورود', array('controller' => 'users', 'action' => 'login')); ?></li>
                <li><?php echo $this->Html->link('ثبت نام', array('controller' => 'users', 'action' => 'join')); ?></li>
            </ul>
        </div>

        <div class="col-xs-16 col-md-2 ">
            <ul>
            </ul>
        </div>
    </div>
</div>
