<?php
/**
* Este archivo contiene los datos de un fixture para los casos de prueba.
*
* PHP versions 5
*
* @filesource
* @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
* @link			http://www.pragmatia.com
* @package			pragtico
* @subpackage		app.tests.fixtures
* @since			Pragtico v 1.0.0
* @version			$Revision: 54 $
* @modifiedby		$LastChangedBy: mradosta $
* @lastmodified	$Date: 2008-10-23 23:14:28 -0300 (Thu, 23 Oct 2008) $
* @author      	Martin Radosta <mradosta@pragmatia.com>
*/
/**
* La clase para un fixture para un caso de prueba.
*
* @package app.tests
* @subpackage app.tests.fixtures
*/
class SiapFixture extends CakeTestFixture {


/**
* El nombre de este Fixture.
*
* @var array
* @access public
*/
    var $name = 'Siap';


/**
* La definicion de la tabla.
*
* @var array
* @access public
*/
    var $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'primary'),
        'version' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '10', 'key' => 'unique'),
        'observacion' => array('type' => 'text', 'null' => false, 'default' => ''),
        'created' => array('type' => 'datetime', 'null' => false),
        'modified' => array('type' => 'datetime', 'null' => false),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'role_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'group_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'permissions' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
    );


/**
* Los registros.
*
* @var array
* @access public
*/
    var $records = array(
        array(
            'id' => '1',
            'version' => '27',
            'observacion' => '',
            'created' => '2008-05-05 15:22:07',
            'modified' => '2008-05-05 15:34:22',
            'user_id' => '1',
            'role_id' => '1',
            'group_id' => '0',
            'permissions' => '500',
        ),
        array(
            'id' => '2',
            'version' => '28',
            'observacion' => 'esto lo duplique',
            'created' => '2008-10-28 15:17:48',
            'modified' => '2008-10-28 15:17:48',
            'user_id' => '1',
            'role_id' => '1',
            'group_id' => '0',
            'permissions' => '496',
        ),
    );
}

?>