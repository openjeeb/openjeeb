<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
App::import('Vendor', 'KavenegarApi', array('file' => 'Kavenegar'.DS.'KavenegarApi.php'));
use Psr\Log\LogLevel;

/**
 * CakePHP Component
 * @author root
 */
class SmsKaveComponent extends Object {

    public $components = array();
    public $settings = array();

    public $client = null;

    private $api_key = "XXXXXX";
     
    //called before Controller::beforeFilter()
    function initialize(&$controller, $settings = array()) {
        // saving the controller reference for later use
        $this->controller =& $controller;
        $this->client = new KavenegarApi($this->api_key);
    }


    public function sendMany(){

    }

    public function sendOne($receptor,$message,$sender = "XXXXXX"){
        try{
            $res =  $this->client->Send($sender,$receptor,$message);
            if (!empty($res)){
                $res = (array) $res[0];
            }
            return $res;
        }catch (\Exception $e){
            return false;
        }
    }
}
