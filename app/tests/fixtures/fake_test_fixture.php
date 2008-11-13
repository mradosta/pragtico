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
class FakeTestFixture extends CakeTestFixture {

/**
 * El nombre de este Fixture.
 *
 * @var string
 * @access public
 */
	var $name = 'FakeTestFixture';


/**
 * La definicion de la tabla.
 *
 * @var array
 * @access public
 */
    var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'campo_string' => array('type' => 'string', 'length' => 50, 'null' => false),
        'campo_text' => array('type' => 'text', 'null' => false),
        'campo_integer' => array('type' => 'integer', 'null' => false),
        'campo_decimal' => array('type' => 'float', 'length' => '10,3', 'null' => false),
        'campo_fecha' => array('type' => 'date', 'null' => false),
        'campo_fechahora' => array('type' => 'datetime', 'null' => false),
        'created' => array('type' => 'datetime', 'null' => false),
        'modified' => array('type' => 'datetime', 'null' => false),
        'user_id' => array('type' => 'integer', 'null' => false),
        'role_id' => array('type' => 'integer', 'null' => false),
        'group_id' => array('type' => 'integer', 'null' => false),
        'permissions' => array('type' => 'integer', 'null' => false),
    );


/**
 * Los registros.
 *
 * @var array
 * @access public
 */
    var $records = array(
        array ('id' 		=> 1,
        'campo_string' 		=> 'Primer valor string',
        'campo_text' 		=> 'Primer valor text',
        'campo_integer' 	=> '1',
        'campo_decimal' 	=> '11.150',
        'campo_fecha' 		=> '2007-03-18',
        'campo_fechahora'	=> '2007-03-18 10:41:31',
        'created' 			=> '2007-03-18 10:39:23',
        'modified' 			=> '2007-03-18 10:41:31',
        'user_id' 			=> '1',
        'role_id' 			=> '1',
        'group_id' 			=> '1',
        'permissions' 		=> '496'),
        
        array ('id' 		=> 2,
        'campo_string' 		=> 'Segundo valor string',
        'campo_text' 		=> 'Segundo valor text',
        'campo_integer' 	=> '2',
        'campo_decimal' 	=> '12.250',
        'campo_fecha' 		=> '2007-04-19',
        'campo_fechahora'	=> '2007-04-19 11:41:31',
        'created' 			=> '2007-03-18 10:39:23',
        'modified' 			=> '2007-03-18 10:41:31',
        'user_id' 			=> '1',
        'role_id' 			=> '1',
        'group_id' 			=> '1',
        'permissions' 		=> '496'),
        
        array ('id' 		=> 3,
        'campo_string' 		=> 'Tercer valor string',
        'campo_text' 		=> 'Tercer valor text',
        'campo_integer' 	=> '3',
        'campo_decimal' 	=> '13.350',
        'campo_fecha' 		=> '2008-08-19',
        'campo_fechahora'	=> '2008-08-19 21:41:31',
        'created' 			=> '2007-03-18 10:39:23',
        'modified' 			=> '2007-03-18 10:41:31',
        'user_id' 			=> '1',
        'role_id' 			=> '1',
        'group_id' 			=> '1',
        'permissions' 		=> '496')
    );
}

?>