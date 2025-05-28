<?php

class ResetOldUsersShell extends Shell {

    var $uses = array(
            'Account',
            'Transaction',
            'ExpenseCategory',
            'Check',
            'Debt',
            'Loan',
            'Note',
            'Income',
            'IncomeType',
            'Investment',
            'Individual',
            'Budget',
            'Reminder',
            'Tag',
            'User',
            'ServiceTransaction'
        );


    function main() {

        $users = $this->User->find('all',[
            'conditions' => [
                'expire_date <= CURRENT_DATE - INTERVAL 2 YEAR',
                'force_init' => '0'
            ],
            'order' => 'User.id ASC'
        ]);

        $dataSource = $this->User->getDataSource();

        foreach($users as $i=>$user){
            $userId = $user['User']['id'];
            if( $this->ServiceTransaction->remainingCredit('sms', $userId) ) {
                continue;
            }

            $dataSource->begin( $this->User );

            //delete the accounts
            if ( !$this->Account->deleteAll( array( 'Account.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the transactions
            if ( !$this->Transaction->deleteAll( array( 'Transaction.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the expense categories
            if ( !$this->ExpenseCategory->deleteAll( array( 'ExpenseCategory.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the income types
            if ( !$this->IncomeType->deleteAll( array( 'IncomeType.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the checks
            if ( !$this->Check->deleteAll( array( 'Check.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the debts
            if ( !$this->Debt->deleteAll( array( 'Debt.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the loans
            if ( !$this->Loan->deleteAll( array( 'Loan.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the notes
            if ( !$this->Note->deleteAll( array( 'Note.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the investments
            if ( !$this->Investment->deleteAll( array( 'Investment.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the individuals
            if ( !$this->Individual->deleteAll( array( 'Individual.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the Budget
            if ( !$this->Budget->deleteAll( array( 'Budget.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the Reminder
            if ( !$this->Reminder->deleteAll( array( 'Reminder.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            //delete the Tag
            if ( !$this->Tag->deleteAll( array( 'Tag.user_id' => $userId ) ) ) {
                $dataSource->rollback( $this->User );
                return false;
            }

            $this->User->id = $userId;
            $this->User->saveField('force_init',1);

            $dataSource->commit( $this->User );

            $this->out("\r".$userId,0);

        }

        $this->out("\nDone");

    }

}

?>
