<?php

uses('sanitize');

class RemindersController extends AppController {
    
    var $name = 'Reminders';
    var $uses = array( 'Reminder' , 'ReminderLog', 'Config', 'User', 'Loan', 'Installment', 'Debt', 'Check', 'Note' );
    
    function index()
    {
        $this->set( 'title_for_layout' , 'یادآور' );
        
        $pdate = new PersianDate();
        if(empty($this->params['named'])) {
            $this->paginate['order'] = 'Reminder.time ASC';
        }
        $this->paginate['conditions'] = array('Reminder.deleted'=>'0');
        $this->set( 'reminders', $r=$this->paginate() );
        
        $this->set( 'sentlogcount' , Set::classicExtract( $this->ReminderLog->find('first', array( 'fields' => 'COUNT(*) AS count' )) , '0.count' ) );
        
        if(!empty($this->data)) {
            $this->_savesettings();
        }
        
        $user = $this->Reminder->User->find('first', array(
            'fields' => array('User.cell, User.blocked')
        ));
        $this->set( compact( 'user' ) );
        
        $this->set( 'settings' , $this->_getSettings() );
        
        $this->set( compact( 'settings' ) );
    }
    
    function add()
    {
        $this->set( 'title_for_layout' , 'افزودن یادآور' );
        
        $args = array_keys($this->passedArgs);
        $type = $args[0];
        $refId = $this->passedArgs[$type];
        if(!$refId) {
            $this->Session->setFlash( 'امکان تعریف یادآوری برای این مورد نیست', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( arraY('action'=>'index') );
        }
        
        $pdate = new PersianDate();
        switch($type) {
            case 'installment':
                $this->Installment->convertDateFormat = "Y/m/d";
                $reference = $this->Installment->read(null, $refId);
                $date = $reference['Installment']['due_date'];
                break;
            case 'note':
                $this->Note->convertDateTimeFormat= "Y/m/d H:i:s";
                $reference = $this->Note->read(null, $refId);
                $date = $reference['Note']['date']? $reference['Note']['date'] : $pdate->pdate("Y/m/d");
                break;
            case 'check':
                $this->Check->convertDateFormat = "Y/m/d";
                $reference = $this->Check->read(null, $refId);
                $date = $reference['Check']['due_date'];
                break;
            case 'debt':
                $this->Debt->convertDateFormat = "Y/m/d";
                $reference = $this->Debt->read(null, $refId);
                $date = $reference['Debt']['due_date'];
                break;
            case 'loan':
                $this->Session->setFlash( 'برای تعریف یادآوری لطفا یکی از اقساط را انتخاب نمائید', 'default', array( 'class' => 'success' ) );
                $this->redirect( array('controller'=>'loans','action'=>'view',$refId) );
                break;
        }
        
        //Reference either is not realted to the user or time is passed
        if(empty($reference) || strtotime($pdate->pdate_format_reverse($date, "/"))<=time()) {
            $this->Session->setFlash( 'امکان تعریف یادآوری برای این مورد نیست', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( $this->referer( array('action'=>'index') ) );
        }
        
        if($type == 'note') {
            $date = explode(' ',$date);
            $date = $date[0];
        }
        
        $this->set( 'date' , $date );
        $this->set( 'settings' , $this->_getSettings() );
        $this->set( 'referer', $this->referer( array('action'=>'index') ) );
        $this->set( compact( 'type' ) );
        $this->set( compact( 'refId' ) );
        
        if(!empty($this->data['Reminder'])) {
            
            $data = &$this->data['Reminder'];
            $data['type'] = $type;
            $data['reference_id'] = $refId;
            
            if(!preg_match('/^(\d{2}):(\d{2})$/',$data['exacttime'], $match)) {
                $this->Session->setFlash( 'لطفا ساعت دریافت پیامک را صحیح وارد نمائید', 'default', array( 'class' => 'error-message' ) );
                return;
            } elseif( $match[1]>24 || $match[1]<0 || $match[2]>59 || $match[2]<0 ) {
                $this->Session->setFlash( 'لطفا ساعت دریافت پیامک را صحیح وارد نمائید', 'default', array( 'class' => 'error-message' ) );
                return;
            }
            
            #$data['time'] = $data['time']." ".(($date==$data['time'])? '08:00' : '10:00');
            $data['time'] = $data['time']." ".$data['exacttime'];
            
            $reveresedtime = strtotime($pdate->pdate_format_reverse($data['time'], "/"));
            if($reveresedtime<=time()) {
                $this->Session->setFlash( 'لطفا زمان ارسال یادآور را در آینده انتخاب نمائید', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( $this->referer( array('action'=>'index') ) );
            }
            
            #if($reveresedtime > strtotime($pdate->pdate_format_reverse($date, "/")." 08:00")) {
            if($reveresedtime > strtotime($pdate->pdate_format_reverse($date, "/")." ".$data['exacttime'])) {
                $this->Session->setFlash( 'زمان یادآور شما بعداز موعد سررسید است', 'default', array( 'class' => 'error-message' ) );
                //$this->redirect( $this->referer( array('action'=>'index') ) );
                return;
            }
            
            if(empty($data['medium'])) {
                $this->Session->setFlash( 'لطفا یکی از طرق ارسال یادآوری را انتخاب نمائید', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( $this->referer( array('action'=>'index') ) );
            }
            $data['medium'] = implode(",",$data['medium']);
            
            unset($data['exacttime']);
            $this->Reminder->create();
            if( !$this->Reminder->save($data) ) {
                $this->Session->setFlash( 'متاسفانه در ثبت یادآور مشکلی به وجود آمد لطفا مجدد تلاش کنید', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( $this->referer( array('action'=>'index') ) );
            }
            
            $this->Session->setFlash( 'ثبت یادآور با موفقیت انجام شد', 'default', array( 'class' => 'success' ) );
            $this->redirect( array('action'=>'view',$type=>$refId) );
        }
        
    }
    
    function edit($id)
    {
        $this->set( 'title_for_layout' , 'ویرایش یادآور' );
        if(!$id) {
            $this->Session->setFlash( 'امکان تغییر این یادآوری وجود ندارد', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( arraY('action'=>'index') );
        }
        
        $this->Reminder->convertDateTimeFormat = "Y/m/d H:i";
        $reminder = $this->Reminder->read(null,$id);
        
        preg_match('/^(\d{4}\/\d{2}\/\d{2})\s(\d{2}:\d{2})/', $reminder['Reminder']['time'], $reminder['Reminder']['time']);
        $reminder['Reminder']['exacttime'] = $reminder['Reminder']['time'][2];
        $reminder['Reminder']['time'] = $reminder['Reminder']['time'][1];
        
        $pdate = new PersianDate();
        # Reference either is not realted to the user or time is passed
        if(empty($reminder) || strtotime($pdate->pdate_format_reverse($reminder['Reminder']['time'], "/")." ".$reminder['Reminder']['exacttime'])<=time()) {
            $this->Session->setFlash( 'امکان تغییر این یادآوری وجود ندارد', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( $this->referer( array('action'=>'index') ) );
        }
        
        $reminder['Reminder']['medium'] = array_flip( explode(",",$reminder['Reminder']['medium']) );
        
        if( empty($this->data) ) {
            $this->data = $reminder;
        }
        $this->set( 'referer', $this->referer( array('action'=>'index') ) );
        
        if(!empty($this->data['Reminder'])  && !empty( $_POST ) ) {
            $data = &$this->data['Reminder'];
            
            if(!preg_match('/^(\d{2}):(\d{2})$/',$data['exacttime'], $match)) {
                $this->Session->setFlash( 'لطفا ساعت دریافت پیامک را صحیح وارد نمائید', 'default', array( 'class' => 'error-message' ) );
                return;
            } elseif( $match[1]>24 || $match[1]<0 || $match[2]>59 || $match[2]<0 ) {
                $this->Session->setFlash( 'لطفا ساعت دریافت پیامک را صحیح وارد نمائید', 'default', array( 'class' => 'error-message' ) );
                return;
            }
            
            $data['time'] = $data['time'].' '.$data['exacttime'];
            if(strtotime($pdate->pdate_format_reverse($data['time'], "/"))<=time()) {
                $this->Session->setFlash( 'لطفا زمان ارسال یادآور را در آینده انتخاب نمائید', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( $this->referer( array('action'=>'index') ) );
            }
            
            if(empty($data['medium'])) {
                $this->Session->setFlash( 'لطفا یکی از طرق ارسال یادآوری را انتخاب نمائید', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( $this->referer( array('action'=>'index') ) );
            }
            $data['medium'] = implode(",",$data['medium']);
            
            $this->Reminder->id = $id;
            if( !$this->Reminder->save($data) ) {
                $this->Session->setFlash( 'متاسفانه در ثبت یادآور مشکلی به وجود آمد لطفا مجدد تلاش کنید', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( $this->referer( array('action'=>'index') ) );
            }
            
            $this->Session->setFlash( 'تغییر یادآور با موفقیت انجام شد', 'default', array( 'class' => 'success' ) );
            $this->redirect( array('action'=>'view',$reminder['Reminder']['type']=>$reminder['Reminder']['reference_id']) );
        }
    }
    
    function view()
    {
        $this->set( 'title_for_layout' , 'یادآور' );
        
        $args = array_keys($this->passedArgs);
        $type = $args[0];
        $refId = $this->passedArgs[$type];
        if(!$refId) {
            $this->Session->setFlash( 'برای این مورد یادآوری تعریف نشده است', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array('action'=>'index') );
        } 
        
        switch($type) {
            case 'loan':
                $reference = $this->Loan->read(null, $refId);
                $list = $this->Installment->find( 'all' , array(
                    'fields' => array( 'id' ),
                    'conditions' => array( 'Installment.loan_id' => $refId )
                ));
                $list = Set::extract( '/Installment/id', $list );
                $t = 'installment';
                break;
            case 'installment':
                $reference = $this->Installment->read(null, $refId);
                $list = $refId;
                break;
            case 'note':
                $reference = $this->Note->read(null, $refId);
                $list = $refId;
                break;
            case 'check':
                $reference = $this->Check->read(null, $refId);
                $list = $refId;
                break;
            case 'debt':
                $reference = $this->Debt->read(null, $refId);
                $list = $refId;
                break;
            
            default:
                $this->Session->setFlash( 'برای این مورد یادآوری تعریف نشده است', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( arraY('action'=>'index') );
        }
        
        $t = isset($t)? $t : $type;
        $reminders = $this->Reminder->find( 'all' , array(
            'conditions' => array(
                'Reminder.type' => $t,
                'Reminder.reference_id' => $list,
                'Reminder.deleted' => 0
            ),
            'order' => 'Reminder.time ASC'
        ) );
        
        if(empty($reference)){
            $this->Session->setFlash( 'چنین موردی وجود ندارد', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( arraY('action'=>'index') );
        }
        
        $this->set( compact( 'type' ) );
        $this->set( compact( 'refId' ) );
        $this->set( compact( 'reference' ) );
        $this->set( compact( 'reminders' ) );
        $this->set( 'referer', $this->referer( array('action'=>'index') ) );
        
        #debug($reminders);
        
    }
    
    public function delete($id=null)
    {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        
        
        //delete
        if ( !$this->Reminder->delete( $id ) ) {
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( $this->referer( array('action' => 'index') ) );
            return false;
        }
        
        $this->Session->setFlash( 'داده مورد نظر با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
        $this->redirect( $this->referer( array('action' => 'index') ) );
        return true;
    }
    
    public function logview()
    {
        $this->set( 'title_for_layout' , 'یادآورهای ارسال شده' );
        
        $this->paginate['order'] = 'ReminderLog.senddate DESC';
        $this->set( 'reminderlogs' , $this->paginate( 'ReminderLog' ) );
    }
    
    public function help()
    {
        
    }
    
    public function export()
    {
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion( 8 );
        $workbook->send( 'reminders.xls' );
        $worksheet = & $workbook->addWorksheet( 'reminders' );
        $worksheet->setInputEncoding( 'utf-8' );

        //get the data
        $res = $this->Reminder->find( 'all' );
        $data = array( );
        $data[] = array( 'عنوان یادآور', 'زمان', 'طریقه ارسال' );
        $i = 1;
        foreach ( $res as $entry ) {
            $data[$i][] = $entry['Reminder']['name'];
            $data[$i][] = $entry['Reminder']['time'];
            
            $medium = explode(",",$entry['Reminder']['medium']);
            foreach ( $medium as &$md ) {
                $md = __($md,true);
            }
            $data[$i][] = implode(" / ",$medium);
            $i++;
        }

        // write to excel
        $i = 0;
        foreach ( $data as $row ) {
            $worksheet->writeRow( $i, 0, $row );
            $i++;
        }

        // send the file
        $workbook->close();
        return;
    }
    
    public function ajaxShowText($id=null)
    {
        if(!$id) {
            return false;
        }
        
        $item = $this->Reminder->find('first',array(
            'conditions' => array( 'Reminder.id' => $id )
        ));
        $txt = $this->Reminder->makeText($item);
        die($txt['sms']);
        
    }
    
    public function unblock()
    {
        $this->User->id = $this->Auth->user( 'id' );
        $this->User->save( array(
            'blocked' => 0
        ) , false );
        $this->Session->setFlash( 'شماره شما فعال شد.', 'default', array( 'class' => 'success' ) );
        $this->redirect(array('action'=>'index'));
    }
    
    private function _getSettings()
    {
        $settings = $this->Config->getAll( 'reminder' );
        
        if(!$settings){
            $this->Config->setupConfig();
            $settings = $this->Config->getAll( 'reminder' );
        }
        
        foreach($settings as &$opt){
            $opt = array_flip(explode(",",$opt));
        }
        
        return $settings;
    }
    
    private function _savesettings()
    {
        $uid = $this->Auth->user('id');
        $this->User->ownData = false;
        $count = $this->User->find('count',array(
            'conditions' => array(
                'User.id <> ' => $uid,
                'User.cell' => $this->data['User']['cell']
            )
        ));
        $this->User->ownData = true;
        if( !$count ) {
            $this->User->id = $uid;
            if(!$this->User->save($this->data['User'])){
                return false;
            }
        } else {
            $this->Session->setFlash( 'این شماره در سیستم قبلا به ثبت رسیده است', 'default', array( 'class' => 'error' ) );
            return false;
        }
        
        
        $reminder = &$this->data['Reminder'];
        
        $this->Config->setValue( 'reminder', 'check_freq', @implode(',',$reminder['check']['frequency']) );
        $this->Config->setValue( 'reminder', 'check_medium', @implode(',',$reminder['check']['medium']) );
        
        $this->Config->setValue( 'reminder', 'note_freq', @implode(',',$reminder['note']['frequency']) );
        $this->Config->setValue( 'reminder', 'note_medium', @implode(',',$reminder['note']['medium']) );
        
        $this->Config->setValue( 'reminder', 'debt_freq', @implode(',',$reminder['debt']['frequency']) );
        $this->Config->setValue( 'reminder', 'debt_medium', @implode(',',$reminder['debt']['medium']) );
        
        $this->Config->setValue( 'reminder', 'installment_freq', @implode(',',$reminder['installment']['frequency']) );
        $this->Config->setValue( 'reminder', 'installment_medium', @implode(',',$reminder['installment']['medium']) );
        
        $this->Config->setValue( 'reminder', 'loginreminder_medium', @implode(',',$reminder['login']['medium']) );
        
        $this->Session->setFlash( 'تغییرات با موفقیت ذخیره شدند', 'default', array( 'class' => 'success' ) );
        
        
    }
    
}

?>
