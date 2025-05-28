<?php

class Transfer extends AppModel {

    var $name = 'Transfer';
    /* var $hasMany = array(
        'Transaction' => array (
            'foreignKey'=>false,
            'finderQuery' => 'SELECT * FROM transaction WHERE Transfer.transaction_credit_id=Transaction.id OR Transfer.transaction_debt_id=Transaction.id'
            )
    ); */

}

?>