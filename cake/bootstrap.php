<?php
/* SVN FILE: $Id: bootstrap.php 7666 2008-09-26 14:03:16Z gwoo $ */
/**
 * Basic Cake functionality.
 *
 * Core functions for including other source files, loading models and so forth.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 7666 $
 * @modifiedby		$LastChangedBy: gwoo $
 * @lastmodified	$Date: 2008-09-26 11:03:16 -0300 (Fri, 26 Sep 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
if (!defined('PHP5')) {
	define ('PHP5', (PHP_VERSION >= 5));
}
/**
 * Configuration, directory layout and standard libraries
 */
	if (!isset($bootstrap)) {
		require CORE_PATH . 'cake' . DS . 'basics.php';
		$TIME_START = getMicrotime();
		require CORE_PATH . 'cake' . DS . 'config' . DS . 'paths.php';
		require LIBS . 'object.php';
		require LIBS . 'inflector.php';
		require LIBS . 'configure.php';
	}
	require LIBS . 'cache.php';

	Configure::getInstance();

	$url = null;

	App::import('Core', array('Dispatcher'));
?>