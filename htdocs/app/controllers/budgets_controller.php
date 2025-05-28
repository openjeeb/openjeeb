<?php

uses( 'sanitize' );

class BudgetsController extends AppController {

    private $budgets_dates = array();
    
    var $name = 'Budgets';
    var $components = array( 'Security' );
    var $uses = array( 'Budget' , 'ExpenseCategory', 'Transaction', 'Expense' );

    function beforeFilter() {
        parent::beforeFilter();
        $this->Security->requireAuth( 'index', 'edit' );
        $this->Security->blackHoleCallback = 'fail';
    }

    function index() {
        
        $this->set( 'title_for_layout', 'بودجه بندی' );

        //sanitize the data
        $san = new Sanitize();
        $this->data = $san->clean( $this->data );
        
        //reset the conditions
        $this->_setCombos();
        $this->_setBudgetDates();
        
        $pd = new PersianDate();
        list($pyear, $pmonth) = explode(':',$pd->pdate('Y:m'));
        $inchart = $chart = array();
        $maxchart = 7;
        
        
        $this->Budget->recursive = 1;
        $this->Budget->outputConvertDate = false;
        $this->paginate['order'] = 'Budget.id DESC';
        // filter datagrid
        if($this->budgets_dates){
            $def = isset($this->budgets_dates["{$pyear}_{$pmonth}"]) ? "{$pyear}_{$pmonth}" : key($this->budgets_dates);
            list($y,$m) = explode('_', (isset($this->data['Budget']['date']['ym']) ? $this->data['Budget']['date']['ym'] : $def).'_');
            $this->paginate['conditions'] = array('pyear'=>$y,'pmonth'=>$m);
            $pyear = $y;
            $pmonth = $m;
        }else{
            $this->paginate['conditions'] = array('pyear'=>$pyear,'pmonth'=>$pmonth);
        }
        
        $this->set( 'chartname' , 'بودجه بندی '. __('month_'.intval($pmonth),true) .' '.PersianLib::FA_($pyear) );
                
        $paginate = $this->paginate();
        $total_amount_used = 0;
        foreach($paginate as &$item) {
            $item['Budget']['amount_used'] = $this->_getTransactionSum( $item['Budget']['expense_category_id'], $item['Budget']['start_date'], $item['Budget']['end_date']);
            $total_amount_used += $item['Budget']['amount_used'];
            if($item['Budget']['pmonth']==$pmonth && $item['Budget']['pyear']==$pyear && count($inchart)<$maxchart) {
                $name = $item['ExpenseCategory']['name'].' در '.PersianLib::FA_($item['Budget']['pyear'].'/'.$item['Budget']['pmonth']);
                $inchart[] = $item['Budget']['id'];
                $chart[$name] = array(
                    'بودجه تعریف شده' => $item['Budget']['amount'],
                    'بودجه مصرفی' => $item['Budget']['amount_used']
                );
            }
        }
        $this->set( 'budgets', $paginate );
        
        //Totals
        $where = $this->Budget->getDataSource()->conditions($this->paginate['conditions']+array('Budget.user_id'=>$this->Auth->user( 'id' )), true, true, $this->Budget);
        $q = "SELECT sum(Budget.amount) as total_amount FROM `budgets` AS `Budget` "
            . "LEFT JOIN `users` AS `User` ON (`Budget`.`user_id` = `User`.`id`) "
            . "LEFT JOIN `expense_categories` AS `ExpenseCategory` ON (`Budget`.`expense_category_id` = `ExpenseCategory`.`id`) "
            . "$where";
        $total = $this->Budget->query($q);    
        if($total){
            $this->set('total_amount', $total[0][0]['total_amount'] );
            $this->set('total_amount_used', $total_amount_used);
            $this->set('total', $total[0][0]['total_amount'] - $total_amount_used);            
        }
        
        // chart
        if(count($inchart)<$maxchart) {
            $data = $this->Budget->find('all', array(
                'conditions' => array(
                    'Budget.pyear' => $pyear,
                    'Budget.pmonth' => $pmonth
                    //'Budget.pyear' => isset($y) ? $y : $pyear,
                    //'Budget.pmonth' => isset($m) ? $m : $pmonth

                ),
                'limit' => $maxchart-count($chart)
            ));
            foreach($data as &$item) {
                $item['Budget']['amount_used'] = $this->_getTransactionSum( $item['Budget']['expense_category_id'], $item['Budget']['start_date'], $item['Budget']['end_date']);
                $name = $item['ExpenseCategory']['name'].' در '.PersianLib::FA_($item['Budget']['pyear'].'/'.$item['Budget']['pmonth']);
                $chart[$name] = array(
                    'بودجه تعریف شده' => $item['Budget']['amount'],
                    'بودجه مصرفی' => $item['Budget']['amount_used']
                );
            }
        }
        
        $this->set( 'chart', $chart );
        
        //add
        if ( !empty( $this->data ) AND !isset( $this->data['Budget']['search'] ) ) {
            
            $this->data['Budget']['expense_category_id'] = intval( $this->data['Budget']['expense_category_id'] );
            
            //check for category
            if ( $this->data['Budget']['expense_category_id'] == 0 ) {
                $this->Session->setFlash( 'لطفا یک نوع هزینه را انتخاب کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            //remove price formating strings and 
            $this->data['Budget']['amount'] = floatval( str_replace( ',', '', $this->data['Budget']['amount'] ) );
            
            //check for amount
            if ( !$this->data['Budget']['amount'] ) {
                $this->Session->setFlash( 'لطفا مبلغ بودجه را وارد نمائید', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            if( $this->Budget->checkDuplicity($this->data['Budget']['expense_category_id'], $this->data['Budget']['date']['month'], $this->data['Budget']['date']['year']) ) {
                $this->Session->setFlash( 'شما یک بودجه بندی تعریف شده مشابه برای این بازه زمانی دارید', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            // processing date
            list( $this->data['Budget']['start_date'] , $this->data['Budget']['end_date'], $future ) = $this->_processDate( $this->data['Budget']['date']['month'] , $this->data['Budget']['date']['year'] );
            unset($this->data['Budget']['date']);
            if( $future ) {
                $this->Session->setFlash( 'بودجه بندی فقط برای ماه های آینده امکان پذیر است', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            $this->Budget->inputConvertDate = false;            
            //save data
            $this->Budget->create();
            if ( !$this->Budget->save( $this->data ) ) {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return true;
        }
    }

    function edit( $id = null ) {
        $this->set( 'title_for_layout', 'ویرایش بودجه بندی' );

        if ( !$id && empty( $this->data ) ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        
        $this->_setCombos();

        //get expense
        $this->Budget->recursive = 1;
        $this->Budget->outputConvertDate = false;
        $this->Budget->convertDateFormat = 'Y/m/d';
        $budget = $this->Budget->read( null, $id );
        
        $budget['Budget']['date'] = array(
            'year' => $budget['Budget']['pyear'],
            'month' => $budget['Budget']['pmonth']
        );
        
        $this->set( 'amount_used' , $this->_getTransactionSum( $budget['Budget']['expense_category_id'], $budget['Budget']['start_date'], $budget['Budget']['end_date']) );
        
        //set the data
        if ( empty( $this->data ) ) {
            $this->data = $budget;
        }

        //saving the posted data
        if ( !empty( $this->data ) && !empty( $_POST ) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean( $this->data );

            $this->data['Budget']['expense_category_id'] = intval( $this->data['Budget']['expense_category_id'] );
            
            //check for category
            if ( $this->data['Budget']['expense_category_id'] == 0 ) {
                $this->Session->setFlash( 'لطفا یک نوع هزینه را انتخاب کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }
            
            //remove price formating strings and 
            $this->data['Budget']['amount'] = floatval( str_replace( ',', '', $this->data['Budget']['amount'] ) );
            
            //check for amount
            if ( !$this->data['Budget']['amount'] ) {
                $this->Session->setFlash( 'لطفا مبلغ بودجه را وارد نمائید', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( Controller::referer() );
            }
            
            if( $this->Budget->checkDuplicity($this->data['Budget']['expense_category_id'], $this->data['Budget']['date']['month'], $this->data['Budget']['date']['year'], $id) ) {
                $this->Session->setFlash( 'شما یک بودجه بندی تعریف شده مشابه برای این بازه زمانی دارید', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( Controller::referer() );
            }
            
            // processing date
            list( $this->data['Budget']['start_date'] , $this->data['Budget']['end_date'], $future ) = $this->_processDate( $this->data['Budget']['date']['month'] , $this->data['Budget']['date']['year'] );
            unset($this->data['Budget']['date']);
            
            $this->Budget->inputConvertDate = false;            
            //save data
            $this->Budget->id = $id;
            if ( !$this->Budget->save( $this->data ) ) {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                $this->redirect( Controller::referer() );
            }

            $this->Session->setFlash( 'بودجه بندی مورد نظر شما با موفقیت ویرایش شد', 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return true;
        }
    }

    function delete( $id = null )
    {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        
        $this->Budget->recursive = -1;
        $item = $this->Budget->read( null, intval( $id ) );
        
        //check user
        if ( $item['Budget']['user_id'] != $this->Auth->user( 'id' ) ) {
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        
        //delete
        if ( !$this->Budget->delete( $id ) ) {
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        
        $this->Session->setFlash( 'داده مورد نظر با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
        $this->redirect( Controller::referer() );
        return true;
    }

    function export( $limit = null ) {
        //set memory limit
        ini_set( 'memory_limit', '64M' );
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion( 8 );
        $workbook->send( 'budgets.xls' );
        $worksheet = & $workbook->addWorksheet( 'budgets' );
        $worksheet->setInputEncoding( 'utf-8' );

        //get the data
        $this->Budget->recursive = 1;
        $this->Budget->outputConvertDate = false;
        
        $options = array( );
        //$options['fields'] = array( 'Transaction.amount', 'Transaction.date', 'Expense.description', 'ExpenseCategory.name', 'ExpenseSubCategory.name', 'Account.name', 'Individual.name' );
        $options['order'] = "Budget.start_date DESC";
        //apply the conditions
        if ( $this->Session->check( 'Budget.conditions' ) ) {
            $options['conditions'] = $this->Session->read( 'Budget.conditions' );
        }
        if ( !is_null( $limit ) ) {
            $options['limit'] = $limit;
        }
        $result = $this->Budget->find( 'all', $options );
        $data = array( );
        $data[] = array( 'گروه هزینه', 'بازه زمانی', 'مبلغ بودجه (ریال)', 'میزان مصرف شده (ریال)', 'درصد مصرفی', 'کسر بودجه (ریال)', 'بودجه مازاد (ریال)' );
        $i = 1;
        foreach ( $result as $entry ) {
            $data[$i][] = $entry['ExpenseCategory']['name'];
            $data[$i][] = $entry['Budget']['pyear'].'/'.$entry['Budget']['pmonth'];
            $data[$i][] = $entry['Budget']['amount'];
            $data[$i][] = $entry['Budget']['amount_used'] = 
                    $this->_getTransactionSum( $entry['Budget']['expense_category_id'], $entry['Budget']['start_date'], $entry['Budget']['end_date']);
            $data[$i][] = round(($entry['Budget']['amount_used']/$entry['Budget']['amount'])*100, 1).'%';
            $data[$i][] = ($entry['Budget']['amount']<$entry['Budget']['amount_used'])? $entry['Budget']['amount_used']-$entry['Budget']['amount'] : 0;
            $data[$i][] = ($entry['Budget']['amount']>$entry['Budget']['amount_used'])? $entry['Budget']['amount']-$entry['Budget']['amount_used'] : 0;
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
    
    private function _setCombos($year=null)
    {
        $this->set( 'expenseCategories' , $this->ExpenseCategory->fetchList() );
        
        $pd = new PersianDate();
        
        $months = array();
        for( $i=1; $i<=12; $i++ ) {
            $months[$i] = __('month_'.$i,true);
        }
        $this->set( 'months', $months );
        
        $nowyear = $pd->pdate('Y');
        $year = $year?: $nowyear;
        $years = array();
        for( $i=$year; $i<=$nowyear+4; $i++ ) {
            $years[$i] = $i;
        }
        $this->set( 'years', $years );
        
    }
    
    private function _processDate($month, $year)
    {
        $pd = new PersianDate();
        list($nowyear, $nowmonth) = explode(':',$pd->pdate('Y:m'));            
        
        $future =  ( $month < $nowmonth && $year <= $nowyear );
        $date = $year.'/'.$month;            
        
        $start_date = $pd->pdate_format_reverse($date.'/1');
        list($gyear, $gmonth, $gday) = explode('-',$start_date);
        
        $end_date = $pd->pdate_format_reverse($date.'/'. $pd->lastday($gmonth, $gday, $gyear));
        
        return array($start_date, $end_date, $future);
    }
    
    private function _getTransactionSum($category, $start_date, $end_date)
    {
        $this->Transaction->recursive = 0;
        $this->Transaction->outputConvertDate = true;
        $this->Transaction->convertDateFormat = 'Y/m/d';
        $this->Transaction->unbindModelAll();
        $this->Transaction->bindModel( array(
                'hasOne' => array(
                    'Expense' => array(
                        'foreignKey' => false,
                        'conditions' => 'Expense.id = Transaction.expense_id'
                     ),
                    'ExpenseCategory' => array(
                        'foreignKey' => false,
                        'conditions' => 'Expense.expense_category_id = ExpenseCategory.id'
                    )
                )
            ), false );
        
        $res = $this->Transaction->find( 'first', array(
            'fields' => 'SUM(Transaction.amount) as sum',
            'conditions' => array(
                'Transaction.type' => 'debt',
                'ExpenseCategory.id' => $category,
                'Transaction.date >=' => $start_date,
                'Transaction.date <=' => $end_date
            )
        ) );
        
        return intval(@$res[0]['sum']);
    }
    
    private function _setBudgetDates(){
        $conditions = array('user_id'=>$this->Auth->user( 'id' ));
        $this->Budget->recursive = 0;
        $this->Budget->outputConvertDate = false;

        $where = $this->Budget->getDataSource()->conditions($conditions, true, true, $this->Budget);
        //$sql = "SELECT distinct pyear , pmonth FROM demo.budgets $where";
        $sql = "SELECT distinct concat_ws('_',pyear,pmonth) as ym FROM budgets $where ORDER BY pyear DESC , pmonth DESC";
        $rows = $this->Budget->query($sql);  
        //$d = array('y'=>null,'m'=>null,$s=>null);
        //var_dump($rows);
        $this->budgets_dates = array();
        if(isset($rows["0"]) && is_array($rows["0"])){
            foreach ($rows as $k => $v) {
                //$d['y'][$v["budgets"]['pyear']] = $v["budgets"]['pyear'];
                //$d['m'][$v["budgets"]['pmonth']] = __('month_'.$v["budgets"]['pmonth'],true);
                $ym = $v["0"]['ym'];
                list($y,$m) = explode('_', $ym);
                $this->budgets_dates[$ym] = $y.' '.__('month_'.$m,true);
            }
        }
        //$bcYear = $d['y'];
        //$bcMonth = $d['m'];
        $this->set('cmbBC',$this->budgets_dates);
        
        $this->set('cmbBCSel',key($this->budgets_dates));
        //$this->set(compact('bcYear','bcMonth'));
    }
    
}

?>
