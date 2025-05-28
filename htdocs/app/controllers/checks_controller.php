<?php

uses('sanitize');

class ChecksController extends AppController {

    var $name = 'Checks';
    var $uses = array('Check', 'Expense', 'Income', 'Transaction', 'Reminder', 'Config', 'Tag', 'CheckTag' );
    var $components = array('Security');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Security->requireAuth('index', 'edit');
        $this->Security->blackHoleCallback = 'fail';
    }

    function index() {
        $this->set('title_for_layout', 'چک‌ها');
        //sanitize the data
        $san = new Sanitize();
        $this->data = $san->clean($this->data);
        //paginate
        $conditions = array();
        if (isset($this->data['Check']['search'])) {
            if (!empty($this->data['Check']['type']) AND $this->data['Check']['type'] != 'all') {
                $conditions['Check.type'] = $this->data['Check']['type'];
            }
            if (!empty($this->data['Check']['status']) AND $this->data['Check']['status'] != 'all') {
                $conditions['Check.status'] = $this->data['Check']['status'];
            }
            if (!empty($this->data['Check']['bank_id'])) {
                $conditions['Check.bank_id'] = $this->data['Check']['bank_id'];
            }
            if (!empty($this->data['Check']['account_id'])) {
                $conditions['Check.account_id'] = $this->data['Check']['account_id'];
            }
            if (!empty($this->data['Check']['amount'])) {
                $this->data['Check']['amount'] = floatval( str_replace( ',', '', $this->data['Check']['amount'] ) );
                switch($this->data['Check']['type']){
                    case 'all':
                        $conditions['Check.amount'] = array($this->data['Check']['amount'],$this->data['Check']['amount']*-1);
                        break;
                    case 'received':
                        $conditions['Check.amount'] = $this->data['Check']['amount'];
                        break;
                    case 'drawed':
                        $conditions['Check.amount'] = $this->data['Check']['amount']*-1 ;
                        break;
                }
            }
            if (!empty($this->data['Check']['individual_id'])) {
                $conditions['Check.individual_id'] = $this->data['Check']['individual_id'];
            }
            if (!empty($this->data['Check']['description_search'])) {
                $conditions['Check.description LIKE '] = '%'.$this->data['Check']['description_search'].'%';
            }
            if (!empty($this->data['Check']['start_date'])) {
                $persianDate = new PersianDate();
                $this->data['Check']['start_date'] = $persianDate->pdate_format_reverse($this->data['Check']['start_date']);
                $conditions['Check.due_date >='] = $this->data['Check']['start_date'];
            }
            if (!empty($this->data['Check']['end_date'])) {
                $persianDate = new PersianDate();
                $this->data['Check']['end_date'] = $persianDate->pdate_format_reverse($this->data['Check']['end_date']);
                $conditions['Check.due_date <='] = $this->data['Check']['end_date'];
            }
            
            $catlist = array();
            if( empty($this->data['CheckTagSearch']['tag_id']) || (count($this->data['CheckTagSearch']['tag_id'])==1 && $this->data['CheckTagSearch']['tag_id'][0]=='0') ){
                //$catlist = $subcatlist = null;
            } else {
                foreach($this->data['CheckTagSearch']['tag_id'] as $k=>$v) {
                    switch( substr($v, 0,1) ) {
                        case 't':
                            $catlist[] = substr($v, 1);
                            break;
                        default:
                            unset($this->data['CheckTagSearch']['tag_id'][$k]);
                            break;
                    }
                }
                if ( $catlist ) {
                    $conditions['CheckTag.tag_id'] = $catlist;
                }
                $this->set( 'report_catlist' , $this->data['CheckTagSearch']['tag_id'] );
            }
            
            //save it into session
            $this->Session->delete('Check.conditions');
            $this->Session->write('Check.conditions', $conditions);
        }
        //reset the conditions
        if (empty($this->params['named']) AND empty($this->data)) {
            $this->Session->delete('Check.conditions');
        }
        //apply the conditions
        if ($this->Session->check('Check.conditions') AND !empty($this->params['named'])) {
            $conditions = $this->Session->read('Check.conditions');
        }
        
        $this->loadTags();
        
        $this->Check->recursive = 0;
        $this->Check->outputConvertDate = true;
        $this->Check->convertDateFormat = 'Y/m/d';
        $this->bindTagModel();
        $this->paginate['order'] = 'Check.due_date DESC,Check.id DESC';
        $this->paginate['group'] = 'Check.id';
        $this->paginate['conditions'] = $conditions;
        $this->set('checks', $this->paginate());
        
        /*$lQuery = $this->Check->getDataSource()->getLog(false, false);
        $where = explode('WHERE',$lQuery['log'][count($lQuery['log'])-1]['query']); $where = explode('ORDER',$where[count($where)-1]); $where = explode('LIMIT',$where[0]); $where = explode('GROUP',$where[0]); $where = $where[0];*/

        $where = $this->Check->getDataSource()->conditions($conditions+array('Check.user_id'=>$this->Auth->user( 'id' )), true, true, $this->Check);
        
        $sql = "SELECT
                    SUM(IF(type='drawed',amount,0)) AS sum_drawed,
                    SUM(IF(type='drawed',IF(status='done',amount,0),0)) AS sum_donedrawed,
                    SUM(IF(type='drawed',IF(status='due',amount,0),0)) AS sum_duedrawed,
                    
                    SUM(IF(type='received',amount,0)) AS sum_received,
                    SUM(IF(type='received',IF(status='done',amount,0),0)) AS sum_donereceived,
                    SUM(IF(type='received',IF(status='due',amount,0),0)) AS sum_duereceived
                    
                    FROM 
                        (SELECT Check.id, Check.amount AS amount, Check.type, Check.status
                        FROM `checks` AS `Check`
                        LEFT JOIN `check_tags` AS `CheckTag` ON (`CheckTag`.`check_id` = `Check`.`id`)
                        $where
                        GROUP BY Check.id)t";
        $sum = $this->Check->query($sql);
        
        $this->set('drawedSum', $sum[0][0]['sum_drawed']);
        $this->set('drawedDoneSum', $sum[0][0]['sum_donedrawed']);
        $this->set('drawedDueSum', $sum[0][0]['sum_duedrawed']);
        $this->set('receivedSum', $sum[0][0]['sum_received']);
        $this->set('receivedDoneSum', $sum[0][0]['sum_donereceived']);
        $this->set('receivedDueSum', $sum[0][0]['sum_duereceived']);
        
        // chart of drawed ones
        $sql = "SELECT
                    CONCAT(pyear,'/',pmonth) AS k,
                    SUM(ABS(amount)) AS value
                    FROM 
                        (SELECT Check.id, Check.amount AS amount, Check.pyear, Check.pmonth
                        FROM `checks` AS `Check`
                        LEFT JOIN `check_tags` AS `CheckTag` ON (`CheckTag`.`check_id` = `Check`.`id`)
                        $where AND `Check`.`type` = 'drawed'
                        GROUP BY Check.id)t
                GROUP BY k
                ORDER BY pyear ASC, pmonth ASC";
        $drawedChecksColumn = $this->Check->query($sql);
        $this->set('drawedChecksColumn', Set::classicExtract($drawedChecksColumn, '{n}.0'));
        
        // chart of received ones
        $sql = "SELECT
                    CONCAT(pyear,'/',pmonth) AS k,
                    SUM(ABS(amount)) AS value
                    FROM 
                        (SELECT Check.id, Check.amount AS amount, Check.pyear, Check.pmonth
                        FROM `checks` AS `Check`
                        LEFT JOIN `check_tags` AS `CheckTag` ON (`CheckTag`.`check_id` = `Check`.`id`)
                        $where AND `Check`.`type` = 'received'
                        GROUP BY Check.id)t
                GROUP BY k
                ORDER BY pyear ASC, pmonth ASC";
        $receivedChecksColumn = $this->Check->query($sql);
        $this->set('receivedChecksColumn', Set::classicExtract($receivedChecksColumn, '{n}.0'));

        //get banks and accounts
        $banks = $this->Check->Bank->find('list',array('order'=>'Bank.name ASC'));
        
        list($accounts, $accountsbalance) = $this->Check->Account->listAccounts('check');
        $this->set( compact( 'accounts' ) );
        $this->set( compact( 'accountsbalance' ) );
        
        $individuals = $this->Check->Individual->fetchList();
        list($allAccounts, $allAccountsbalance) = $this->Check->Account->listAccounts();
        $this->set( compact( 'allAccounts' ) );
        $this->set( compact( 'allAccountsbalance' ) );
        
        $this->set(compact('banks', 'accounts', 'individuals', 'allAccounts'));

        //add
        if (!empty($this->data) AND !isset($this->data['Check']['search'])) {
            //remove price formating strings and 
            $this->data['Check']['amount'] = str_replace(',', '', $this->data['Check']['amount']);
            if ($this->data['Check']['amount'] == 0) {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                return false;
            }
            if ($this->data['Check']['type'] == 'drawed') {
                $this->data['Check']['amount'] = $this->data['Check']['amount'] * (-1);
                //check for selected account
                if (!isset($this->data['Check']['account_id']) OR intval($this->data['Check']['account_id']) == 0) {
                    $this->Check->invalidate('account_id', 'حساب جاری وجود ندارد.');
                    $this->Session->setFlash('لطفا حساب جاری مربوط به این چک را انتخاب کنید، در صورتی که حساب جاری را ایجاد نکرده‌اید میتوانید از منوی حساب‌ها آن را ایجاد کنید.', 'default', array('class' => 'error-message'));
                    return false;
                }
                //get the bank id from account info
                $this->data['Check']['bank_id'] = $this->Check->Account->field('bank_id', array('id' => $this->data['Check']['account_id']));
            } else {
                $this->data['Check']['account_id']=null;
            }
            //save
            $this->Check->create();
            if ($this->Check->save($this->data)) {
                $refId = $this->Check->getLastInsertID();
                
                $this->CheckTag->replaceTags($refId, empty($this->data['CheckTag']['tag_id'])? array() : $this->data['CheckTag']['tag_id'] );
                
                $editurl = Router::url(array('controller'=>'reminders','action'=>'view','check'=>$refId));
                if($this->data['Check']['notify'] && ($num = $this->Reminder->addReminder('check', $this->data['Check']['due_date'], $refId))) {
                    $rmdtxt = str_replace('%NUM%',$num,'تعداد %NUM% یادآور نیز برای این مورد در سیستم ثبت شد که میتوانید آنها را در <a href="'.$editurl.'">این بخش</a> مدیریت نمائید');
                }else {
                    $rmdtxt = 'با توجه به تاریخ مورد و تنظیمات یادآور شما برای این مورد یادآوری ذخیره نشد که میتوانید برای مدیریت یادآورهای این مورد از <a href="'.$editurl.'">این بخش</a> اقدام نمائید';
                }
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.<br />'.$rmdtxt, 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                return false;
            }
        }
    }

    function edit($id = null) {
        $this->set('title_for_layout', 'ویرایش چک');
        
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        
        //get check
        $this->Check->recursive = 0;
        $this->Check->outputConvertDate = true;
        $this->Check->convertDateFormat = 'Y/m/d';
        $check = $this->Check->read(null, intval($id));
        $check['Check']['description'] = html_entity_decode( str_replace( '\n', "\n", $check['Check']['description'] ), ENT_QUOTES, 'UTF-8' );
        
        $this->CheckTag->recursive = -1;
        $check['Check']['CheckTag'] = Set::extract("{n}.0.tag_id", $this->CheckTag->find('all', array(
            'fields' => 'CONCAT("t",CheckTag.tag_id) AS tag_id',
            'conditions' => array(
                'check_id' => $check['Check']['id']
                )
        )));
        $this->loadTags();
        
        
        //don't let edit for settled debts
        if($check['Check']['status']=='done') {
            $this->Session->setFlash( 'چک‌های تسویه شده قابل ویرایش نیستند.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect(array('action' => 'index'));
            return false;
        }
        
        //get banks and accounts
        $banks = $this->Check->Bank->find('list');
        #$accounts = $this->Check->Account->listAccounts('check');
        list($accounts, $accountsbalance) = $this->Check->Account->listAccounts('check');
        $individuals = $this->Check->Individual->fetchList();
        $this->set(compact('banks', 'accounts', 'accountsbalance' , 'individuals'));
        
        if (empty($this->data)) {
            $this->data = $check;
            $this->data['Check']['amount'] = abs($this->data['Check']['amount']);
        }
        
        if (!empty($this->data) AND !empty($_POST)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);

            //do validation
            if ($check['Check']['type'] == 'drawed' AND empty($check['Check']['account_id']) AND !$this->data['Check']['account_id']) {
                $this->Session->setFlash('لطفا حساب جاری مربوط به این چک را انتخاب کنید. در صورتی که حساب جاری مربوط را ایجاد نکرده‌اید از منوی حساب‌ها آن را ایجاد کنید.', 'default', array('class' => 'error-message'));
                return false;
            }

            //remove price formating strings and 
            $this->data['Check']['amount'] = str_replace(',', '', $this->data['Check']['amount']);
            
            //
            if ($check['Check']['type'] == 'drawed') {
                $this->data['Check']['amount'] = $this->data['Check']['amount'] * (-1);
                $acc = $this->Check->Account->read(null, $this->data['Check']['account_id']);
                $this->data['Check']['bank_id'] = $acc['Bank']['id'];
            }

            //start transaction
            $dataSource = $this->Check->getDataSource();
            $dataSource->begin($this->Check);

            //check for amount change and clearing transaction
            if(abs($check['Check']['amount']) != abs($this->data['Check']['amount']) AND $check['Check']['clear_transaction_id']) {              
                //fix clearing transaction
                $this->Transaction->id=$check['Check']['clear_transaction_id'];
                if(!$this->Transaction->saveField('amount',abs($this->data['Check']['amount']))){                   
                    $dataSource->rollback($this->Check);
                    $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                    return false;
                }
                //update balance
                $amount=abs($this->data['Check']['amount']) - abs($check['Check']['amount']);
                if($check['Check']['type']=='drawed') {
                    $amount=-1*$amount;
                }
                $this->Transaction->Account->updateBalance($this->Transaction->field('account_id',array('id'=>$check['Check']['clear_transaction_id'])),$amount);
            }
            
            //save
            if (!$this->Check->save($this->data,true,array('amount','due_date','pyear','pmonth','serial','description','notify','account_id','bank_id','individual_id'))) {
                $dataSource->rollback($this->Check);
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                return false;
            }   
            
            $this->CheckTag->replaceTags($id, empty($this->data['CheckTag']['tag_id'])? array() : $this->data['CheckTag']['tag_id'] );
            
            //commit
            $dataSource->commit($this->Check);
            $editurl = Router::url(array('controller'=>'reminders','action'=>'view','check'=>$this->data['Check']['id']));
            $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.<br />برای تغییر یادآورها از <a href="'.$editurl.'">این بخش</a> اقدام نمائید', 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
            return true;
        }
    }

    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }

        //get check
        $this->Check->recursive = -1;
        $check = $this->Check->read(null, intval($id));

        //check user
        if ($check['Check']['user_id'] != $this->Auth->user('id')) {
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }

        //start transaction
        $dataSource = $this->Check->getDataSource();
        $dataSource->begin($this->Check);

        //delete
        if (!$this->Check->delete($id)) {
            $dataSource->rollback($this->Check);
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }
        $this->Reminder->deleteRegarding('check',$id);

        //check for check clearing transaction
        if ( intval($check['Check']['clear_transaction_id']) > 0) {
            //delete transaction and update the balance
            $this->Transaction->deleteAndUpdateBalance(intval($check['Check']['clear_transaction_id']));
        }

        //commit
        $dataSource->commit($this->Check);
        $this->Session->setFlash('داده مورد نظر با موفقیت پاک شد.', 'default', array('class' => 'success'));
        $this->redirect(Controller::referer());
    }

    function export($limit=null) {
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion(8);
        $workbook->send('checks.xls');
        $worksheet = & $workbook->addWorksheet('checks');
        $worksheet->setInputEncoding('utf-8');

        //get the data
        $this->Check->recursive = 0;
        $this->Check->outputConvertDate = true;
        $this->Check->convertDateFormat = 'Y/m/d';
        $options = array();
        $options['fields'] = array('Check.amount', 'Check.type', 'Check.due_date', 'Bank.name', 'Check.serial', 'Check.status', 'Check.description');
        $options['order'] = "Check.due_date DESC";
        if ( $this->Session->check( 'Check.conditions' ) ) {
            $options['conditions'] = $this->Session->read( 'Check.conditions' );
        }
        if (!is_null($limit)) {
            $conditions['limit'] = $limit;
        }
        $checks = $this->Check->find('all', $options);
        $data = array();
        $data[] = array('مبلغ', 'نوع چک', 'موعد چک', 'بانک', 'سریال', 'وضعیت', 'توضیحات');
        $i = 1;
        foreach ($checks as $entry) {
            $data[$i][] = $entry['Check']['amount'];
            $data[$i][] = __($entry['Check']['type'], true);
            $data[$i][] = $entry['Check']['due_date'];
            $data[$i][] = $entry['Bank']['name'];
            $data[$i][] = ' '.$entry['Check']['serial'];
            $data[$i][] = __($entry['Check']['status'], true);
            $data[$i][] = $entry['Check']['description'];
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

    function ajaxGetWeekChecks($startDate, $endDate, $order='Check.due_date ASC') {
        if ($this->RequestHandler->isAjax()) {
            //sanitize the data
            $san = new Sanitize();
            $this->params = $san->clean($this->params);
            //get current week's due checks
            $this->Check->convertDateFormat = 'l j F Y';
            $this->set('response', $this->Check->getChecks(date('Y-m-d', strtotime('last saturday')), date('Y-m-d', strtotime('next friday'))));
            $this->render('ajax', 'json');
            return true;
        }
        $this->redirect(array('action' => 'index'));
    }

    function ajaxGetMonthChecks($startDate, $endDate, $order='Check.due_date ASC') {
        if ($this->RequestHandler->isAjax()) {
            //sanitize the data
            $san = new Sanitize();
            $this->params = $san->clean($this->params);
            //get current month's due checks
            $currentYear = $this->Check->pDate(date('Y/m/d'), 'Y');
            $currentMonth = $this->Check->pDate(date('Y/m/d'), 'm');
            $monthStart = $this->Check->pDateReverse($currentYear . '/' . $currentMonth . '/' . '01');
            $monthEnd = date('Y-m-d', strtotime($this->Check->pDateReverse($currentYear . '/' . ($currentMonth + 1) . '/' . '01')) - 86400);
            $this->set('response', $this->Check->getChecks($monthStart, $monthEnd));
            $this->render('ajax', 'json');
            return true;
        }
        $this->redirect(array('action' => 'index'));
    }

    /*
     * mark drawed check as done
     */

    function drawedcheckdDone($id) {
        //get check
        $this->Check->outputConvertDate = false;
        $check = $this->Check->read(null, intval($id));

        //do some validations
        if (empty($check)) {
            $this->Session->setFlash('مشکلی در تسویه چک بوجود آمد. لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }
        if ($check['Check']['type'] != 'drawed') {
            $this->Session->setFlash('مشکلی در تسویه چک بوجود آمد. لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }
        if ($check['Check']['status'] == 'done') {
            $this->Session->setFlash('این چک تسویه شده است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }
        if (!$check['Check']['account_id']) {
            $this->Session->setFlash('لطفا حساب جاری مربوط به این چک را انتخاب کنید. در صورتی که حساب جاری مربوط را ایجاد نکرده‌اید از منوی حساب‌ها آن را ایجاد کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'edit', $check['Check']['id']));
            return false;
        }

        //get the checks expense category
        $this->Expense->ExpenseCategory->recursive = -1;
        $expenseCategory = $this->Expense->ExpenseCategory->find('first', array('fields' => array('ExpenseCategory.id'), 'conditions' => array('name' => 'چک‌')));
        if (empty($expenseCategory)) {
            $this->Session->setFlash('مشکلی در تسویه چک بوجود آمد. لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }

        //start transaction
        $dataSource = $this->Check->getDataSource();
        $dataSource->begin($this->Check);

        //save status
        $this->Check->id = intval($id);
        if (!$this->Check->saveField('status', 'done', true)) {
            $this->Session->setFlash('مشکلی در تسویه چک بوجود آمد. لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }

        //gather data
        $data['Transaction']['account_id'] = intval($check['Check']['account_id']);
        $data['Transaction']['amount'] = abs($check['Check']['amount']);
        $data['Transaction']['date'] = date('Y-m-d');
        $data['Expense']['individual_id'] = $check['Check']['individual_id'];
        $data['Expense']['description'] = 'تسویه چک ' . $check['Check']['description'];
        $data['Expense']['expense_category_id'] = $expenseCategory['ExpenseCategory']['id'];

        //save the expense
        $this->Expense->inputConvertDate = false;
        if (!$transactionId = $this->Expense->saveExpense($data)) {
            $dataSource->rollback($this->Expense);
            $this->Session->setFlash('مشکلی در تسویه چک بوجود آمد. لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }

        //save check clearing transaction id back to check
        if (!$this->Check->saveField('clear_transaction_id', $transactionId)) {
            $dataSource->rollback($this->Expense);
            $this->Session->setFlash('مشکلی در تسویه چک بوجود آمد. لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }

        //commit
        $dataSource->commit($this->Expense);
        $this->Session->setFlash('چک مورد نظر با موفقیت تسویه شد.<br />یادآورهای این موضوع تغییری نکرده اند. در صورت تمایل به حذف آنها از <a href="'.Router::url( array( 'controller'=>'reminders' , 'action'=>'view', 'check'=>$id ) ).'">اینجا</a> اقدام کنید', 'default', array('class' => 'success'));
        $this->redirect(Controller::referer());
        return true;
    }

    /*
     * mark check as done
     */
    function ajaxCheckDone($id) {
        if ($this->RequestHandler->isAjax()) {
            Configure::write('debug', 0);

            //sanitize the data
            $san = new Sanitize();
            $this->params = $san->clean($this->params);

            //get check
            $this->Check->outputConvertDate = false;
            $check = $this->Check->read(null, intval($id));

            //do some validations
            if (empty($check)) {
                $this->Session->setFlash('مشکلی در تسویه چک بوجود آمد. لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                $this->redirect(array('action' => 'index'));
                return false;
            }
            if ($check['Check']['status'] == 'done') {
                $this->set('response', false);
                $this->render('ajax', 'json');
                return false;
            }
            if ($check['Check']['type'] == 'drawed' AND !$check['Check']['account_id']) {
                $this->set('response', false);
                $this->render('ajax', 'json');
                return false;
            }
            if ($check['Check']['type'] == 'received' AND intval($this->params['form']['account_id']) <= 0) {
                $this->set('response', false);
                $this->render('ajax', 'json');
                return false;
            }

            //start transaction
            $dataSource = $this->Check->getDataSource();
            $dataSource->begin($this->Check);

            //save status
            $this->Check->id = intval($id);
            if (!$this->Check->saveField('status', 'done', true)) {
                $dataSource->rollback($this->Check);
                $this->set('response', false);
                $this->render('ajax', 'json');
                return false;
            }

            if ($check['Check']['type'] == 'drawed') {

                //get the checks expense category
                $this->Expense->ExpenseCategory->recursive = -1;
                $expenseCategory = $this->Expense->ExpenseCategory->find('first', array('fields' => array('ExpenseCategory.id'), 'conditions' => array('name' => 'چک‌')));
                if (empty($expenseCategory)) {
                    $dataSource->rollback($this->Check);
                    $this->set('response', false);
                    $this->render('ajax', 'json');
                    return false;
                }

                //gather data
                $data['Transaction']['account_id'] = intval($check['Check']['account_id']);
                $data['Transaction']['amount'] = abs($check['Check']['amount']);
                $data['Transaction']['date'] = date('Y-m-d');
                $data['Expense']['individual_id'] = $check['Check']['individual_id'];
                $data['Expense']['description'] = 'تسویه چک ' . $check['Check']['description'];
                $data['Expense']['expense_category_id'] = $expenseCategory['ExpenseCategory']['id'];

                //save the expense
                $this->Expense->inputConvertDate = false;
                if (!$transactionId = $this->Expense->saveExpense($data)) {
                    $dataSource->rollback($this->Expense);
                    $this->set('response', false);
                    $this->render('ajax', 'json');
                    return false;
                }

                //save check clearing transaction id back to check
                $this->Check->id=$check['Check']['id'];
                if (!$this->Check->saveField('clear_transaction_id', $transactionId)) {
                    $dataSource->rollback($this->Expense);
                    $this->set('response', false);
                    $this->render('ajax', 'json');
                    return false;
                }
            } else {

                //get the checks income type
                $this->Income->IncomeType->recursive = -1;
                $incomeType = $this->Income->IncomeType->find('first', array('fields' => array('IncomeType.id'), 'conditions' => array('name' => 'چک‌')));
                if (empty($incomeType)) {
                    $dataSource->rollback($this->Check);
                    $this->set('response', false);
                    $this->render('ajax', 'json');
                    return false;
                }

                //gather data
                $data['Transaction']['account_id'] = intval($this->params['form']['account_id']);
                $data['Transaction']['amount'] = abs($check['Check']['amount']);
                $data['Transaction']['date'] = date('Y-m-d');
                $data['Income']['individual_id'] = $check['Check']['individual_id'];
                $data['Income']['description'] = 'تسویه چک ' . $check['Check']['description'];
                $data['Income']['income_type_id'] = $incomeType['IncomeType']['id'];

                //save the income
                $this->Income->inputConvertDate = false;
                if (!$transactionId = $this->Income->saveIncome($data)) {
                    $dataSource->rollback($this->Income);
                    $this->set('response', false);
                    $this->render('ajax', 'json');
                    return false;
                }

                //save check clearing transaction id back to check
                $this->Check->id=$check['Check']['id'];
                if (!$this->Check->saveField('clear_transaction_id', $transactionId)) {
                    $dataSource->rollback($this->Income);
                    $this->set('response', false);
                    $this->render('ajax', 'json');
                    return false;
                }
            }

            //commit
            $dataSource->commit($this->Income);
            $this->Session->setFlash('چک مورد نظر با موفقیت تسویه شد.<br />یادآورهای این موضوع تغییری نکرده اند. در صورت تمایل به حذف آنها از <a href="'.Router::url( array( 'controller'=>'reminders' , 'action'=>'view', 'check'=>$id ) ).'">اینجا</a> اقدام کنید', 'default', array('class' => 'success'));
            $this->set('response', true);
            $this->render('ajax', 'json');
            return true;
        }
        $this->redirect(array('action' => 'index'));
    }
    
    private function loadTags()
    {
        $this->set( 'tags' , $this->Tag->prepareList( $this->Tag->loadTags() ) );
    }
    
    private function bindTagModel($force=false)
    {
        $this->Check->bindModel( array(
            'hasOne' => array(
                'CheckTag' => array(
                        'foreignKey' => false,
                        'conditions' => array( 'CheckTag.check_id = Check.id' )
                    )
            )
            ), false );
    }

}

?>