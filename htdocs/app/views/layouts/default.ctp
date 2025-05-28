<?php
//Set no caching
header("Expires: Thu, 07 Apr 1983 03:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
//Persian Date
App::import('Vendor', 'PersianDate', array('file' => 'persian.date.php'));
$persianDate=new PersianDate();
// Combine & Minify CSS
$this->AssetCompress->css('reset');
$this->AssetCompress->css('bootstrap.min');
//$this->AssetCompress->css('fontawesome-all.min');
$this->AssetCompress->css('fontawesome47.min');
$this->AssetCompress->css('jeeb');
$this->AssetCompress->css('smoothness/jquery-ui-1.8.7.custom');
$this->AssetCompress->css('amib');
$this->AssetCompress->css('20180614');
// Combine & Minify JS
$this->AssetCompress->script('jquery-1.9.1.min');
$this->AssetCompress->script('compatibility');
$this->AssetCompress->script('jquery-ui.min.1.13.3');
$this->AssetCompress->script('jquery.bt.min');
$this->AssetCompress->script('bootstrap.min');
$this->AssetCompress->script('jquery.format_currency');
$this->AssetCompress->script('jquery.klass-0.1a');
$this->AssetCompress->script('jquery.klass.proxy-0.1a');
$this->AssetCompress->script('jquery.maskedinput.min');
$this->AssetCompress->script('jquery-jeeb');
$this->AssetCompress->script('amib');
$this->AssetCompress->script('20180614');
if(Configure::read('chart')=='native'){
    $this->AssetCompress->script('charts');
    $this->AssetCompress->script('charts.export');
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php echo $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>جیب::<?php echo $title_for_layout; ?></title>
    <meta name="description" content="سیستم حسابداری شخصی جیب به مدیریت هزینه‌، درآمد، چک، بدهی و طلب شما کمک میکند." />
    <?php

    echo $this->Html->meta('icon');

    echo $this->AssetCompress->includeCss();
    echo $this->AssetCompress->includeJs();
    echo $this->Js->writeBuffer(); // Any Buffered Scripts
    echo $scripts_for_layout;
    ?>

    <link type="text/plain" rel="author" href="http://jeeb.ir/humans.txt" />
    <?php if(Configure::read('chart')=='google'): ?>
        <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <?php endif; ?>

</head>
<body>
<?php echo $this->element('topnav'); ?>
<?php echo $this->element('mobilenav'); ?>
<div id="content" class="container">
    <div class="row">
        <div class="col-xs-16">

            <div class="row">
                <div class="col-xs-16">
                    <?php echo $this->element('nav'); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-16">
                    <?php if(!empty($demo)): ?>
                        <div class="info" id="flashMessage" style="text-align:center;">
                            <?php echo $this->Html->link('شما در حال امتحان جیب هستید. برای ثبت نام اینجا کلیک کنید.', array( 'controller'=>'users','action'=>'join' ), array('style'=>'color: #31708f;')) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-16">
                    <?php echo $this->Session->flash(); ?>
                    <?php echo $this->Session->flash('auth'); ?>
                </div>
            </div>

            <div class="row">
                <?php echo $content_for_layout; ?>
            </div>
            <div class="row">
                <div class="col-xs-16">
                   <!-- <div align="center"><?php /*echo $this->Html->link($this->Html->image('invite_10days.png',array('alt'=>'دوستان خود را دعوت کنید','class'=>'img-responsive')), array('controller'=>'invitations','action'=>'index'), array('escape' => false)); */?></div>-->
                </div>
            </div>

            <div class="row">
                <div class="col-xs-16 col-md-4 col-md-offset-2 credit">
                    <span>&nbsp;</span>
                    <?php if($this->Session->read('Auth.User.id')): ?>
                        <span>اعتبار باقیمانده</span>
                        <span><?php if($remaining_days>0) {echo $persianDate->Convertnumber2farsi($remaining_days);} else {echo '0';} ?></span>
                        <span>روز</span>
                    <?php endif; ?>
                </div>

                <div class="col-xs-16 col-md-4 credit">تاریخ امروز: <?php echo $persianDate->pdate('Y/m/d'); ?></div>

                <?php if($this->Session->read('Auth.User.id')): ?>
                    <div class="col-xs-16 col-md-4  credit">آخرین ورود: <?php if(!is_null($this->Session->read('Auth.User.last_login'))) {echo $persianDate->pdate('H:i Y/m/d',  strtotime($this->Session->read('Auth.User.last_login')));} ?></div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-xs-16 col-md-16">
                        <br/>
                        <?php echo $this->Html->link('گزارش اشکال',array('controller'=>'pages','action'=>'bugs'),array('class'=>'btn btn-danger pull-left','style'=>'margin:5px')); ?>
                        <?php echo $this->Html->link('از ما بپرسید',array('controller'=>'pages','action'=>'contact'),array('class'=>'btn btn-default pull-left','style'=>'margin:5px')); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<div class="container-fluid" style="padding-left: 0; padding-right: 0;">
    <?php echo $this->element('footer_app'); ?>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-16">
            <?php echo $this->element('sql_dump'); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    //<![CDATA[
    $(function () {
        $('#ssl').bt('<b>SSL فعال است</b><br/>با فعال بودن SSL اطلاعاتی که مابین رایانه شما و سرور جیب رد و بدل میشوند کد گذاری شده و در میانه راه قابل خوانده شدن نخواهند بود.', {
            positions: 'bottom',
            width: 150,
            fill: '#EFF2F5',
            strokeStyle: '#B7B7B7',
            spikeLength: 10,
            spikeGirth: 10,
            padding: 10,
            cornerRadius: 8,
            cssStyles: {fontSize: '11px'}
        });
        $('#no-ssl').bt('<b>SSL فعال نیست</b><br/>با فعال بودن SSL اطلاعاتی که مابین رایانه شما و سرور جیب رد و بدل میشوند کد گذاری شده و در میانه راه قابل خوانده شدن نخواهند بود.', {
            positions: 'bottom',
            width: 150,
            fill: '#EFF2F5',
            strokeStyle: '#B7B7B7',
            spikeLength: 10,
            spikeGirth: 10,
            padding: 10,
            cornerRadius: 8,
            cssStyles: {fontSize: '11px'}
        });
    });
    //]]>
</script>

</body>
</html>