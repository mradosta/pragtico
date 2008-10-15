<?php
class UnixGroup extends AppModel {

	var $name = 'UnixGroup';
	var $useTable = 'unix_groups';

	var $hasAndBelongsToMany = array(
			'User' => array('className' => 'User',
						'joinTable' => 'unix_groups_users',
						'foreignKey' => 'unix_group_id',
						'associationForeignKey' => 'user_id',
						'unique' => false,
						'conditions' => '',
						'fields' => '',
						'order' => '',
						'limit' => '',
						'offset' => '',
						'finderQuery' => '',
						'deleteQuery' => '',
						'insertQuery' => ''
			)
	);

}
?>