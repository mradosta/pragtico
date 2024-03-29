<?php
/**
 * Este archivo contiene un model generico (fake) para los casos de pruebas.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.tests.models
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
 
App::import("Model", array("Liquidacion"));
 
/**
 * La clase para un para un caso de prueba generico (fake).
 *
 * @package app.tests
 * @subpackage app.tests.models
 */
class LiquidacionTest_borrar extends Liquidacion {

	
/**
 * Indico en nombre del model.
 *
 * @var string
 * @access public
 */
	var $name = 'Liquidacion';
	
	
/**
 * Indico que use la conexion de prueba (test).
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'test_suite';


}
?>