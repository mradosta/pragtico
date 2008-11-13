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
class BancoFixture extends CakeTestFixture {


/**
 * El nombre de este Fixture.
 *
 * @var array
 * @access public
 */
    var $name = 'Banco';


/**
 * La definicion de la tabla.
 *
 * @var array
 * @access public
 */
    var $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'primary'),
        'codigo' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '3', 'key' => 'unique'),
        'nombre' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '150', 'key' => 'unique'),
        'direccion' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '50'),
        'telefono' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '50'),
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
            'codigo' => '123',
            'nombre' => 'Banco Prueba 1',
            'direccion' => '',
            'telefono' => '',
            'observacion' => '',
            'created' => '2008-09-19 17:15:04',
            'modified' => '2008-10-12 19:36:30',
            'user_id' => '1',
            'role_id' => '1',
            'group_id' => '1',
            'permissions' => '500',
        ),
        array(
            'id' => '2',
            'codigo' => '432',
            'nombre' => 'Banco Prueba 2',
            'direccion' => '',
            'telefono' => '',
            'observacion' => '',
            'created' => '2008-10-12 21:34:38',
            'modified' => '2008-10-12 22:37:19',
            'user_id' => '1',
            'role_id' => '1',
            'group_id' => '1',
            'permissions' => '500',
        )
    );
}

?>