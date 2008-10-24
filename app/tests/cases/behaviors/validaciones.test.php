<?php
/**
 * Este archivo contiene un caso de prueba.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.tests
 * @since			Pragtico v 1.0.0
 * @version			$Revision: 54 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2008-10-23 23:14:28 -0300 (Thu, 23 Oct 2008) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */

App::import('Behavior', 'validaciones');
App::import('Core', 'ConnectionManager');

/**
 * La clase encapsula el caso de prueba.
 *
 * @package		pragtico
 * @subpackage	app.tests.cases.behaviors
 */
class BarTestModel extends CakeTestModel {

	/**
	 * El nombre de este Model.
	 *
	 * @var string
	 * @access public
	 */
	var $name = 'BarTestModel';
	
	/**
	 * Indico el nombre de la tabla (fisica) que debe utilizar.
	 *
	 * @var array
	 * @access public
	 */
	var $useTable = 'bar_test_fixtures';

	/**
	 * Los Behaviors asociados al model.
	 *
	 * @var array
	 * @access public
	 */
	var $actsAs = array('Validaciones');
}


/**
 * Caso de prueba para el Behavior Validaciones.
 *
 * @package app.tests
 * @subpackage app.tests.cases.behaviors
 */
class ValidacionesTestCase extends CakeTestCase {

	/**
	 * Fixtures asociados a este caso de prueba.
	 *
	 * @var array
	 * @access public
	 */
	var $fixtures = array('bar_test');

	/**
	 * Metodo que se ejecuta antes de cada test.
	 *
	 * @access public
	 */
	function startTest() {
		$this->Model =& new BarTestModel();
	}
	
	function testValidCuitCuil() {

		$this->Model->validate = array(
			'title' => array(
				array(
					'rule'	=> 'validCuitCuil',
					'message'	=>'La Cuit no es valida.')
			)
		);

		/**
		 * Pruebo con una cuit valida.
		 */
		$data = array('BarTestModel' => array(
			'title' => '20-27959940-4'
		));
		$this->Model->create($data);
		$result = $this->Model->validates();
		$this->assertTrue($result);

		/**
		 * Pruebo con una cuit valida.
		 */
		$data = array('BarTestModel' => array(
			'title' => '20-11363961-0'
		));
		$this->Model->create($data);
		$result = $this->Model->validates();
		$this->assertTrue($result);

		/**
		 * Pruebo con una cuit no valida.
		 * Pruebo que el mensaje de error sea el correcto.
		 */
		$data = array('BarTestModel' => array(
			'title' => '20-27959940-5'
		));
		$this->Model->create($data);
		$result = $this->Model->validates();
		$this->assertFalse($result);
		
		$result = $this->Model->validationErrors;
		$expected = array('title' => 'La Cuit no es valida.');
		$this->assertEqual($result, $expected);
		
	}
	
	/**
	 * Method executed after each test
	 *
	 * @access public
	 */
	function endTest() {
		unset($this->Model);
		ClassRegistry::flush();
	}

}

?>