<?php

uses( 'sanitize' );

class ReportsController extends AppController {

    var $name = 'Reports';
    var $uses = array( 'User', 'Expense', 'ExpenseCategory', 'ExpenseSubCategory', 'Income', 'IncomeType', 'IncomeSubType', 'Check', 'Loan', 'Installment', 'Debt', 'Transaction', 'Account', 'Transfer', 'Individual', 'Tag', 'Budget' );
    
    function index() {
        $this->set( 'title_for_layout', 'گزارش‌ها کلی' );
        //get user
        $userId = $this->Auth->user( 'id' );
        //expense pie data
        $this->Expense->recursive = 0;
        $expensePie = $this->Expense->find( 'all', array(
            'fields' => array(
                'ExpenseCategory.name as k',
                'SUM(Transaction.amount) AS value'
            ),
            'group' => array( 'Expense.expense_category_id' ),
            ) );
        $this->set( 'expensePieData', $this->Chart->formatPieData( $expensePie, 'ExpenseCategory' ) );

        //income pie data
        $this->Income->recursive = 0;
        $incomePie = $this->Income->find( 'all', array(
            'fields' => array(
                'IncomeType.name AS k',
                'SUM(Transaction.amount) AS value'
            ),
            'conditions' => array(
                'Income.user_id' => $this->Auth->user( 'id' )
            ),
            'group' => array( 'Income.income_type_id' ),
            ) );
        $this->set( 'incomePieData', $this->Chart->formatPieData( $incomePie, 'IncomeType' ) );

        //expense line chart
        $this->Expense->recursive = -1;
        $this->Expense->Behaviors->attach( 'Containable' );
        $this->Expense->contain( 'Transaction' );
        $expenseLine = $this->Expense->find( 'all', array(
            'fields' => array(
                "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS node",
                'SUM(Transaction.amount) AS amount'
            ),
            'conditions' => array(
                'Expense.user_id' => $this->Auth->user( 'id' )
            ),
            'group' => array( 'node' ),
            ) );

        //income line chart
        $this->Income->recursive = -1;
        $this->Income->Behaviors->attach( 'Containable' );
        $this->Income->contain( 'Transaction' );
        $incomeLine = $this->Income->find( 'all', array(
            'fields' => array(
                "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS node",
                "CONCAT(Transaction.pyear,' ',PMONTHNAMEBYNUMBER(Transaction.pmonth)) AS month",
                'SUM(Transaction.amount) AS amount'
            ),
            'conditions' => array(
                'Income.user_id' => $this->Auth->user( 'id' )
            ),
            'group' => array( 'node' ),
            ) );

        //get monthly average income
        $averageIncome = array_sum( Set::extract( '/0/amount', $incomeLine ) ) / count( $incomeLine );
        $this->set( compact( 'averageIncome' ) );

        //get monthly average expense
        $averageExpense = array_sum( Set::extract( '/0/amount', $expenseLine ) ) / count( $expenseLine );
        $this->set( compact( 'averageExpense' ) );

        //get highest expense day of week
        $highestExpnseWeekDay = $this->Expense->query( "SELECT MAX(t.sum) AS max,t.dayname AS dayname FROM (SELECT SUM(Transaction.amount) AS sum, DAYNAME(Transaction.date) AS dayname FROM expenses AS Expense LEFT JOIN transactions AS Transaction ON(Expense.transaction_id=Transaction.id) WHERE Expense.user_id=" . $userId . " GROUP BY DAYOFWEEK(Transaction.date) ORDER BY sum DESC) AS t" );
        $this->set( 'highestExpnseWeekDay', $highestExpnseWeekDay[0]['t']['dayname'] );

        //get highest expense day of week
        $lowestExpnseWeekDay = $this->Expense->query( "SELECT MIN(t.sum) AS min,t.dayname AS dayname FROM (SELECT SUM(Transaction.amount) AS sum, DAYNAME(Transaction.date) AS dayname FROM expenses AS Expense LEFT JOIN transactions AS Transaction ON(Expense.transaction_id=Transaction.id) WHERE Expense.user_id=" . $userId . " GROUP BY DAYOFWEEK(Transaction.date) ORDER BY sum ASC) AS t" );
        $this->set( 'lowestExpnseWeekDay', $lowestExpnseWeekDay[0]['t']['dayname'] );

        //get highest expense month
        $highestExpnseMonth = $this->Expense->query( "SELECT MAX(t.sum) AS max,t.month AS month FROM (SELECT SUM(Transaction.amount) AS sum, CONCAT(PMONTHNAMEBYNUMBER(Transaction.pmonth),' ',Transaction.pyear ) AS month FROM expenses AS Expense LEFT JOIN transactions AS Transaction ON(Expense.transaction_id=Transaction.id) WHERE Expense.user_id=" . $userId . " GROUP BY month ORDER BY sum DESC) AS t" );
        $this->set( 'highestExpnseMonth', $highestExpnseMonth[0]['t']['month'] );

        //get highest income month
        $highestIncomeMonth = $this->Income->query( "SELECT MAX(t.sum) AS max,t.month AS month FROM (SELECT SUM(Transaction.amount) AS sum, CONCAT(PMONTHNAMEBYNUMBER(Transaction.pmonth),' ',Transaction.pyear) AS month FROM incomes AS Income LEFT JOIN transactions AS Transaction ON(Income.transaction_id=Transaction.id) WHERE Income.user_id=" . $userId . " GROUP BY month ORDER BY sum DESC) AS t" );
        $this->set( 'highestIncomeMonth', $highestIncomeMonth[0]['t']['month'] );

        //get lowest expense month
        $lowestExpnseMonth = $this->Expense->query( "SELECT MIN(t.sum) AS min,t.month AS month FROM (SELECT SUM(Transaction.amount) AS sum, CONCAT(PMONTHNAMEBYNUMBER(Transaction.pmonth),' ',Transaction.pyear) AS month FROM expenses AS Expense LEFT JOIN transactions AS Transaction ON(Expense.transaction_id=Transaction.id) WHERE Expense.user_id=" . $userId . " GROUP BY month ORDER BY sum ASC) AS t" );
        $this->set( 'lowestExpnseMonth', $lowestExpnseMonth[0]['t']['month'] );

        //get lowest income month
        $lowestIncomeMonth = $this->Income->query( "SELECT MIN(t.sum) AS min,t.month AS month FROM (SELECT SUM(Transaction.amount) AS sum, CONCAT(PMONTHNAMEBYNUMBER(Transaction.pmonth),' ',Transaction.pyear) AS month FROM incomes AS Income LEFT JOIN transactions AS Transaction ON(Income.transaction_id=Transaction.id) WHERE Income.user_id=" . $userId . " GROUP BY month ORDER BY sum ASC) AS t" );
        $this->set( 'lowestIncomeMonth', $lowestIncomeMonth[0]['t']['month'] );

        //get expense sum
        $this->Expense->recursive = -1;
        $this->Expense->Behaviors->attach( 'Containable' );
        $this->Expense->contain( 'Transaction' );
        $expenseSum = $this->Expense->find( 'first', array( 'fields' => 'SUM(Transaction.amount) AS sum' ) );
        $this->set( 'expenseSum', $expenseSum['0']['sum'] );

        //get income sum
        $this->Income->recursive = -1;
        $this->Income->Behaviors->attach( 'Containable' );
        $this->Income->contain( 'Transaction' );
        $incomeSum = $this->Income->find( 'first', array( 'fields' => 'SUM(Transaction.amount) AS sum' ) );
        $this->set( 'incomeSum', $incomeSum['0']['sum'] );
    }

    function expenses_new()
    {
        $this->expenses();
    }
    
    function expenses()
    {
        $this->set( 'title_for_layout', 'گزارش هزینه' );
        
        $excel = (!empty($this->params['pass'][0]) && $this->params['pass'][0]=='export');
        $excelsummary = (!empty($this->params['pass'][0]) && $this->params['pass'][0]=='exportsummary');
        
        $categories = array();        
        $categories['e1'] = "همه موارد بجز گروه‌های سیستمی";
        
        $this->ExpenseCategory->recursive = -1;
        $expenseList = $this->ExpenseCategory->find('all' , array(
            'order' => 'ExpenseCategory.sort ASC'
        ));
        foreach($expenseList as $item) {
            $categories['c'.$item['ExpenseCategory']['id']] = "گروه ".$item['ExpenseCategory']['name'];
        }
        foreach($this->ExpenseSubCategory->find('all' , array(
            'fields'=>'ExpenseSubCategory.name, ExpenseSubCategory.id, ExpenseCategory.name',
            'order' => 'ExpenseCategory.sort ASC'            
        )) as $item) {
            $categories['s'.$item['ExpenseSubCategory']['id']] = $item['ExpenseCategory']['name'].' > '.$item['ExpenseSubCategory']['name'];
        }
        $this->set( 'catlist' , $categories );
        
        //
        $conditions = array( );
        if ( isset( $this->data['Expense']['search'] ) ) {
            if ( !empty( $this->data['Expense']['expense_category_id'] ) ) {
                $conditions['Expense.expense_category_id'] = $this->data['Expense']['expense_category_id'];
            }
            if ( !empty( $this->data['Expense']['expense_sub_category_id'] ) ) {
                $conditions['Expense.expense_sub_category_id'] = $this->data['Expense']['expense_sub_category_id'];
            }
            if ( !empty( $this->data['Transaction']['account_id'] ) ) {
                $conditions['Transaction.account_id'] = $this->data['Transaction']['account_id'];
            }
            if ( !empty( $this->data['Expense']['individual_id'] ) ) {
                $conditions['Expense.individual_id'] = $this->data['Expense']['individual_id'];
            }
            if ( !empty( $this->data['Transaction']['start_date'] ) ) {
                $persianDate = new PersianDate();
                $conditions['Transaction.date >='] = $persianDate->pdate_format_reverse( $this->data['Transaction']['start_date'] );
            }
            if ( !empty( $this->data['Transaction']['end_date'] ) ) {
                $persianDate = new PersianDate();
                $conditions['Transaction.date <='] = $persianDate->pdate_format_reverse( $this->data['Transaction']['end_date'] );
            }
            
            $catlist = $subcatlist = array();
            if( empty($this->data['Expense']['category_id']) || (count($this->data['Expense']['category_id'])==1 && $this->data['Expense']['category_id'][0]=='0') ){
                //$catlist = $subcatlist = null;
            } else {
                foreach($this->data['Expense']['category_id'] as $k=>$v) {
                    $rest = substr($v, 1);
                    switch( substr($v, 0,1) ) {
                        case 'c':
                            $catlist[] = $rest;
                            break;
                        case 's':
                            $subcatlist[] = $rest;
                            break;
                        case 'e':
                            if ($rest == 1 ) {
                                foreach($expenseList as &$item) {
                                    if($item['ExpenseCategory']['delete'] != 'yes'){
                                        continue;                                        
                                    }
                                    $catlist[] = $item['ExpenseCategory']['id'];                                    
                                }
                            }
                            break;
                    }
                }
                if ( $catlist ) {
                    $conditions['Expense.expense_category_id'] = array_unique($catlist);
                }
                if ( $subcatlist ) {
                    if($catlist) {
                        $conditions['OR'] = array(
                            'Expense.expense_category_id' => $conditions['Expense.expense_category_id'],
                            'Expense.expense_sub_category_id' => array_unique($subcatlist)
                        );
                        unset($conditions['Expense.expense_category_id']);
                    }else{
                        $conditions['Expense.expense_sub_category_id'] = array_unique($subcatlist);
                    }
                }
                $this->set( 'report_catlist' , $this->data['Expense']['category_id'] );
            }
            
            //save it into session
            $this->Session->delete( 'ExpenseReport.conditions' );
            $this->Session->write( 'ExpenseReport.conditions', $conditions );
        }
        
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) && !$excel && !$excelsummary ) {
            $this->Session->delete( 'ExpenseReport.conditions' );
        }
        //apply the conditions
        if ( $this->Session->check( 'ExpenseReport.conditions' ) AND (!empty( $this->params['named'] ) || $excel || $excelsummary) ) {
            $conditions = $this->Session->read( 'ExpenseReport.conditions' );
        }
        //
        $expenseCategories = $this->Expense->ExpenseCategory->fetchList();
        $this->set( compact( 'expenseCategories' ) );
        //get the whole category subcategory data
        $this->Expense->ExpenseCategory->recursive = -1;
        $expenseCategories = $this->Expense->ExpenseCategory->find( 'all', array(
            'contain' => 'ExpenseSubCategory',
            'order' => 'ExpenseCategory.sort ASC'
            ) );
        $expenseCategoriesData = array( );
        $i = 0;
        foreach ( $expenseCategories as $entry ) {
            $expenseCategoriesData[$i]['id'] = $entry['ExpenseCategory']['id'];
            $expenseCategoriesData[$i]['name'] = $entry['ExpenseCategory']['name'];
            $expenseCategoriesData[$i]['subs'] = array( );
            foreach ( $entry['ExpenseSubCategory'] as $entry2 ) {
                $expenseCategoriesData[$i]['subs'][] = array(
                    'id' => $entry2['id'],
                    'name' => $entry2['name'],
                );
            }
            $i++;
        }
        $this->set( compact( 'expenseCategoriesData' ) );
        //get accounts
        list($accounts,$accountsbalance) = $this->Expense->Transaction->Account->listAccounts();
        $this->set( compact( 'accounts' , 'accountsbalance' ) );
        //get individuals
        $individuals = $this->Expense->Individual->fetchList();
        $this->set( compact( 'individuals' ) );
        //get user id
        $userId = $this->Auth->user( 'id' );
        //data table
        $this->Expense->recursive = 0;
        $this->Expense->Transaction->outputConvertDate = true;
        $this->Expense->Transaction->convertDateFormat = 'Y/m/d';
        $this->Expense->bindModel( array(
            'hasOne' => array(
                'Account' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Account.id = Transaction.account_id' )
                ),
            )
            ), false );
        
        if($excel){
            $expenses = $this->Expense->find( 'all' , array(
                'fields' => array( 'Expense.id', 'Expense.transaction_id', 'Expense.description', 'Expense.expense_category_id', 'Expense.expense_sub_category_id', 'Transaction.id', 'Transaction.amount', 'Transaction.date', 'Transaction.pyear', 'Transaction.pmonth', 'Transaction.pday', 'Transaction.account_id', 'ExpenseCategory.name', 'ExpenseSubCategory.name', 'Account.name', 'Individual.name' ),
                'order' => 'Transaction.date DESC, Expense.id DESC',
                'conditions' => $conditions
            ));
        }else{
            $this->paginate['fields'] = array( 'Expense.id', 'Expense.transaction_id', 'Expense.description', 'Expense.expense_category_id', 'Expense.expense_sub_category_id', 'Transaction.id', 'Transaction.amount', 'Transaction.date', 'Transaction.pyear', 'Transaction.pmonth', 'Transaction.pday', 'Transaction.account_id', 'ExpenseCategory.name', 'ExpenseSubCategory.name', 'Account.name', 'Individual.name' );
            $this->paginate['order'] = 'Transaction.date DESC, Expense.id DESC';
            $this->paginate['conditions'] = $conditions;
            $this->set( 'expenses', $this->paginate( 'Expense' ) );
        }
        //pie data
        $this->Expense->recursive = 0;
        
        if( @is_array($conditions['Expense.expense_category_id']) || @is_array($conditions['Expense.expense_sub_category_id']) ) {
            $pie = $this->Expense->find( 'all', array(
                'fields' => array(
                    'CONCAT(ExpenseCategory.id) as cid',
                    'CONCAT(ExpenseCategory.name) as cname',
                    'CONCAT(ExpenseSubCategory.id) as sid',
                    'CONCAT(ExpenseSubCategory.name) as k',
                    'SUM(Transaction.amount) AS value'
                ),
                'conditions' => $conditions,
                'group' => array( 'Expense.expense_category_id, Expense.expense_sub_category_id' ),
                'order' => empty($this->passedArgs['categorysort'])? 'ExpenseCategory.id' : 'value '.$this->passedArgs['categorysort']
                ) );
            
            $cat = array();
            $data = array();
            foreach($pie as $item) {
                $item = $item[0];
                if(empty($cat[$item['cid']])) {
                    $data[] = array( 'ExpenseSubCategory' => array( 'k' => 'گروه '.$item['cname'] ), array( 'value' => 0 ) );
                    $cat[$item['cid']] = &$data[count($data)-1][0]['value'];
                }
                $cat[$item['cid']] += $item['value'];
                if($item['sid']) {
                    $data[] = array( 'ExpenseSubCategory' => array( 'k' => $item['cname'].' >> '.$item['k'] ), array( 'value' => $item['value'] ) );
                }
            }            
            $this->set( 'pieData', $pieData = $this->Chart->formatPieData( $data, 'ExpenseSubCategory' ) );            
        } elseif ( !empty( $this->data['Expense']['expense_category_id'] ) ) {
            $pie = $this->Expense->find( 'all', array(
                'fields' => array(
                    'ExpenseSubCategory.name as k',
                    'SUM(Transaction.amount) AS value'
                ),
                'conditions' => $conditions,
                'group' => array( 'Expense.expense_sub_category_id' ),
                'order' => empty($this->passedArgs['categorysort'])? 'value ASC ' : 'value '.$this->passedArgs['categorysort']
                ) );
            $this->set( 'pieData', $pieData = $this->Chart->formatPieData( $pie, 'ExpenseSubCategory' ) );
        } else {
            $pie = $this->Expense->find( 'all', array(
                'fields' => array(
                    'ExpenseCategory.name as k',
                    'SUM(Transaction.amount) AS value'
                ),
                'conditions' => $conditions,
                'group' => array( 'Expense.expense_category_id' ),
                'order' => empty($this->passedArgs['categorysort'])? 'value DESC ' : 'value '.$this->passedArgs['categorysort']
                ) );
            $this->set( 'pieData', $pieData = $this->Chart->formatPieData( $pie, 'ExpenseCategory' ) );
        }
        
        //column chart
        $this->Expense->recursive = -1;
        $this->Expense->Behaviors->attach( 'Containable' );
        $this->Expense->contain( 'Transaction' );
        $column = $this->Expense->find( 'all', array(
            'fields' => array(
                "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k",
                'SUM(Transaction.amount) AS value'
            ),
            'conditions' => $conditions,
            'group' => array( 'k' ),
            'order' => array( 'Transaction.pyear', 'Transaction.pmonth' )
            ) );
        $this->set( 'columnData', Set::classicExtract( $column, '{n}.0' ) );
        //column data sum
        $this->set( 'columnDataSum', $columnDataSum = array_sum( Set::classicExtract( $column, '{n}.0.value' ) ) );        
        
        // export to excel if parameter is passed
        if($excel){
            $excel = array( array( 'نوع هزینه', 'مبلغ (ریال)', 'تاریخ', 'حساب', 'شخص', 'توضیحات') );
            $i=1;
            foreach($expenses as $d){
                $excel[] = array(
                    $d['ExpenseCategory']['name'].((!is_null($d['ExpenseSubCategory']['name']))? ' >> '.$d['ExpenseSubCategory']['name'] : ''),
                    number_format($d['Transaction']['amount']),
                    $d['Transaction']['date'],
                    $d['Account']['name'],
                    $d['Individual']['name'],
                    $d['Expense']['description']
                );
            }
            $this->export('Expense', $excel);
        }elseif($excelsummary){
            $excel = array( array( 'نوع هزینه', 'مجموع هزینه') );
            $i=1;
            foreach($pieData as $d){
                $excel[] = array(
                    $d['key'],
                    number_format($d['value'])
                );
            }
            $excel[] = array(
                'جمع',
                number_format($columnDataSum)
            );
            $this->export('ExpenseSummary', $excel);
        }
    }

    function incomes_new()
    {
        $this->incomes();
    }
    
    function incomes()
    {
        $this->set( 'title_for_layout', 'گزارش درآمد' );
        $excel = (!empty($this->params['pass'][0]) && $this->params['pass'][0]=='export');
        $excelsummary = (!empty($this->params['pass'][0]) && $this->params['pass'][0]=='exportsummary');
        
        $categories = array();
        $categories['e1'] = "همه موارد بجز گروه‌های سیستمی";
        $this->IncomeType->recursive = -1;
        $incomeList = $this->IncomeType->find('all' , array(
            'order' => 'IncomeType.sort ASC'
        ));
        foreach($incomeList as $item) {
            $categories['c'.$item['IncomeType']['id']] = "گروه ".$item['IncomeType']['name'];
        }
        foreach($this->IncomeSubType->find('all' , array(
            'fields'=>'IncomeType.name, IncomeSubType.id, IncomeSubType.name',
            'order' => 'IncomeType.sort ASC'
        )) as $item) {
            $categories['s'.$item['IncomeSubType']['id']] = $item['IncomeType']['name'].' > '.$item['IncomeSubType']['name'];
        }
        $this->set( 'catlist' , $categories );
        
        //
        $conditions = array( );
        if ( isset( $this->data['Income']['search'] ) ) {
            if ( !empty( $this->data['Income']['income_sub_type_id'] ) ) {
                //$conditions['Income.income_sub_type_id'] = $this->data['Income']['income_sub_type_id'];
                $this->data['Income']['category_id'][] = 's'.$this->data['Income']['income_sub_type_id'];
            } elseif ( !empty( $this->data['Income']['income_type_id'] ) ) {
                //$conditions['Income.income_type_id'] = $this->data['Income']['income_type_id'];
                $this->data['Income']['category_id'][] = 'c'.$this->data['Income']['income_type_id'];
            }
            
            if ( !empty( $this->data['Transaction']['account_id'] ) ) {
                $conditions['Transaction.account_id'] = $this->data['Transaction']['account_id'];
            }
            if ( !empty( $this->data['Income']['individual_id'] ) ) {
                $conditions['Income.individual_id'] = $this->data['Income']['individual_id'];
            }
            if ( !empty( $this->data['Transaction']['start_date'] ) ) {
                $persianDate = new PersianDate();
                $conditions['Transaction.date >='] = $persianDate->pdate_format_reverse( $this->data['Transaction']['start_date'] );
            }
            if ( !empty( $this->data['Transaction']['end_date'] ) ) {
                $persianDate = new PersianDate();
                $conditions['Transaction.date <='] = $persianDate->pdate_format_reverse( $this->data['Transaction']['end_date'] );
            }
            
            $catlist = $subcatlist = array();
            if( empty($this->data['Income']['category_id']) || (count($this->data['Income']['category_id'])==1 && $this->data['Income']['category_id'][0]=='0') ){
                //$catlist = $subcatlist = null;
            } else {
                foreach($this->data['Income']['category_id'] as $k=>$v) {
                    $rest = substr($v, 1);
                    switch( substr($v, 0,1) ) {
                        case 'c':
                            $catlist[] = $rest;
                            break;
                        case 's':
                            $subcatlist[] = $rest;
                            break;
                        case 'e':
                            if ($rest == 1 ) {
                                foreach($incomeList as &$item) {
                                    if($item['IncomeType']['delete'] != 'yes'){
                                        continue;                                        
                                    }
                                    $catlist[] = $item['IncomeType']['id'];                                    
                                }
                            }
                            break;
                    }
                }
                if ( $catlist ) {
                    $conditions['Income.income_type_id'] = array_unique( $catlist );
                }
                if ( $subcatlist ) {
                    if($catlist) {
                        $conditions['OR'] = array(
                            'Income.income_type_id' => $conditions['Income.income_type_id'],
                            'Income.income_sub_type_id' => array_unique( $subcatlist )
                        );
                        unset($conditions['Income.income_type_id']);
                    }else{
                        $conditions['Income.income_sub_type_id'] = array_unique( $subcatlist );
                    }
                }
                $this->set( 'report_catlist' , $this->data['Income']['category_id'] );
            }
            //save it into session
            $this->Session->delete( 'IncomeReport.conditions' );
            $this->Session->write( 'IncomeReport.conditions', $conditions );
        }

        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) && !$excel && !$excelsummary ) {
            $this->Session->delete( 'IncomeReport.conditions' );
        }
        //apply the conditions
        if ( $this->Session->check( 'IncomeReport.conditions' ) AND (!empty( $this->params['named'] ) || $excel || $excelsummary) ) {
            $conditions = $this->Session->read( 'IncomeReport.conditions' );
        }

        //data table
        $this->Income->recursive = 0;
        $this->Income->Transaction->outputConvertDate = true;
        $this->Income->Transaction->convertDateFormat = 'Y/m/d';
        $this->Income->bindModel( array(
            'hasOne' => array(
                'Account' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Account.id = Transaction.account_id' )
                ),
            )
            ), false );
        
        if($excel){
            $incomes = $this->Income->find( 'all' , array(
                'fields' => array( 'Income.id', 'Income.description', 'Income.income_type_id', 'IncomeType.name', 'Income.income_sub_type_id', 'IncomeSubType.name', 'Transaction.id', 'Transaction.amount', 'Transaction.date', 'Transaction.account_id', 'Account.name', 'Individual.name' ),
                'order' => 'Transaction.date DESC,Income.id DESC',
                'conditions' => $conditions
            ) );
            // export to excel if parameter is passed
            $excel = array( array( 'نوع درآمد', 'مبلغ (ریال)', 'تاریخ', 'حساب', 'شخص', 'توضیحات') );
            $i=1;
            foreach($incomes as $d){
                $excel[] = array(
                    $d['IncomeType']['name'].((!is_null($d['IncomeSubType']['name']))? ' >> '.$d['IncomeSubType']['name'] : ''),
                    number_format($d['Transaction']['amount']),
                    $d['Transaction']['date'],
                    $d['Account']['name'],
                    $d['Individual']['name'],
                    $d['Income']['description']
                );
            }
            $this->export('Income', $excel);
        } else {
            $this->paginate['fields'] = array( 'Income.id', 'Income.description', 'Income.income_type_id', 'IncomeType.name', 'Income.income_sub_type_id', 'IncomeSubType.name', 'Transaction.id', 'Transaction.amount', 'Transaction.date', 'Transaction.account_id', 'Account.name', 'Individual.name' );
            $this->paginate['order'] = 'Transaction.date DESC,Income.id DESC';
            $this->paginate['conditions'] = $conditions;
            $this->set( 'incomes', $this->paginate( 'Income' ) );
        }

        //pie
        $this->Income->recursive = 0;
        
        if( !empty($subcatlist) || !empty($catlist) ) {
            
            $pie = $this->Income->find( 'all', array(
                'fields' => array(
                    'CONCAT(IncomeType.id) as cid',
                    'CONCAT(IncomeType.name) as cname',
                    'CONCAT(IncomeSubType.id) as sid',
                    'CONCAT(IncomeSubType.name) as k',
                    'SUM(Transaction.amount) AS value'
                ),
                'conditions' => $conditions,
                'group' => array( 'Income.income_type_id,Income.income_sub_type_id' ),
                'order' => empty($this->passedArgs['categorysort'])? 'value DESC ' : 'value '.$this->passedArgs['categorysort']
                ) );
            
            $cat = array();
            $data = array();
            foreach($pie as $item) {
                $item = $item[0];
                if(empty($cat[$item['cid']])) {
                    $data[] = array( 'IncomeSubType' => array( 'k' => 'گروه '.$item['cname'] ), array( 'value' => 0 ) );
                    $cat[$item['cid']] = &$data[count($data)-1][0]['value'];
                }
                $cat[$item['cid']] += $item['value'];
                if($item['sid']) {
                    $data[] = array( 'IncomeSubType' => array( 'k' => $item['cname'].' >> '.$item['k'] ), array( 'value' => $item['value'] ) );
                }
            }
            
            $this->set( 'pieData', $pieData = $this->Chart->formatPieData( $data, 'IncomeSubType' ) );
            
        } elseif ( !empty( $this->data['Income']['income_type_id'] ) ) {
            $pie = $this->Income->find( 'all', array(
                'fields' => array(
                    'IncomeSubType.name AS k',
                    'SUM(Transaction.amount) AS value'
                ),
                'conditions' => $conditions,
                'group' => array( 'Income.income_type_id' ),
                ) );
            $this->set( 'pieData', $this->Chart->formatPieData( $pie, 'IncomeSubType' ) );
        } else {
            $pie = $this->Income->find( 'all', array(
                'fields' => array(
                    'IncomeType.name AS k',
                    'SUM(Transaction.amount) AS value'
                ),
                'conditions' => $conditions,
                'group' => array( 'Income.income_type_id' ),
                'order' => empty($this->passedArgs['categorysort'])? 'value DESC ' : 'value '.$this->passedArgs['categorysort']
                ) );
            $this->set( 'pieData', $pieData = $this->Chart->formatPieData( $pie, 'IncomeType' ) );
        }


        //get income types
        $incomeTypes = $this->Income->IncomeType->fetchList();
        $this->set( compact( 'incomeTypes' ) );

        //get the whole type subtype data
        $this->Income->IncomeType->recursive = -1;
        $incomeTypes = $this->Income->IncomeType->find( 'all', array(
            'contain' => array( 'IncomeSubType' => array( 'order' => 'name ASC' ) ),
            'order' => 'IncomeType.sort ASC'
            ) );
        $incomeTypesData = array( );
        $i = 0;
        foreach ( $incomeTypes as $entry ) {
            $incomeTypesData[$i]['id'] = $entry['IncomeType']['id'];
            $incomeTypesData[$i]['name'] = $entry['IncomeType']['name'];
            $incomeTypesData[$i]['subs'] = array( );
            foreach ( $entry['IncomeSubType'] as $entry2 ) {
                $incomeTypesData[$i]['subs'][] = array(
                    'id' => $entry2['id'],
                    'name' => $entry2['name'],
                );
            }
            $i++;
        }
        $this->set( compact( 'incomeTypesData' ) );

        //get accounts
        list($accounts,$accountsbalance) = $this->Income->Transaction->Account->listAccounts();
        $this->set( compact( 'accounts', 'accountsbalance' ) );

        //get individuals
        $individuals = $this->Income->Individual->fetchList();
        $this->set( compact( 'individuals' ) );

        //column chart
        $this->Income->recursive = -1;
        $this->Income->Behaviors->attach( 'Containable' );
        $this->Income->contain( 'Transaction' );
        $column = $this->Income->find( 'all', array(
            'fields' => array(
                "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k",
                'SUM(Transaction.amount) AS value'
            ),
            'conditions' => $conditions,
            'group' => array( 'k' ),
            'order' => array( 'Transaction.pyear', 'Transaction.pmonth' )
            ) );
        $this->set( 'columnData', Set::classicExtract( $column, '{n}.0' ) );

        //column data sum
        $this->set( 'columnDataSum', $columnDataSum = array_sum( Set::classicExtract( $column, '{n}.0.value' ) ) );
        
        if($excelsummary){
            $excel = array( array( 'نوع درآمد', 'مجموع درآمد') );
            $i=1;
            foreach($pieData as $d){
                $excel[] = array(
                    $d['key'],
                    number_format($d['value'])
                );
            }
            $excel[] = array(
                'جمع',
                number_format($columnDataSum)
            );
            $this->export('IncomeSummary', $excel);
        }
        
    }
    
    public function expense_comparison()
    {
        $this->set( 'title_for_layout', 'گزارش مقایسه هزینه ماهانه' );
        
        $subcats = $categorie_calculation = $categories = array();
        foreach($this->ExpenseCategory->find('list' , array(
                'fields'=>'name',
                'order' => 'ExpenseCategory.sort ASC'
            )) as $k=>$item) {
            $categories['c'.$k] = "گروه ".$item;
            $categorie_calculation['c'.$k] = 1;
            $categorie_calculation['e'.$k] = 1;
            $this->ExpenseSubCategory->recrsive = -1;
            $subs = $this->ExpenseSubCategory->find('list' , array(
                'fields'=>'ExpenseSubCategory.name',
                'conditions' => array( 'ExpenseSubCategory.expense_category_id' => $k )
            ));
            if($subs) {
                $categorie_calculation['e'.$k] = count($subs);
                $categories['e'.$k] = "زیرگروه های ".$item." (".PersianLib::FA_($categorie_calculation['e'.$k])." مورد)";
            }
            foreach($subs as $s=>$sub) {
                $subcats['s'.$s] = $item.' > '.$sub;                
                $categorie_calculation['s'.$s] = 1;
            }
        }
        
        
        $this->set( 'catlist' , $categories = array_merge($categories,$subcats) );
        $this->set( 'catcalc' , $categorie_calculation );
        
        //
        $conditions = array( );
        if ( isset( $this->data['Expense']['search'] ) ) {
            
            if ( !empty( $this->data['Transaction']['account_id'] ) ) {
                $conditions['Transaction.account_id'] = $this->data['Transaction']['account_id'];
            }
            if ( !empty( $this->data['Expense']['individual_id'] ) ) {
                $conditions['Expense.individual_id'] = $this->data['Expense']['individual_id'];
            }
            if ( !empty( $this->data['Transaction']['start_date'] ) ) {
                $persianDate = new PersianDate();
                $conditions['Transaction.date >='] = $persianDate->pdate_format_reverse( $this->data['Transaction']['start_date'] );
            }
            if ( !empty( $this->data['Transaction']['end_date'] ) ) {
                $persianDate = new PersianDate();
                $conditions['Transaction.date <='] = $persianDate->pdate_format_reverse( $this->data['Transaction']['end_date'] );
            }
            
            $catlist = $subcatlist = array();
            if( empty($this->data['Expense']['category_id']) || (count($this->data['Expense']['category_id'])==1 && $this->data['Expense']['category_id'][0]=='0') ){
                //$catlist = $subcatlist = null;
            } else {
                foreach($this->data['Expense']['category_id'] as $k=>$v) {
                    switch( substr($v, 0,1) ) {
                        case 'c':
                            $catlist[] = substr($v, 1);
                            break;
                        case 'e':
                            $l = $this->ExpenseSubCategory->find('all', array(
                                'conditions' => array( 'ExpenseCategory.id'=> substr($v, 1))
                            ));
                            foreach(Set::extract('/ExpenseSubCategory/id',$l) as $i) {
                                $subcatlist[] = $i;
                            }
                            $catlist[] = substr($v, 1);
                            break;
                        case 's':
                            $subcatlist[] = substr($v, 1);
                            break;
                    }
                }
                if ( $catlist ) {
                    $conditions['Expense.expense_category_id'] = $catlist;
                }
                if ( $subcatlist ) {
                    if($catlist) {
                        $conditions['OR'] = array(
                            'Expense.expense_category_id' => $conditions['Expense.expense_category_id'],
                            'Expense.expense_sub_category_id' => $subcatlist
                        );
                        unset($conditions['Expense.expense_category_id']);
                    }else{
                        $conditions['Expense.expense_sub_category_id'] = $subcatlist;
                    }
                }
                $this->set( 'report_catlist' , $this->data['Expense']['category_id'] );
            }
            
            //save it into session
            $this->Session->delete( 'ExpenseMonthlyReport.conditions' );
            $this->Session->write( 'ExpenseMonthlyReport.conditions', $conditions );
        }
        
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) ) {
            $this->Session->delete( 'ExpenseMonthlyReport.conditions' );
        }
        //apply the conditions
        if ( $this->Session->check( 'ExpenseMonthlyReport.conditions' ) AND (!empty( $this->params['named'] ) ) ) {
            $conditions = $this->Session->read( 'ExpenseMonthlyReport.conditions' );
        }
        
        //get user id
        $userId = $this->Auth->user( 'id' );
        
        
        $fields = $columnData = array();
        $selectfields = array( "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k" );
        
        if( empty($catlist) && empty($subcatlist) ) {
            $fields[] = array( 'value' => 'SUM(Transaction.amount)' , 'name' => 'همه هزینه ها' );
        }else{
            foreach($catlist as $k=>$c) {
                $fields[] = array( 'value' => "SUM( IF(Expense.expense_category_id={$c},Transaction.amount,0) )" , 'name' => $categories['c'.$c] );
            }
            foreach($subcatlist as $k=>$s) {
                $fields[] = array( 'value' => "SUM( IF(Expense.expense_sub_category_id={$s},Transaction.amount,0) )" , 'name' => $categories['s'.$s] );
            }
        }        
        
        foreach($fields as $n=>&$v) {
            // making select fields
            $selectfields[] = $v['value'].' as value_'.$n;
            $selectfields[] = '"'.addslashes($v['name']).'" as name_'.$n;
            // making legends
            $columnData[$n] = array(
                'name' => $v['name'],
                'data' => array() );
        }
        
        //column chart
        $this->Expense->recursive = -1;
        $this->Expense->Behaviors->attach( 'Containable' );
        $this->Expense->contain( 'Transaction' );
        $column = $this->Expense->find( 'all', array(
            'fields' => $selectfields,
            'conditions' => $conditions,
            'group' => array( 'k' ),
            'order' => array( 'Transaction.pyear', 'Transaction.pmonth' )
            ) );
        
        foreach($column as $cl) {
            foreach($fields as $n=>$v) {
                $columnData[$n]['data'][$cl[0]['k']] = $cl[0]['value_'.$n];
            }            
        }
        //debug($columnData);
        $this->set( 'columnData', $columnData );
        
    }
    
    public function income_comparison()
    {
        $this->set( 'title_for_layout', 'گزارش مقایسه درآمد ماهانه' );
        
        $subcats = $categorie_calculation = $categories = array();
        foreach($this->IncomeType->find('list' , array(
                'fields'=>'name',
                'order' => 'IncomeType.sort ASC'
            )) as $k=>$item) {
            $categories['c'.$k] = "گروه ".$item;
            $categorie_calculation['c'.$k] = 1;
            $categorie_calculation['e'.$k] = 1;
            $this->IncomeSubType->recrsive = -1;
            $subs = $this->IncomeSubType->find('list' , array(
                'fields'=>'IncomeSubType.name',
                'conditions' => array( 'IncomeSubType.income_type_id' => $k )
            ));
            if($subs) {
                $categorie_calculation['e'.$k] = count($subs);
                $categories['e'.$k] = "زیرگروه های ".$item." (".PersianLib::FA_($categorie_calculation['e'.$k])." مورد)";
            }
            foreach($subs as $s=>$sub) {
                $subcats['s'.$s] = $item.' > '.$sub;                
                $categorie_calculation['s'.$s] = 1;
            }
        }
        
        
        $this->set( 'catlist' , $categories = array_merge($categories,$subcats) );
        $this->set( 'catcalc' , $categorie_calculation );
        
        //
        $conditions = array( );
        if ( isset( $this->data['Income']['search'] ) ) {
            
            if ( !empty( $this->data['Transaction']['account_id'] ) ) {
                $conditions['Transaction.account_id'] = $this->data['Transaction']['account_id'];
            }
            if ( !empty( $this->data['Transaction']['start_date'] ) ) {
                $persianDate = new PersianDate();
                $conditions['Transaction.date >='] = $persianDate->pdate_format_reverse( $this->data['Transaction']['start_date'] );
            }
            if ( !empty( $this->data['Transaction']['end_date'] ) ) {
                $persianDate = new PersianDate();
                $conditions['Transaction.date <='] = $persianDate->pdate_format_reverse( $this->data['Transaction']['end_date'] );
            }
            
            $catlist = $subcatlist = array();
            if( empty($this->data['Income']['category_id']) || (count($this->data['Income']['category_id'])==1 && $this->data['Income']['category_id'][0]=='0') ){
                //$catlist = $subcatlist = null;
            } else {
                foreach($this->data['Income']['category_id'] as $k=>$v) {
                    switch( substr($v, 0,1) ) {
                        case 'c':
                            $catlist[] = substr($v, 1);
                            break;
                        case 'e':
                            $l = $this->IncomeSubType->find('all', array(
                                'conditions' => array( 'IncomeType.id'=> substr($v, 1))
                            ));
                            foreach(Set::extract('/IncomeSubType/id',$l) as $i) {
                                $subcatlist[] = $i;
                            }
                            $catlist[] = substr($v, 1);
                            break;
                        case 's':
                            $subcatlist[] = substr($v, 1);
                            break;
                    }
                }
                if ( $catlist ) {
                    $conditions['Income.income_type_id'] = $catlist;
                }
                if ( $subcatlist ) {
                    if($catlist) {
                        $conditions['OR'] = array(
                            'Income.income_type_id' => $conditions['Income.income_type_id'],
                            'Income.income_sub_type_id' => $subcatlist
                        );
                        unset($conditions['Income.income_type_id']);
                    }else{
                        $conditions['Income.income_sub_type_id'] = $subcatlist;
                    }
                }
                $this->set( 'report_catlist' , $this->data['Income']['category_id'] );
            }
            
            //save it into session
            $this->Session->delete( 'IncomeMonthlyReport.conditions' );
            $this->Session->write( 'IncomeMonthlyReport.conditions', $conditions );
        }
        
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) ) {
            $this->Session->delete( 'IncomeMonthlyReport.conditions' );
        }
        //apply the conditions
        if ( $this->Session->check( 'IncomeMonthlyReport.conditions' ) AND (!empty( $this->params['named'] ) ) ) {
            $conditions = $this->Session->read( 'IncomeMonthlyReport.conditions' );
        }
        
        //get user id
        $userId = $this->Auth->user( 'id' );
        
        
        $fields = $columnData = array();
        $selectfields = array( "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k" );
        
        if( empty($catlist) && empty($subcatlist) ) {
            $fields[] = array( 'value' => 'SUM(Transaction.amount)' , 'name' => 'همه درآمدها' );
        }else{
            foreach($catlist as $k=>$c) {
                $fields[] = array( 'value' => "SUM( IF(Income.income_type_id={$c},Transaction.amount,0) )" , 'name' => $categories['c'.$c] );
            }
            foreach($subcatlist as $k=>$s) {
                $fields[] = array( 'value' => "SUM( IF(Income.income_sub_type_id={$s},Transaction.amount,0) )" , 'name' => $categories['s'.$s] );
            }
        }        
        
        foreach($fields as $n=>&$v) {
            // making select fields
            $selectfields[] = $v['value'].' as value_'.$n;
            $selectfields[] = '"'.addslashes($v['name']).'" as name_'.$n;
            // making legends
            $columnData[$n] = array(
                'name' => $v['name'],
                'data' => array() );
        }
        
        //column chart
        $this->Income->recursive = -1;
        $this->Income->Behaviors->attach( 'Containable' );
        $this->Income->contain( 'Transaction' );
        $column = $this->Income->find( 'all', array(
            'fields' => $selectfields,
            'conditions' => $conditions,
            'group' => array( 'k' ),
            'order' => array( 'Transaction.pyear', 'Transaction.pmonth' )
            ) );
        
        foreach($column as $cl) {
            foreach($fields as $n=>$v) {
                $columnData[$n]['data'][$cl[0]['k']] = $cl[0]['value_'.$n];
            }            
        }
        //debug($columnData);
        $this->set( 'columnData', $columnData );
    }

    function monthly()
    {
        $excel = (!empty($this->params['pass'][0]) && $this->params['pass'][0]=='export');
        
        $conditions = array( );
        if ( isset( $this->passedArgs['year'] ) ) {
            $conditions['year'] = $this->passedArgs['year'];
            //save it into session
            $this->Session->delete( 'Monthly.conditions' );
            $this->Session->write( 'Monthly.conditions', $conditions );
        }
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) && !$excel ) {
            $this->Session->delete( 'Monthly.conditions' );
        }
        //apply the conditions
        if ( $this->Session->check( 'Monthly.conditions' ) AND (!empty( $this->params['named'] ) || $excel) ) {
            $conditions = $this->Session->read( 'Monthly.conditions' );
        }
        //
        $year = empty($conditions['year'])? 0 : $conditions['year'];
        
        $pd = new PersianDate();
        $years = array();
        for($i=1388; $i<=$pd->pdate("Y")+5; $i++){
            $years[$i] = $i;
        }
        $this->set( compact( 'years' ) );
        
        //drawed checks         
        $this->Check->recursive = -1;
        $drawedChecks = $this->Check->find( 'all', array(
            'fields' => array(
                "CONCAT(Check.pyear,'/',Check.pmonth) AS k",
                "Check.pyear AS year",
                "Check.pmonth AS month",
                'SUM(ABS(Check.amount)) AS value'
            ),
            'conditions' => array( 'Check.type' => 'drawed' ) + ($year? array('Check.pyear'=>$year) : array() ),
            'group' => array( 'k' ),
            'order' => array( 'Check.pyear', 'Check.pmonth' )
            ) );
        $this->set( 'drawedChecksColumnData', Set::classicExtract( $drawedChecks, '{n}.0' ) );
        $temp = array( );
        foreach ( $drawedChecks as $key => $value ) {
            $temp[$key]['k'] = $value[0]['k'];
            $temp[$key]['value'] = $value[0]['value'];
            $temp[$key]['year'] = $value['Check']['year'];
            $temp[$key]['month'] = $value['Check']['month'];
        }
        $drawedChecks = $temp;

        //recieved checks 
        $this->Check->recursive = -1;
        $receivedChecks = $this->Check->find( 'all', array(
            'fields' => array(
                "CONCAT(Check.pyear,'/',Check.pmonth) AS k",
                "Check.pyear AS year",
                "Check.pmonth AS month",
                'SUM(Check.amount) AS value'
            ),
            'conditions' => array( 'Check.type' => 'received' ) + ($year? array('Check.pyear'=>$year) : array() ),
            'group' => array( 'k' ),
            'order' => array( 'Check.pyear', 'Check.pmonth' )
            ) );
        $this->set( 'receivedChecksColumnData', Set::classicExtract( $receivedChecks, '{n}.0' ) );
        $temp = array( );
        foreach ( $receivedChecks as $key => $value ) {
            $temp[$key]['k'] = $value[0]['k'];
            $temp[$key]['value'] = $value[0]['value'];
            $temp[$key]['year'] = $value['Check']['year'];
            $temp[$key]['month'] = $value['Check']['month'];
        }
        $receivedChecks = $temp;

        //debts chart
        $this->Debt->recursive = -1;
        $debts = $this->Debt->find( 'all', array(
            'fields' => array(
                "CONCAT(Debt.pyear,'/',Debt.pmonth) AS k",
                "Debt.pyear AS year",
                "Debt.pmonth AS month",
                'SUM(ABS(Debt.amount)) AS value'
            ),
            'conditions' => array( 'Debt.type' => 'debt' ) + ($year? array('Debt.pyear'=>$year) : array() ),
            'group' => array( 'k' ),
            'order' => array( 'Debt.pyear', 'Debt.pmonth' )
            ) );
        $this->set( 'debtsColumnData', Set::classicExtract( $debts, '{n}.0' ) );
        $temp = array( );
        foreach ( $debts as $key => $value ) {
            $temp[$key]['k'] = $value[0]['k'];
            $temp[$key]['value'] = $value[0]['value'];
            $temp[$key]['year'] = $value['Debt']['year'];
            $temp[$key]['month'] = $value['Debt']['month'];
        }
        $debts = $temp;

        //credits chart
        $this->Debt->recursive = -1;
        $credits = $this->Debt->find( 'all', array(
            'fields' => array(
                "CONCAT(Debt.pyear,'/',Debt.pmonth) AS k",
                "Debt.pyear AS year",
                "Debt.pmonth AS month",
                'SUM(Debt.amount) AS value'
            ),
            'conditions' => array( 'Debt.type' => 'credit' ) + ($year? array('Debt.pyear'=>$year) : array() ),
            'group' => array( 'k' ),
            'order' => array( 'Debt.pyear', 'Debt.pmonth' )
            ) );
        $this->set( 'creditsColumnData', Set::classicExtract( $credits, '{n}.0' ) );
        $temp = array( );
        foreach ( $credits as $key => $value ) {
            $temp[$key]['k'] = $value[0]['k'];
            $temp[$key]['value'] = $value[0]['value'];
            $temp[$key]['year'] = $value['Debt']['year'];
            $temp[$key]['month'] = $value['Debt']['month'];
        }
        $credits = $temp;
        
        //expense chart
        $this->Expense->recursive = -1;
        $this->Expense->Behaviors->attach( 'Containable' );
        $this->Expense->contain( 'Transaction' );
        $expenses = $this->Expense->find( 'all', array(
            'fields' => array(
                "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k",
                "Transaction.pyear AS year",
                "Transaction.pmonth AS month",
                'SUM(Transaction.amount) AS value'
            ),
            'group' => array( 'k' ),
            'order' => array( 'Transaction.pyear', 'Transaction.pmonth' )
            ) + ($year? array('conditions'=>array('Transaction.pyear'=>$year)) : array() ) );
        $this->set( 'expensesColumnData', Set::classicExtract( $expenses, '{n}.0' ) );
        $temp = array( );
        foreach ( $expenses as $key => $value ) {
            $temp[$key]['k'] = $value[0]['k'];
            $temp[$key]['value'] = $value[0]['value'];
            $temp[$key]['year'] = $value['Transaction']['year'];
            $temp[$key]['month'] = $value['Transaction']['month'];
        }
        $expenses = $temp;

        //incomes chart
        $this->Income->recursive = -1;
        $this->Income->Behaviors->attach( 'Containable' );
        $this->Income->contain( 'Transaction' );
        $incomes = $this->Income->find( 'all', array(
            'fields' => array(
                "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k",
                "Transaction.pyear AS year",
                "Transaction.pmonth AS month",
                'SUM(Transaction.amount) AS value'
            ),
            'group' => array( 'k' ),
            'order' => array( 'Transaction.pyear', 'Transaction.pmonth' )
            ) + ($year? array('conditions'=>array('Transaction.pyear'=>$year)) : array() ) );
        $this->set( 'incomesColumnData', Set::classicExtract( $incomes, '{n}.0' ) );
        $temp = array( );
        foreach ( $incomes as $key => $value ) {
            $temp[$key]['k'] = $value[0]['k'];
            $temp[$key]['value'] = $value[0]['value'];
            $temp[$key]['year'] = $value['Transaction']['year'];
            $temp[$key]['month'] = $value['Transaction']['month'];
        }
        $incomes = $temp;

        $temp = array( );
        foreach ( $incomes as $value ) {
            $key = $value['k'];
            $temp[$key] = array('k'=>$value['k'],'value'=>$value['value'],'year'=>$value['year'],'month'=>$value['month']);
        }
        foreach ( $expenses as $value ) {
            $key = $value['k'];
            if(isset($temp[$key])){
                $temp[$key]['value'] -= $value['value'];
            }else{
                $temp[$key] = array('k'=>$value['k'],'value'=>$value['value']*-1,'year'=>$value['year'],'month'=>$value['month']);
            }
        }
        $outcomes = $temp;
        //var_dump($outcomes,$expenses,$incomes);

        
        //installments chart
        $this->Installment->recursive = -1;
        /*$installments = $this->Installment->find( 'all', array(
            'fields' => array(
                "CONCAT(PYEAR(Installment.due_date ),'/',PMONTH(Installment.due_date )) AS k",
                "PYEAR(Installment.due_date ) AS year",
                "PMONTH(Installment.due_date ) AS month",
                'SUM(Installment.amount) AS value'
            ),
            'group' => array( 'k' ),
            'order' => array( 'PYEAR(Installment.due_date )', 'PMONTH(Installment.due_date )' )
            ) + ($year? array('conditions'=>array('Installment.pyear'=>$year)) : array() ) );*/
        $installments = $this->Installment->find( 'all', array(
            'fields' => array(
                "CONCAT(Installment.pyear,'/',Installment.pmonth) AS k",
                "CONCAT(Installment.pyear) AS year",
                "CONCAT(Installment.pmonth) AS month",
                'SUM(Installment.amount) AS value'
            ),
            'group' => array( 'k' ),
            'order' => array( 'Installment.pyear', 'Installment.pmonth' )
            ) + ($year? array('conditions'=>array('Installment.pyear'=>$year)) : array() ) );
        $this->set( 'installmentsColumnData', Set::classicExtract( $installments, '{n}.0' ) );
        $installments = Set::classicExtract( $installments, '{n}.0' );

        //get years list
        $years = array( );
        $years = array_merge( $years, array_unique( Set::classicExtract( $drawedChecks, '{n}.year' ) ) );
        $years = array_merge( $years, array_unique( Set::classicExtract( $receivedChecks, '{n}.year' ) ) );
        $years = array_merge( $years, array_unique( Set::classicExtract( $debts, '{n}.year' ) ) );
        $years = array_merge( $years, array_unique( Set::classicExtract( $credits, '{n}.year' ) ) );
        $years = array_merge( $years, array_unique( Set::classicExtract( $expenses, '{n}.year' ) ) );
        $years = array_merge( $years, array_unique( Set::classicExtract( $incomes, '{n}.year' ) ) );
        $years = array_merge( $years, array_unique( Set::classicExtract( $installments, '{n}.year' ) ) );
        $years = array_unique( $years );
        sort( $years );
        
        $data = array( );
        foreach ( $years as $year ) {
            for ( $i = 1; $i <= 12; $i++ ) {
                $data[$year . '/' . $i]['year'] = $year;
                $data[$year . '/' . $i]['month'] = $i;
                //drawed checks
                $data[$year . '/' . $i]['drawed_check'] = 0;
                foreach ( $drawedChecks as $drawedCheck ) {
                    if ( $drawedCheck['year'] == $year AND $drawedCheck['month'] == $i ) {
                        $data[$year . '/' . $i]['drawed_check'] = $drawedCheck['value'];
                    }
                }
                //recieved checks
                $data[$year . '/' . $i]['received_check'] = 0;
                foreach ( $receivedChecks as $receivedCheck ) {
                    if ( $receivedCheck['year'] == $year AND $receivedCheck['month'] == $i ) {
                        $data[$year . '/' . $i]['received_check'] = $receivedCheck['value'];
                    }
                }
                //debts
                $data[$year . '/' . $i]['debt'] = 0;
                foreach ( $debts as $debt ) {
                    if ( $debt['year'] == $year AND $debt['month'] == $i ) {
                        $data[$year . '/' . $i]['debt'] = $debt['value'];
                    }
                }
                //credits
                $data[$year . '/' . $i]['credit'] = 0;
                foreach ( $credits as $credit ) {
                    if ( $credit['year'] == $year AND $credit['month'] == $i ) {
                        $data[$year . '/' . $i]['credit'] = $credit['value'];
                    }
                }
                //expenses
                $data[$year . '/' . $i]['expense'] = 0;
                foreach ( $expenses as $expense ) {
                    if ( $expense['year'] == $year AND $expense['month'] == $i ) {
                        $data[$year . '/' . $i]['expense'] = $expense['value'];
                    }
                }
                //incomes
                $data[$year . '/' . $i]['income'] = 0;
                foreach ( $incomes as $income ) {
                    if ( $income['year'] == $year AND $income['month'] == $i ) {
                        $data[$year . '/' . $i]['income'] = $income['value'];
                    }
                }
                //outcome
                $data[$year . '/' . $i]['outcome'] = 0;
                foreach ( $outcomes as $outcome ) {
                    if ( $outcome['year'] == $year AND $outcome['month'] == $i ) {
                        $data[$year . '/' . $i]['outcome'] = $outcome['value'];
                    }
                }
                //installments
                $data[$year . '/' . $i]['installment'] = 0;
                foreach ( $installments as $installment ) {
                    if ( $installment['year'] == $year AND $installment['month'] == $i ) {
                        $data[$year . '/' . $i]['installment'] = $installment['value'];
                    }
                }
            }
        }
        $this->set( compact( 'data' ) );
        
        // export to excel if parameter is passed
        if($excel){
            $excel = array( array( 'ماه', 'هزینه', 'درآمد', 'قسط', 'طلب', 'بدهی', 'دریافتی', 'چک صادره') );
            $i=1;
            foreach($data as $d){
                $excel[] = array(
                    __('month_'.$d['month'],true).' '.$d['year'],
                    number_format($d['expense']),
                    number_format($d['income']),
                    number_format($d['installment']),
                    number_format($d['credit']),
                    number_format($d['debt']),
                    number_format($d['received_check']),
                    number_format($d['drawed_check'])
                );
            }
            $this->export('Monthly', $excel);
        }
    }

    /**
     * Displays the summery report
     */
    function dashboard() {
        $this->set( 'title_for_layout', 'پیشخوان' );
        //get user
        $userId = $this->Auth->user( 'id' );
        //expense pie data
        $this->Expense->recursive = 0;
        $expensePie = $this->Expense->find( 'all', array(
            'fields' => array(
                'ExpenseCategory.name as k',
                'SUM(Transaction.amount) AS value'
            ),
            'conditions' => array(
                'Expense.user_id' => $this->Auth->user( 'id' ),
                'Transaction.pmonth' => $this->Expense->pDate( date( 'Y-m-d' ), 'n' ),
                'Transaction.pyear' => $this->Expense->pDate( date( 'Y-m-d' ), 'Y' ),
            ),
            'group' => array( 'Expense.expense_category_id' ),
            ) );
        $this->set( 'expensePieData', $this->Chart->formatPieData( $expensePie, 'ExpenseCategory' ) );

        //income pie data
        $this->Income->recursive = 0;
        $incomePie = $this->Income->find( 'all', array(
            'fields' => array(
                'IncomeType.name AS k',
                'SUM(Transaction.amount) AS value'
            ),
            'conditions' => array(
                'Income.user_id' => $this->Auth->user( 'id' ),
                'Transaction.pmonth' => $this->Income->pDate( date( 'Y-m-d' ), 'n' ),
                'Transaction.pyear' => $this->Income->pDate( date( 'Y-m-d' ), 'Y' ),
            ),
            'group' => array( 'Income.income_type_id' ),
            ) );
        $this->set( 'incomePieData', $this->Chart->formatPieData( $incomePie, 'IncomeType' ) );

        //expense line chart
//        $this->Expense->recursive = -1;
//        $this->Expense->Behaviors->attach('Containable');
//        $this->Expense->contain('Transaction');
//        $expenseLine=$this->Expense->find('all',array(
//            'fields'=>array(
//                            "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS node",
//                            'SUM(Transaction.amount) AS amount'
//                        ),
//            'conditions'=>array(
//                'Expense.user_id'=>$this->Auth->user('id')
//            ),
//            'group'=>array('node'),
//        ));
//        
//        //income line chart
//        $this->Income->recursive = -1;
//        $this->Income->Behaviors->attach('Containable');
//        $this->Income->contain('Transaction');
//        $incomeLine=$this->Income->find('all',array(
//            'fields'=>array(
//                            "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS node",
//                            "CONCAT(Transaction.pyear,' ',PMONTHNAMEBYNUMBER(Transaction.pmonth)) AS month",
//                            'SUM(Transaction.amount) AS amount'
//                        ),
//            'conditions'=>array(
//                'Income.user_id'=>$this->Auth->user('id')
//            ),
//            'group'=>array('node'),
//        ));
//        
//        //format line categories
//        $lineCategories=array();
//        foreach ($expenseLine as $entry) {
//            if(!in_array($entry[0]['node'], $lineCategories)){
//                $lineCategories[]=$entry[0]['node'];
//            }
//        }
//        foreach ($incomeLine as $entry) {
//            if (!in_array($entry[0]['node'], $lineCategories)) {
//                $lineCategories[] = $entry[0]['node'];              
//            }
//        }
//        sort($lineCategories);
//        
//        //format expense line data
//        $expenseLineData=array();
//        foreach ($lineCategories as $lineCategory) {
//            $value=0;
//            foreach ($expenseLine as $key=>$entry){
//                if($entry[0]['node']==$lineCategory){
//                    $value=$entry[0]['amount'];
//                }
//            }
//            $expenseLineData[]=$value;
//        }
//        
//        //format income line data
//        $incomeLineData=array();
//        foreach ($lineCategories as $lineCategory) {
//            $value=0;
//            foreach ($incomeLine as $key=>$entry){
//                if($entry[0]['node']==$lineCategory){
//                    $value=$entry[0]['amount'];
//                }
//            }
//            $incomeLineData[]=$value;
//        }
//        $this->set(compact('lineCategories','expenseLineData','incomeLineData'));
        //get currents month expense
        $this->Expense->recursive = -1;
        $this->Expense->Behaviors->attach( 'Containable' );
        $this->Expense->contain( 'Transaction' );
        $monthExpense = $this->Expense->find( 'first', array(
            'fields' => 'SUM(Transaction.amount) AS sum',
            'conditions' => array(
                'Transaction.pmonth' => $this->Expense->pDate( date( 'Y-m-d' ), 'n' ),
                'Transaction.pyear' => $this->Expense->pDate( date( 'Y-m-d' ), 'Y' ),
            ),
            ) );
        $this->set( 'monthExpense', $monthExpense['0']['sum'] );

        //get currents month income
        $this->Income->recursive = -1;
        $this->Income->Behaviors->attach( 'Containable' );
        $this->Income->contain( 'Transaction' );
        $monthIncome = $this->Income->find( 'first', array(
            'fields' => 'SUM(Transaction.amount) AS sum',
            'conditions' => array(
                'Transaction.pmonth' => $this->Income->pDate( date( 'Y-m-d' ), 'n' ),
                'Transaction.pyear' => $this->Income->pDate( date( 'Y-m-d' ), 'Y' ),
            ),
            ) );
        $this->set( 'monthIncome', $monthIncome['0']['sum'] );

        //get currents month drawed check
        $monthDrawedCheck = $this->Check->find( 'first', array(
            'fields' => 'SUM(Check.amount) AS sum',
            'conditions' => array(
                'Check.type' => 'drawed',
                'Check.pmonth' => $this->Check->pDate( date( 'Y-m-d' ), 'n' ),
                'Check.pyear' => $this->Debt->pDate( date( 'Y-m-d' ), 'Y' ),
            ),
            ) );
        $this->set( 'monthDrawedCheck', $monthDrawedCheck['0']['sum'] );

        //get currents month received check
        $monthReceivedCheck = $this->Check->find( 'first', array(
            'fields' => 'SUM(Check.amount) AS sum',
            'conditions' => array(
                'Check.type' => 'received',
                'Check.pmonth' => $this->Check->pDate( date( 'Y-m-d' ), 'n' ),
                'Check.pyear' => $this->Debt->pDate( date( 'Y-m-d' ), 'Y' ),
            ),
            ) );
        $this->set( 'monthReceivedCheck', $monthReceivedCheck['0']['sum'] );

        //get currents month debts
        $monthDebts = $this->Debt->find( 'first', array(
            'fields' => 'SUM(Debt.amount) AS sum',
            'conditions' => array(
                'Debt.type' => 'debt',
                'Debt.pmonth' => $this->Debt->pDate( date( 'Y-m-d' ), 'n' ),
                'Debt.pyear' => $this->Debt->pDate( date( 'Y-m-d' ), 'Y' ),
            ),
            ) );
        $this->set( 'monthDebts', $monthDebts['0']['sum'] );

        //get currents month credits
        $monthCredits = $this->Debt->find( 'first', array(
            'fields' => 'SUM(Debt.amount) AS sum',
            'conditions' => array(
                'Debt.type' => 'credit',
                'Debt.pmonth' => $this->Debt->pDate( date( 'Y-m-d' ), 'n' ),
                'Debt.pyear' => $this->Debt->pDate( date( 'Y-m-d' ), 'Y' ),
            ),
            ) );
        $this->set( 'monthCredits', $monthCredits['0']['sum'] );

        //get highest expense day of week for this month
        //$highestExpenseWeekDay=$this->Expense->query("SELECT MAX(t.sum) AS max,t.dayname AS dayname FROM (SELECT SUM(Transaction.amount) AS sum, DAYNAME(Transaction.date) AS dayname FROM expenses AS Expense LEFT JOIN transactions AS Transaction ON(Expense.transaction_id=Transaction.id) WHERE Expense.user_id = ".$this->Auth->user('id')." AND Transaction.pmonth = ". $this->Expense->pDate(date('Y-m-d'),'n') ." GROUP BY DAYOFWEEK(Transaction.date) ORDER BY sum DESC) AS t");
        //$this->set('highestExpenseWeekDay',$highestExpenseWeekDay[0]['t']['dayname']);
        //get lowest expense day of week for this month
        //$lowestExpnseWeekDay=$this->Expense->query("SELECT MIN(t.sum) AS min,t.dayname AS dayname FROM (SELECT SUM(Transaction.amount) AS sum, DAYNAME(Transaction.date) AS dayname FROM expenses AS Expense WHERE Expense.user_id = ".$this->Auth->user('id')." AND Transaction.pmonth = ". $this->Expense->pDate(date('Y-m-d'),'n') ." GROUP BY DAYOFWEEK(Transaction.date) ORDER BY sum ASC) AS t");
        //$this->set('lowestExpenseWeekDay',$lowestExpnseWeekDay[0]['t']['dayname']);        
        //get the list of user accounts
        list($accounts,$accountsbalance) = $this->Expense->Transaction->Account->listAccounts();
        $this->set( compact( 'accounts' , 'accountsbalance' ) );

        //get total accounts balance
        $this->Expense->Transaction->Account->recursive = -1;
        $totalBalance = $this->Expense->Transaction->Account->find( 'first', array( 'fields' => 'SUM(Account.balance) AS sum' ) );
        $this->set( 'totalBalance', $totalBalance['0']['sum'] );
    }

    function weekAlerts() {
        $this->layout = 'ajax';
        //get current week's due checks
        $this->Check->convertDateFormat = 'l j F Y';
        $this->set( 'weekChecks', $this->Check->getChecks( date( 'Y-m-d', strtotime( 'last saturday' ) ), date( 'Y-m-d', strtotime( 'next friday' ) ) ) );
        //get current week's due installments
        $this->Installment->convertDateFormat = 'l j F Y';
        $this->set( 'weekInstallments', $this->Installment->getInstallments( date( 'Y-m-d', strtotime( 'last saturday' ) ), date( 'Y-m-d', strtotime( 'next friday' ) ) ) );
    }

    function monthAlerts() {
        $this->disableCache();
        $this->layout = 'ajax';

        $currentYear = $this->Check->pDate( date( 'Y/m/d' ), 'Y' );
        $currentMonth = $this->Check->pDate( date( 'Y/m/d' ), 'm' );
        $start = $this->Check->pDateReverse( '1380/01/01' );
        $next15 = date( 'Y-m-d', strtotime( "+15 days" ) );

        $this->Check->outputConvertDate = false;
        $monthChecks = $this->Check->getChecks( $start, $next15 );

        //get current month's due installments
        $this->Installment->outputConvertDate = false;
        $monthInstallments = $this->Installment->getInstallments( $start, $next15 );

        //get current month's due debts
        $this->Debt->outputConvertDate = false;
        $monthDebts = $this->Debt->getDebts( $start, $next15 );

        //sort by date
        $data = array( );
        $i = 0;
        foreach ( $monthChecks as $entry ) {
            $data[$i] = $entry['Check'];
            $data[$i]['time'] = strtotime( $entry['Check']['due_date'] );
            $data[$i]['days'] = date_diff( date_create( date( 'Y-m-d' ) ), date_create( $entry['Check']['due_date'] ) );
            $data[$i]['description'] = '';
            if ( $entry['Check']['individual_id'] ) {
                if ( $entry['Check']['type'] == 'drawed' ) {
                    $data[$i]['description'].='به ' . $entry['Individual']['name'];
                } else {
                    $data[$i]['description'].='از ' . $entry['Individual']['name'];
                }
            }
            if ( !empty( $entry['Check']['serial'] ) ) {
                $data[$i]['description'].=' شماره سریال ' . $entry['Check']['serial'];
            }
            $data[$i]['entry'] = 'check';
            $i++;
        }
        foreach ( $monthInstallments as $entry ) {
            $data[$i] = $entry['Installment'];
            $data[$i]['time'] = strtotime( $entry['Installment']['due_date'] );
            $data[$i]['days'] = date_diff( date_create( date( 'Y-m-d' ) ), date_create( $entry['Installment']['due_date'] ) );
            $data[$i]['loan_name'] = $entry['Loan']['name'];
            $data[$i]['entry'] = 'installment';
            $i++;
        }
        foreach ( $monthDebts as $entry ) {
            $data[$i] = $entry['Debt'];
            $data[$i]['time'] = strtotime( $entry['Debt']['due_date'] );
            $data[$i]['days'] = date_diff( date_create( date( 'Y-m-d' ) ), date_create( $entry['Debt']['due_date'] ) );
            $data[$i]['description'] = '';
            if ( $entry['Debt']['individual_id'] ) {
                if ( $entry['Debt']['type'] == 'debt' ) {
                    $data[$i]['description'].='به ' . $entry['Individual']['name'];
                } else {
                    $data[$i]['description'].='از ' . $entry['Individual']['name'];
                }
            }
            $data[$i]['entry'] = 'debt';
            $i++;
        }
        $this->set( compact( 'data' ) );
    }
    
    function accounts()
    {
        $this->set( 'title_for_layout', 'گزارش حسابها' );
        
        $excel = (!empty($this->params['pass'][0]) && $this->params['pass'][0]=='export');
        
        $accounts = $this->Transaction->Account->find( 'list' , array(
            'order' => 'Account.sort ASC'
        ));
        $this->set( compact( 'accounts' ) );
        $this->set( $this->Expense->Individual->find( 'list' ) );
        
        $persianDate = new PersianDate();
        $conditions = array();
        if($this->data){            
            if(!empty($this->data['Account']['account_id'])){
                $conditions['account_id'] = $this->data['Account']['account_id'];
            }
            if(!empty($this->data['Account']['start_date'])){
                $conditions['Transaction.date >='] = $persianDate->pdate_format_reverse( $this->data['Account']['start_date'] );
            }
            if(!empty($this->data['Account']['end_date'])){
                $conditions['Transaction.date <='] = $persianDate->pdate_format_reverse( $this->data['Account']['end_date'] );
            }
            $this->Session->delete('Account.Report');
            $this->Session->write('Account.Report', $conditions);
        }
        
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) && !$excel ) {
            $this->Session->delete( 'Account.Report' );
        }
        //apply the conditions
        if ( $this->Session->check( 'Account.Report' ) AND (!empty( $this->params['named'] ) || $excel) ) {
            $conditions = $this->Session->read( 'Account.Report' );
        }
        
        $this->Account->recursive = -1;
        $this->Transaction->recursive = 0;
        
        $account_report = $this->Account->find( 'all' , array(
            'order' => 'Account.name ASC',
            'conditions' => array() + (empty($conditions['account_id'])? array() : array('Account.id'=>$conditions['account_id']))
        ));

        foreach ( $account_report as &$rep ) {
			$rep['Transaction'] = $this->Transaction->find('first' , [
				'fields' => [
					"SUM(IF(Transaction.type='debt', amount, 0)) AS sum_expense",
					"SUM(IF(Transaction.type='credit', amount, 0)) AS sum_income"
				],
				'conditions' => [
					'Transaction.account_id' => $rep['Account']['id'],
					'OR' => [
						'Transaction.expense_id NOT' => null,
						'Transaction.income_id NOT' => null,
					]
				] + $conditions
			]);

            $rep['Transaction'] = @$rep['Transaction'][0];
        }
		
        $this->set( 'account_report' , $account_report );
        
        $this->Transaction->Account->unbindModel(array(
            'hasMany' => array(
                'Transaction'
            )
        ));
        
        if($excel){
            $data = $this->Transaction->find( 'all' , array(
                'conditions' => $conditions,
                'order' => 'Transaction.date DESC'
            ));
        }else{
            $this->paginate['recursive'] = 0;
            $this->paginate['conditions'] = $conditions;
            $this->paginate['order'] = 'Transaction.date DESC';
            $data = $this->paginate($this->Transaction);
        }
        foreach($data as &$item){
            if(empty($item['Transaction']['income_id']) && empty($item['Transaction']['expense_id'])){
                $item['Transfer'] = Set::extract('Transfer',$this->Transfer->find('first', array(
                    'conditions'=> array(
                        'OR'=>array(
                            'transaction_debt_id' => $item['Transaction']['id'],
                            'transaction_credit_id' => $item['Transaction']['id']
                        )
                        ),
                    'limit'=>1
                )));
                $accid = ($item['Transfer']['transaction_debt_id']==$item['Transaction']['id'])? $item['Transfer']['transaction_credit_id'] : $item['Transfer']['transaction_debt_id'];
                $this->Transaction->recursive = 0;
                $item['Transfer']['Account'] = Set::extract('Account',$this->Transaction->find('first',array( 'conditions' => array( 'Transaction.id'=>$accid )  )));
            }
        }
        $this->set('transactions', $data);
        
        // export to excel if parameter is passed
        if($excel){
            $excel = array( array( 'حساب', 'هزینه', 'درآمد', 'تاریخ', 'توضیحات') );
            $i=1;
            foreach($data as $d){
                if(!empty($d['Transaction']['expense_id'])){
                    $description = $d['Expense']['description'];
                } elseif(!empty($d['Transaction']['income_id'])){
                    $description = $d['Income']['description'];
                } elseif(!empty($d['Transfer']['Account'])) {
                    $description = (($d['Transaction']['type']=='debt')? "انتقال به حساب" : "انتقال از حساب").' '.$d['Transfer']['Account']['name'];
                    $description.= $d['Transfer']['description']? "<br /> توضیحات: ".$d['Transfer']['description'] : "";
                } else {
                    $description = "تراکنش متناظر این انتقال حذف شده است.";
                }
                $excel[] = array(
                    $accounts[$d['Transaction']['account_id']],
                    ($d['Transaction']['type']=='debt')? '-'.$d['Transaction']['amount'] : 0,
                    ($d['Transaction']['type']=='credit')? $d['Transaction']['amount'] : 0,
                    $d['Transaction']['date'],
                    $description
                );
            }
            $this->export('Account', $excel);
        }
        
    }
    
    function individuals()
    {
        $this->set( 'title_for_layout', 'گزارش دریافت و پرداخت اشخاص' );
        
        $excel = (!empty($this->params['pass'][0]) && $this->params['pass'][0]=='export');
        
        $individuals = $this->Individual->find( 'list', array('Individual.sort') );
        $this->set( compact( 'individuals' ) );
        
        $persianDate = new PersianDate();
        $conditions = array();
        
        if($this->data){
            if(!empty($this->data['Individual']['individual_id'])){
                $conditions['individual_id'] = $this->data['Individual']['individual_id'];
            }
            if(!empty($this->data['Individual']['start_date'])){
                $conditions['start_date'] = $persianDate->pdate_format_reverse( $this->data['Individual']['start_date'] );
            }
            if(!empty($this->data['Individual']['end_date'])){
                $conditions['end_date'] = $persianDate->pdate_format_reverse( $this->data['Individual']['end_date'] );
            }
            $this->Session->delete('Individual.Report');
            $this->Session->write('Individual.Report', $conditions);
        }
        
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) && !$excel ) {
            $this->Session->delete( 'Individual.Report' );
        }
        //apply the conditions
        if ( $this->Session->check( 'Individual.Report' ) AND (!empty( $this->params['named'] ) || $excel) ) {
            $conditions = $this->Session->read( 'Individual.Report' );
        }
        
        /* Expenses for each one */
        $this->Expense->recursive = -1;
        $expenses = $this->Expense->Behaviors->attach('Containable');        
        $expenses = $this->Expense->find( 'all' , array(
            'contain' => 'Transaction.amount',
            'fields' => array(
                'CONCAT(Expense.individual_id) AS individual_id',
                'SUM(Transaction.amount) AS amount' 
            ),
            'conditions' => array(
                'Expense.individual_id <> '=>'NULL'
                )
                + ( empty($conditions['individual_id'])? array('Expense.individual_id <> '=>'NULL') : array('Expense.individual_id'=>$conditions['individual_id']) )
                + ( empty($conditions['start_date'])? array() : array('Transaction.date >='=>$conditions['start_date']) )
                + ( empty($conditions['end_date'])? array() : array('Transaction.date <='=>$conditions['end_date']) ),
            'group' => 'Expense.individual_id'
        ));
        $expenses = Set::classicExtract($expenses, '{n}.0');
        
        /* Incomeso from eachone */
        $this->Income->recursive = -1;
        $incomes = $this->Income->Behaviors->attach('Containable');        
        $incomes = $this->Income->find( 'all' , array(
            'contain' => 'Transaction.amount',
            'fields' => array(
                'CONCAT(Income.individual_id) AS individual_id',
                'SUM(Transaction.amount) AS amount' 
            ),
            'conditions' => array(
                )
                + ( empty($conditions['individual_id'])? array('Income.individual_id <> '=>'NULL') : array('Income.individual_id'=>$conditions['individual_id']) )
                + ( empty($conditions['start_date'])? array() : array('Transaction.date >='=>$conditions['start_date']) )
                + ( empty($conditions['end_date'])? array() : array('Transaction.date <='=>$conditions['end_date']) ),
            'group' => 'Income.individual_id'
        ));
        $incomes = Set::classicExtract($incomes, '{n}.0');
        
        /* Unsettled Debts */
        $this->Debt->recursive = -1;
/*        $debts = $this->Debt->find( 'all' , array(
            'fields' => array(
                'CONCAT(Debt.individual_id) AS individual_id',
                'ABS(Debt.amount) AS amount',
                'SUM(IF( Debt.type="credit", Debt.amount-IFNULL(DebtSettlement.amount,0) , 0 )) AS unsettled_credit',
                'SUM(IF( Debt.type="debt", ABS(Debt.amount+IFNULL(DebtSettlement.amount,0)) , 0 )) AS unsettled_debt',
                'SUM(IF( Debt.type="credit", IFNULL(DebtSettlement.amount,0) , 0 )) AS settled_credit',
                'SUM(IF( Debt.type="debt", IFNULL(DebtSettlement.amount,0) , 0 )) AS settled_debt',
//                'ABS(Debt.amount) AS amount',
//                'CONCAT(Debt.type) AS type',
//                'CONCAT(IFNULL(DebtSettlement.amount,0)) AS settled',
//                'CONCAT(Debt.status) AS status'
            ),
            'conditions' => array(
                'Debt.status <>' => 'done'
                )
                + ( empty($conditions['individual_id'])? array('Debt.individual_id <> '=>'NULL') : array('Debt.individual_id'=>$conditions['individual_id']) ),
            'joins' => array(
                array(
                        'table' => '(SELECT debt_id, SUM(amount) AS amount, created FROM debt_settlements GROUP BY debt_id) DebtSettlement',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'DebtSettlement.debt_id = Debt.id'
                        )
                        + ( empty($conditions['start_date'])? array() : array('DebtSettlement.created >='=>$conditions['start_date']) )
                        + ( empty($conditions['end_date'])? array() : array('DebtSettlement.created <='=>$conditions['end_date']) )
                    )
            ),
            'group' => 'Debt.individual_id'
        )); 
        $debts = Set::classicExtract($debts, '{n}.0');*/
        
        $individual_report = array();
        /*foreach( $individuals as $ikey=>$item ) {
            $individual_report[$ikey] = array(
                'name' => $item,
                'expenses' => 0,
                'incomes' => 0,
                'unsettled_credit' => 0,
                'unsettled_debt' => 0,
                'settled_credit' => 0,
                'settled_debt' => 0
                );
        }*/
        foreach( $expenses as $item ) {
            if(empty($individual_report[$item['individual_id']])) $individual_report[$item['individual_id']] = array('name' => $individuals[$item['individual_id']] );
            $individual_report[$item['individual_id']]['expenses'] = $item['amount'];
        }
        foreach( $incomes as $item ) {
            if(empty($individual_report[$item['individual_id']])) $individual_report[$item['individual_id']] = array('name' => $individuals[$item['individual_id']] );
            $individual_report[$item['individual_id']]['incomes'] = $item['amount'];
        }
        /*foreach( $debts as $item ) {
            $individual_report[$item['individual_id']]['unsettled_credit'] = $item['unsettled_credit'];
            $individual_report[$item['individual_id']]['unsettled_debt'] = $item['unsettled_debt'];
            $individual_report[$item['individual_id']]['settled_credit'] = $item['settled_credit'];
            $individual_report[$item['individual_id']]['settled_debt'] = $item['settled_debt'];
        }*/
        $this->set( compact( 'individual_report' ) );
        
        /*$this->Account->recursive = -1;
        $this->Transaction->recursive = 0;
        
        $account_report = $this->Account->find( 'all' , array(
            'order' => 'Account.name ASC',
            'conditions' => array() + (empty($conditions['account_id'])? array() : array('Account.id'=>$conditions['account_id']))
        ));
        foreach($account_report as &$rep){
            $rep['Transaction'] = $this->Transaction->find( 'first' , array(
                'fields' => array( 'SUM(IF(Transaction.type=\'debt\',amount,0)) AS sum_expense' , 'SUM(IF(Transaction.type=\'credit\',amount,0)) AS sum_income' ),
                'conditions' => array( 'Transaction.account_id' => $rep['Account']['id'] )+$conditions
                ) );
            $rep['Transaction'] = @$rep['Transaction'][0];
        }
        $this->set( 'account_report' , $account_report );
        
        $this->Transaction->Account->unbindModel(array(
            'hasMany' => array(
                'Transaction'
            )
        ));
        
        if($excel){
            $data = $this->Transaction->find( 'all' , array(
                'conditions' => $conditions,
                'order' => 'Transaction.date DESC'
            ));
        }else{
            $this->paginate['recursive'] = 0;
            $this->paginate['conditions'] = $conditions;
            $this->paginate['order'] = 'Transaction.date DESC';
            $data = $this->paginate($this->Transaction);
        }
        foreach($data as &$item){
            if(empty($item['Transaction']['income_id']) && empty($item['Transaction']['expense_id'])){
                $item['Transfer'] = Set::extract('Transfer',$this->Transfer->find('first', array(
                    'conditions'=> array(
                        'OR'=>array(
                            'transaction_debt_id' => $item['Transaction']['id'],
                            'transaction_credit_id' => $item['Transaction']['id']
                        )
                        ),
                    'limit'=>1
                )));
                $accid = ($item['Transfer']['transaction_debt_id']==$item['Transaction']['id'])? $item['Transfer']['transaction_credit_id'] : $item['Transfer']['transaction_debt_id'];
                $this->Transaction->recursive = 0;
                $item['Transfer']['Account'] = Set::extract('Account',$this->Transaction->find('first',array( 'conditions' => array( 'Transaction.id'=>$accid )  )));
            }
        }
        $this->set('transactions', $data); */
        
        // export to excel if parameter is passed
        if($excel){
            $excel = array( array( 'شخص', 'هزینه', 'درآمد') );
            $i=1;
            foreach($individual_report as $d){
                $excel[] = array(
                    $d['name'],
                    (!empty($d['expenses']))? $d['expenses'] : 0,
                    (!empty($d['incomes']))? $d['incomes'] : 0,
                );
            }
            $this->export('Individuals', $excel);
        }
    }
    
    function tags()
    {
        $this->set( 'title_for_layout', 'گزارش برچسب ها' );
        
        $excel = (!empty($this->params['pass'][0]) && $this->params['pass'][0]=='export');
        $uid = $this->Auth->user( 'id' );
        
        $this->set( 'tags' , $this->Tag->prepareList( $this->Tag->loadTags() ) );
        $persianDate = new PersianDate();

        //
        $conditions = array( );
        if ( isset( $this->data['Tag']['search'] ) ) {
            
            if(!empty($this->data['Tag']['start_date'])){
                $start_date = $persianDate->pdate_format_reverse( $this->data['Tag']['start_date'] );
            }
            if(!empty($this->data['Tag']['end_date'])){
                $end_date = $persianDate->pdate_format_reverse( $this->data['Tag']['end_date'] );
            }
            
            $catlist = $subcatlist = array();
            if( empty($this->data['Tag']['tag_id']) || (count($this->data['Tag']['tag_id'])==1 && $this->data['Tag']['tag_id'][0]=='0') ){
                //$catlist = $subcatlist = null;
            } else {
                foreach($this->data['Tag']['tag_id'] as $k=>$v) {
                    $catlist[] = substr($v, 1);
                }
                if ( $catlist ) {
                    $conditions['tag_id'] = $catlist;
                }
                $this->set( 'report_catlist' , $this->data['Tag']['tag_id'] );
            }
            
            //save it into session
            $this->Session->delete( 'TagReport.conditions' );
            $this->Session->write( 'TagReport.conditions', $conditions );
        }
        
        if(empty($conditions['tag_id'])) {
            $conditions['tag_id <>'] = NULL;
        }
        
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) && !$excel ) {
            $this->Session->delete( 'TagReport.conditions' );
        }
        //apply the conditions
        if ( $this->Session->check( 'TagReport.conditions' ) AND (!empty( $this->params['named'] ) || $excel ) ) {
            $conditions = $this->Session->read( 'TagReport.conditions' );
        }
        
        $tags = $this->Tag->find('list', array(
            'conditions' => empty($conditions['tag_id'])? array() : array( 'id' => $conditions['tag_id'] )
        ));
        
        foreach($tags as $tag_id=>&$tag) {
            
            //get sum of transactions with this tag
            $where = $this->Transaction->getDataSource()->conditions(
                    $conditions +
                    array('Transaction.user_id' => $this->Auth->user( 'id' )) +
                    array('TransactionTag.tag_id' => $tag_id) +
                    (isset($start_date)? array('Transaction.date >='=>$start_date) : array()) +
                    (isset($end_date)? array('Transaction.date <='=>$end_date) : array()),
                    true, true, 
                $this->Transaction);            
            $transactions = $this->Transaction->query(''
                . 'SELECT '
                . "SUM(IF(Transaction.type='debt',ABS(Transaction.amount),0)) AS sum_expense, "
                . "SUM(IF(Transaction.type='credit',ABS(Transaction.amount),0)) AS sum_income "
                . 'FROM `transactions` AS `Transaction` '
                . 'LEFT JOIN `transaction_tags` AS `TransactionTag` ON (`TransactionTag`.`transaction_id` = `Transaction`.`id`) '
                . $where
            );
            
            //get sum of check with this tag
            $where = $this->Check->getDataSource()->conditions(
                        $conditions +
                        array('Check.user_id'=>$this->Auth->user( 'id' )) +
                        array('CheckTag.tag_id' => $tag_id) +
                        (isset($start_date)? array('Check.due_date >='=>$start_date) : array()) +
                        (isset($end_date)? array('Check.due_date <='=>$end_date) : array()),
                    true, true, $this->Check);
            $checks = $this->Check->query(''
                . 'SELECT '
                . "SUM(IF(type='drawed',ABS(amount),0)) AS sum_drawed, "
                . "SUM(IF(type='received',ABS(amount),0)) AS sum_received "
                . 'FROM `checks` AS `Check` '
                . 'LEFT JOIN `check_tags` AS `CheckTag` ON (`CheckTag`.`check_id` = `Check`.`id`) '
                . $where
            );
            
            //get sum of debts with this tag
            $where = $this->Debt->getDataSource()->conditions(
                    $conditions +
                    array('Debt.user_id'=>$this->Auth->user( 'id' )) +
                    array('DebtTag.tag_id' => $tag_id) +
                    (isset($start_date)? array('Debt.due_date >='=>$start_date) : array()) +
                    (isset($end_date)? array('Debt.due_date <='=>$end_date) : array()),
                true, true, $this->Debt);
            
            $debts = $this->Debt->query(''
                . 'SELECT '
                . "SUM(IF(type='debt',ABS(amount),0)) AS sum_debt, "
                . "SUM(IF(type='credit',ABS(amount),0)) AS sum_credit "
                . 'FROM `debts` AS `Debt` '
                . 'LEFT JOIN `debt_tags` AS `DebtTag` ON (`DebtTag`.`debt_id` = `Debt`.`id`) '
                . $where
            );
            
            //set the tag values
            $tag = array(
                'id' => $tag_id,
                'name' => $tag,
                'amount' => 0,
                'expense' => $transactions[0][0]['sum_expense'],
                'income' => $transactions[0][0]['sum_income'],
                'drawed' => $checks[0][0]['sum_drawed'],
                'received' => $checks[0][0]['sum_received'],
                'loan' => 0,
                'debt' => $debts[0][0]['sum_debt'],
                'credit' => $debts[0][0]['sum_credit']
            );
        }

        //get transaction sums with all selected tags
        $where = $this->Transaction->getDataSource()->conditions(
                $conditions +
                array('Transaction.user_id' => $this->Auth->user( 'id' )) +
                array('TransactionTag.tag_id' => array_keys($tags)) +
                (isset($start_date)? array('Transaction.date >='=>$start_date) : array()) +
                (isset($end_date)? array('Transaction.date <='=>$end_date) : array()),
                true, true, 
            $this->Transaction);            
        $result = $this->Transaction->query(''
            . 'SELECT '
            . "SUM(IF(T.type='debt',ABS(T.amount),0)) AS sum_expense, "
            . "SUM(IF(T.type='credit',ABS(T.amount),0)) AS sum_income "
            . " FROM ("
                . "SELECT "
                . "DISTINCT Transaction.id , Transaction.amount, Transaction.type "
                . 'FROM `transactions` AS `Transaction` '
                . 'INNER JOIN `transaction_tags` AS `TransactionTag` ON (`TransactionTag`.`transaction_id` = `Transaction`.`id`) '
                . $where
            . ") AS T"
        );
        $sum_expense = $result[0][0]['sum_expense'];
        $sum_income = $result[0][0]['sum_income'];
        
        //get check sums with all selected tags
        $where = $this->Check->getDataSource()->conditions(
                    $conditions +
                    array('Check.user_id'=>$this->Auth->user( 'id' )) +
                    (isset($start_date)? array('Check.due_date >='=>$start_date) : array()) +
                    (isset($end_date)? array('Check.due_date <='=>$end_date) : array()),
                true, true, $this->Check);
        $result = $this->Check->query(''
            . 'SELECT '
            . "SUM(IF(type='drawed',ABS(amount),0)) AS sum_drawed, "
            . "SUM(IF(type='received',ABS(amount),0)) AS sum_received "
            . " FROM ("
                . "SELECT "
                . "DISTINCT Check.id , Check.amount, Check.type "
                . 'FROM `checks` AS `Check` '
                . 'INNER JOIN `check_tags` AS `CheckTag` ON (`CheckTag`.`check_id` = `Check`.`id`) '
                . $where
            . ") AS T"
        );
        $sum_drawed = $result[0][0]['sum_drawed'];
        $sum_received = $result[0][0]['sum_received'];
        
        // loans
        $where = $this->Transaction->getDataSource()->conditions(
                    $conditions +
                    array('Loan.user_id'=>$this->Auth->user( 'id' )) +
                    (isset($start_date)? array('Installment.due_date >='=>$start_date) : array()) +
                    (isset($end_date)? array('Installment.due_date <='=>$end_date) : array()),
                true, true, $this->Loan);
        $sql = "SELECT
                        SUM(ABS(amount)) AS sum,
                        tag_id
                FROM ( 
                    SELECT
                        SUM(Installment.amount) AS amount,
                        tag_id,
                        `Loan`.`id`
                        FROM `loans` AS `Loan`
                        LEFT JOIN `loan_tags` AS `LoanTag` ON (`LoanTag`.`loan_id` = `Loan`.`id`)
                        LEFT JOIN `installments` AS Installment ON (`Installment`.`loan_id` = `Loan`.`id`)
                        $where
                        GROUP BY `Loan`.`id`
                      )t
                 GROUP BY tag_id";
        $transaction = $this->Loan->query( $sql );
        foreach ($transaction as $t) {
            $tags[$t['t']['tag_id']]['loan'] += $t[0]['sum'];
        }
        
        //get debt/credit sums with all selected tags
        $where = $this->Debt->getDataSource()->conditions(
                    $conditions +
                    array('Debt.user_id'=>$this->Auth->user( 'id' )) +
                    (isset($start_date)? array('Debt.due_date >='=>$start_date) : array()) +
                    (isset($end_date)? array('Debt.due_date <='=>$end_date) : array()),
                true, true, $this->Debt);
        $result = $this->Debt->query(''
            . 'SELECT '
            . "SUM(IF(type='debt',ABS(amount),0)) AS sum_debt, "
            . "SUM(IF(type='credit',ABS(amount),0)) AS sum_credit "
            . " FROM ("
                . "SELECT "
                . "DISTINCT Debt.id , Debt.amount, Debt.type "
                . 'FROM `debts` AS `Debt` '
                . 'INNER JOIN `debt_tags` AS `DebtTag` ON (`DebtTag`.`debt_id` = `Debt`.`id`) '
                . $where
            . ") AS T"
        );
        $sum_debt = $result[0][0]['sum_debt'];
        $sum_credit = $result[0][0]['sum_credit'];
        
        $this->set('taglist',$tags);
        $this->set(array('sum_expense'=>$sum_expense,'sum_income'=>$sum_income,'sum_drawed'=>$sum_drawed,'sum_received'=>$sum_received,'sum_debt'=>$sum_debt,'sum_credit'=>$sum_credit));
        
        // export to excel if parameter is passed
        if($excel){
            $excel = array( array( 'نوع هزینه', 'مبلغ (ریال)', 'تاریخ', 'حساب', 'شخص', 'توضیحات') );
            $i=1;
            foreach($expenses as $d){
                $excel[] = array(
                    $d['ExpenseCategory']['name'].((!is_null($d['ExpenseSubCategory']['name']))? ' >> '.$d['ExpenseSubCategory']['name'] : ''),
                    number_format($d['Transaction']['amount']),
                    $d['Transaction']['date'],
                    $d['Account']['name'],
                    $d['Individual']['name'],
                    $d['Expense']['description']
                );
            }
            $this->export('Expense', $excel);
        }
    }
    
    public function budgets()
    {
        $this->set( 'title_for_layout', 'گزارش بودجه بندی' );
        
        $excel = (!empty($this->params['pass'][0]) && $this->params['pass'][0]=='export');
        $this->set( 'categories' , $this->ExpenseCategory->find( 'list' ) );
        
        $pd = new PersianDate();
        $months = array();
        for( $i=1; $i<=12; $i++ ) {
            $months[$i] = __('month_'.$i,true);
        }
        $this->set( 'months', $months );        
        list($nowyear,$nowmonth) = explode(':',$pd->pdate('Y:m'));
        $year = @$year?: ($nowyear-5);
        $years = array();
        for( $i=$year; $i<=$nowyear+4; $i++ ) {
            $years[$i] = $i;
        }
        $this->set( 'years', $years );
        
        //
        $conditions = array( );
        if ( isset( $this->data['Budget']['search'] ) ) {
            if ( !empty( $this->data['Budget']['expense_category_id'] ) ) {
                $conditions['Budget.expense_category_id'] = $this->data['Budget']['expense_category_id'];
            }
            if ( !empty( $this->data['Budget']['start_date']['year'] ) ) {
                $conditions['Budget.start_date >='] = $pd->pdate_format_reverse( $this->data['Budget']['start_date']['yeaer'].'/'.$this->data['Budget']['start_date']['month'].'/1' );
            }
            if ( !empty( $this->data['Budget']['end_date'] ) ) {
                $conditions['Budget.start_date <='] = $pd->pdate_format_reverse( $this->data['Budget']['end_date']['yeaer'].'/'.$this->data['Budget']['end_date']['month'].'/1' );
            }
            
            //save it into session
            $this->Session->delete( 'BudgetReport.conditions' );
            $this->Session->write( 'BudgetReport.conditions', $conditions );
        }
        
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) && !$excel ) {
            $this->Session->delete( 'BudgetReport.conditions' );
        }
        //apply the conditions
        if ( $this->Session->check( 'BudgetReport.conditions' ) AND (!empty( $this->params['named'] ) || $excel) ) {
            $conditions = $this->Session->read( 'BudgetReport.conditions' );
        } else {
            $conditions['Budget']['start_date >='] = $pd->pdate_format_reverse( $nowyear.'/01/01' );
            $conditions['Budget']['start_date <='] = $pd->pdate_format_reverse( $nowyear.'/12/'.$pd->lastday(03, 12, date('Y')) );
        }
        
        $this->Budget->outputConvertDate = false;
        $result = $this->Budget->find( 'all' , array(
            'condtions' => $conditions
        ));
        
        foreach($result as &$item) {
            $item['Budget']['amount_used'] = $this->_getTransactionSum($item['Budget']['expense_category_id'], $item['Budget']['start_date'], $item['Budget']['end_date']);
            $item['Budget']['used_percent'] = round(($item['Budget']['amount_used']/$item['Budget']['amount'])*100, 1);
            $item['Budget']['minus'] = ($item['Budget']['amount_used']>$item['Budget']['amount'])? $item['Budget']['amount_used']-$item['Budget']['amount'] : 0;
            $item['Budget']['plus'] = ($item['Budget']['amount_used']<$item['Budget']['amount'])? $item['Budget']['amount']-$item['Budget']['amount_used'] : 0;
        }
        
        $this->set( 'budgets' , $result );
        
        // export to excel if parameter is passed
        if($excel){
            $excel = array( array( 'گروه هزینه', 'بازه زمانی', 'مبلغ بودجه (ریال)', 'میزان مصرف شده (ریال)', 'درصد مصرفی', 'کسر بودجه (ریال)', 'بودجه مازاد (ریال)' ) );
            $i=1;
            foreach($result as $d){
                $excel[] = array(
                    $d['ExpenseCategory']['name'],
                    __('month_'.$item['Budget']['pmonth'],true).' '.$item['Budget']['pyear'],
                    number_format($d['Budget']['amount']),
                    number_format($d['Budget']['amount_used']),
                    $d['Budget']['used_percent'].'%',
                    $d['Budget']['minus'],
                    $d['Budget']['plus']
                );
            }
            $this->export('Budget', $excel);
        }
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
    
    function export($name,$data)
    {
        //set memory limit
        
        #ini_set( 'memory_limit', '128M' );
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion( 8 );
        $workbook->send( $name.'.xls' );
        $worksheet = & $workbook->addWorksheet( $name );
        $worksheet->setInputEncoding( 'utf-8' );
        
        // write to excel
        $size = count($data);
        
        for($i=0; $i<$size; $i++) {
            $worksheet->writeRow( $i, 0, $data[$i] );
        }
        // send the file
        $workbook->close();
        die;
    }
    

}

?>
