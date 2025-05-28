<?php

class captcha {

    public function show_captcha() {
        if (session_id() == "") {
            session_name("CAKEPHP");
            session_start();
        }

        $captchatext = rand(10000, 99999);
        $captchatext = substr($captchatext, 0, 5);
        $_SESSION['captcha'] = $captchatext;

        
        //background
        $im = imagecreatetruecolor(80, 40);
        $white = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $white);        
        
        //text
        $font = APP. 'vendors/captcha/fonts/IRANSansWeb_Bold.ttf';
        $black = imagecolorallocate($im, 80, 80, 80);
        imagettftext($im, 18, 0, 5, 27, $black, $font, self::_latinDigitToPersianDigit($captchatext));

        header('Content-Type: image/jpeg');
        header("Cache-control: private, no-cache");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Pragma: no-cache");
        
        imagejpeg($im);
        imagedestroy($im);
        ob_flush();
        flush();
    }

    function _latinDigitToPersianDigit($string){
        $trans = array(
            "0" => "۰",
            "1" => "۱",
            "2" => "۲",
            "3" => "۳",
            "4" => "۴",
            "5" => "۵",
            "6" => "۶",
            "7" => "۷",
            "8" => "۸",
            "9" => "۹"
        );
        $string = strtr($string, $trans);
        return $string;
    }
}
?>