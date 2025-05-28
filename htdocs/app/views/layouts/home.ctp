<?php
// Combine & Minify CSS
$this->AssetCompress->css('reset');
$this->AssetCompress->css('text');
$this->AssetCompress->css('bootstrap.min');
//$this->AssetCompress->css('fontawesome-all.min');
$this->AssetCompress->css('fontawesome47.min');
$this->AssetCompress->css('jeeb');
$this->AssetCompress->css('smoothness/jquery-ui-1.8.7.custom');
$this->AssetCompress->css('20180614');

// Combine & Minify JS
$this->AssetCompress->script('jquery-1.9.1.min');
$this->AssetCompress->script('compatibility');
$this->AssetCompress->script('jquery-ui-1.10.1.custom.min');
$this->AssetCompress->script('bootstrap.min');
$this->AssetCompress->script('jquery.klass-0.1a');
$this->AssetCompress->script('jquery.klass.proxy-0.1a');
$this->AssetCompress->script('jquery.maskedinput.min');
$this->AssetCompress->script('jquery-jeeb');
$this->AssetCompress->script('jquery.bt.min');
$this->AssetCompress->script('20180614');
echo $html->docType( 'xhtml-trans' );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>جیب::<?php echo $title_for_layout; ?></title>
	<?php
		echo $this->Html->meta('icon');
		echo $scripts_for_layout;
		        echo $this->AssetCompress->includeCss();
                echo $this->AssetCompress->includeJs();
                echo $this->Js->writeBuffer(); // Any Buffered Scripts
	?>

         <!--[if lt IE 9]>
        <script src="<?php echo $this->webroot; ?>js/html5.js"></script>
        <![endif]-->
        <link rel="stylesheet" href="<?php echo $this->webroot; ?>css/slide.css" type="text/css" />
        <script src="<?php echo $this->webroot; ?>js/jquery-easing-1.3.js" type="text/javascript"></script>
        <script src="<?php echo $this->webroot; ?>js/slide.js"></script>

        <?php if(Configure::read('chart')=='google'): ?>
        <script type="text/javascript" src="http://www.google.com/jsapi"></script>
        <?php endif; ?>
</head>
<body>

<?php echo $this->element('topnav'); ?>
<?php echo $this->element('mobilenav'); ?>


<div class="container jeeb-container">
    <div class="row">
        <div id="home-top" class="col-xs-16 col-md-16">
            <?php echo $this->element('nav'); ?>
            <?php if(Configure::read('browser_support')=='none'): ?>
                <div class="error-message" id="flashMessage" align="center">
                    <span>کاربر گرامی متاسفانه مرورگر شما توسط جیب پشتیبانی نمیشود، لطفا از یک مرورگر مدرن مانند فایرفاکس نسخه ۴ به بالا استفاده نمایید.</span>
                    <br/>
                    <span><a href='http://www.mozilla.org/firefox?WT.mc_id=aff_en18&WT.mc_ev=click'><img src='http://www.mozilla.org/contribute/buttons/468x60bubble_b.png' alt='Firefox Download Button' border='0' /></a></span>
                </div>
            <?php endif; ?>
            <?php echo $this->Session->flash(); ?>
            <?php echo $this->Session->flash('auth'); ?>
        </div>
    </div>
    <div class="row hidden-xs"  style="margin-left: -24px;margin-right: -24px;">
        <div class="col-xs-16 col-md-16" >
            <div id="slide" class="hidden-xs">
                <div class="ls-layer" style="slideDirection:top;">
                    <img class="ls-bg" width="1164" height="450" src="/img/slide/slide_bg1.png" alt="layer"/>
                    <img class="ls-s1" style="right:50px;top:100px;" src="/img/slide/monitor.png" alt="جیب مانیتور"/>
                    <img class="ls-s2" style="left:110px;top:140px;slideDirection:left;delayin:600;durationin:1000;"
                         src="/img/slide/manage_your_pocket.png" alt="مدیر جیب خود باشید!"/>
                    <img class="ls-s3" style="left:270px;top:50px;slideDirection:top;delayin:900;durationin:1000;"
                         src="/img/jeeb_logo_ex.png" alt="جیب سیستم حسابداری شخصی"/>
                    <a class="ls-s4 blue_button" style="left:365px;top:230px;"
                       href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'join')); ?>">ثبت نام کنید</a>
                </div>

                <div class="ls-layer" style="slidedirection:right;">
                    <img class="ls-bg" width="1164" height="450" src="/img/slide/slide_bg2.png" alt="layer"/>
                    <img class="ls-s1" style="right:585px;top:190px;" src="/img/jeeb.png" alt="جیب"/>
                    <img class="ls-s2" style="right:510px;top:50px;delayin:200;durationin:1000;slideDirection:top;"
                         src="/img/slide/s2_income.png" alt="درآمد"/>
                    <img class="ls-s3" style="left:320px;top:55px;delayin:300;durationin:1000;slideDirection:top;"
                         src="/img/slide/s2_expense.png" alt="هزینه"/>
                    <img class="ls-s4" style="left:200px;top:140px;delayin:400;durationin:1000;slideDirection:left;"
                         src="/img/slide/s2_investment.png" alt="سرمایه"/>
                    <img class="ls-s5" style="left:210px;top:240px;delayin:500;durationin:1000;slideDirection:left;"
                         src="/img/slide/s2_debt.png" alt="بدهی"/>
                    <img class="ls-s6" style="left:310px;top:320px;delayin:600;durationin:1000;slideDirection:bottom;"
                         src="/img/slide/s2_credit.png" alt="طلب"/>
                    <img class="ls-s7" style="right:475px;top:320px;delayin:700;durationin:1000;slideDirection:bottom;"
                         src="/img/slide/reports.png" alt="گزارش‌ها"/>
                    <img class="ls-s8" style="right:410px;top:240px;delayin:800;durationin:1000;slideDirection:bottom;"
                         src="/img/slide/s2_loan.png" alt="وام"/>
                    <img class="ls-s9" style="right:410px;top:130px;delayin:900;durationin:1000;slideDirection:bottom;"
                         src="/img/slide/s2_check.png" alt="چک"/>-->
                    <img class="ls-s10" style="left:885px;top:75px;delayin:1200;durationin:1000;slideDirection:top;"
                         src="/img/slide/right_now.png" alt="همین حالا"/>-->
                    <a class="ls-s11 blue_button"
                       style="left:885px;top:260px;delayin:1400;durationin:1000;slideDirection:bottom;easingin:easeOutExpo;"
                       href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'join')); ?>">ثبت نام کنید</a>
                </div>

                <div class="ls-layer" style="slidedelay:4000;slidedirection:bottom;">
                    <img class="ls-bg" src="/img/slide/slide_bg4.jpg" alt="layer"/>
                    <img class="ls-s1" id="earth" style="left:770px;top:-67px;" src="/img/slide/earth.jpg" alt="جهان"/>
                    <a class="ls-s2" style="left:30px;top:40px;delayin:400;durationin:1000;slideDirection:top;"
                       href="<?php echo $this->Html->url(array('controller' => 'pages', 'action' => 'mobile')); ?>"><img
                                src="/img/slide/access_from_everwhere.png" alt="دسترسی به اطلاعات از هر نقطه جهان"/></a>
                    <a class="ls-s3"
                       style="left:570px;top:140px;delayin:600;durationin:900;slideDirection:left;easingIn:easeOutExpo;"
                       href="<?php echo $this->Html->url(array('controller' => 'pages', 'action' => 'mobile')); ?>"><img
                                src="/img/slide/tablet.png" alt="Tablet"/></a>
                    <a class="ls-s4"
                       style="left:340px;top:200px;delayin:800;durationin:900;slideDirection:left;easingIn:easeOutExpo;"
                       href="<?php echo $this->Html->url(array('controller' => 'pages', 'action' => 'mobile')); ?>"><img
                                src="/img/slide/mobile.png" alt="Mobile"/></a>
                    <a class="ls-s5"
                       style="left:150px;top:250px;delayin:1000;durationin:900;slideDirection:left;easingIn:easeOutExpo;"
                       href="<?php echo $this->Html->url(array('controller' => 'pages', 'action' => 'mobile')); ?>"><img
                                src="/img/slide/android_logo.png" alt="Mobile"/></a>
                    <a class="ls-s8 blue_button"
                       style="left:75px;top:150px;delayin:1350;durationin:900;slideDirection:left;easingIn:easeOutExpo;"
                       href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'join')); ?>">ثبت نام کنید</a>
                </div>

            </div>
        </div>
    </div>
    <div class="row xs-only">
        <div class="col-xs-16">
            <img class="img-responsive" style="margin-top: -1px;" src="/img/mobile_home.png" alt="layer"/>
        </div>
    </div>
    <div class="row" id="home-content">
        <?php echo $content_for_layout; ?>
        <div class="col-xs-16 col-md-16">
            <div >

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-16 col-md-16">
            <?php if(!in_array($this->params['action'] ,array('home','whatisjeeb','features','faq','help','contact','about','bugs'))):?>
                <span id="bug-report"><?php echo $this->Html->link('گزارش اشکال',array('controller'=>'pages','action'=>'bugs')); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="container-fluid" style="padding-left: 0; padding-right: 0;">
    <?php echo $this->element('footer'); ?>
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
        $('#slide').layerSlider({
            autostart: true,
            skin: 'lightskin',
            skinsPath: '/css/'
        });
        $('#topLogin').click(function () {
            $('#login-bar').slideToggle(300);
        });
        $('#mobileLogin').click(function () {
            $('#mobile-login-bar').slideToggle(300);
            if ($(".navbar-collapse").is(":visible") && $(".navbar-toggle").is(":visible") ) {
                $('.navbar-collapse').collapse('toggle');
            }
        });
        $('.navbar-toggle').click(function (e) {
            if ($('#mobile-login-bar').css("display") !='none'){
                $('#mobile-login-bar').slideToggle(300);
            }
        });
    });
    //]]>
</script>

</body>
</html>
