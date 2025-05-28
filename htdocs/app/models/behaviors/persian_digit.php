<?php
class PersianDigitBehavior extends ModelBehavior {

    /**
     * Empty Setup Function
    */
    function setup(&$model) {
        $this->model = $model;
    }

    /**
     * Function which handle the convertion of the Persian digits to latin digits
     * @param array $data data array
     * @return array converted array;
     * @access restricted
     */
    function _persianDigit2LatinDigit(&$string){
        $trans = array( "۰" => "0", "۱" => "1", "۲" => "2", "۳" => "3", "۴" => "4", 
                        "٠" => "0", "١" => "1", "٢" => "2", "٣" => "3", "٤" => "4", 
                        "۵" => "5", "۶" => "6",	 "۷" => "7", "۸" => "8", "۹" => "9",
                        "٥" => "5", "٦" => "6",	 "٧" => "7", "٨" => "8", "٩" => "9");
        $string = strtr($string, $trans);
        
//        $string = (string) preg_replace('/[\x{06F0}]+/u', '0', $string);
//        $string = (string) preg_replace('/[\x{06F1}]+/u', '1', $string);
//        $string = (string) preg_replace('/[\x{06F2}]+/u', '2', $string);
//        $string = (string) preg_replace('/[\x{06F3}]+/u', '3', $string);
//        $string = (string) preg_replace('/[\x{06F4}]+/u', '4', $string);
//        $string = (string) preg_replace('/[\x{06F5}]+/u', '5', $string);
//        $string = (string) preg_replace('/[\x{06F6}]+/u', '6', $string);
//        $string = (string) preg_replace('/[\x{06F7}]+/u', '7', $string);
//        $string = (string) preg_replace('/[\x{06F8}]+/u', '8', $string);
//        $string = (string) preg_replace('/[\x{06F9}]+/u', '9', $string);
    }
    
    function beforeValidate(&$model) {
        if(!empty($model->data)){
            array_walk_recursive($model->data, array($this,'_persianDigit2LatinDigit'));
        }
        return true;
    }
}
?>