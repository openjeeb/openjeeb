<?php

class GoogleComponent extends Object {

    public $auth_token_url = "https://www.google.com/accounts/OAuthAuthorizeToken";

    public function initialize(&$controller) {
        $this->controller = $controller;
        $this->Google = ConnectionManager::getDataSource('google');
    }

    public function requestAuth($callbackUrl) {
        $this->controller->autoRender = false;
        $this->controller->redirect($this->Google->requestAuthUrl($callbackUrl));
    }

    public function requestAuthToken($requestTokens, $callbackUrl) {
        return $this->Google->requestAuthToken($requestTokens, $callbackUrl);
    }

    public function getContacts($accesstoken) {
        $response = $this->Google->requestContacts($accesstoken);
        if (empty($response)) {
            return false;
        }
        //parse
        $xml = new SimpleXMLElement($response);
        $xml->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');
        $result = $xml->xpath('//gd:email');
        if (empty($result)) {
            return false;
        }
        $contacts = array();
        foreach ($result as $title) {
            $tmp=strval($title->attributes()->address);
            if(strpos($tmp,'@') === false) {
                $tmp.='@gmail.com';
            }
            $contacts[] = $tmp;
        }
        return array_unique($contacts);
    }

}
