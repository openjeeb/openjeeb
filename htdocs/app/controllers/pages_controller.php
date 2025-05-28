<?php

uses( 'sanitize' );

class PagesController extends AppController {

    var $name = 'Pages';
    var $uses = array( 'User' );
    var $components = array( 'Rabbit' );

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allowedActions = array( 'home', 'whatisjeeb', 'features', 'mobile', 'faq', 'help', 'contact', 'about', 'off', 'bugs', 'application', 'activation', 'captchaImage', 'offlinePurchase', 'windows', 'smstonote', 'loans' );
        $this->layout = 'page';
    }

    function home() {
        $this->layout = 'home';
        $this->set( 'title_for_layout', 'سیستم حسابداری شخصی' );
    }

    function whatisjeeb() {
        $this->set( 'title_for_layout', 'جیب چیست؟' );
    }

    function features() {
        $this->set( 'title_for_layout', 'امکانات' );
    }

    function captchaImage() {
        App::import( 'Vendor', 'captcha' );
        $captcha = new captcha();
        $captcha->show_captcha();
    }
    
    function _persianDigit2LatinDigit($string){
        $trans = array( "۰" => "0", "۱" => "1", "۲" => "2", "۳" => "3", "۴" => "4", 
                        "٠" => "0", "١" => "1", "٢" => "2", "٣" => "3", "٤" => "4", 
                        "۵" => "5", "۶" => "6",	 "۷" => "7", "۸" => "8", "۹" => "9",
                        "٥" => "5", "٦" => "6",	 "٧" => "7", "٨" => "8", "٩" => "9");
        $string = strtr($string, $trans);
        return $string;
    }
    
    function _isValidIBAN($iban) {
        $iban = str_replace(' ', '', strtoupper($iban)); // حذف فاصله‌ها و تبدیل به حروف بزرگ
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $iban)) {
            return false; // بررسی فرمت کلی
        }
    
        $rearranged = substr($iban, 4) . substr($iban, 0, 4); // جابجایی ۴ کاراکتر اول
        $numeric = '';
        foreach (str_split($rearranged) as $char) {
            $numeric .= ctype_digit($char) ? $char : (ord($char) - 55); // تبدیل حروف به عدد
        }
    
        // محاسبه مود ۹۷
        $remainder = '';
        foreach (str_split($numeric, 9) as $chunk) {
            $remainder = ($remainder . $chunk) % 97;
        }
    
        return $remainder == 1;
    }    
}

?>