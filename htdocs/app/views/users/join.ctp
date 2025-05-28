<?php if( Configure::read('Newrouz.inTime') && false ): ?>
    <div id="flashMessage" class="error-message text-center">
        مجموعه جیب نوروز ۹۹ را <?= (time()<=strtotime('2020-03-20 07:49:00'))? "پیشاپیش" : "" ?> به شما تبریک میگوید.
        <br />
        به مناسبت فرارسیدن سال نو، مجموعه جیب تخفیف‌های پکیج‌ها را از ۱۷ اسفند الی ۱۷ فروردین برای شما در نظر گرفته است.
    </div>
<?php endif; ?>

<div class="col-xs-16 col-md-8 col-md-offset-4">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2>ثبت نام</h2>
        </div>
        <div class="panel-body">
            <?php echo $this->Session->flash('auth'); ?>
            <div class="users form">
                <?php echo $this->Form->create('User', array('action' => 'join'));?>
                <p align="center" style="color: #DF6300;padding: 10px;margin:0px;">لطفا آدرس ایمیل خود را جهت سهولت در پشتیبانی و ارسال یادآوری‌ها به طور صحیح وارد نمایید.‬</p>
                <div class="form-group">
                    <?php echo $this->Form->label('User.email','آدرس ایمیل'); ?><br/>
                    <?php echo $this->Form->error('User.email'); ?>
                    <?php echo $this->Form->text('User.email',array('class'=>'form-control jeeb-inline-input','style'=>'direction:ltr')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'UserEmailTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->Form->error('User.password'); ?>
                    <?php echo $this->Form->label('User.password','رمز عبور'); ?><br/>
                    <?php echo $this->Form->text('password',array('type'=>'password','class'=>'form-control jeeb-inline-input','style'=>'direction:ltr','value'=>'')); ?>
                    <?php echo $this->Html->image('info.png', array('id'=>'UserPasswordTip', 'alt'=>'راهنما', 'border' => '0')); ?>
                </div>
                <div class="form-group">

                </div>
                <div class="form-group">

                </div>
                <fieldset>
                    <br/><br/>
                    <div>
                        <?php echo $this->Form->label('User.captcha','کد امنیتی'); ?>
                        <?php echo $this->Form->text('captcha',array('style'=>'direction:ltr;width:80px;','value'=>'')); ?>
                        <div style="display: inline-block;vertical-align: bottom;">
                            <img id="captcha" style="vertical-align: baseline;" src="<?php echo $this->Html->url(array('controller'=>'pages','action'=>'captchaImage')); ?>" alt="" />
                            <a onclick="javascript:document.images.captcha.src='<?php echo $html->url(array('controller'=>'pages','action'=>'captchaImage')); ?>/?' + Math.round(Math.random(0)*1000)+1" href="javascript:void(0);">
                                <img src="<?php echo $this->webroot; ?>img/reload.png" alt="reset" border="0" />
                            </a>
                        </div>
                    </div>
                    <br/>
                </fieldset>
                <?php echo $this->Form->end(array('label' => 'ثبت نام', 'id'=>'blue_button', 'name' => 'pay', 'div' => array('style' => 'float:none;margin-left:auto;margin-right:auto;width:120px')));?>
            </div>
        </div>
    </div>



</div>
<div class="clear"></div>

<script type="text/javascript">
//<![CDATA[
$(function(){
    $("UserEmail").focus();
    //tips
    jeeb.tip($('#UserEmailTip'),'310','آدرس ایمیل خود را وارد کنید.<br/>آدرس ایمیل شما بعنوان نام کاربری شما در جیب استفاده خواهد شد.');
    jeeb.tip($('#UserPasswordTip'),'330','یک رمز عبور اختیاری وارد کنید.<br/>سعی کنید از رمزهایی مستحکم و با ترکیب اعداد و حروف استفاده کنید.');
    jeeb.tip($('#UserReferenceTip'),'300','آدرس ایمیل کاربری که جیب را به شما معرفی کرده است وارد کنید. پر کردن این قسمت اجباری نیست.');
    jeeb.tip($('#UserDiscountCodeTip'),'260','در صورتی که کد تخفیف دارید آنرا در این قسمت وارد کنید. پر کردن این قسمت اجباری نیست.');
    jeeb.tip($('#UserPlanTip'),'380','یکی از بسته‌های استفاده از جیب را انتخاب کنید.<br/>+ اطلاعات شما پس از اتمام اعتبار حداقل تا یکماه در سیستم نگهداری خواهد شد.');
    //check code
    $('#check_discount').click(function (e) {
        e.preventDefault();
        response=jeeb.CheckDiscountCode(
            '<?php echo $this->Html->url(array('controller'=>'users','action'=>'ajaxCheckDiscountCode'));?>',
            $('#UserDiscountCode').val()
        );
        if(!response){
            $('#check_discount_response').html('کد معتبر نیست');
            $('#check_discount_response').attr('style', 'color:#953B39;margin-right:5px;');
            $('#UserDiscountCode').attr('style', 'direction:ltr; width:100px; background-color: #D59392;');
            return;
        }
        $('input[name="data[User][plan]"]', '#UserJoinForm').filter('[value='+response['valid_plan']+']').attr('checked', true);
        $('#check_discount_response').html('تخفیف شما '+response['amount']+' ریال');
        $('#check_discount_response').attr('style', 'color: green;margin-right:5px;');
        $('#UserDiscountCode').attr('style', 'direction:ltr; width:100px; background-color: #CEECCE;');
        
    });
});
//]]>
</script>
