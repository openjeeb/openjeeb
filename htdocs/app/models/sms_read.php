<?php

class SmsRead extends AppModel {

    var $name = 'SmsRead';
    var $displayField = 'body';
    
    var $blongsTo = array( 'User' );
    
    function getData( $id ) {
        
        return $this->find( 'first' , array(
            'conditions' => array(
                'identifier' => $id
            )
        ) );
        
    }
    
}

?>