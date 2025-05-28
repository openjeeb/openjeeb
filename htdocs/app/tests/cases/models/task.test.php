<?php
/* Task Test cases generated on: 2016-01-14 15:48:07 : 1452773887*/
App::import('Model', 'Task');

class TaskTestCase extends CakeTestCase {
	var $fixtures = array('app.task', 'app.user', 'app.user_group');

	function startTest() {
		$this->Task =& ClassRegistry::init('Task');
	}

	function endTest() {
		unset($this->Task);
		ClassRegistry::flush();
	}

}
