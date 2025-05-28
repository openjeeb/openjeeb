<?php

class YahooSource extends DataSource {

    public $defaults = array(
        'services' => array('web'),
    );
    public $oauth;
    public $response;

    function __construct(&$config) {
        if (empty($config)) {
            die('Please specify the Yahoo Contact configuration in app/config/database.php');
        }
        $this->config = $config;
        parent::__construct($this->config);
    }

    public function buildOAuth() {
        $this->oauth = new OAuth($this->config['consumer_key'], $this->config['consumer_secret'], OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
        $this->oauth->enableDebug();
    }

    public function requestToken($callbackUrl) {
        if (!is_object($this->oauth)) {
            $this->buildOAuth();
        }
        try {
            return $this->oauth->getRequestToken('https://api.login.yahoo.com/oauth/v2/get_request_token', Router::url($callbackUrl, true));
        } catch (OAuthException $error) {
            $this->log($this->oauth->debugInfo,'yahoo');
        }
    }

    public function requestAccessToken($requestTokens) {
        $this->buildOAuth();
        $this->oauth->setToken($requestTokens['oauth_token'], $requestTokens['oauth_token_secret']);
        try {
            return $this->oauth->getAccessToken('https://api.login.yahoo.com/oauth/v2/get_token');
        } catch (OAuthException $error) {
            $this->log($this->oauth->debugInfo,'yahoo');
        }
    }

    public function requestContacts($accessToken) {
        $this->buildOAuth();
        $this->oauth->setToken($accessToken['oauth_token'], $accessToken['oauth_token_secret']);
        try {
            $this->oauth->fetch('http://social.yahooapis.com/v1/user/'.$accessToken['xoauth_yahoo_guid'].'/contacts;out=yahooid,email',array('format'=>'json'),OAUTH_HTTP_METHOD_GET);
        } catch (OAuthException $error) {
            $this->log($this->oauth->debugInfo,'yahoo');
        }
        return $this->oauth->getLastResponse();
    }    
}