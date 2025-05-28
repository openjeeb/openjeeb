<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * CakePHP Shell
 * @author root
 */
class sortShell extends Shell {

    public $uses = array( 'User' , 'Account' , 'ExpenseCategory', 'Individual', 'IncomeType');
    public $task = array();

    function main()
    {
        switch($this->args[0]) {
            case 'account':
                $this->sortAccount();
                break;
            
            case 'expensecategory':
                $this->sortExpenseCategory();
                break;
            
            case 'incomecategory':
                $this->sortIncomeCategory();
                break;
            
            case 'individual':
                $this->sortIndividual();
                break;
            
        }
    }
    
    function sortAccount()
    {
        $this->User->recursive = -1;
        $this->Account->recursive = -1;
        foreach($this->User->find('list', array('fields'=>'id', 'order'=> 'id ASC')) as $user) {
            print "User $user: ";
            
            $accounts = $this->Account->find(
                    'all',
                    array(
                        'fields' => 'id',
                        'conditions' => array('user_id'=>$user),
                        'order'=>'Account.name'
                        )
                    );
            print count($accounts)." Accounts : ";
            foreach($accounts as $k=>&$acc) {
                $acc['Account']['sort'] = $k;
            }
            $this->Account->saveAll($accounts);
            print "Done\n";
        }
    }
    
    function sortExpenseCategory()
    {
        $this->User->recursive = -1;
        $this->ExpenseCategory->recursive = -1;
        foreach($this->User->find('list', array('fields'=>'id', 'order'=> 'id ASC')) as $user) {
            print "User $user: ";
            
            $data = $this->ExpenseCategory->find(
                    'all',
                    array(
                        'fields' => 'id,sort',
                        'conditions' => array('user_id'=>$user),
                        'order'=>'ExpenseCategory.name'
                        )
                    );
            print count($data)." Categories : ";
            foreach($data as $k=>&$acc) {
                $acc['ExpenseCategory']['sort'] = $k;
            }
            $this->ExpenseCategory->saveAll($data, array('validate'=>false));
            print "Done\n";
        }
    }
    
    function sortIncomeCategory()
    {
        $this->User->recursive = -1;
        $this->IncomeType->recursive = -1;
        foreach($this->User->find('list', array('fields'=>'id', 'order'=> 'id ASC')) as $user) {
            print "User $user: ";
            
            $data = $this->IncomeType->find(
                    'all',
                    array(
                        'fields' => 'id,sort',
                        'conditions' => array('user_id'=>$user),
                        'order'=>'name'
                        )
                    );
            print count($data)." Categories : ";
            foreach($data as $k=>&$acc) {
                $acc['IncomeType']['sort'] = $k;
            }
            $this->IncomeType->saveAll($data, array('validate'=>false));
            print "Done\n";
        }
    }
    
    function sortIndividual()
    {
        $this->User->recursive = -1;
        $this->Individual->recursive = -1;
        foreach($this->User->find('list', array('fields'=>'id', 'order'=> 'id ASC')) as $user) {
            print "User $user: ";
            $data = $this->Individual->find(
                    'all',
                    array(
                        'fields' => 'id,sort',
                        'conditions' => array('user_id'=>$user),
                        'order'=>'Individual.id'
                        )
                    );
            print intval(count($data))." Individuals : ";
            if(!$data){
                print "Skip\n";
                continue;
            }
            foreach($data as $k=>&$acc) {
                $acc['Individual']['sort'] = $k;
            }
            $this->Individual->saveAll($data, array('validate'=>false));
            print "Done\n";
        }
    }

}
