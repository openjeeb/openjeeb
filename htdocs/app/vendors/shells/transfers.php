<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * CakePHP Shell
 * @author root
 */
class transfersShell extends Shell {

    public $uses = array( 'User' , 'Transaction' );
    public $task = array();

    function main() {
        
        $users = $this->User->find( 'list' , array(
            'order' => 'User.id ASC'
        ));
        
        foreach($users as $uid=>&$user) {
            $miss = $correct = 0;
            echo "$uid : ";
            $transactions = $this->Transaction->find( 'all', array(
                'fields' => 'Transaction.*',
                'conditions' => array(
                    'Transaction.user_id'=>$uid,
                    'expense_id'=>NULL,
                    'income_id'=>NULL,
                    'Transfer.id'=>NULL
                    ),
                'order' => 'Transaction.id'
                ));
            $cnt = count($transactions);
            for($i=0;$i<$cnt;$i++){
                //echo $transactions[$i]['Transaction']['id']." : ".$transactions[$i+1]['Transaction']['id'];
                if ($transactions[$i+1]['Transaction']['id']!=($transactions[$i]['Transaction']['id']+1) || ($transactions[$i]['Transaction']['amount']!=$transactions[$i+1]['Transaction']['amount'])){
                    $miss++;
                    continue;
                }
                $correct++;
                
                $this->Transaction->Transfer->create();
                $this->Transaction->Transfer->save(array(
                    'transaction_debt_id'=>$transactions[$i]['Transaction']['id'],
                    'transaction_credit_id'=>$transactions[$i+1]['Transaction']['id']
                ));
                
                $i++;
            }
            echo " $miss Mismatche(s) , $correct Fixes";
            echo "\n";
        }
        
    }

}
