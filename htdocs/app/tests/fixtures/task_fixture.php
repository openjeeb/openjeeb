<?php
/* Task Fixture generated on: 2016-01-14 15:48:07 : 1452773887 */
class TaskFixture extends CakeTestFixture {
	var $name = 'Task';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'title' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'sdate' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'edate' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'fk_tasks_1_idx' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

	var $records = array(
		array(
			'id' => 1,
			'title' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'user_id' => 1,
			'sdate' => '2016-01-14 15:48:07',
			'edate' => '2016-01-14 15:48:07'
		),
	);
}
