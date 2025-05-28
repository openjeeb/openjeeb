<?php

class BalanceResetShell extends Shell {

    var $uses = array('User', 'Account', 'Transaction');

    function main() {
        $users = $this->User->find('list',array('order'=>'id ASC'));
        foreach ($users as $userId=>$userEmail) {
            
            $debts = $this->Transaction->find('all', array(
                'fields' => array(
                    'Transaction.account_id',
                    'SUM(Transaction.amount) AS sum',
                    'Transaction.type'
                ),
                'conditions' => array(
                    'Transaction.type' => 'debt',
                    'Transaction.user_id' => $userId,
                ),
                'group' => 'Transaction.account_id'
                ));
            //print_r($debts);

            $credits = $this->Transaction->find('all', array(
                'fields' => array(
                    'Transaction.account_id',
                    'SUM(Transaction.amount) AS sum',
                    'Transaction.type'
                ),
                'conditions' => array(
                    'Transaction.type' => 'credit',
                    'Transaction.user_id' => $userId,
                ),
                'group' => 'Transaction.account_id'
                ));

            //get accounts
            $accounts1 = Set::extract('/Transaction/account_id', $debts);
            $accounts2 = Set::extract('/Transaction/account_id', $credits);
            $accounts = array_unique(array_merge($accounts1, $accounts2));
            //print_r($accounts);

            foreach ($accounts as $account) {
                //get account init balance
                $accountInitBalance = 0;
                $accountInitBalance = $this->Account->field('init_balance', array('id' => $account));

                //get account debts sum
                $debtSum = 0;
                foreach ($debts as $entry) {
                    if ($entry['Transaction']['account_id'] == $account AND $entry['Transaction']['type'] == 'debt') {
                        $debtSum = $entry['0']['sum'];
                    }
                }

                //get account credits sum
                $creditSum = 0;
                foreach ($credits as $entry) {
                    if ($entry['Transaction']['account_id'] == $account AND $entry['Transaction']['type'] == 'credit') {
                        $creditSum = $entry['0']['sum'];
                    }
                }

                //calculate balance
                $balance = ($accountInitBalance + $creditSum) - $debtSum;

                //update balance
                $this->Account->id = 0;
                $this->Account->id = $account;
                $this->Account->saveField('balance', $balance);

                //print
                echo $userId. ' : '. $userEmail . "\n";
                //echo 'account: ' . $account . "\n";
                //echo 'accountInitBalance: ' . $accountInitBalance . "\n";
                //echo 'debts: ' . $debtSum . "\n";
                //echo 'credits: ' . $creditSum . "\n";
                //echo 'balance: ' . $balance . "\n" . "\n" . "\n";
            }
        }
    }

}

?>
