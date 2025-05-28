<?php

uses( 'sanitize' );

class NotesController extends AppController {

    var $name = 'Notes';
    
    var $uses = array( 'Note' , 'Reminder' , 'Config' );

    function index() {
        $this->set('title_for_layout', 'یادداشت ها');
        $this->Note->recursive = 0;
        
        //sanitize the data
        /*$san = new Sanitize();
        $this->data = $san->clean( $this->data );*/
        $this->data = $this->sanitize($this->data);

        //paginate data
        $conditions = array( );
        if ( isset( $this->data['Note']['search'] ) ) {
            if ( !empty( $this->data['Income']['start_date'] ) ) {
                $persianDate = new PersianDate();
                $this->data['Note']['start_date'] = $persianDate->pdate_format_reverse( $this->data['Note']['start_date'] );
                $conditions['Note.date >='] = $this->data['Note']['start_date'];
            }
            if ( !empty( $this->data['Income']['end_date'] ) ) {
                $persianDate = new PersianDate();
                $this->data['Note']['end_date'] = $persianDate->pdate_format_reverse( $this->data['Note']['end_date'] );
                $conditions['Note.date <='] = $this->data['Income']['end_date'];
            }
            if ( !empty( $this->data['Note']['subject_search'] ) ) {
                $conditions['Note.subject LIKE'] = "%" . $this->data['Note']['subject_search'] . "%";
            }
            if ( !empty( $this->data['Note']['content_search'] ) ) {
                $conditions['Note.content LIKE'] = "%" . $this->data['Note']['content_search'] . "%";
            }
            
            //save it into session
            $this->Session->delete( 'Note.conditions' );
            $this->Session->write( 'Note.conditions', $conditions );
        }
        
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) ) {
            $this->Session->delete( 'Note.conditions' );
        }
        
        //apply the conditions
        if ( $this->Session->check( 'Note.conditions' ) AND !empty( $this->params['named'] ) ) {
            $conditions = $this->Session->read( 'Note.conditions' );
        }
        
        $this->paginate['order'] = 'Note.created DESC';
        $this->paginate['conditions'] = $conditions;
        $this->set( 'notes', $this->paginate() );
        //add
        if ( !empty( $this->data ) AND !isset( $this->data['Note']['search'] ) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean( $this->data );
            $this->data['Note']['notify'] = intval(@$this->data['Note']['notify']);
            
            if(!preg_match('/^(\d{2}):(\d{2})$/',$this->data['Note']['time'], $match)) {
                $this->Session->setFlash( 'لطفا ساعت دریافت پیامک را صحیح وارد نمائید', 'default', array( 'class' => 'error-message' ) );
                return;
            } elseif( $match[1]>24 || $match[1]<0 || $match[2]>59 || $match[2]<0 ) {
                $this->Session->setFlash( 'لطفا ساعت دریافت پیامک را صحیح وارد نمائید', 'default', array( 'class' => 'error-message' ) );
                return;
            }
            $this->data['Note']['date'] = $this->data['Note']['date'].' '.$this->data['Note']['time'];
            unset($this->data['Note']['time']);
            
            $this->Note->create();
            if ( $this->Note->save( $this->data ) ) {
                $refId = $this->Note->getInsertID();
                $editurl = Router::url(array('controller'=>'reminders','action'=>'view','note'=>$refId));
                if($this->data['Note']['notify'] && ($num = $this->Reminder->addReminder('note', $this->data['Note']['date'], $refId))) {
                    $rmdtxt = str_replace('%NUM%',$num,'تعداد %NUM% یادآور نیز برای این مورد در سیستم ثبت شد که میتوانید آنها را در <a href="'.$editurl.'">این بخش</a> مدیریت نمائید');
                }else {
                    $rmdtxt = 'با توجه به تاریخ مورد و تنظیمات یادآور شما برای این مورد یادآوری ذخیره نشد که میتوانید برای مدیریت یادآورهای این مورد از <a href="'.$editurl.'">این بخش</a> اقدام نمائید';
                }
                $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.<br />'.$rmdtxt, 'default', array( 'class' => 'success' ) );
                $this->redirect( array( 'action' => 'index' ) );
            } else {
                $this->data['Note']['date'] = explode(' ',$this->data['Note']['date']);
                $this->data['Note']['time'] = $this->data['Note']['date'][1];
                $this->data['Note']['date'] = $this->data['Note']['date'][0];
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            }
        }
    }

    function view( $id = null )
    {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        $this->set( 'note', $this->Note->read( null, $id ) );
    }

    function edit( $id = null )
    {
        if ( !$id && empty( $this->data ) ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        
        $this->Note->convertDateTimeFormat = 'Y/m/d H:i';
        
        if ( !empty( $this->data ) ) {
            //sanitize the data
            /*$san = new Sanitize();
            $this->data = $san->clean( $this->data );*/
            //$this->data = $this->sanitize($this->data);
            
            
            if(!preg_match('/^(\d{2}):(\d{2})$/',$this->data['Note']['time'], $match)) {
                $this->Session->setFlash( 'لطفا ساعت دریافت پیامک را صحیح وارد نمائید', 'default', array( 'class' => 'error-message' ) );
                return;
            } elseif( $match[1]>24 || $match[1]<0 || $match[2]>59 || $match[2]<0 ) {
                $this->Session->setFlash( 'لطفا ساعت دریافت پیامک را صحیح وارد نمائید', 'default', array( 'class' => 'error-message' ) );
                return;
            }
            
            $this->data['Note']['date'] = $this->data['Note']['date'].' '.$this->data['Note']['time'];
            unset($this->data['Note']['time']);
            
            if ( $this->Note->save( $this->data ) ) {
                $editurl = Router::url(array('controller'=>'reminders','action'=>'view','note'=>$this->data['Note']['id']));
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.<br />برای تغییر یادآورها از <a href="'.$editurl.'">این بخش</a> اقدام نمائید', 'default', array('class' => 'success'));
                $this->redirect( array( 'action' => 'index' ) );
            } else {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                $this->data['Note']['date'] = explode(' ',$this->data['Note']['date']);
                $this->data['Note']['time'] = $this->data['Note']['date'][1];
                $this->data['Note']['date'] = $this->data['Note']['date'][0];
            }
        }
        if ( empty( $this->data ) ) {
            $this->Note->convertDateFormat = 'Y/m/d';
            $this->data = $this->Note->read( null, $id );
            
            $this->data['Note']['subject'] = html_entity_decode( str_replace( '\n', "\n", $this->data['Note']['subject'] ), ENT_QUOTES, 'UTF-8' );
            $this->data['Note']['content'] = html_entity_decode( str_replace( '\n', "\n", $this->data['Note']['content'] ), ENT_QUOTES, 'UTF-8' );
            
            $this->data['Note']['date'] = explode(' ',$this->data['Note']['date']);
            $this->data['Note']['time'] = $this->data['Note']['date'][1];
            $this->data['Note']['date'] = $this->data['Note']['date'][0];
        }
    }

    function delete( $id = null ) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        //check user
        $this->Note->recursive = -1;
        if ( $this->Note->field( 'user_id', array( 'id' => $id ) ) != $this->Auth->user( 'id' ) ) {
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        //delete
        elseif ( $this->Note->delete( $id ) ) {
            $this->Reminder->recursive = -1;
            $this->Reminder->deleteRegarding('note',$id);
            $this->Session->setFlash( 'داده مورد نظر با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
        $this->redirect( array( 'action' => 'index' ) );
    }

    function markDone($id=null) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        $this->Note->id = intval($id);
        if(!$this->Note->saveField('status','done')){
            $this->Session->setFlash( 'مشکلی در انجام عملیات بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        $this->Session->setFlash('یادداشت مورد نظر به عنوان انجام شده علامت زده شد.<br />یادآورهای این موضوع تغییری نکرده اند. در صورت تمایل به حذف آنها از <a href="'.Router::url( array( 'controller'=>'reminders' , 'action'=>'view', 'note'=>$id ) ).'">اینجا</a> اقدام کنید', 'default', array('class' => 'success'));
        $this->redirect( array( 'action' => 'index' ) );
    }

    function export($limit=null) {
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion(8);
        $workbook->send('notes.xls');
        $worksheet = & $workbook->addWorksheet('notes');
        $worksheet->setInputEncoding('utf-8');

        //get the data
        $this->Note->recursive = 0;
        $this->Note->outputConvertDate = true;
        $this->Note->convertDateFormat = 'Y/m/d';
        $options = array();
        $options['fields'] = array( 'Note.subject', 'Note.content', 'Note.status', 'Note.created', 'Note.modified' );
        $options['order'] = "Note.created DESC";
        //apply the conditions
        if (!is_null($limit)) {
            $options['limit'] = $limit;
        }
        $notes = $this->Note->find('all', $options);
        $data = array();
        $data[] = array('عنوان', 'محتوا', 'وضعیت', 'تاریخ ایجاد', 'تاریخ ویرایش');
        $i = 1;
        foreach ($notes as $entry) {
            $data[$i][] = $entry['Note']['subject'];
            $data[$i][] = $entry['Note']['content'];
            $data[$i][] = __('note_'.$entry['Note']['status'], true);
            $data[$i][] = $entry['Note']['created'];
            $data[$i][] = $entry['Note']['modified'];
            $i++;
        }

        // write to excel
        $i = 0;
        foreach ($data as $row) {
            $worksheet->writeRow($i, 0, $row);
            $i++;
        }

        // send the file
        $workbook->close();
        return;
    }
    
    private function sanitize( $content )
    {
        if( is_array( $content ) ) {
            foreach( $content as &$val ) {
                $val = $this->sanitize($val);
            }
            return $content;
        }
        $san = new Sanitize();
        $content = $san->html($content);
        //$content = $san->escape($content);
        return $content;
    }
    
}

?>