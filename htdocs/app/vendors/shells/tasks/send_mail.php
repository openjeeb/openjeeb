<?php

App::import( 'Core', 'Controller' );
App::import( 'Component', 'Email' );

Class SendMailTask extends Shell {

    var $components = array( 'Email' );

    function execute( $to, $subject, $message, $layout='default' ) {
        $this->Controller = & new Controller();
        $this->Email = & new EmailComponent( null );
        $this->Email->initialize( $this->Controller );
        $this->Email->to = $to;
        $this->Email->subject = $subject;
        $this->Email->from = 'جیب <no-reply@jeeb.ir>';
        $this->Email->layout = $layout;
        $this->Email->template = 'message';
        $this->Email->sendAs = 'html';
        $this->Email->smtpOptions = array(
            'port' => '25',
            'timeout' => '30',
            'host' => 'smtp.jeeb.ir',
            'username' => 'no-reply@jeeb.ir',
            'password' => '',
        );
        $this->Email->delivery = 'mail';
        //Set view variables
        $this->Controller->set( 'message', $message );
        $this->Controller->set( 'email', $to );
        //send
        try {
            if ( $this->Email->send() ) {
                return true;
            } else {
                return false;
            }
        } catch ( Exception $exc ) {
                return false;
        }
    }

}

?>
