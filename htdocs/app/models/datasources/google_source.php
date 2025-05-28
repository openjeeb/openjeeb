<?php

class GoogleSource extends DataSource {

    public $defaults = array(
        'services' => array('web'),
    );
    public $oauth;
    public $response;
    public $request_auth_url = "https://accounts.google.com/o/oauth2/auth";
    public $request_token_url = "https://accounts.google.com/o/oauth2/token";
    public $contacts_scope_url = "https://www.google.com/m8/feeds";
    public $contacts_feed_url = "https://www.google.com/m8/feeds/contacts/default/full";

    function __construct(&$config) {
        if (empty($config)) {
            die('Please specify the Google configuration in app/config/database.php');
        }
        $this->config = $config;
        parent::__construct($this->config);
    }

    public function buildOAuth() {
        $this->oauth = new OAuth($this->config['consumer_key'], $this->config['consumer_secret']);
        $this->oauth->enableDebug();
    }

    public function requestAuthUrl($callbackUrl) {
        $request_url = $this->request_auth_url . '?';
        $scope = 'scope=' . urlencode($this->contacts_scope_url);
        $redirect_uri = 'redirect_uri=' . urlencode($callbackUrl);
        $response_type = 'response_type=code';
        $client_id = 'client_id=' . urlencode($this->config['consumer_key']);
        $url = $request_url . $scope . '&' . $redirect_uri . '&' . $response_type . '&' . $client_id;
        return $url;
    }

    public function requestAuthToken($requestTokens, $callbackUrl) {
        //generate the post headers
        $fields = array(
            'code' => urlencode($requestTokens),
            'client_id' => urlencode($this->config['consumer_key']),
            'client_secret' => urlencode($this->config['consumer_secret']),
            'redirect_uri' => urlencode($callbackUrl),
            'grant_type' => urlencode('authorization_code')
        );
        $post = '';
        foreach ($fields as $key => $value) {
            $post .= $key . '=' . $value . '&';
        }
        $post = rtrim($post, '&');

        //post and get result
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->request_token_url);
        curl_setopt($curl, CURLOPT_POST, 5);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        $result = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($result);

        if (!isset($response->access_token)) {
            return false;
        }
        return $response->access_token;
    }

    public function requestContacts($accesstoken) {
        $url = $this->contacts_feed_url.'?max-results=400' . '&oauth_token=' . $accesstoken;
        $xmlresponse = $this->curl_file_get_contents($url);
        if ((strlen(stristr($xmlresponse, 'Authorization required')) > 0) && (strlen(stristr($xmlresponse, 'Error ')) > 0)) { //At times you get Authorization error from Google.
            return false;
        }
        return $xmlresponse;
    }

    public function curl_file_get_contents($url) {
        $curl = curl_init();
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';

        curl_setopt($curl, CURLOPT_URL, $url); //The URL to fetch. This can also be set when initializing a session with curl_init().
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); //The number of seconds to wait while trying to connect.	

        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent); //The contents of the "User-Agent: " header to be used in a HTTP request.
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE); //To follow any "Location: " header that the server sends as part of the HTTP header.
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE); //To automatically set the Referer: field in requests where it follows a Location: redirect.
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); //The maximum number of seconds to allow cURL functions to execute.
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); //To stop cURL from verifying the peer's certificate.

        $contents = curl_exec($curl);
        curl_close($curl);
        return $contents;
    }

    public function requestTokenOAuth1($callbackUrl) {
        if (!is_object($this->oauth)) {
            $this->buildOAuth();
        }
        $scopes = urlencode($this->contacts_scope_url);
        $request_scope_token_url = $this->request_token_url . "?scope=" . $scopes;
        try {
            return $this->oauth->getRequestToken($request_scope_token_url, Router::url($callbackUrl, true));
        } catch (OAuthException $error) {
            $this->log($this->oauth->debugInfo, 'google');
        }
        return false;
    }

}