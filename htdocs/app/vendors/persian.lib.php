<?php

class PersianLib
{
    static public function enDigits($string)
    {
        $trans = array( "۰" => "0", "۱" => "1", "۲" => "2", "۳" => "3", "۴" => "4", 
                        "٠" => "0", "١" => "1", "٢" => "2", "٣" => "3", "٤" => "4", 
                        "۵" => "5", "۶" => "6",	 "۷" => "7", "۸" => "8", "۹" => "9",
                        "٥" => "5", "٦" => "6",	 "٧" => "7", "٨" => "8", "٩" => "9");
        return strtr($string, $trans);
    }

    static public function fa_number_format($val)
    {
        $fmt = numfmt_create( 'fa', NumberFormatter::DECIMAL);
        return numfmt_format($fmt, (string) $val);
                    
        return self::fa_normalize(number_format($val, 0, ".", ","), true);
    }
    
    static public function currency($val)
    {
        return self::fa_number_format($val);
    }
    
    static public function fa_normalize($string, $normalizeDigits=false)
    {
        if(gettype($string)=='array' or gettype($string)=='object') {
            foreach($string as $key => $val) {
                $string[$key] = self::fa_normalize($val, $normalizeDigits);
            }
            return $string;
        }
        $trans = array(
        "ا" => "ا", "أ" => "ا", "آ" => "آ", "ب" => "ب", "پ" => "پ", "ت" => "ت", "ث" => "ث", "ج" => "ج",
        "چ" => "چ", "ح" => "ح", "خ" => "خ", "د" => "د", "ذ" => "ذ", "ر" => "ر", "ز" => "ز", "ژ" => "ژ",
        "س" => "س", "ش" => "ش", "ص" => "ص", "ض" => "ض", "ط" => "ط", "ظ" => "ظ", "ع" => "ع", "غ" => "غ",
        "ف" => "ف", "ق" => "ق", "ک" => "ک", "ك" => "ک", "گ" => "گ", "ل" => "ل", "م" => "م", "ن" => "ن",
        "و" => "و", "ؤ" => "و", "ه" => "ه", "ة" => "ه", "ئ" => "ئ", "ى" => "ی", "ي" => "ی", "ی" => "ی"); 
        if($normalizeDigits){
            foreach(array(
                "0" => "۰", "1" => "۱", "2" => "۲", "3" => "۳", "4" => "۴", "5" => "۵", "6" => "۶", "7" => "۷",
                "8" => "۸", "9" => "۹", 
                "٠" => "۰", "١" => "۱", "٢" => "۲", "٣" => "۳", "٤" => "۴", "٥" => "۵", "٦" => "۶", "٧" => "۷",
                "٨" => "۸", "٩" => "۹")
                    as $k => $v){
                $trans[$k] = $v;
                
                    }
        }
        return strtr($string, $trans);
    }

    static public function FA_($txt)
    {
        return self::fa_normalize($txt, true);
    }
}

?>
