<?php

class PackageService extends AppModel {

    var $name = 'PackageService';
    var $displayField = 'name';
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
    );
    
    var $belongsTo = array(
        'Package',
        'Service' => array(
            'foreignKey' => 'service_identifier'
        )
    );

}

?>