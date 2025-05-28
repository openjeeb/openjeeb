<?php

App::import('Vendor', 'Browser', array('file' => 'browser.php'));
App::import('Vendor', 'PersianLib', array('file' => 'persian.lib.php'));

class AppController extends Controller {
    var $components = array('Acl', 'Auth', 'Session', 'RequestHandler', 'Chart', 'DebugKit.Toolbar');
    var $helpers = array('Html', 'Form', 'Session','Javascript','Js','Chart','AssetCompress.AssetCompress');
    var $uses = array( 'ServiceTransaction' );
    var $layout = 'default';

    function beforeFilter() {               
        //Configure AuthComponent
        $this->Auth->autoRedirect = false;
        $this->Auth->fields = array('username' => 'email','password' => 'password');
        $this->Auth->userScope = array('User.status' => 'active'/*, 'User.verified'=>'yes'*/);
        $this->Auth->loginError = 'نام کاربری یا کلمه رمز اشتباه است.';
        $this->Auth->authError = 'شما دسترسی لازم برای مشاهده این صفحه را ندارید.';
        $this->Auth->loginRedirect = array('controller' => 'reports', 'action' => 'dashboard');
        $this->Auth->logoutRedirect = array('controller' => 'pages', 'action' => 'home');
        $this->Auth->authorize = 'actions';
        $this->Auth->actionPath = 'controllers/';

        //l10n
        Configure::write('Config.language','per');
        //write user id to config so that appModel can read it
        Configure::write('user_id', $this->Auth->user('id'));
        //check referer
        $this->getReferer();
        //detect user browser and make necessery decision
        $this->detectBrowser();
        //get the remaining days
        $this->getRemainingDays();
        // Pagination options
        $this->set('paginationOptions', array(20, 50, 75, 100));
        $this->set('paginationLimit', $this->_paginationLimit());
        
        // Remaining Credit
        $this->set( 'remaining_sms' , $this->ServiceTransaction->remainingCredit('reminder_sms') );
    }
    
    /*
     * Get the referer for marketing and statistic information
     */
    function getReferer() {
        if( isset($_SERVER['HTTP_REFERER']) AND !$this->Session->check('Jeeb.referer') ) {
            $this->Session->write('Jeeb.referer', parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) );
        }
        if( $this->params['url']['url']=='/' AND isset($_GET['rid'])) {
            $this->Session->write('Jeeb.rid', intval($_GET['rid']));
        }
    }

    /*
     * Detect user browser and decide which chart to use
     */
    function detectBrowser () {
        $browser = new Browser();
        
        //chart mode
        if( $browser->getBrowser() == Browser::BROWSER_FIREFOX && $browser->getVersion() < 4 ) {
            Configure::write('chart', 'google');
	} else {
            Configure::write('chart', 'native');
        }
        
        //unsupported browsers
        switch ($browser->getBrowser()) {
            case Browser::BROWSER_FIREFOX:
                if($browser->getVersion() < 4) {
                    Configure::write('browser_support', 'none');
                }
            break;
            
            case Browser::BROWSER_IE:
                if($browser->getVersion() < 8) {
                    Configure::write('browser_support', 'none');
                }                
            break;
            
            default:
                    Configure::write('browser_support', 'full');
            break;
        }
    }
    
    /*
     * Get remaining days to expire date
     */
    function getRemainingDays() {
        if($this->Auth->user()) {
            $remainingDays=round(((((strtotime($this->Auth->user('expire_date')))-time())/24)/60)/60);
            if($remainingDays<0) {
                $remainingDays = 0;
                if(!in_array($this->params['controller'], array('pages','users','invitations','reminders'))){
                    //this is where system checks for remaining days and redirects user to extend page, you can uncomment the line below to enable this feature
                    //$this->Session->setFlash('کاربر گرامی اعتبار حساب کاربری شما به پایان رسیده است، لطفا حساب خود را تمدید کنید.', 'default', array('class' => 'error-message'));
                    //$this->redirect(array('controller'=>'users','action'=>'extend'));
                }
            }            
            $this->set('remaining_days',  $remainingDays);
        }
    }
    
    function _setPaginate($options = array()) {
        $defaults = array(
            'limit' => $this->_paginationLimit()
        );
        $this->paginate = array_merge($defaults, $options);
    }
    
    function _paginationLimit() {
        if (isset($this->params['named']['Paginate'])) {
            $this->Session->write('Pagination.limit', $this->params['named']['Paginate']);
        }
        $limit=($this->Session->check('Pagination.limit') ? $this->Session->read('Pagination.limit') :
                Configure::read('default_pagination_limit'));
        $this->paginate['limit']=$limit;
        return $limit;
    }

}
?>
