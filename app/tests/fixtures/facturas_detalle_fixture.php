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
class FacturasDetalleFixture extends CakeTestFixture {


/**
 * El nombre de este Fixture.
 *
 * @var array
 * @access public
 */
    var $name = 'FacturasDetalle';


/**
 * La definicion de la tabla.
 *
 * @var array
 * @access public
 */
    var $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'primary'),
        'factura_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'coeficiente_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'subtotal' => array('type' => 'float', 'null' => false, 'default' => '', 'length' => '10,3'),
        'valor' => array('type' => 'float', 'null' => false, 'default' => '', 'length' => '10,3'),
        'total' => array('type' => 'float', 'null' => false, 'default' => '', 'length' => '10,3'),
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
    );
}

?>