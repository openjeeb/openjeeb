<?php

class DiscountCode extends AppModel {

    var $name = 'DiscountCode';

    function getDiscountCodeId($code) {
        return $this->field('DiscountCode.id', array('DiscountCode.code' => low(trim($code))));
    }

    function getDiscountAmountByid($id) {
        return $this->field('DiscountCode.amount', array('DiscountCode.id' => intval($id)));
    }
    
    function getDiscountValidPlan($id) {
        return $this->field('DiscountCode.valid_plan', array('DiscountCode.id' => intval($id)));
    }
        
    function markDiscountCodeUsed($id) {
        return $this->query("UPDATE jeeb.discount_codes SET used = used+1 WHERE discount_codes.id =".intval($id));
    }

    function checkCode($code,$type='user') {
        $discountCode=$this->find('first',array(
            'fields'=>array('amount','valid_plan'),
            'conditions'=>array(
                'code'=>low(trim($code)),
                'type'=>$type,
                'count > used',
                'expire_date >= CURDATE()'
        )));
        if(empty($discountCode)) {
            return false;
        }
        return $discountCode['DiscountCode'];
    }
    
}

?>