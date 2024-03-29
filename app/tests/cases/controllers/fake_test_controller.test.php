<?php
/**
 * Este archivo contiene un controlador generico (fake) para los casos de pruebas.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.tests.controllers
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */

 require_once(APP . 'app_controller.php');
 
/**
 * La clase para un para un controlador de prueba generico (fake).
 *
 * @package app.tests
 * @subpackage app.tests.controllers
 */
class FakeTestController extends AppController {

/**
 * name property
 *
 * @var string $name
 * @access public
 */
	var $name = 'FakeTestController';
	
/**
 * uses property
 *
 * @var mixed null
 * @access public
 */
	//var $uses = null;
	
}
?>