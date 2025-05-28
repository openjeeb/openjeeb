<?php

App::import('Lib', 'Localized.IrValidation');

class User extends AppModel {

    var $name = 'User';
    var $displayField = 'email';
    var $actsAs = array('Acl' => array('type' => 'requester'));
    var $outputConvertDate = false;
    var $validate = array(
        'email' => array(
            'duplicate' => array(
                'rule' => 'isUnique',
                'message' => 'آدرس ایمیل وارد شده هم اکنون در سیستم موجود میباشد.',
            ),
            'email' => array(
                'rule' => array('email'),
                'message' => 'لطفا آدرس ایمیل خود را وارد کنید.',
            ),
        ),
        'mobile' => array(
            'duplicate' => array(
                'rule' => 'isUnique',
                'message' => 'شماره موبایل وارد شده هم اکنون در سیستم موجود است.',
            ),
            /*'email' => array(
                'rule' => array('email'),
                'message' => 'لطفا آدرس ایمیل خود را وارد کنید.',
            ),*/
        ),
        'password' => array(
            'notempty' => array(
                'rule' => array('notempty'),
            ),
        ),
        'cell' => array(
            'valid' => array(
                'rule' => '/^0{1}((((910)|(911)|(912)|(913)|(914)|(915)|(916)|(917)|(918)|(919)|(990)|(991)|(901)|(902)|(903)|(904)|(905)|(930)|(933)|(934)|(935)|(936)|(937)|(938)|(939)|(940)|(941)|(932)|(920)|(921)|(922)){1}[0-9]{7})|((99999|99998|99911|99912|99913|99914){1}[0-9]{5})|(9981[0-9]{6})|(99[012][0-9]{7}))$/i',
                'message' => 'شماره موبایل وارد شده صحیح نیست',
                'allowEmpty' => true
            )
        )
    );
    var $belongsTo = array(
        'UserGroup' => array(
            'className' => 'UserGroup',
            'foreignKey' => 'user_group_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['user_group_id'])) {
            $groupId = $this->data['User']['user_group_id'];
        } else {
            $groupId = $this->field('User.user_group_id');
        }
        if (!$groupId) {
            return null;
        } else {
            return array('UserGroup' => array('id' => $groupId));
        }
    }

    function bindNode($user) {
        return array('model' => 'UserGroup', 'foreign_key' => $user['User']['user_group_id']);
    }
    
    function extend($id, $amount) {
        //get user
        $this->recursive = -1;
        $user = $this->read(null, $id);

        //generate the new expire date
        if ($user['User']['expire_date'] < date('Y-m-d', time())) {
            $expireDate = date('Y-m-d', strtotime($amount));
        } else {
            $expireDate = date("Y-m-d", strtotime(date("Y-m-d", strtotime($user['User']['expire_date'])) . $amount));
        }

        //check for faults
        if ($expireDate < date('Y-m-d', time())) {
            return false;
        }

        //save
        $this->id = $id;
        $this->outputConvertDate = false;
        $this->inputConvertDate = false;
        $result = $this->save([
            'expire_date' => $expireDate,
            'force_init' => 0
        ]);

        if( !$result ) {
            return false;
        }

        return true;
    }

    function getEmailbyId($id) {
        return $this->field('email', array('id' => intval($id)));
    }

    function getReferenceUserId($id) {
        $this->recursive = -1;
        return $this->field('reference_user_id', array('id' => intval($id)));
    }

}

?>