<?php

uses('sanitize');
/**
 *
 * @property EmailComponent $Email
 * @property RabbitComponent $Rabbit
 *
 */
class UsersController extends AppController {
    var $name = 'Users';
    var $uses = array('User','Order','Config');
    var $components = array('Email','Rabbit');
    var $emptyPassword=false;

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allowedActions = array('join', 'verify', 'forgotPassword', 'reset', 'login', 'logout', 'unsubscribe','ajaxCheckDiscountCode', 'demo', 'manualInitUser', 'backup');
        //sanitize the data
        $san = new Sanitize();
        $this->data = $san->clean($this->data);
        if (!empty($this->data) AND $this->params['action'] == 'join')  {
            //check empty password
            if (strlen($this->data['User']['password']) < 5) {
                $this->emptyPassword=true;
            }
        }
    }

    function account() {
        $this->set( 'title_for_layout', 'حساب کاربری' );
        $orders = $this->Order->find( 'all' );
        $this->set( compact( 'orders' ) );
    }

    function login() {
        $this->set('title_for_layout','ورود');
        $this->layout = 'page';
       if (!(empty($this->data)) && $this->Auth->user()) {
            //save last login
            $this->User->id = $this->Auth->user('id');
            $pdate = new PersianDate();
            $data = array(
                'last_login' => $pdate->pdate('Y/m/d H:i:s'),
                'last_ip' => $this->RequestHandler->getClientIP()
            );
            $this->User->save($data);
            //first time login
            if(!$this->Auth->user('last_login')) {
                //get account model
                Controller::loadModel('Account');
                //check for jeeb data entry
                if($this->Account->field('init_balance',array('name'=>'جیب','init_balance'=>0,'delete'=>'no','user_id'=>$this->Auth->user('id')))==0) {
                    //write the setup needed data in session
                    $this->Session->write('Auth.User.setup', true);
                    //redirect to accounts page
                    $this->redirect(array('controller' => 'accounts', 'action' => 'index'));
                }
            }
            $this->redirect($this->Auth->redirect());
        }
        elseif (empty($this->data) && $this->Auth->user()) {
            $this->redirect(array('controller' => 'reports', 'action' => 'dashboard'));
        }
        //Auth Magic
    }

    function logout() {
        $this->redirect($this->Auth->logout());
    }
    
    function join($reference=false) {
        $this->set('title_for_layout', 'ثبت نام');
        $this->layout = 'page';
        $user = $this->Auth->user();
        
        if( $user ) {
            $this->Auth->logout();
            $user = false;
        }
        
        if (!$user) {            
            if (!empty($this->data)) {
                //check for captcha
                if ($this->data['User']['captcha'] != $this->Session->read('captcha')) {
                    $this->Session->setFlash('کد امنیتی وارد شده صحیح نمیباشد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                    return false;
                }
                
                //sanitize the data
                $san = new Sanitize();
                $this->data = $san->clean($this->data);
                
                if($this->emptyPassword) {
                    $this->Session->setFlash('طول رمز عبور بایستی ۵ حرف یا بیشتر باشد.', 'default', array('class' => 'error-message'));
                    return false;
                }
                
                //validate the user data
                $this->User->data = $this->data;
                if ( !$this->User->validates() ) {
                    $this->Session->setFlash( 'مشکلی در ثبت نام وجود دارد، لطفا اطلاعات درخواستی را تکمیل کنید.', 'default', array( 'class' => 'error-message' ) );
                    return false;
                }

                //create user
                if($this->_createFreeUser($this->data)) {
                    $this->Session->setFlash('با تشکر ثبت نام شما انجام شد، لطفا جهت فعال سازی حساب کاربری ایمیل خود را چک کنید.', 'default', array('class' => 'success'));
                    $this->redirect(array('controller' => 'users', 'action' => 'login'));
                    return true;
                } else {
                    $this->Session->setFlash('مشکلی در ثبت نام وجود دارد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                    return false;
                }
            }
        } else {
            $this->redirect(array('controller' => 'pages', 'action' => 'home'));
            return true;
        }
    }

    function edit() {
        $this->set('title_for_layout','ویرایش رمز عبور');
        $userId=$this->Auth->user('id');
        if (!$userId && empty($this->data)) {
            $this->Session->setFlash('کاربر نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            //sanitize the data
            
            if(Configure::read('demo')) {
                $this->Session->setFlash('کاربر آزمایشی مجاز به انجام این عملیات نیست', 'default', array('class' => 'error-message'));
                return false;
            }
            
            //check the old password
            if($this->Auth->password($this->data['User']['old_password'])!=$this->User->field('password',array('id'=>$userId))){
                $this->Session->setFlash('رمز قبلی صحیح نیست.', 'default', array('class' => 'error-message'));
                return false;
            }
            //check the 2 passwords
            if ($this->data['User']['password'] != $this->data['User']['password_repeat']) {
                $this->Session->setFlash('رمز وارد شده و تکرار آن یکسان نیست.', 'default', array('class' => 'error-message'));
                return false;
            }
            
            $this->User->id=$userId;
            if ($this->User->saveField('password',$this->Auth->password($this->data['User']['password']))) {
                $this->Session->setFlash('رمز عبور شما با موفقیت تغییر یافت.', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'edit'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
    }

    function changemail() {
        $this->set('title_for_layout','ویرایش ایمیل ورود');
        
        $userId=$this->Auth->user('id');
        $oldemail=$this->Auth->user('email');
        if (!$userId && empty($this->data)) {
            $this->Session->setFlash('کاربر نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);
            
            if(Configure::read('demo')) {
                $this->Session->setFlash('کاربر آزمایشی مجاز به انجام این عملیات نیست', 'default', array('class' => 'error-message'));
                $this->redirect( $this->referer() );
                return;
            }
            
            //check the old password
            if($this->data['User']['password']!=$this->User->field('password',array('id'=>$userId))){
                $this->Session->setFlash('رمز صحیح نیست.', 'default', array('class' => 'error-message'));
                return false;
            }
            //check the 2 passwords
            $this->User->data = $this->data;
            if ( !$this->User->validates() ) {
                $this->Session->setFlash( 'لطفا یک آدرس ایمیل معتبر وارد نمائید', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            $this->User->id=$userId;
            if ($this->User->saveField('email',$this->data['User']['email'])) {
                $this->Order->updateAll(array(
                    'email' => "'".$this->Order->escapeField($this->data['User']['email'])."'"
                ), array(
                    'email' => $oldemail
                ));
                $this->Session->setFlash('آدرس ایمیل شما با موفقیت تغییر یافت. لطفا مججد وارد سیستم شوید', 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'logout'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            }
        }
    }

    function verify($verification_code=null) {
        $this->set('title_for_layout','فعال سازی حساب کاربری');
        //sanitize the data
        $san = new Sanitize();
        $verification_code = $san->clean($verification_code);
        //check for verification code existance
        if (!$verification_code) {
            $this->redirect(array('controller'=>'users','action' => 'login'));
            return;
        }
        //get user
        $user = $this->User->find(array('User.verification_code' => $verification_code));
        //check for the verification code
        if (!$user) {
            $this->Session->setFlash('کد فعال سازی وارد شده صحیح نمیباشد.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'users','action' => 'login'));
            return;
        }
        //check if the user is verified or not
        if ($user['User']['verified'] == 'yes') {
            $this->Session->setFlash('این کاربر قبلا فعال شده است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('controller'=>'users','action' => 'login'));
            return;
        }
        //verify the user
        $this->User->id = $user['User']['id'];
        $this->User->saveField('verified', 'yes');
        //
        $this->Session->setFlash('با تشکر حساب کاربری شما فعال شد، هم اکنون میتوانید وارد سایت شوید.', 'default', array('class' => 'success'));
        $this->redirect(array('controller'=>'users','action' => 'login'));
    }

    function forgotPassword() {
        $this->set('title_for_layout','بازیابی رمز عبور');
        //sanitize the data
        $san = new Sanitize();
        $this->data = $san->clean($this->data);
        //
        if (!empty($this->data)) {
            //check if the requested email exists
            $user = $this->User->find(array('User.email' => $this->data['User']['email']));
            if (!$user) {
                $this->Session->setFlash('آدرس ایمیل وارد شده در سیستم موجود نیست.', 'default', array('class' => 'error-message'));
                return;
            }
            //generate the verification code
            $forgot_password_code = substr(sha1(time() . $this->data['User']['email']), 2, 10);
            $this->User->id = $user['User']['id'];
            $this->User->saveField('forgot_password_verification_code', $forgot_password_code);
            $this->User->saveField('forgot_password_request_date', date('Y-m-d', time()));
            $this->_sendForgotPasswordMail($this->data['User']['email'], $forgot_password_code);
            $this->Session->setFlash('یک ایمیل حاوی دستورات بازگرداندن رمز عبور به آدرس شما ارسال شد، لطفا ایمیل خود را چک کنید.', 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'login'));
        }
    }

    function reset($forgot_password_code=null) {
        $this->set('title_for_layout','بازیابی رمز عبور');
        //sanitize the data
        $san = new Sanitize();
        $this->data = $san->clean($this->data);
        $forgot_password_code = $san->clean($forgot_password_code);

        //if code is entered and no data is posted we need to show the reset password form
        if ($forgot_password_code AND empty($this->data)) {
            //security check
            if(is_null($forgot_password_code)){
                $this->Session->setFlash('کد وارد شده صحیح نیست.', 'default', array('class' => 'error-message'));
                $this->redirect(array('action' => 'login'));
                return;                
            }
            //check for the forgot_password_code
            $this->User->outputConvertDate=false;
            if (!$user = $this->User->find(array('User.forgot_password_verification_code' => $forgot_password_code))) {
                $this->Session->setFlash('کد وارد شده صحیح نیست.', 'default', array('class' => 'error-message'));
                $this->redirect(array('action' => 'login'));
                return;
            }
            //check if the forgot password date belongs to the last 24 hours
            if (intval(strtotime($user['User']['forgot_password_request_date']) + 86400) < intval(time())) {
                $this->Session->setFlash('کد وارد شده منقضی شده است.', 'default', array('class' => 'error-message'));
                //$this->redirect(array('action' => 'login'));
                return;
            }
            $this->set('forgot_password_code', $forgot_password_code);
        }
        //the user submits the reset password form
        elseif (!empty($this->data) AND isset($this->data['User']['forgot_password_code'])) {
            //check for the forgot_password_code
            if (!$user = $this->User->find(array('User.forgot_password_verification_code' => $this->data['User']['forgot_password_code']))) {
                $this->Session->setFlash('کد وارد شده صحیح نیست.', 'default', array('class' => 'error-message'));
                $this->redirect(array('action' => 'login'));
                return;
            }
            //check password
            if ($this->data['User']['password'] != $this->data['User']['verify_password']) {
                $this->Session->setFlash('رمز عبور وارد شده با تکرار آن مطابقت ندارد.', 'default', array('class' => 'error-message'));
                return;
            }
            //
            $this->User->id = $user['User']['id'];
            $data['User']['password'] = $this->Auth->password($this->data['User']['password']);
            $data['User']['forgot_password_verification_code'] = null;
            $data['User']['forgot_password_request_date'] = null;
            $this->User->save($data);
            //logout
            $this->Auth->logout();
            //redirect
            $this->Session->setFlash('رمز عبور شما با موفقیت تغییر یافت.', 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'login'));
            return;
        } else {
            $this->Session->setFlash('کد وارد شده صحیح نمیباشد.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'login'));
            return;
        }
    }
    
    function unsubscribe($unsubscribeCode) {
        //find the user based on code
        $this->User->ownData=false;
        $this->User->recursive=-1;
        $user=$this->User->find('first',array(
            'conditions'=>array(
                'User.unsubscribe_code'=>$unsubscribeCode
            )
        ));
        
        if(empty($user)) {
            $user=$this->Invitation->find('first',array(
                'conditions'=>array(
                    'Invitation.unsubscribe_code'=>$unsubscribeCode
                )
            ));
            
            if(empty($user)){
                $this->Session->setFlash('چنین کاربری در سیستم موجود نیست.', 'default', array('class' => 'error-message'));
                return;
            }
            
            $this->Invitation->id=$user['Invitation']['id'];
            $this->Invitation->saveField('notifications','no');
            //$this->User->saveField('unsubscribe_code',null);
            $this->Session->setFlash('آدرس ایمیل شما از لیست حذف گردید. [You have been unsubscribed from the mailing list.]', 'default', array('class' => 'success'));
            return;
        }

        //unsubscribe
        $this->User->id=$user['User']['id'];
        $this->User->saveField('notifications','no');

        $this->Session->setFlash('آدرس ایمیل شما از لیست حذف گردید. [You have been unsubscribed from the mailing list.]', 'default', array('class' => 'success'));
    }
    
    function resetData() {
        $this->set( 'title_for_layout', 'پاک کردن تمام اطلاعات' );
        $this->User->id = $this->Auth->user( 'id' );
        //
        if ( !empty( $this->data ) ) {
            
            if(Configure::read('demo')) {
                $this->Session->setFlash('کاربر آزمایشی مجاز به انجام این عملیات نیست', 'default', array('class' => 'error-message'));
                $this->redirect( $this->referer() );
                return;
            }
            
            //check the 2 passwords
            if ( $this->data['User']['user_password'] != $this->data['User']['password_repeat'] ) {
                $this->Session->setFlash( 'رمز عبور و تکرار آن یکسان نیست.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            //check the password
            if ( $this->Auth->password( $this->data['User']['user_password'] ) != $this->User->field( 'User.password' ) ) {
                $this->Session->setFlash( 'رمز عبور اشتباه است.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            $userId = $this->Auth->user( 'id' );

            //start the transaction
            $dataSource = $this->User->getDataSource();
            $dataSource->begin( $this->User );

            //load the models
            Controller::loadModel( 'Account' );
            Controller::loadModel( 'Transaction' );
            Controller::loadModel( 'ExpenseCategory' );
            Controller::loadModel( 'IncomeType' );
            Controller::loadModel( 'Account' );
            Controller::loadModel( 'Check' );
            Controller::loadModel( 'Debt' );
            Controller::loadModel( 'Loan' );
            Controller::loadModel( 'Note' );
            Controller::loadModel( 'Investment' );
            Controller::loadModel( 'Individual' );
            Controller::loadModel( 'Budget' );
            Controller::loadModel( 'Reminder' );
            Controller::loadModel( 'Tag' );

            //delete the accounts
            if ( !$this->Account->deleteAll( array( 'Account.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //delete the transactions
            if ( !$this->Transaction->deleteAll( array( 'Transaction.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //delete the expense categories
            if ( !$this->ExpenseCategory->deleteAll( array( 'ExpenseCategory.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //delete the income types
            if ( !$this->IncomeType->deleteAll( array( 'IncomeType.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //delete the checks
            if ( !$this->Check->deleteAll( array( 'Check.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //delete the debts
            if ( !$this->Debt->deleteAll( array( 'Debt.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //delete the loans
            if ( !$this->Loan->deleteAll( array( 'Loan.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //delete the notes
            if ( !$this->Note->deleteAll( array( 'Note.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //delete the investments
            if ( !$this->Investment->deleteAll( array( 'Investment.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //delete the individuals
            if ( !$this->Individual->deleteAll( array( 'Individual.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            //delete the Budget
            if ( !$this->Budget->deleteAll( array( 'Budget.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            //delete the Reminder
            if ( !$this->Reminder->deleteAll( array( 'Reminder.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            //delete the Tag
            if ( !$this->Tag->deleteAll( array( 'Tag.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                $this->Session->setFlash( 'مشکلی در پاک کردن اطلاعات بوجود آمد لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //commit delete
            $dataSource->commit( $this->User );

            //init data
            $this->_initData( $userId );

            //success
            $this->Session->setFlash( 'تمام اطلاعات شما با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'controller' => 'Reports', 'action' => 'dashboard' ) );
            return true;
        }
    }

    function backup() {
        $this->set('title_for_layout', 'پشتیبان‌گیری از اطلاعات');
        $userId = $this->Auth->user('id');

        if (!$userId) {
            $this->Session->setFlash('کاربر نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }

        // Load database config
        App::import('Core', 'File');
        require_once(APP . 'config' . DS . 'database.php');
        $dbConfig = new DATABASE_CONFIG();
        $db = $dbConfig->default;

        $dbUser = escapeshellarg($db['login']);
        $dbPass = escapeshellarg($db['password']);
        $dbName = escapeshellarg($db['database']);

        $tables = 'expenses incomes transactions transfers expense_categories expense_sub_categories income_types income_sub_types accounts checks debts loans installments notes investments individuals budgets reminders tags';
        $where = escapeshellarg('user_id = ' . $userId);
        $dumpFile = APP . 'tmp' . DS . 'export.sql';

        $deleteSql = "DELETE FROM users WHERE id = $userId;\n";
        $tablesArr = explode(' ', $tables);
        foreach ($tablesArr as $table) {
            $deleteSql .= "DELETE FROM $table WHERE user_id = $userId;\n";
        }

        $usersWhere = escapeshellarg('id = ' . $userId);
        $usersDumpCmd = "mysqldump -h mysql -u $dbUser -p$dbPass $dbName users --where=$usersWhere --no-tablespaces --no-create-info --skip-triggers";
        $userDump = shell_exec($usersDumpCmd);

        $arosInsert = "INSERT INTO `aros` (`parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES (1, 'User', $userId, '', 0, 0);\n";
        $arosInsert .= "UPDATE aros SET lft = 1, rght = 4 WHERE id = 1;\n";
        $arosInsert .= "UPDATE aros SET lft = 2, rght = 3 WHERE id = LAST_INSERT_ID();\n";

        $cmd = "mysqldump -h mysql -u $dbUser -p$dbPass $dbName $tables --where=$where --no-tablespaces --no-create-info --skip-triggers";
        $restDump = shell_exec($cmd);

        $transfersWhere = escapeshellarg(
            "transaction_debt_id IN (SELECT id FROM transactions WHERE user_id = $userId) OR transaction_credit_id IN (SELECT id FROM transactions WHERE user_id = $userId)"
        );
        $transfersDumpCmd = "mysqldump -u $dbUser -p$dbPass $dbName transfers --where=$transfersWhere --no-tablespaces --no-create-info --skip-triggers";
        $transfersDump = shell_exec($transfersDumpCmd);

        file_put_contents($dumpFile, $deleteSql . $userDump . $arosInsert . $restDump . $transfersDump, LOCK_EX);

        if (file_exists($dumpFile)) {
            $this->autoRender = false;
            $filename = 'export.sql';
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($dumpFile));
            readfile($dumpFile);
            @unlink($dumpFile);
            exit;
        } else {
            $this->Session->setFlash('مشکلی در تهیه فایل پشتیبان بوجود آمد.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }
    }

    function ajaxEmailDuplicateChecker() {
        $san = new Sanitize();
        if ($this->RequestHandler->isAjax()) {
            Configure::write('debug', 0);
            $this->layout = 'json';
            $this->set('response', $this->User->hasAny(array('User.email' => $san->clean($this->data['User']['email']))));
            $this->RequestHandler->setContent('json', 'text/x-json');
            $this->render('ajax');
            return;
        }
        $this->redirect(array('controller' => 'pages', 'action' => 'home'));
    }

    function manualInitUser($userId) {
        if( $this->_initData($userId) ) {
            //success
            $this->Session->setFlash( 'اطلاعات پایه وارد شد.', 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'controller' => 'Reports', 'action' => 'dashboard' ) );
            return true;            
        }
    }
    
    function _createFreeUser($data,$plan='1w') {
        //sanitize the data
        $san = new Sanitize();
        $this->data = $san->clean($data);

        //user data 
        $data=array();
        $data['User']['email']=$this->data['User']['email'];
        $data['User']['password']=$this->data['User']['password'];
        $data['User']['verification_code'] = substr($this->Auth->password($data['User']['email']), 2, 6);
        $data['User']['expire_date'] = date("Y-m-d",strtotime("+100 year"));
        $data['User']['unsubscribe_code'] = md5('jeeb'.$data['User']['email'].'jeeb');
        $data['User']['last_ip'] = $this->RequestHandler->getClientIP();
 
        //create the user
        $this->User->create();
        $this->User->inputConvertDate = false;
        if ($this->User->save($data)) {
            $uid = intval($this->User->getLastInsertID());
            $this->Config->setupConfig($uid);
            //submit the order
            $orderData=array();
            $orderData['Order']['user_id']=$uid;
            $orderData['Order']['bank']='-';
            $orderData['Order']['email']=$data['User']['email'];
            $orderData['Order']['amount']=0;
            $orderData['Order']['plan']='1w';
            $orderData['Order']['rid']=$this->Session->read('Jeeb.rid');;
            $orderData['Order']['type']='registration';
            $orderData['Order']['result']='success';
            $this->Order->create();
            $this->Order->save($orderData);
            
            //send verification email
            if (!$this->_sendVerificationMail($data['User']['email'], $data['User']['verification_code'])) {
                $this->Session->setFlash('مشکلی در ارسال ایمیل فعال سازی بوجود آمد، لطفا با پشتیبانی تماس بگیرید.', 'default', array('class' => 'error-message'));
            }
            $this->_initData($this->User->getLastInsertID());
            return true;
        } else {
            return false;
        }        
    }

    function _sendVerificationMail($to, $verificationCode) {
        $subject = 'به سیستم جیب خوش آمدید';
        $template = 'verification';
        $params = [
            'verificationCode' => $verificationCode
        ];

        //send
        if ($this->Rabbit->sendEmailToRabbitMQ($to,$subject,$params,$template,'default')) {
            return true;
        } else {
            return false;
        }
    }

    function _sendForgotPasswordMail($to, $forgot_password_code) {
        $subject = 'جیب: بازیابی رمز عبور';
        $template = 'forgot_password';
        $params = [
            'forgot_password_code' => $forgot_password_code
        ];

        //send
        if ($this->Rabbit->sendEmailToRabbitMQ($to,$subject,$params,$template,'default')) {
            return true;
        } else {
            return false;
        }
    }

    function _initData($userId=null) {
        $userId=intval($userId);
        if($userId<=0){
            return false;
        }
        
        //add expense categories
        Controller::loadModel('ExpenseCategory');
        Controller::loadModel('ExpenseSubCategory');
        
        $data=array();
        $data['ExpenseCategory']=array('name'=>'آموزش','user_id'=>$userId,'sort'=>0);
        $data['ExpenseSubCategory'][]=array('name'=>'شهریه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'کتاب','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'کلاس تقویتی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'تدریس خصوصی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'کمک آموزشی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));
        
        $data=array();
        $data['ExpenseCategory']=array('name'=>'اتومبیل','user_id'=>$userId,'sort'=>1);
        $data['ExpenseSubCategory'][]=array('name'=>'کرایه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'بیمه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سوخت','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'تعمیر و نگهداری','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'پارکینگ','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'جریمه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'عوارض','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));
        
        $data=array();
        $data['ExpenseCategory']=array('name'=>'اقساط','user_id'=>$userId,'delete'=>'no','sort'=>2);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));

        $data=array();
        $data['ExpenseCategory']=array('name'=>'بهداشت و درمان','user_id'=>$userId,'sort'=>3);
        $data['ExpenseSubCategory'][]=array('name'=>'دکتر','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'بیمه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'دارو','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'آزمایشگاه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'بیمارستان','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'باشگاه ورزشی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'داروخانه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));

        $data=array();
        $data['ExpenseCategory']=array('name'=>'چک','user_id'=>$userId,'delete'=>'no','sort'=>4);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));

        $data=array();
        $data['ExpenseCategory']=array('name'=>'سرگرمی','user_id'=>$userId,'sort'=>5);
        $data['ExpenseSubCategory'][]=array('name'=>'رستوران','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سینما','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'تئاتر','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'فیلم','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'موزیک','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'کنسرت','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'بازی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'مجله','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'کتاب','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'روزنامه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'پارک','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'شهربازی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));
        
        $data=array();
        $data['ExpenseCategory']=array('name'=>'حمل و نقل','user_id'=>$userId,'sort'=>6);
        $data['ExpenseSubCategory'][]=array('name'=>'اتوبوس','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'هواپیما','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'قطار','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'ماشین','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'مترو','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));

        $data=array();
        $data['ExpenseCategory']=array('name'=>'حیوانات خانگی','user_id'=>$userId,'sort'=>7);
        $data['ExpenseSubCategory'][]=array('name'=>'غذا','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'بهداشت','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));

        $data=array();
        $data['ExpenseCategory']=array('name'=>'خانه','user_id'=>$userId,'sort'=>8);
        $data['ExpenseSubCategory'][]=array('name'=>'کرایه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'شارژ','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'آشپزخانه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'دکوراسیون','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'تعمیر و نگهداری','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));
        
        $data=array();
        $data['ExpenseCategory']=array('name'=>'خرید','user_id'=>$userId,'sort'=>9);
        $data['ExpenseSubCategory'][]=array('name'=>'پوشاک','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'لوازم برقی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'لوازم ورزشی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'لوازم زینتی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'هدیه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));
        
        $data=array();
        $data['ExpenseCategory']=array('name'=>'خیریه','user_id'=>$userId,'sort'=>10);
        $data['ExpenseSubCategory'][]=array('name'=>'ایتام','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'صدقه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'مستمندان','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));

        $data=array();
        $data['ExpenseCategory']=array('name'=>'صورتحساب','user_id'=>$userId,'sort'=>12);
        $data['ExpenseSubCategory'][]=array('name'=>'اینترنت','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'قبض آب','userId'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'قبض برق','userId'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'قبض گاز','userId'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'قبض تلفن','userId'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'قبض تلفن همراه','userId'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'قبض عوارض شهرداری','userId'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','userId'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));

        $data=array();
        $data['ExpenseCategory']=array('name'=>'فرزندان','user_id'=>$userId,'sort'=>13);
        $data['ExpenseSubCategory'][]=array('name'=>'اسباب بازی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'پوشاک','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'پول توجیبی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'کتاب','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));

        $data=array();
        $data['ExpenseCategory']=array('name'=>'قرض','user_id'=>$userId,'delete'=>'no','sort'=>14);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));
        
        $data=array();
        $data['ExpenseCategory']=array('name'=>'مایحتاج روزانه','user_id'=>$userId,'sort'=>15);
        $data['ExpenseSubCategory'][]=array('name'=>'سوپر مارکت','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'میوه و سبزیجات','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'حبوبات','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'غذای آماده','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));

        $data=array();
        $data['ExpenseCategory']=array('name'=>'مراقبت شخصی','user_id'=>$userId,'sort'=>16);
        $data['ExpenseSubCategory'][]=array('name'=>'آرایشگاه','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'لوازم آرایشی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'بهداشت شخصی','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));

        $data=array();
        $data['ExpenseCategory']=array('name'=>'مسافرت','user_id'=>$userId,'sort'=>17);
        $data['ExpenseSubCategory'][]=array('name'=>'بلیط','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'هتل','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'تفریح','user_id'=>$userId);
        $data['ExpenseSubCategory'][]=array('name'=>'سایر','user_id'=>$userId);
        $this->ExpenseCategory->saveAll($data,array('atomic'=>true));

        
        //add income types
        Controller::loadModel('IncomeType');
        $data=array();
        $data['IncomeType'][]=array('name'=>'اجاره ملک','user_id'=>$userId,'sort'=>0);
        $data['IncomeType'][]=array('name'=>'پاداش','user_id'=>$userId,'sort'=>1);
        $data['IncomeType'][]=array('name'=>'پروژه','user_id'=>$userId,'sort'=>2);
        $data['IncomeType'][]=array('name'=>'جایزه','user_id'=>$userId,'sort'=>3);
        $data['IncomeType'][]=array('name'=>'چک','user_id'=>$userId,'delete'=>'no','sort'=>4);
        $data['IncomeType'][]=array('name'=>'حقوق','user_id'=>$userId,'sort'=>5);
        $data['IncomeType'][]=array('name'=>'سود سرمایه گذاری','user_id'=>$userId,'sort'=>6);
        $data['IncomeType'][]=array('name'=>'فروش','user_id'=>$userId,'sort'=>7);
        $data['IncomeType'][]=array('name'=>'قرض','user_id'=>$userId,'delete'=>'no','sort'=>8);
        $data['IncomeType'][]=array('name'=>'هدیه','user_id'=>$userId,'sort'=>9);
        $data['IncomeType'][]=array('name'=>'سایر','user_id'=>$userId,'sort'=>10);
        $this->IncomeType->saveAll($data['IncomeType'],array('atomic'=>true));
        
        //create the jeeb cash account
        Controller::loadModel('Account');
        $data=array();
        $data['Account']['name']='جیب';
        $data['Account']['balance']=0;
        $data['Account']['type']='cash';
        $data['Account']['delete']='no';
        $data['Account']['user_id']=$userId;
        $this->Account->create();
        $this->Account->save($data);
        
        $this->Config->setupConfig($userId);
        
        return true;
    }
}

?>
