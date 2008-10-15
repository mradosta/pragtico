<?php
/* SVN FILE: $Id: tests_apps_posts_controller.php 7654 2008-09-24 11:52:29Z DarkAngelBGE $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) Tests <https://trac.cakephp.org/wiki/Developement/TestSuite>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 *  Licensed under The Open Group Test Suite License
 *  Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				https://trac.cakephp.org/wiki/Developement/TestSuite CakePHP(tm) Tests
 * @package			cake.tests
 * @subpackage		cake.tests.test_app.plugins.test_plugin.views.helpers
 * @since			CakePHP(tm) v 1.2.0.4206
 * @version			$Revision: 7654 $
 * @modifiedby		$LastChangedBy: DarkAngelBGE $
 * @lastmodified	$Date: 2008-09-24 08:52:29 -0300 (Wed, 24 Sep 2008) $
 * @license			http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
class TestsAppsPostsController extends AppController {
	var $name = 'TestsAppsPosts';
	var $uses = array('Post');
	var $viewPath = 'tests_apps';

	function add() {
		$data = array(
			'Post' => array(
				'title' => 'Test article',
				'body' => 'Body of article.'
			)
		);
		$this->Post->save($data);

		$this->set('posts', $this->Post->find('all'));
		$this->render('index');
	}
	
	function url_var() {
		$this->set('params', $this->params);
		$this->render('index');
	}
	
	function post_var() {
		$this->set('data', $this->data);
		$this->render('index');
	}

}
?>