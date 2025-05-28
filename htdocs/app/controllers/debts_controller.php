<?php

uses('sanitize');

class DebtsController extends AppController {

    var $name = 'Debts';
    var $uses = array('Debt', 'DebtSettlement', 'Expense', 'Income', 'Transaction', 'Reminder', 'Config', 'Tag', 'DebtTag', 'TransactionTag' );
    var $components = array('Security');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Security->requireAuth('index', 'edit');
        $this->Security->blackHoleCallback = 'fail';
    }

    function index() {
        
        $this->set('title_for_layout', 'بدهی و طلب');
        //sanitize the data
        $san = new Sanitize();
        $this->data = $san->clean($this->data);
        //paginate
        $conditions = array();
        if (isset($this->data['Debt']['search'])) {
            if (!empty($this->data['Debt']['type']) AND $this->data['Debt']['type'] != 'all') {
                $conditions['Debt.type'] = $this->data['Debt']['type'];
            }
            if (!empty($this->data['Debt']['status']) AND $this->data['Debt']['status'] != 'all') {
                if($this->data['Debt']['status']=='due'){
                    $conditions['OR'] = array(array('Debt.status'=>'due'),array('Debt.status'=>'part'));
                } else {
                    $conditions['Debt.status'] = $this->data['Debt']['status'];
                }
            }
            if (!empty($this->data['Debt']['individual_id'])) {
                $conditions['Debt.individual_id'] = $this->data['Debt']['individual_id'];
            }
            if (!empty($this->data['Debt']['name'])) {
                $conditions['Debt.name LIKE '] = '%'.$this->data['Debt']['name'].'%';
            }
            if (!empty($this->data['Debt']['amount'])) {
                $amount = abs(str_replace(',', '', $this->data['Debt']['amount']));
                if( !empty($this->data['Debt']['type']) AND $this->data['Debt']['type'] != 'all' ) {
                    $conditions['Debt.amount'] = $amount * ( ($this->data['Debt']['type']=='debt')? -1 : 1 );
                } else {
                    $conditions['Debt.amount'] = array( $amount , $amount * -1 );
                }
            }
            if (!empty($this->data['Debt']['start_date'])) {
                $persianDate = new PersianDate();
                $this->data['Debt']['start_date'] = $persianDate->pdate_format_reverse($this->data['Debt']['start_date']);
                $conditions['Debt.due_date >='] = $this->data['Debt']['start_date'];
            }
            if (!empty($this->data['Debt']['end_date'])) {
                $persianDate = new PersianDate();
                $this->data['Debt']['end_date'] = $persianDate->pdate_format_reverse($this->data['Debt']['end_date']);
                $conditions['Debt.due_date <='] = $this->data['Debt']['end_date'];
            }
            
            $catlist = array();
            if( empty($this->data['DebtTagSearch']['tag_id']) || (count($this->data['DebtTagSearch']['tag_id'])==1 && $this->data['DebtTagSearch']['tag_id'][0]=='0') ){
                //$catlist = $subcatlist = null;
            } else {
                foreach($this->data['DebtTagSearch']['tag_id'] as $k=>$v) {
                    switch( substr($v, 0,1) ) {
                        case 't':
                            $catlist[] = substr($v, 1);
                            break;
                        default:
                            unset($this->data['DebtTagSearch']['tag_id'][$k]);
                            break;
                    }
                }
                if ( $catlist ) {
                    $conditions['DebtTag.tag_id'] = $catlist;
                }
                $this->set( 'report_catlist' , $this->data['DebtTagSearch']['tag_id'] );
            }
            
            //save it into session
            $this->Session->delete('Debt.conditions');
            $this->Session->write('Debt.conditions', $conditions);
        }
        //reset the conditions
        if (empty($this->params['named']) AND empty($this->data)) {
            $this->Session->delete('Debt.conditions');
        }
        //apply the conditions
        if ($this->Session->check('Debt.conditions') AND !empty($this->params['named'])) {
            $conditions = $this->Session->read('Debt.conditions');
        }
        
        $this->loadTags();
        
        $this->Debt->recursive = 0;
        $this->Debt->convertDateFormat = 'Y/m/d';
        $this->Debt->convertDateTimeFormat = 'Y/m/d';
        $this->bindTagModel();
        $this->paginate['order'] = 'Debt.due_date DESC,Debt.id DESC';
        $this->paginate['conditions'] = $conditions;
        $this->paginate['group'] = 'Debt.id';
        $this->set('debts', $this->paginate());
        
        /*$lQuery = $this->Debt->getDataSource()->getLog(false, false);
        $where = explode('WHERE',$lQuery['log'][count($lQuery['log'])-1]['query']); $where = explode('ORDER',$where[count($where)-1]); $where = explode('LIMIT',$where[0]); $where = explode('GROUP',$where[0]); $where = $where[0];*/
        
        unset($conditions['DebtTag.tag_id']);
        $where = $this->Debt->getDataSource()->conditions($conditions+array('Debt.user_id'=>$this->Auth->user( 'id' )), true, true, $this->Debt);
        foreach( $catlist as $v ) {
            $where .= " AND FIND_IN_SET($v,`DebtTag`.`tag_id`) ";
        }
        $tagsJoinSql = "";
        if (count($catlist)>0){
            $tagsJoinSql= " LEFT JOIN ( SELECT `debt_id`, GROUP_CONCAT(`tag_id` SEPARATOR ',') AS `tag_id`  FROM `debt_tags` GROUP BY `debt_id`) AS `DebtTag` ON (`DebtTag`.`debt_id` = `Debt`.`id`) ";
        }
        $sql = "SELECT
                    SUM(IF(type='debt',amount,0)) AS sum_debt,
                    SUM(IF(type='debt',IF(status<>'due',IFNULL(partly_amount,0)*-1,0),0)) AS sum_donedebt,
                    SUM(IF(type='debt',IF(status<>'done',amount+IFNULL(partly_amount,0),0),0)) AS sum_duedebt,
                    #SUM(IF(type='debt',IF(status='part',IFNULL(partly_amount,0)*-1,0),0)) AS sum_partdebt,
                    
                    SUM(IF(type='credit',amount,0)) AS sum_credit,
                    SUM(IF(type='credit',IF(status<>'due',IFNULL(partly_amount,0),0),0)) AS sum_donecredit,
                    SUM(IF(type='credit',IF(status<>'done',amount-IFNULL(partly_amount,0),0),0)) AS sum_duecredit
                    #,SUM(IF(type='credit',IF(status='part',IFNULL(partly_amount,0),0),0)) AS sum_partcredit
                    
                    FROM 
                        (SELECT Debt.id, Debt.amount AS amount, Debt.type, Debt.status, SUM(`DebtSettlement`.`amount`) AS partly_amount
                        FROM `debts` AS `Debt`
                        $tagsJoinSql 
                        LEFT JOIN `debt_settlements` AS `DebtSettlement` ON (`DebtSettlement`.`debt_id` = `Debt`.`id`)
                        $where
                        GROUP BY Debt.id)t";
        $sum = $this->Debt->query($sql);
        
        $this->set('debtsSum', $sum[0][0]['sum_debt']);
        $this->set('debtsDoneSum', $sum[0][0]['sum_donedebt']);
        $this->set('debtsDueSum', $sum[0][0]['sum_duedebt']);
        $this->set('creditsSum', $sum[0][0]['sum_credit']);
        $this->set('creditsDoneSum', $sum[0][0]['sum_donecredit']);
        $this->set('creditsDueSum', $sum[0][0]['sum_duecredit']);
        
        // chart of debts
        $sql = "SELECT
                    CONCAT(pyear,'/',pmonth) AS k,
                    SUM(ABS(amount)) AS value
                    FROM 
                        (SELECT Debt.id, Debt.amount AS amount, Debt.pyear, Debt.pmonth
                        FROM `debts` AS `Debt`
                        $tagsJoinSql 
                        $where AND `Debt`.`type` = 'debt'
                        GROUP BY Debt.id)t
                GROUP BY k
                ORDER BY pyear ASC, pmonth ASC";
        $debtsColumn = $this->Debt->query($sql);
        $this->set('debtsColumn', Set::classicExtract($debtsColumn, '{n}.0'));
        
        // chart of credits
        $sql = "SELECT
                    CONCAT(pyear,'/',pmonth) AS k,
                    SUM(ABS(amount)) AS value
                    FROM 
                        (SELECT Debt.id, Debt.amount AS amount, Debt.pyear, Debt.pmonth
                        FROM `debts` AS `Debt`
                        $tagsJoinSql 
                        $where AND `Debt`.`type` = 'credit'
                        GROUP BY Debt.id)t
                GROUP BY k
                ORDER BY pyear ASC, pmonth ASC";
        $creditsColumn = $this->Debt->query($sql);
        $this->set('creditsColumn', Set::classicExtract($creditsColumn, '{n}.0'));

        //get accounts
        list($accounts, $accountsbalance) = $this->Income->Transaction->Account->listAccounts();
        $this->set(compact('accounts','accountsbalance'));

        //get individuals
        $individuals = $this->Debt->Individual->fetchList();
        $this->set(compact('individuals'));
        
        //add
        if (!empty($this->data) AND !isset($this->data['Debt']['search'])) {
            $this->data['Debt']['amount'] = str_replace(',', '', $this->data['Debt']['amount']);
            if ($this->data['Debt']['type'] == 'debt') {
                $this->data['Debt']['amount'] = $this->data['Debt']['amount'] * (-1);
            }
            //save
            $dataSource = $this->Debt->getDataSource();
            $dataSource->begin($this->Debt);
            $this->Debt->create();
            if ($this->Debt->save($this->data)) {
                $debtId=  $this->Debt->getInsertID();
                $this->DebtTag->replaceTags($debtId, empty($this->data['DebtTag']['tag_id'])? array() : $this->data['DebtTag']['tag_id'] );
                $persianDate = new PersianDate();
                if ($this->data['Debt']['type'] == 'debt' AND $this->data['Debt']['add']=='yes') {
                    //add income
                    $data['Transaction']['amount'] = abs($this->data['Debt']['amount']);
                    $data['Transaction']['date'] = $persianDate->pdate('Y/m/d', 'now');
                    $data['Transaction']['type'] = 'credit';
                    $data['Transaction']['account_id'] = $this->data['Transaction']['account_id'];
                    $data['Income']['individual_id'] = $this->data['Debt']['individual_id'];
                    $data['Income']['income_type_id'] = $this->Income->IncomeType->getIncomeTypeIdByName('قرض');
                    $data['Income']['description'] = 'بدهی '.$this->data['Debt']['name'];
                    $data['TransactionTag']['tag_id'] = $this->data['DebtTag']['tag_id'];
                    if (!$transactionId = $this->Income->saveIncome($data)) {
                        $dataSource->rollback($this->Debt);
                        $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                        $this->redirect(array('action' => 'index'));
                        return false;
                    }
                    //save transaction id back to debt
                    $this->Debt->id = $debtId;
                    if(!$this->Debt->saveField('transaction_id',$transactionId)) {
                        $dataSource->rollback($this->Debt);
                        $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                        $this->redirect(array('action' => 'index'));
                        return false;
                    }
                } 
                if ($this->data['Debt']['type'] == 'credit' AND $this->data['Debt']['add']=='yes') {
                    //add expense
                    $data['Transaction']['amount'] = abs($this->data['Debt']['amount']);
                    $data['Transaction']['date'] = $persianDate->pdate('Y/m/d', 'now');
                    $data['Transaction']['type'] = 'debt';
                    $data['Transaction']['account_id'] = $this->data['Transaction']['account_id'];
                    $data['Expense']['individual_id'] = $this->data['Debt']['individual_id'];
                    $data['Expense']['expense_category_id'] = $this->Expense->ExpenseCategory->getExpenseCategoryIdByName('قرض');
                    $data['Expense']['description'] = 'طلب '.$this->data['Debt']['name'];
                    $data['TransactionTag']['tag_id'] = $this->data['DebtTag']['tag_id'];
                    if (!$transactionId = $this->Expense->saveExpense($data)) {
                        $dataSource->rollback($this->Debt);
                        $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                        $this->redirect(array('action' => 'index'));
                        return false;
                    }
                    //save transaction id back to credit
                    $this->Debt->id = $debtId;
                    if(!$this->Debt->saveField('transaction_id',$transactionId)) {
                        $dataSource->rollback($this->Debt);
                        $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                        $this->redirect(array('action' => 'index'));
                        return false;
                    }
                }
                
                $editurl = Router::url(array('controller'=>'reminders','action'=>'view','debt'=>$debtId));
                if($this->data['Debt']['notify']=='yes'){
                    if($num = $this->Reminder->addReminder('debt', $this->data['Debt']['due_date'], $debtId)) {
                        $rmdtxt = str_replace('%NUM%',$num,'تعداد %NUM% یادآور نیز برای این مورد در سیستم ثبت شد که میتوانید آنها را در <a href="'.$editurl.'">این بخش</a> مدیریت نمائید');
                    }else {
                        $rmdtxt = 'با توجه به تاریخ مورد و تنظیمات یادآور شما برای این مورد یادآوری ذخیره نشد که میتوانید برای مدیریت یادآورهای این مورد از <a href="'.$editurl.'">این بخش</a> اقدام نمائید';
                    }
                } else {
                    $rmdtxt = 'با توجه به تاریخ مورد و تنظیمات یادآور شما برای این مورد یادآوری ذخیره نشد که میتوانید برای مدیریت یادآورهای این مورد از <a href="'.$editurl.'">این بخش</a> اقدام نمائید';
                }
                $dataSource->commit($this->Debt);
                $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.<br />'.$rmdtxt, 'default', array('class' => 'success'));
                $this->redirect(array('action' => 'index'));
                return true;
            } else {
                $dataSource->rollback($this->Debt);
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                return false;
            }
        }
    }
    
    function edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
            return false;
        }
        
        //get debt
        $this->Debt->outputConvertDate = true;
        $this->Debt->convertDateFormat = 'Y/m/d';
        $this->Debt->convertDateTimeFormat = 'Y/m/d';
        $this->Debt->recursive = -1;
        $debt = $this->Debt->read(null, intval($id));
        $debt['Debt']['name'] = html_entity_decode( str_replace( '\n', "\n", $debt['Debt']['name'] ), ENT_QUOTES, 'UTF-8' );
        $debt['Debt']['DebtTag'] = Set::extract("{n}.0.tag_id", $this->DebtTag->find('all', array(
            'fields' => 'CONCAT("t",DebtTag.tag_id) AS tag_id',
            'conditions' => array(
                'debt_id' => $debt['Debt']['id']
                )
        )));
        
        $this->loadTags();
       
        //don't let edit for settled debts
        if($debt['Debt']['status']=='done') {
            $this->Session->setFlash( 'بدهی / طلب‌های تسویه شده قابل ویرایش نیستند.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect(array('action' => 'index'));
            return false;
        }
        
        //get individuals
        $individuals = $this->Debt->Individual->fetchList();
        $this->set(compact('individuals'));

        $this->set('title_for_layout', 'ویرایش '.__($debt['Debt']['type'],true));
        
        if (empty($this->data)) {
            $this->data = $debt;
            $this->data['Debt']['amount'] = abs($this->data['Debt']['amount']);
        }
               
        //save
        if (!empty($this->data) && !empty($_POST)) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean($this->data);

            //remove price formatting
            $this->data['Debt']['amount'] = str_replace(',', '', $this->data['Debt']['amount']);
            
            //check for settled amount more than debt amount
            if ( $debt['Debt']['settled'] > $this->data['Debt']['amount'] ) {
                $this->Session->setFlash( 'مبلغ وارد شده از مبلغ تسویه شده بیشتر است.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //check for settled amount is equal to the debt amount
            $this->data['Debt']['status'] = $debt['Debt']['status'];
            if ( $debt['Debt']['settled'] == $this->data['Debt']['amount'] ) {
                $this->data['Debt']['status'] = 'done';
            }

            //check for new amount more than the debt amount
            if ( $debt['Debt']['status'] == 'done' AND $this->data['Debt']['amount'] > abs( $debt['Debt']['amount'] ) ) {
                $this->data['Debt']['status'] = 'due';
            }
            
            //
            if ($debt['Debt']['type'] == 'debt' AND $this->data['Debt']['amount'] > 0) {
                $this->data['Debt']['amount'] = $this->data['Debt']['amount'] * (-1);
            }
            
            //start transaction
            $dataSource = $this->Debt->getDataSource();
            $dataSource->begin($this->Debt);
            
            //check for amount change and clearing transaction
            if(abs($debt['Debt']['amount']) != abs($this->data['Debt']['amount'])) {
                
                if($debt['Debt']['transaction_id']) {
                  
                    //fix clearing transaction
                    $this->Transaction->id=$debt['Debt']['transaction_id'];
                    //if the transaction exists
                    if($this->Transaction->exists()) {
                        if(!$this->Transaction->saveField('amount',abs($this->data['Debt']['amount']))) {
                            $dataSource->rollback($this->Debt);
                            $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                            return false;
                        }
                        //update balance
                        $amount=abs($this->data['Debt']['amount'])-abs($debt['Debt']['amount']);
                        if($debt['Debt']['type'] == 'credit') {
                            $amount=-1*$amount;
                        }
                        $this->Transaction->Account->updateBalance( $this->Transaction->field('account_id',array('Transaction.id'=>$debt['Debt']['transaction_id'])) ,$amount);
                    }
                }              
            }
            
            //update the transaction date if created date is modified
            if ( $debt['Debt']['created'] != $this->data['Debt']['created'] ) {
                $this->Transaction->inputConvertDate=true;
                $this->Transaction->id = $debt['Debt']['transaction_id'];
                $this->Transaction->save(array('Transaction'=>array('date'=>$this->data['Debt']['created'])),true,array('date'));
                $this->TransactionTag->replaceTags($debt['Debt']['transaction_id'], empty($this->data['DebtTag']['tag_id'])? array() : $this->data['DebtTag']['tag_id'] );
            }
            
            //update the expense/income individual_id if individual is modified
            if ( $debt['Debt']['individual_id'] != $this->data['Debt']['individual_id'] ) {
                //get transaction
                $this->Transaction->recusrive = -1;
                $transaction = $this->Transaction->read( null, $debt['Debt']['transaction_id'] );
                if ( $transaction['Transaction']['expense_id'] ) {
                    $this->Transaction->Expense->id = $transaction['Transaction']['expense_id'];
                    $this->Transaction->Expense->saveField( 'individual_id', $this->data['Debt']['individual_id'] );
                } elseif ( $transaction['Transaction']['income_id'] ) {
                    $this->Transaction->Income->id = $transaction['Transaction']['income_id'];
                    $this->Transaction->Income->saveField( 'individual_id', $this->data['Debt']['individual_id'] );
                }
            }

            //save
            $this->Debt->inputConvertDate=true;
            if (!$this->Debt->save($this->data,true,array('amount','name','individual_id','due_date','pyear','pmonth','status','notify','created'))) {
                $dataSource->rollback($this->Debt);
                $this->Session->setFlash('مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
                return false;
            }
            
            $this->DebtTag->replaceTags($id, empty($this->data['DebtTag']['tag_id'])? array() : $this->data['DebtTag']['tag_id'] );
            
            //commit
            $dataSource->commit($this->Debt);
            $editurl = Router::url(array('controller'=>'reminders','action'=>'view','debt'=>$this->data['Debt']['id']));
            $this->Session->setFlash('داده‌های موردنظر با موفقیت وارد شد.<br />برای تغییر یادآورها از <a href="'.$editurl.'">این بخش</a> اقدام نمائید', 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
            return true;
        }
    }

    function view($id = null) {
        $this->set('title_for_layout', 'نمایش بدهی/طلب');
        
        if (!$id) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        
        $this->Debt->recursive=1;
        $this->Debt->outputConvertDate = true;
        $this->Debt->convertDateFormat = 'Y/m/d';
        $this->set('debt',$this->Debt->read(null,intval($id)));
    }
        
    
    function delete($id = null) {
        if (!$id) {
            $this->Session->setFlash('شماره نامعتبر است.', 'default', array('class' => 'error-message'));
            $this->redirect(array('action' => 'index'));
        }
        
        //get debt
        $this->Debt->recursive = 1;
        $debt = $this->Debt->read(null,intval($id));
        
        //check user
        if ($debt['Debt']['user_id'] != $this->Auth->user('id')) {
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            #$this->redirect(array('action' => 'index'));
            return false;
        }
        
        //start transaction
        $dataSource = $this->Debt->getDataSource();
        $dataSource->begin($this->Debt);
        
        //delete
        if (!$this->Debt->delete($id)) {
            $dataSource->rollback($this->Debt);
            $this->Session->setFlash('مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array('class' => 'error-message'));
            #$this->redirect(array('action' => 'index'));
            return false;
        }
        $this->Reminder->deleteRegarding('debt',$id);
        
        //check for transaction
        if(intval($debt['Debt']['transaction_id'])>0) {
            //delete transaction and update the balance
            $this->Transaction->deleteAndUpdateBalance(intval($debt['Debt']['transaction_id']));
        }
        
        //check for debt clearing transactions
        foreach ( $debt['DebtSettlement'] as $debtSettlement ) {
            if(intval($debtSettlement['transaction_id'])>0) {
                //delete transaction and update the balance
                $this->Transaction->deleteAndUpdateBalance(intval($debtSettlement['transaction_id']));
            }
        }
        
        //commit
        $dataSource->commit($this->Check);
        $this->Session->setFlash('داده مورد نظر با موفقیت پاک شد.', 'default', array('class' => 'success'));
        $this->redirect(array('action' => 'index'));
        return true;
    }

    /*
     * mark debt as done
     */

    function ajaxDebtDone($id) {
        if ($this->RequestHandler->isAjax()) {
            Configure::write('debug', 0);
            
            //sanitize the data
            $san = new Sanitize();
            $this->params = $san->clean($this->params);
            
            //get debt
            $this->Debt->outputConvertDate = false;
            $debt = $this->Debt->read(null, intval($id));
            $total = intval( abs( $debt['Debt']['amount'] ) );
            $settled = intval( $debt['Debt']['settled'] );

            //check for obvious situations
            if ( $debt['Debt']['status'] == 'done' OR $total <= $settled ) {
                $this->set( 'response', false );
                $this->render( 'ajax', 'json' );
                return true;
            }
            
            //check for settled amount and settle request settled_amount
            $this->params['form']['settled_amount'] = str_replace(',', '', $this->params['form']['settled_amount']);
            if ( $total < ($settled + intval( $this->params['form']['settled_amount'] ) ) ) {
                $this->set('response', false);
                $this->render('ajax', 'json');
                return false;
            }
            
            //get the status after settle
            $status = 'part';
            if ( $total == ($settled + intval( $this->params['form']['settled_amount'] ) ) OR $this->params['form']['state'] == 'all' ) {
                $status = 'done';
            }
            
            //get settle amount
            if ( $status == 'part' AND $this->params['form']['state'] != 'all' ) {
                $amount = intval( $this->params['form']['settled_amount'] );
            } else {
                $amount = $total - $settled;
            }
            
            //start transaction
            $dataSource = $this->Debt->getDataSource();
            $dataSource->begin($this->Debt);
                        
            //save status
            $this->Debt->id = intval($id);
            if (!$this->Debt->saveField('status', $status, true)) {
                $this->set('response', false);
                $this->render('ajax', 'json');
                return false;
            }
                        
            //add data
            $transactionId = null;
            if ((bool) $this->params['form']['addData']) {
                
                //check for account id
                if(!isset($this->params['form']['account_id'])) {
                    $dataSource->rollback($this->Debt);
                    $this->set('response', false);
                    $this->render('ajax', 'json');
                    return false;                        
                }
                
                //add it as income or expense
                if ($debt['Debt']['type'] == 'debt') {
                                        
                    //get the debts expense category
                    $this->Expense->ExpenseCategory->recursive = -1;
                    $expenseCategory = $this->Expense->ExpenseCategory->find('first', array('conditions' => array('name' => 'قرض')));
                    if (empty($expenseCategory)) {
                        $dataSource->rollback($this->Debt);
                        $this->set('response', false);
                        $this->render('ajax', 'json');
                        return false;
                    }

                    //gather data
                    $data['Transaction']['account_id'] = intval($this->params['form']['account_id']);
                    $data['Transaction']['amount'] = $amount;
                    $data['Transaction']['date'] = date('Y-m-d');
                    $data['Expense']['individual_id'] = $debt['Debt']['individual_id'];
                    $data['Expense']['description'] = 'تسویه بدهی ' . $debt['Debt']['name'];
                    $data['Expense']['expense_category_id'] = $expenseCategory['ExpenseCategory']['id'];

                    //save the expense
                    $this->Expense->inputConvertDate = false;
                    if (!$transactionId = $this->Expense->saveExpense($data)) {
                        $dataSource->rollback($this->Debt);
                        $this->set('response', $amount);
                        $this->render('ajax', 'json');
                        return false;
                    }
                    
                } elseif ($debt['Debt']['type'] == 'credit') {
                    
                    //get the debts income type
                    $this->Income->IncomeType->recursive = -1;
                    $incomeType = $this->Income->IncomeType->find('first', array('conditions' => array('name' => 'قرض')));
                    if (empty($incomeType)) {
                        $dataSource->rollback($this->Debt);
                        $this->set('response', false);
                        $this->render('ajax', 'json');
                        return false;
                    }
                    
                    //gather data
                    $data['Transaction']['account_id'] = intval($this->params['form']['account_id']);
                    $data['Transaction']['amount'] = $amount;
                    $data['Transaction']['date'] = date('Y-m-d');
                    $data['Income']['individual_id'] = $debt['Debt']['individual_id'];
                    $data['Income']['description'] = 'تسویه طلب ' . $debt['Debt']['name'];
                    $data['Income']['income_type_id'] = $incomeType['IncomeType']['id'];

                    //save the income
                    $this->Income->inputConvertDate = false;
                    if (!$transactionId = $this->Income->saveIncome($data)) {
                        $dataSource->rollback($this->Debt);
                        $this->set('response', false);
                        $this->render('ajax', 'json');
                        return false;
                    }                    
                }
            }
            
            //add settlement
            $debtSettlement['DebtSettlement']['amount'] = $amount;
            $debtSettlement['DebtSettlement']['debt_id'] = $debt['Debt']['id'];
            $debtSettlement['DebtSettlement']['transaction_id'] = $transactionId;
            $debtSettlement['DebtSettlement']['user_id'] = $this->Auth->user('id');
            $this->DebtSettlement->create();
            if ( !$this->DebtSettlement->save( $debtSettlement ) ) {
                $dataSource->rollback( $this->Debt );
                $this->set( 'response', false );
                $this->render( 'ajax', 'json' );
                return false;
            }
            
            $this->Reminder->deleteRegarding('debt',$id);
            $this->Session->setFlash('بدهی طلب مورد نظر تسویه شد.<br />یادآورهای این موضوع نیز حذف شده اند. در صورت تمایل به اضافه کردن یادآوری برای این موضوع از <a href="'.Router::url( array( 'controller'=>'reminders' , 'action'=>'view', 'debt'=>$id ) ).'">اینجا</a> اقدام کنید', 'default', array('class' => 'success'));
            
            $dataSource->commit($this->Debt);
            $this->set('response', true);
            $this->render('ajax', 'json');
            return true;
        }
        $this->redirect(array('action' => 'index'));
    }

    function export($limit=null) {
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion(8);
        $workbook->send('debts.xls');
        $worksheet = & $workbook->addWorksheet('expenses');
        $worksheet->setInputEncoding('utf-8');

        //get the data
        $this->Debt->recursive = 0;
        $this->Debt->outputConvertDate = true;
        $this->Debt->convertDateFormat = 'Y/m/d';
        $options = array();
        $options['fields'] = array('Debt.name', 'Debt.amount', 'Debt.settled', 'Debt.due_date', 'Debt.type', 'Individual.name', 'Debt.status', );
        $options['order'] = "Debt.due_date DESC";
        //apply the conditions
        if ( $this->Session->check('Debt.conditions') ) {
            $options['conditions'] = $this->Session->read('Debt.conditions');
        }
        if (!is_null($limit)) {
            $options['limit'] = $limit;
        }
        $debts = $this->Debt->find('all', $options);
        $data = array();
        $data[] = array('عنوان', 'مبلغ', 'تسویه شده', 'باقیمانده', 'تاریخ موعد', 'نوع', 'شخص' ,'وضعیت');
        $i = 1;
        foreach ($debts as $entry) {
            $data[$i][] = $entry['Debt']['name'];
            $data[$i][] = $entry['Debt']['amount'];
            $data[$i][] = $entry['Debt']['settled'];
            $data[$i][] = ($entry['Debt']['amount'] - $entry['Debt']['settled']);
            $data[$i][] = $entry['Debt']['due_date'];
            $data[$i][] = __($entry['Debt']['type'], true);
            $data[$i][] = $entry['Individual']['name'];
            $data[$i][] = __($entry['Debt']['status'], true);
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
    
    private function loadTags()
    {
        $this->set( 'tags' , $this->Tag->prepareList( $this->Tag->loadTags() ) );
    }
    
    private function bindTagModel($force=false, $model=null)
    {
        if(!$model) {
            $model = &$this->Debt;
        }
        $model->bindModel( array(
            'hasOne' => array(
                'DebtTag' => array(
                        'foreignKey' => false,
                        'conditions' => array( 'DebtTag.debt_id = Debt.id' )
                    )
            )
            ), false );
        if($force) {
            $model->Behaviors->attach( 'Containable' );
            $model->contain( 'DebtTag' );
        }
    }

}

?>