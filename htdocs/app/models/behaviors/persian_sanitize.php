<?php
class PersianSanitizeBehavior extends ModelBehavior {
    
    var $data=array();
    
    /**
     * Empty Setup Function
    */
    function setup(&$model) {
        $this->model = $model;
    }

    /**
     * Function which handle the convertion of the date to and from Persian date
     * @param array $data data array from and to database
     * @return sanitized array;
     * @access restricted
     */
    function _persianSanitize(){
        if(!empty($this->data)){
            array_walk_recursive($this->data, array($this,'_whitespace'));
            array_walk_recursive($this->data, array($this,'_replaceArabic'));
        }
    }

    function _whitespace(&$string){
        $string=trim($string);
    }

    function _replaceArabic(&$string){
        /*$string=preg_replace('/[\x{064A}]+/u', 'ی', $string);
        $string=preg_replace('/[\x{0643}]+/u', 'ک', $string);*/
        
        $trans = array(
        "ا" => "ا", "أ" => "ا", "آ" => "آ", "ب" => "ب", "پ" => "پ", "ت" => "ت", "ث" => "ث", "ج" => "ج",
        "چ" => "چ", "ح" => "ح", "خ" => "خ", "د" => "د", "ذ" => "ذ", "ر" => "ر", "ز" => "ز", "ژ" => "ژ",
        "س" => "س", "ش" => "ش", "ص" => "ص", "ض" => "ض", "ط" => "ط", "ظ" => "ظ", "ع" => "ع", "غ" => "غ",
        "ف" => "ف", "ق" => "ق", "ک" => "ک", "ك" => "ک", "گ" => "گ", "ل" => "ل", "م" => "م", "ن" => "ن",
        "و" => "و", "ؤ" => "و", "ه" => "ه", "ة" => "ه", "ئ" => "ئ", "ى" => "ی", "ي" => "ی", "ی" => "ی"); 
        
        $string = strtr($string, $trans);
    }

    /**
     * Function before Validate.
     */
    function beforeValidate($model) {
        $this->data=$model->data;
        $this->_persianSanitize();
        $model->data = $this->data;
        return true;
    }
}
?>