<?php

class YahooComponent extends Object {

    public function initialize(&$controller) {
        $this->controller = $controller;
        $this->Yahoo = ConnectionManager::getDataSource('yahoo');
    }

    public function requestToken($callbackUrl = '') {
        $this->controller->autoRender = false;
        $response = $this->Yahoo->requestToken($callbackUrl);
        if (!empty($response['xoauth_request_auth_url'])) {
            $this->controller->Session->write('Yahoo.response', $response);
            $this->controller->redirect($response['xoauth_request_auth_url']);
        }
    }

    public function requestAccessToken() {
        $requestToken = $this->controller->Session->read('Yahoo.response');
        return $this->Yahoo->requestAccessToken($requestToken);
    }

    public function getContacts($accessToken) {
        $response = json_decode($this->Yahoo->requestContacts($accessToken), true);
        $email_contacts = Set::extract('/fields[type=email]', $response['contacts']['contact']);
        $email_contacts = Set::extract('/fields/value', $email_contacts);
        $yahoo_ids = Set::extract('/fields[type=yahooid]', $response['contacts']['contact']);
        $yahoo_ids = Set::extract('/fields/value', $yahoo_ids);
        $emails = $email_contacts;
        foreach ($yahoo_ids as $yahoo_id) {
            $emails[] = $yahoo_id . '@yahoo.com';
        }
        return array_unique($emails);
    }

}
