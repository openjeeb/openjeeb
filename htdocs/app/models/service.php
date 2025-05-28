<?php

class Service extends AppModel {

    var $name = 'Service';
    var $displayField = 'name';
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
    );
    
    function getService($name)
    {
        $s =  $this->find( 'first' , array(
            'conditions' => array(
                'Service.identifier' => $name,
                'Service.status' => 'active'
            )
        ));
        // To make all keys value more than 0
        $s['Service']['usebase'] = explode( ",", $s['Service']['usebase'] );
        $firstkey = $s['Service']['usebase'][0];
        $s['Service']['usebase'] = array_flip( $s['Service']['usebase'] );
        $s['Service']['usebase'][$firstkey] = 1;
        return $s;
    }

}

?>