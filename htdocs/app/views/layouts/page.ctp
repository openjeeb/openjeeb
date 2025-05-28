<?php
// Combine & Minify CSS
$this->AssetCompress->css('reset');
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
       
       
       
       <link type="text/plain" rel="author" href="http://jeeb.ir/humans.txt" />
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
        <div class="row" id="pages-content">
            <?php echo $content_for_layout; ?>
        </div>
        <?php if(!in_array($this->params['action'] ,array('home','whatisjeeb','features','faq','help','contact','about','bugs'))):?>
            <div class="row">
                <div class="col-xs-16 col-md-16">
                    <br/>
                    <?php echo $this->Html->link('گزارش اشکال',array('controller'=>'pages','action'=>'bugs'),array('class'=>'btn btn-danger pull-left')); ?>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <div class="container-fluid" style="padding-left: 0; padding-right: 0;">
        <?php echo $this->element('footer'); ?>
    </div>

    <script type="text/javascript">
        //<![CDATA[
        $(function(){
            $('#ssl').bt('<b>SSL فعال است</b><br/>با فعال بودن SSL اطلاعاتی که مابین رایانه شما و سرور جیب رد و بدل میشوند کد گذاری شده و در میانه راه قابل خوانده شدن نخواهند بود.',{positions:'bottom',width:150,fill:'#EFF2F5',strokeStyle:'#B7B7B7',spikeLength:10,spikeGirth:10,padding:10,cornerRadius:8,cssStyles:{fontSize:'11px'}});
            $('#no-ssl').bt('<b>SSL فعال نیست</b><br/>با فعال بودن SSL اطلاعاتی که مابین رایانه شما و سرور جیب رد و بدل میشوند کد گذاری شده و در میانه راه قابل خوانده شدن نخواهند بود.',{positions:'bottom',width:150,fill:'#EFF2F5',strokeStyle:'#B7B7B7',spikeLength:10,spikeGirth:10,padding:10,cornerRadius:8,cssStyles:{fontSize:'11px'}});
            $('#slide').layerSlider({
                autostart : true,
                skin : 'lightskin',
                skinsPath : '/css/'
            });
            $('#topLogin').click(function(){
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-16">
                <?php echo $this->element('sql_dump'); ?>
            </div>
        </div>
    </div>

</body>
</html>
