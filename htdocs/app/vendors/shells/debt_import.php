<?php

class DebtImportShell extends Shell {

    var $uses = array( 'Debt', 'DebtSettlement', 'Transaction' );

    function main() {
        //get debts
        $this->Debt->recursive = -1;
        $debts = $this->Debt->find( 'all', array(
            'fields' => array(
                'Debt.id', 'Debt.amount', 'Debt.status', 'Debt.clear_transaction_id', 'Debt.user_id', 'Debt.modified'
            )
            ) );

        foreach ( $debts as $debt ) {
            echo 'Debt: ' . $debt['Debt']['id'] . "\n";
            //check if the debt is settled
            if($debt['Debt']['status']=='done') {
                //check if clear transaction exists
                $transactionId = $debt['Debt']['clear_transaction_id'];
                $this->Transaction->id = $transactionId;
                if ( !$this->Transaction->exists() ) {
                    $transactionId=null;
                }
                $this->DebtSettlement->create();
                $this->DebtSettlement->save( array(
                    'DebtSettlement' => array(
                        'amount' => abs( $debt['Debt']['amount'] ),
                        'debt_id' => $debt['Debt']['id'],
                        'user_id' => $debt['Debt']['user_id'],
                        'transaction_id' => $transactionId,
                        'created' => $debt['Debt']['modified'],
                        'modified' => $debt['Debt']['modified'],
                    )
                ));
            }
        }
    }

}

?>
