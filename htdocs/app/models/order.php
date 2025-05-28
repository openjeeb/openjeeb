<?php

class Order extends AppModel {

    var $name = 'Order';

    var $validate = array(
        'gateway' => array(
            'rule' => array('inList', array('mellat', 'parsian')),
            'message' => 'درگاه پرداخت را به درستی انتخاب کنید'
        )
    );
    function userSuccessfulOrders($userId) {
        $this->ownData = false;
        return $this->find('count', array(
            'conditions'=>array(
                'user_id' => intval($userId),
                'result' => 'success',
                'plan <>' => '1w'
                )
            )
        );
    }

}

?>