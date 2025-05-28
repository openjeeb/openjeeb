<?php

uses( 'sanitize' );
App::import( 'Vendor', 'PersianDate', array( 'file' => 'persian.date.php' ) );

class IncomesController extends AppController {

    var $name = 'Incomes';
    var $components = array( 'Security' );
    var $uses = array( 'Income' , 'Tag' );

    function beforeFilter() {
        parent::beforeFilter();
        $this->Security->requireAuth( 'index', 'edit' );
        $this->Security->blackHoleCallback = 'fail';
    }

    function index() {
        $this->set( 'title_for_layout', 'درآمد‌ها' );

        //sanitize the data
        $san = new Sanitize();
        $this->data = $san->clean( $this->data );

        //paginate data
        $conditions = array( );
        if ( isset( $this->data['Income']['search'] ) ) {
            if ( !empty( $this->data['Income']['income_type_id'] ) ) {
                $conditions['Income.income_type_id'] = $this->data['Income']['income_type_id'];
            }
            if ( !empty( $this->data['Income']['income_sub_type_id'] ) ) {
                $conditions['Income.income_sub_type_id'] = $this->data['Income']['income_sub_type_id'];
            }
            if ( !empty( $this->data['Transaction']['account_id'] ) ) {
                $conditions['Transaction.account_id'] = $this->data['Transaction']['account_id'];
            }
            if ( !empty( $this->data['Transaction']['amount'] ) ) {
                $conditions['Transaction.amount'] = floatval( str_replace( ',', '', $this->data['Transaction']['amount'] ) );
            }
            if ( !empty( $this->data['Income']['individual_id'] ) ) {
                $conditions['Income.individual_id'] = $this->data['Income']['individual_id'];
            }
            if ( !empty( $this->data['Income']['start_date'] ) ) {
                $persianDate = new PersianDate();
                $this->data['Income']['start_date'] = $persianDate->pdate_format_reverse( $this->data['Income']['start_date'] );
                $conditions['Transaction.date >='] = $this->data['Income']['start_date'];
            }
            if ( !empty( $this->data['Income']['end_date'] ) ) {
                $persianDate = new PersianDate();
                $this->data['Income']['end_date'] = $persianDate->pdate_format_reverse( $this->data['Income']['end_date'] );
                $conditions['Transaction.date <='] = $this->data['Income']['end_date'];
            }
            if ( !empty( $this->data['Income']['description_search'] ) ) {
                $conditions['Income.description LIKE'] = "%" . $this->data['Income']['description_search'] . "%";
            }
            
            $catlist = array();
            if( empty($this->data['TransactionTagSearch']['tag_id']) || (count($this->data['TransactionTagSearch']['tag_id'])==1 && $this->data['TransactionTagSearch']['tag_id'][0]=='0') ){
                //$catlist = $subcatlist = null;
            } else {
                foreach($this->data['TransactionTagSearch']['tag_id'] as $k=>$v) {
                    switch( substr($v, 0,1) ) {
                        case 't':
                            $catlist[] = substr($v, 1);
                            break;
                        default:
                            unset($this->data['TransactionTagSearch']['tag_id'][$k]);
                            break;
                    }
                }
                if ( $catlist ) {
                    $conditions['TransactionTag.tag_id'] = $catlist;
                }
                $this->set( 'report_catlist' , $this->data['TransactionTagSearch']['tag_id'] );
            }
            
            //save it into session
            $this->Session->delete( 'Income.conditions' );
            $this->Session->write( 'Income.conditions', $conditions );
        }
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) ) {
            $this->Session->delete( 'Income.conditions' );
        }
        //apply the conditions
        if ( $this->Session->check( 'Income.conditions' ) AND !empty( $this->params['named'] ) ) {
            $conditions = $this->Session->read( 'Income.conditions' );
        }
        
        $this->loadTags();

        $this->Income->recursive = 0;
        $this->Income->Transaction->outputConvertDate = true;
        $this->Income->Transaction->convertDateFormat = 'Y/m/d';
        $this->Income->convertDateFormat = 'Y/m/d';        
        $this->Income->bindModel( array(
            'hasOne' => array(
                'Account' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Account.id = Transaction.account_id' )
                ),
                'TransactionTag' => array(
                        'className' => null,
                        'fields' => 'CONCAT_WS(",", TransactionTag.tag_id )',
                        'foreignKey' => false,
                        'conditions' => array( 'TransactionTag.transaction_id = Transaction.id' )
                    )
            )
            ), false );
        $this->paginate['fields'] = array( 'Income.id', 'Income.description', 'Income.income_type_id', 'IncomeType.name', 'Income.income_sub_type_id', 'IncomeSubType.name', 'Transaction.id', 'Transaction.amount', 'Transaction.date', 'Transaction.account_id', 'Account.name', 'Individual.name' );
        $this->paginate['order'] = 'Transaction.date DESC,Income.id DESC';
        $this->paginate['conditions'] = $conditions;
        $this->paginate['group'] = 'Income.id';
        $this->set( 'incomes', $this->paginate() );
        
        //find the sum
        /*$this->Income->recursive = -1;
        $this->Income->Behaviors->attach( 'Containable' );        
        $this->Income->bindModel( array(
            'hasOne' => array(
                'TransactionTag' => array(
                        'fields' => 'TransactionTag.transaction_id',
                        'foreignKey' => false,
                        'conditions' => array( 'TransactionTag.transaction_id = Transaction.id' )
                    )
            )
            ), false );
        $this->Income->contain( 'TransactionTag', 'Transaction' );
        $sum = $this->Income->find( 'first', array( 'fields' => array( 'COUNT(DISTINCT Income.id), SUM(1), SUM(Transaction.amount) AS sum' ), 'conditions' => $conditions ) );*/        
        
        //preg_match('/where(?:(?=.+?\b(?:order|limit)\b)(.+?)(?:order|limit)|(.+))/i', $lQuery['log'][count($lQuery['log'])-1]['query'], $match); // for practice
        /*$lQuery = $this->Income->getDataSource()->getLog(false, false);
        $where = explode('WHERE',$lQuery['log'][count($lQuery['log'])-1]['query']); $where = explode('ORDER',$where[count($where)-1]); $where = explode('LIMIT',$where[0]); $where = explode('GROUP',$where[0]); $where = $where[0];*/
        
        $where = $this->Income->getDataSource()->conditions($conditions+array('Income.user_id'=>$this->Auth->user( 'id' )), true, true, $this->Income);
        $sql = "SELECT SUM(amount) AS sum
                    FROM 
                        (SELECT Income.id, Transaction.amount AS amount
                        FROM `incomes` AS `Income`
                        LEFT JOIN `transactions` AS `Transaction` ON (`Income`.`transaction_id` = `Transaction`.`id`)
                        LEFT JOIN `transaction_tags` AS `TransactionTag` ON (`TransactionTag`.`transaction_id` = `Transaction`.`id`)
                        $where
                        GROUP BY Income.id)t";
        $sum = $this->Income->query($sql);        
        $this->set( 'sum', $sum[0][0]['sum'] );

        //get accounts$accounts = $accountsbalance = array();
        $list = $this->Income->Transaction->Account->find( 'all' , array(
            'conditions' => array(
                'Account.status' => 'active'
                ),
            'fields' => 'Account.id,Account.name,Account.balance',
            'order' => 'Account.sort ASC'
            ) );
        foreach($list as $val){
            $accounts[$val['Account']['id']] = $val['Account']['name']; 
            $accountsbalance[$val['Account']['id']] = $val['Account']['balance'];
        }
        $this->set( compact( 'accounts' ) );
        $this->set( compact( 'accountsbalance' ) );

        //get individuals
        $individuals = $this->Income->Individual->fetchList();
        $this->set( compact( 'individuals' ) );

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

        //pie data
        $this->Income->recursive = 0;
        $this->Income->unbindModelAll();
        $this->Income->bindModel(array(
            'belongsTo' => array(
                'Transaction' => array(
                    'className' => 'Transaction',
                    'foreignKey' => 'transaction_id',
                    'conditions' => '',
                    'fields' => array('Transaction.id','Transaction.amount','Transaction.date','Transaction.type'),
                    'order' => '',
                    'dependent' => true
                ),
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id',
                    'conditions' => '',
                    'fields' => array('User.id'),
                    'order' => ''
                ),
                'IncomeType' => array(
                    'className' => 'IncomeType',
                    'foreignKey' => 'income_type_id',
                    'conditions' => '',
                    'fields' => array('IncomeType.id','IncomeType.name'),
                    'order' => ''
                )
            )
        ));
        $pie = $this->Income->find( 'all', array(
            'fields' => array(
                'IncomeType.name AS k',
                'SUM(Transaction.amount) AS value'
            ),
            'group' => array( 'Income.income_type_id' ),
            ) );
        $this->set( 'pieData', $this->Chart->formatPieData( $pie, 'IncomeType' ) );

        //column chart
//        $this->Income->recursive = -1;
//        $column=$this->Income->find('all',array(
//            'fields'=>array(
//                            "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k",
//                            'SUM(Transaction.amount) AS value'
//                        ),
//            'group'=>array('k'),
//            'order'=>'Transaction.date ASC',
//        ));
//        $this->set('columnData',Set::classicExtract($column,'{n}.0'));
        //add
        if ( !empty( $this->data ) AND !isset( $this->data['Income']['search'] ) ) {
            //sanitize income type id
            $this->data['Income']['income_type_id'] = intval( $this->data['Income']['income_type_id'] );
            $this->data['Income']['income_sub_type_id'] = intval( $this->data['Income']['income_sub_type_id'] );

            //remove price formating strings and 
            $this->data['Transaction']['amount'] = str_replace( ',', '', $this->data['Transaction']['amount'] );

            //check for income type
            if ( intval( $this->data['Income']['income_type_id'] == 0 ) ) {
                $this->Session->setFlash( 'لطفا یک نوع درآمد را انتخاب کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            if ( $this->data['Income']['income_sub_type_id'] == 0 ) {
                $this->data['Income']['income_sub_type_id'] = null;
            }

            //save income
            if ( !$this->Income->saveIncome( $this->data ) ) {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'action' => 'index' ) );
            return true;
        }
    }

    function edit( $id = null ) {
        $this->set( 'title_for_layout', 'ویرایش درآمد' );

        if ( !$id && empty( $this->data ) ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }

        //get income
        $this->Income->recursive = 0;
        $this->Income->outputConvertDate = true;
        $this->Income->convertDateFormat = 'Y/m/d';
        $income = $this->Income->read( array( 'Income.id', 'Income.transaction_id', 'Income.description', 'Income.income_type_id', 'Income.income_sub_type_id', 'Income.individual_id', 'Transaction.id', 'Transaction.amount', 'Transaction.date', 'Transaction.account_id', 'IncomeType.name', 'IncomeSubType.id' ), $id );
        $income['Income']['description'] = html_entity_decode( str_replace( '\n', "\n", $income['Income']['description'] ), ENT_QUOTES, 'UTF-8' );
        // reading tags
        $this->Income->Transaction->TransactionTag->recursive = -1;
        $income['Transaction']['TransactionTag'] = Set::extract("{n}.0.tag_id", $this->Income->Transaction->TransactionTag->find('all', array(
            'fields' => 'CONCAT("t",TransactionTag.tag_id) AS tag_id',
            'conditions' => array(
                'transaction_id' => $income['Transaction']['id']
                )
        )));
        $this->set( 'tags' , $this->Tag->prepareList($this->Tag->loadTags()) );

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
        $this->set( compact( 'accounts' , 'accountsbalance' ) );

        //get individuals
        $individuals = $this->Income->Individual->fetchList();
        $this->set( compact( 'individuals' ) );

        //assign
        if ( empty( $this->data ) ) {
            $this->data = $income;
        }

        //saving the posted data
        if ( !empty( $this->data ) && !empty( $_POST ) ) {

            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean( $this->data );
            $this->data['Income']['income_type_id'] = intval( $this->data['Income']['income_type_id'] );
            $this->data['Income']['income_sub_type_id'] = intval( $this->data['Income']['income_sub_type_id'] );

            if ( $this->data['Income']['income_sub_type_id'] == 0 ) {
                $this->data['Income']['income_sub_type_id'] = null;
            }

            //remove price formating strings and 
            $this->data['Transaction']['amount'] = str_replace( ',', '', $this->data['Transaction']['amount'] );

            //save
            $dataSource = $this->Income->getDataSource();
            $dataSource->begin( $this->Income );

            //save income
            if ( !$this->Income->save( $this->data, true, array( 'income_type_id', 'income_sub_type_id', 'individual_id', 'description' ) ) ) {
                $dataSource->rollback( $this->Income );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //save transaction
            if ( !$this->Income->Transaction->save( $this->data, true, array( 'amount', 'date', 'pyear', 'pmonth', 'pday', 'account_id' ) ) ) {
                $dataSource->rollback( $this->Income );
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                return false;
            }

            //check for account change
            if ( $income['Transaction']['account_id'] != intval( $this->data['Transaction']['account_id'] ) ) {

                //update balance for original account
                if ( !$this->Income->Transaction->Account->updateBalance( $income['Transaction']['account_id'], '-' . $income['Transaction']['amount'] ) ) {
                    $dataSource->rollback( $this->Income );
                    $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                    return false;
                }

                //update balance for new account
                if ( !$this->Income->Transaction->Account->updateBalance( $this->data['Transaction']['account_id'], intval( $this->data['Transaction']['amount'] ) ) ) {
                    $dataSource->rollback( $this->Income );
                    $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                    return false;
                }
            } else {

                //update account balance
                if ( !$this->Income->Transaction->Account->updateBalance( $income['Transaction']['account_id'], $this->data['Transaction']['amount'] - $income['Transaction']['amount'] ) ) {
                    $dataSource->rollback( $this->Income );
                    $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
                    return false;
                }
            }
            
            $this->Income->Transaction->TransactionTag->replaceTags($this->data['Transaction']['id'], $this->data['TransactionTag']['tag_id']);

            //commit
            $dataSource->commit( $this->Income );
            $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
            $this->redirect(array('action' => 'index'));
            return true;
        }
    }

    function delete( $id = null ) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        //delete
        $dataSource = $this->Income->getDataSource();
        $dataSource->begin( $this->Income );
        //get income
        $this->Income->recursive = -1;
        $this->Income->Behaviors->attach( 'Containable' );
        $this->Income->contain( 'Transaction' );
        $income = $this->Income->read( array( 'Income.id', 'Income.user_id', 'Transaction.id', 'Transaction.account_id', 'Transaction.amount' ), intval( $id ) );
        //check user
        if ( $income['Income']['user_id'] != $this->Auth->user( 'id' ) ) {
            $dataSource->rollback( $this->Income );
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        //delete
        if ( !$this->Income->delete( $id ) ) {
            $dataSource->rollback( $this->Income );
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        //update balance
        if ( !$this->Income->Transaction->Account->updateBalance( $income['Transaction']['account_id'], '-' . $income['Transaction']['amount'] ) ) {
            $dataSource->rollback( $this->Income );
            $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( Controller::referer() );
            return false;
        }
        //commit
        $dataSource->commit( $this->Income );
        $this->Session->setFlash( 'داده مورد نظر با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
        $this->redirect( Controller::referer() );
        return true;
    }

    function export( $limit = null )
    {
        ini_set( 'memory_limit', '64M' );
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion( 8 );
        $workbook->send( 'incomes.xls' );
        $worksheet = & $workbook->addWorksheet( 'incomes' );
        $worksheet->setInputEncoding( 'utf-8' );

        //get the data
        $this->Income->recursive = 0;
        $this->Income->Transaction->outputConvertDate = true;
        $this->Income->convertDateFormat = 'Y/m/d';
        $this->Income->bindModel( array(
            'hasOne' => array(
                'Account' => array(
                    'foreignKey' => false,
                    'conditions' => array( 'Account.id = Transaction.account_id' )
                ),
            )
            ), false );
        $options = array( );
        $options['fields'] = array( 'Transaction.amount', 'Transaction.date', 'Income.description', 'IncomeType.name', 'IncomeSubType.name', 'Account.name', 'Individual.name' );
        $options['order'] = "Transaction.date DESC";
        if ( $this->Session->check( 'Income.conditions' ) ) {
            $options['conditions'] = $this->Session->read( 'Income.conditions' );
        }
        if ( !is_null( $limit ) ) {
            $conditions['limit'] = $limit;
        }
        $incomes = $this->Income->find( 'all', $options );
        $data = array( );
        $data[] = array( 'مبلغ', 'تاریخ', 'توضیحات', 'نوع درآمد', 'زیر شاخه نوع درآمد', 'حساب', 'شخص' );
        $i = 1;
        foreach ( $incomes as $entry ) {
            $data[$i][] = $entry['Transaction']['amount'];
            $data[$i][] = $entry['Transaction']['date'];
            $data[$i][] = $entry['Income']['description'];
            $data[$i][] = $entry['IncomeType']['name'];
            $data[$i][] = $entry['IncomeSubType']['name'];
            $data[$i][] = $entry['Account']['name'];
            $data[$i][] = $entry['Individual']['name'];
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
    
    private function loadTags()
    {
        $this->set( 'tags' , $this->Tag->prepareList( $this->Tag->loadTags() ) );
    }

}

?>