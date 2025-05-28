<?php

uses('sanitize');

class EmailHooksController extends AppController {

    var $name = 'EmailHooks';
    var $uses = array('User');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allowedActions = array('elasticemail');
    }

    function elasticemail() {
        //save the bounces, rejects, unsubs and spams
        $this->autoRender = false;

        $status = strtolower(trim($_REQUEST['status']));
        $email = strtolower(trim($_REQUEST['to']));
        $category = strtolower(trim($_REQUEST['category']));

        //log
        $this->log($status . ' -- ' . $email . ' -- ' . $category, 'elasticemail');

        //find the user
        $this->User->recursive = -1;
        $this->User->outputConvertDate = true;
        $this->User->ownData = false;
        $user = $this->User->find('first', array('conditions' => array('email' => $email)));

        if (!empty($user)) {
            if (!empty($status)) {
                if (in_array($status, array('unsubscribed', 'abusereport', 'error'))) {
                    //unsubscribe
                    $this->User->id = $user['User']['id'];
                    $this->User->saveField('notifications', 'no');
                }
            }
        }
        return;
    }

}

?>