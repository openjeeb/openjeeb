<?php

uses( 'sanitize' );

class IndividualsController extends AppController {

    var $name = 'Individuals';
    var $uses = array('Individual','Transaction');

    function index() {
        $this->set('title_for_layout', 'اشخاص');
                
        //paginate data
        $conditions = array( );
        if ( isset( $this->data['Individual']['search'] ) ) {
            if ( !empty( $this->data['Individual']['name'] ) ) {
                $conditions['Individual.name LIKE'] = "%".$this->data['Individual']['name']."%";
            }
            if ( !empty( $this->data['Individual']['status'] ) ) {
                $conditions['Individual.status'] = $this->data['Individual']['status'];
            }
            if ( !empty( $this->data['Individual']['start_date'] ) ) {
                $persianDate = new PersianDate();
                $this->data['Individual']['start_date'] = $persianDate->pdate_format_reverse( $this->data['Individual']['start_date'] );
                $conditions['Individual.created >='] = $this->data['Individual']['start_date'];
            }
            if ( !empty( $this->data['Individual']['end_date'] ) ) {
                $persianDate = new PersianDate();
                $this->data['Individual']['end_date'] = $persianDate->pdate_format_reverse( $this->data['Individual']['end_date'] );
                $conditions['Individual.created <='] = $this->data['Individual']['end_date'];
            }
            if ( !empty( $this->data['Individual']['description_search'] ) ) {
                $conditions['Individual.description LIKE'] = "%" . $this->data['Individual']['description_search'] . "%";
            }
            
            //save it into session
            $this->Session->delete( 'Individual.conditions' );
            $this->Session->write( 'Individual.conditions', $conditions );
        }
        //reset the conditions
        if ( empty( $this->params['named'] ) AND empty( $this->data ) ) {
            $this->Session->delete( 'Individual.conditions' );
        }
        //apply the conditions
        if ( $this->Session->check( 'Individual.conditions' ) AND !empty( $this->params['named'] ) ) {
            $conditions = $this->Session->read( 'Income.conditions' );
        }
        
        $this->Individual->recursive = 0;
        $this->paginate['conditions'] = $conditions;
        $this->Individual->order = 'sort ASC';
        $this->set( 'individuals', $this->paginate() );
        
        //add
        if ( !empty($this->data) AND !isset($this->data['Individual']['search']) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean( $this->data );
            $sort = $this->Individual->find( 'first' , array( 'fields' => array( 'GREATEST(MAX(sort)+1,COUNT(*)) AS sort' )) );
            $this->data['Individual']['sort'] = intval($sort[0]['sort']);
            $this->Individual->create();
            if ( $this->Individual->save( $this->data ) ) {
                $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
                $this->redirect( array( 'action' => 'index' ) );
            } else {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            }
        }
    }

    function view( $id = null ) {
        $this->set('title_for_layout', 'آمار شخص');
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        
        //get the individual
        $individual = $this->Individual->read( null, $id );
        $this->set( compact( 'individual' ) );

        if(empty($individual)) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        
        //individual expense transactions column chart
        $this->Transaction->recursive = 0;
        $debtTransactionsColumn = $this->Transaction->find('all', array(
            'fields' => array(
                "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k",
                'SUM(ABS(Transaction.amount)) AS value'
            ),
            'conditions' => array(
                'Transaction.type' => 'debt',
                'OR'=>array(
                    'Expense.individual_id'=>$individual['Individual']['id'],
                    'Income.individual_id'=>$individual['Individual']['id'],
                )
             ),
            'group' => array('k'),
            'order' => array('Transaction.pyear', 'Transaction.pmonth')
            ));
        $this->set('debtTransactionsColumn', Set::classicExtract($debtTransactionsColumn, '{n}.0'));
        
        //indiviual income transactions column chart
        $this->Transaction->recursive = 0;
        $creditTransactionsColumn = $this->Transaction->find('all', array(
            'fields' => array(
                "CONCAT(Transaction.pyear,'/',Transaction.pmonth) AS k",
                'SUM(Transaction.amount) AS value'
            ),
            'conditions' => array(
                'Transaction.type' => 'credit',
                'OR'=>array(
                    'Expense.individual_id'=>$individual['Individual']['id'],
                    'Income.individual_id'=>$individual['Individual']['id'],
                )
             ),
            'group' => array('k'),
            ));
        $this->set('creditTransactionsColumn', Set::classicExtract($creditTransactionsColumn, '{n}.0'));
        
        //get transactions
        $this->Transaction->convertDateFormat = 'Y/m/d';
        $this->Transaction->Expense->convertDateFormat = 'Y/m/d';
        $this->Transaction->Income->convertDateFormat = 'Y/m/d';
        $this->Transaction->recursive = 0;
        $this->Transaction->bindModel(array(
            'hasOne' => array(
                'ExpenseCategory' => array(
                    'foreignKey' => false,
                    'conditions' => array('Expense.expense_category_id = ExpenseCategory.id')
                ),
                'ExpenseSubCategory' => array(
                    'foreignKey' => false,
                    'conditions' => array('Expense.expense_sub_category_id=ExpenseSubCategory.id')
                ),
                'IncomeType' => array(
                    'foreignKey' => false,
                    'conditions' => array('Income.income_type_id=IncomeType.id')
                ),
                'IncomeSubType' => array(
                    'foreignKey' => false,
                    'conditions' => array('Income.income_sub_type_id=IncomeSubType.id')
                ),
                'IndividualExpense' => array(
                    'className' => 'Individual',
                    'foreignKey' => false,
                    'conditions' => array('Income.individual_id=IndividualExpense.id')
                ),
                'IndividualIncome' => array(
                    'className' => 'Individual',
                    'foreignKey' => false,
                    'conditions' => array('Expense.individual_id=IndividualIncome.id')
                ),
            )), false);
        $this->paginate['order'] = 'Transaction.date DESC, Expense.id DESC, Income.id DESC';
        $this->paginate['conditions'] = array(
            'OR'=>array(
                'Expense.individual_id'=>$individual['Individual']['id'],
                'Income.individual_id'=>$individual['Individual']['id'],
            )
        );
        $this->set('transactions', $this->paginate('Transaction'));
        
        //get sum 
        $sum=  $this->Transaction->find('all',array(
            'fields'=>array('Transaction.type','SUM(Transaction.amount) AS sum'),
            'conditions'=>array(
                'OR'=>array(
                    'Expense.individual_id'=>$individual['Individual']['id'],
                    'Income.individual_id'=>$individual['Individual']['id'],
                )
            ),
            'group'=>array('Transaction.type')
        ));
        //set debts and credits sum
        foreach ($sum as $entry){
            if($entry['Transaction']['type']=='debt') {
                $this->set('debtSum',$entry['0']['sum']);
            } elseif($entry['Transaction']['type']=='credit') {
                $this->set('creditSum',$entry['0']['sum']);
            }
        }
    }

    function edit( $id = null ) {
        $this->set('title_for_layout', 'ویرایش شخص');
        if ( !$id && empty( $this->data ) ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        if ( !empty( $this->data ) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->data = $san->clean( $this->data );
            if ( $this->Individual->save( $this->data ) ) {
                $this->Session->setFlash( 'داده‌های موردنظر با موفقیت وارد شد.', 'default', array( 'class' => 'success' ) );
                $this->redirect( array( 'action' => 'index' ) );
            } else {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            }
        }
        if ( empty( $this->data ) ) {
            $this->data = $this->Individual->read( null, $id );
            $this->data['Individual']['name'] = html_entity_decode( str_replace( '\n', "\n", $this->data['Individual']['name'] ), ENT_QUOTES, 'UTF-8' );
            $this->data['Individual']['description'] = html_entity_decode( str_replace( '\n', "\n", $this->data['Individual']['description'] ), ENT_QUOTES, 'UTF-8' );
        }
    }

    function delete( $id = null ) {
        if ( !$id ) {
            $this->Session->setFlash( 'شماره نامعتبر است.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        //check user
        $this->Individual->recursive = -1;
        if ( $this->Individual->field( 'user_id', array( 'id' => $id ) ) != $this->Auth->user( 'id' ) ) {
            $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        //delete
        elseif ( $this->Individual->delete( $id ) ) {
            $this->Session->setFlash( 'داده مورد نظر با موفقیت پاک شد.', 'default', array( 'class' => 'success' ) );
            $this->redirect( array( 'action' => 'index' ) );
        }
        $this->Session->setFlash( 'مشکلی در پاک کردن داده مورد نظر بوجود آمد، لطفا دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
        $this->redirect( array( 'action' => 'index' ) );
    }

    function export($limit=null) {
        // Pear excel writer
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion(8);
        $workbook->send('individuals.xls');
        $worksheet = & $workbook->addWorksheet('individuals');
        $worksheet->setInputEncoding('utf-8');

        //get the data
        $this->Individual->recursive = -1;
        $this->Individual->convertDateFormat = 'Y/m/d';
        $options = array();
        $options['fields'] = array('Individual.name', 'Individual.description', 'Individual.created');
        $options['order'] = "Individual.sort ASC";
        $individuals = $this->Individual->find('all', $options);
        $data = array();
        $data[] = array('نام','توضیحات','تاریخ ایجاد');
        $i = 1;
        foreach ($individuals as $entry) {
            $data[$i][] = $entry['Individual']['name'];
            $data[$i][] = $entry['Individual']['description'];
            $data[$i][] = $entry['Individual']['created'];
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
    
    function sort()
    {
    	if ( isset($this->passedArgs['up']) || isset($this->passedArgs['down']) ) {
            //sanitize the data
            $san = new Sanitize();
            $this->passedArgs = $san->clean( $this->passedArgs );
            $orientation = isset($this->passedArgs['up'])? 'up' : 'down';

            if ( $this->Individual->moveSortItem( $this->passedArgs[$orientation], $orientation ) ) {
                
            } else {
                $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );    			
            }
    	}else{
            $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );
    	}
        $this->redirect( array( 'action' => 'index' ) );
    	
    }
    
    function toggleshow()
    {
        if( $status = $this->Individual->toggleStatus( intval( $this->passedArgs[0] ) ) ){
            if($status == 'active') {
                $this->Session->setFlash( 'شخص مورد نظر شما فعال شد و در لیستها نمایش داده خواهد شد.', 'default', array( 'class' => 'success' ) );
            } else {
                $this->Session->setFlash( 'شخص مورد نظر شما غیر فعال شد و در لیستها نمایش داده نخواهد شد.', 'default', array( 'class' => 'success' ) );
            }            
        } else {
            $this->Session->setFlash( 'مشکلی در ورود داده‌های مورد نظر پیش آمد، خواهشمند است دوباره تلاش کنید.', 'default', array( 'class' => 'error-message' ) );    			
        }
        
        $this->redirect( $this->referer(array('action'=>'index')), true );        
        return true;
    }
}

?>