<?php

class Reminder extends AppModel {

    var $recursive = -1;
    
    private $_Config;
    private $hash;
    
    var $name = 'Reminder';
//    var $displayField = 'name';
//    var $virtualFields = array('settled' => "(SELECT SUM(amount) FROM debt_settlements WHERE debt_settlements.debt_id=Debt.id)");
//    var $validate = array(
//        'name' => array(
//            'notempty' => array(
//                'rule' => array('notempty'),
//                'message' => 'لطفا عنوان را وارد کنید.',
//                'allowEmpty' => false,
//                'required' => true,
//            ),
//        ),
//        'amount' => array(
//            'numeric' => array(
//                'rule' => array('numeric'),
//                'message' => 'مبلغ بایستی یک عدد باشد.',
//                'allowEmpty' => false,
//                'required' => true,
//            ),
//            'range' => array(
//                'rule' => array('range', -9999999999999, 9999999999999),
//                'message' => 'مبلغ معتبر نیست.',
//                'allowEmpty' => false,
//                'required' => true,
//            ),
//        ),
//        'due_date' => array(
//            'date' => array(
//                'rule' => array('date'),
//                'message' => 'تاریخ معتبر نیست.',
//                'allowEmpty' => false,
//                'required' => true,
//            ),
//        ),
//    );
    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields' => array('User.id'),
            'order' => ''
        ),
        'Debt' => array(
            'foreignKey' => 'reference_id',
        ),
        'Installment' => array(
            'foreignKey' => 'reference_id',
        ),
        'Check' => array(
            'foreignKey' => 'reference_id',
        ),
        'Note' => array(
            'foreignKey' => 'reference_id',
        )
    );
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->hash = getmypid();
    }
    
    private function &_getConfig()
    {
        if(!isset($this->_Config)) {
            ClassRegistry::init('Config');
            $this->_Config = new Config();
        }
        return $this->_Config;
    }
    
    public function afterFind($results, $primary = false)
    {
        $result = parent::afterFind($results, $primary);
        
        ClassRegistry::init('Bank');
        $this->Bank = new Bank();
        
        // The tables is somehow that refrence cannot be queried by cake model on its own
        // First we make sure that the query contains Reminder then if recursive is set by the developer it reads the reference from Model specified by camel case of `type`
        if(!empty($result[0]['Reminder'])) {
            foreach($result as &$res) {
                if( empty($res['Reminder']['type']) ) {
                    continue;
                }
                switch($res['Reminder']['type']) {
                    case 'debt':
                        $res = array_merge($res, $this->Debt->read('*',$res['Reminder']['reference_id'])?: array() );
                        if(!isset($res['Debt'])) {
                            continue;
                        }
                        $res['Reminder']['name'] = __('reminder-'.$res['Reminder']['type'],true).": ".$res['Debt']['name']. (($res['Individual']['name'])? " ( {$res['Individual']['name']} )" : "");
                        break;
                    case 'installment':
                        $this->Installment->recursive = 1;
                        $this->Bank->recursive = 0;
                        $res = array_merge($res, $this->Installment->read('*',$res['Reminder']['reference_id'])?: array() );
                        if(!isset($res['Loan'])) {
                            continue;
                        }
                        $res['Reminder']['name'] = __('reminder-'.$res['Reminder']['type'],true).": ".$res['Loan']['name'].": ".$res['Installment']['due_date'];
                        $res['Loan'] = array_merge( $res['Loan'] , $this->Bank->read(null, $res['Loan']['bank_id']) );
                        break;
                    case 'check':
                        $res = array_merge($res, $this->Check->read('*',$res['Reminder']['reference_id'])?: array() );
                        if(!isset($res['Check'])) {
                            continue;
                        }
                        $res['Reminder']['name'] = 
                                __('reminder-'.$res['Reminder']['type'],true).": ".__($res['Check']['type'],true)
                                .($res['Check']['description']? ' - '.$res['Check']['description'] : '')
                                .($res['Individual']['name']? ' - '.$res['Individual']['name'] : '')
                                ;
                        break;
                    case 'note':
                        // $this->Note->contain( 'Transaction' );
                        $res = array_merge($res, $this->Note->read('*',$res['Reminder']['reference_id'])?: array());
                        if(!isset($res['Note'])) {
                            continue;
                        }
                        $res['Reminder']['name'] = __('reminder-'.$res['Reminder']['type'],true).": ".$res['Note']['subject'];
                        break;
                }
                #$res['User'] = Set::classicExtract( $this->User->read('*',$res['Reminder']['user_id']) , 'User' );
            }
        }
        return $result;
    }
    
    public function deleteRegarding($type, $refId)
    {
        $this->query( "DELETE FROM reminders WHERE type = '".$type."' AND reference_id = ".$refId );
    }
    
    public function addReminder($type, $date, $refId, $uid=null)
    {
        $fq = explode( ",",$this->_getConfig()->getValue('reminder', $type.'_freq', $uid) );
        $md = $this->_getConfig()->getValue('reminder', $type.'_medium', $uid);
        $num = 0;
        $this->inputConvertDate = false;
        
        switch($type) {
            case 'note':
                $date = explode(' ',$date);
                $exacttime = $date[1];
                $date = $date[0];
                break;
            case 'loan':
            case 'installment':
            case 'debt':
            case 'check':
            default:
                $exacttime = '08:00';
        }
        
        if(!empty($fq) && !empty($md)) {
            $fq = array_flip($fq);
            $pdate = new PersianDate();
            #$date = $pdate->pdate_format_reverse($date." 08:00:00");
            $date = $pdate->pdate_format_reverse($date);
            foreach($fq as $freq=>$v){
                $d = $date." ".(intval($freq)? "10:00:00" : $exacttime);
                if( !$time = Set::classicExtract( $this->query("SELECT IF(TIMESTAMP('".$d."') - INTERVAL ".intval($freq)." DAY > CURRENT_TIMESTAMP, TIMESTAMP('".$d."') - INTERVAL ".intval($freq)." DAY, 0) AS date") , "0.0.date") ) {
                    continue;
                }
                $data = array(
                    'type' => $type,
                    'reference_id' => $refId,
                    'medium' => $md,
                    'time' => $time
                );
                if($uid) {
                    $data['user_id'] = $uid;
                }
                $this->create();
                $this->save($data);
                $num++;
            }
        }
        
        return $num;
    }
    
    public function makeText($rem)
    {
        $txt = array('sms' => '', 'email' => '', 'subject'=> '' );
        switch($rem['Reminder']['type']) {
            case 'installment':
                $txt['sms'] =
                    'پرداخت قسط وام '.$rem['Loan']['name'].
                    (($rem['Loan']['Bank']['id'])? ("\n بانک: ". $rem['Loan']['Bank']['name']) : "") .
                    "\n مبلغ: ". $rem['Installment']['amount'] ." ریال".
                    "\n موعد: ". $rem['Installment']['due_date'];
                $txt['subject'] = "جیب::یادآوری پرداخت قسط";
                $txt['email'] =
                        "کاربر محترم<br />".
                        "بدینوسیله قسط وام ".$rem['Loan']['name']." به تاریخ ".$rem['Installment']['due_date']
                        .(($rem['Loan']['Bank']['id'])? (" در بانک ". $rem['Loan']['Bank']['name']) : "")
                        ." به مبلغ ".number_format($rem['Installment']['amount']) ." ریال"
                        ." یاد آوری میشود.<br />";
                break;
            case 'debt':
                $txt['sms'] = 
                    __($rem['Debt']['type'],true).': '.$rem['Debt']['name'].
                    (($rem['Individual']['id'])? (($rem['Debt']['type']=='credit')? "\nبه: " : "\nاز: "). $rem['Individual']['name']: "").
                    "\nمبلغ: ". abs($rem['Debt']['amount']) ." ریال".
                    "\nموعد: ". $rem['Debt']['due_date'];
                $txt['subject'] = "جیب::یادآوری ".(($rem['Debt']['type']=='credit')? "بدهی " : "طلب ");
                $txt['email'] =
                        ("کاربر محترم<br />").
                        ("بدینوسیله موعد"." ".__($rem['Debt']['type'],true).' "'.$rem['Debt']['name'].'" ').
                        (($rem['Individual']['id'])? (($rem['Debt']['type']=='credit')? "از " : "به "). $rem['Individual']['name']: "").
                        (" به مبلغ ". number_format(abs($rem['Debt']['amount'])) ." ریال ").
                        ("به تاریخ ". $rem['Debt']['due_date']." یادآوری میشود ");
                break;
            case 'check':
                $txt['sms'] = 
                    "چک ".(($rem['Check']['type']=='drawed')? " پرداختی" : " دریافتی").
                    (($rem['Bank']['id'])? ("\n بانک: ". $rem['Bank']['name']) : "") .
                    (($rem['Individual']['id'])? (($rem['Check']['type']=='drawed')? "\nبه: " : "\nاز: "). $rem['Individual']['name']: "").
                    "\nمبلغ: ". abs($rem['Check']['amount']) ." ریال".
                    "\nموعد: ". $rem['Check']['due_date'];
                $txt['subject'] = "جیب::چک یادآوری ".(($rem['Check']['type']=='drawed')? " پرداختی" : " دریافتی");
                $txt['email'] =
                        ("کاربر محترم<br />").
                        ("بدینوسیله موعد چک ".(($rem['Check']['type']=='drawed')? " پرداختی" : " دریافتی"))." ".
                        (($rem['Bank']['id'])? (" از بانک ". $rem['Bank']['name']) : "") .
                        (($rem['Individual']['id'])? (($rem['Check']['type']=='drawed')? " به " : " از "). $rem['Individual']['name']: "").
                        (" به مبلغ ". number_format(abs($rem['Check']['amount'])) ." ریال ").
                        ("به تاریخ ". $rem['Check']['due_date']." یادآوری میشود ");
                break;
            case 'note':
                $txt['sms'] = 
                    "\nموضوع: ". $rem['Note']['subject'].
                    "\nموعد: ". $rem['Note']['date'].
                    "\n".$rem['Note']['content'];
                $txt['subject'] = "جیب::یادآوری یادداشت شخصی";
                $txt['email'] = 
                    "کاربر محترم<br />یادداشت وارد شده شما به تاریخ ".$rem['Note']['created']." یادآوری میشود <br />".
                    " موضوع: ". $rem['Note']['subject'].
                    "<br />موعد: ". $rem['Note']['date'].
                    "<br />".$rem['Note']['content'];
                break;
        }
        
        $txt['sms'] .= "\njeeb.ir";
        
        return $txt;
    }
    
    public function getMarked()
    {
        $this->query( "UPDATE `reminders` SET owner = ".$this->hash." WHERE TIMESTAMP(time) <= CURRENT_TIMESTAMP AND owner IS NULL LIMIT 1" );
        return $this->find( 'all' , array(
            'conditions' => array(
                'owner' => $this->hash,
                'deleted ' => 0
            )
        ));
    }

    public function markedAndGet(){
        $this->query( "UPDATE `reminders` SET owner = ".$this->hash." WHERE owner IS NULL AND TIMESTAMP(time) <= CURRENT_TIMESTAMP AND TIMESTAMP(time) > (CURRENT_TIMESTAMP - INTERVAL 1 DAY)" );
        return $this->find( 'all' , array(
            'conditions' => array(
                'owner' => $this->hash,
                'deleted ' => 0
            )
        ));
    }

}

?>