<?php
/* SVN FILE: $Id: film_file_fixture.php 7645 2008-09-22 20:08:31Z DarkAngelBGE $ */
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
 * @subpackage		cake.tests.fixtures
 * @since			CakePHP(tm) v 1.2.0.4667
 * @version			$Revision: 7645 $
 * @modifiedby		$LastChangedBy: DarkAngelBGE $
 * @lastmodified	$Date: 2008-09-22 17:08:31 -0300 (Mon, 22 Sep 2008) $
 * @license			http://www.opensource.org/licenses/opengroup.php The Open Group Test Suite License
 */
/**
 * Short description for class.
 *
 * @package		cake.tests
 * @subpackage	cake.tests.fixtures
 */
class FilmFileFixture extends CakeTestFixture {
/**
 * name property
 * 
 * @var string 'FilmFile'
 * @access public
 */
	var $name = 'FilmFile';
/**
 * fields property
 * 
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'length' => 255)
	);
/**
 * records property
 * 
 * @var array
 * @access public
 */
	var $records = array(
		array('id' => 1, 'name' => 'one'),
		array('id' => 2, 'name' => 'two')
	);
}

?>