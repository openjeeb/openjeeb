<?php
class TempUser extends AppModel {
	var $name = 'TempUser';
        
        function getReferenceUserId($id) {
            $this->recursive=-1;
            return $this->field('reference_user_id', array('id'=>intval($id)));
        }
}
?>