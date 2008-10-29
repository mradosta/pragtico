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
 * @subpackage		app.tests.cases.behaviors
 * @since			Pragtico v 1.0.0
 * @version			$Revision: 54 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2008-10-23 23:14:28 -0300 (Thu, 23 Oct 2008) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */

require_once(APP . "tests" . DS . "cases" . DS . "models" . DS . "fake.test.php");


/**
 * Caso de prueba para el Behavior Util.
 *
 * @package app.tests
 * @subpackage app.tests.cases.behaviors
 */
class UtilTestCase extends CakeTestCase {

/**
 * Model que usare en este caso de prueba.
 *
 * @var array
 * @access public
 */
    var $model;


/**
 * Fixtures asociados a este caso de prueba.
 *
 * @var array
 * @access public
 */
	var $fixtures = array('fake_test');


/**
 * Metodo que se ejecuta antes de cada test.
 *
 * @access public
 */
	function startTest() {
		$this->model =& new FakeTest();

		/**
		* Asocio el behavior que debo utilizar.
		*/
		$this->model->Behaviors->attach('Util');
	}


/**
 * Pruebo la funcion de dateAdd.
 *
 * @access public
 */
	function testDateAdd() {
		$fecha = "2005-12-10 12:40";
		$result = $this->model->dateAdd($fecha);
		$expected = "2005-12-11 12:40:00";
		$this->assertEqual($result, $expected);
	
		$fecha = "2005-12-10 12:40:21";
		$result = $this->model->dateAdd($fecha, "y");
		$expected = "2006-12-10 12:40:21";
		$this->assertEqual($result, $expected);

		$fecha = "2005-12-28 12:40:21";
		$result = $this->model->dateAdd($fecha, "d", "5");
		$expected = "2006-01-02 12:40:21";
		$this->assertEqual($result, $expected);
	}

	
/**
 * Pruebo la funcion de dateDiff.
 *
 * @access public
 */
	function testDateDiff() {
		$fechaDesde = "2005-12-10 12:40";
		$fechaHasta = "2005-12-10 12:42:02";
		$result = $this->model->dateDiff($fechaDesde, $fechaHasta);
		$expected = array(	"dias" 		=> "0",
							"horas" 	=> "0",
							"minutos" 	=> "2",
							"segundos" 	=> "2");
		$this->assertEqual($result, $expected);
		
		$fechaDesde = "2005-12-10";
		$fechaHasta = "2006-12-10";
		$result = $this->model->dateDiff($fechaDesde, $fechaHasta);
		$expected = array(	"dias" 		=> "365",
							"horas" 	=> "0",
							"minutos" 	=> "0",
							"segundos" 	=> "0");
		$this->assertEqual($result, $expected);
	
		/**
		* Pruebo con ano bisiesto (2008).
		*/
		$fechaDesde = "2007-12-10";
		$fechaHasta = "2008-12-10";
		$result = $this->model->dateDiff($fechaDesde, $fechaHasta);
		$expected = array(	"dias" 		=> "366",
							"horas" 	=> "0",
							"minutos" 	=> "0",
							"segundos" 	=> "0");
		$this->assertEqual($result, $expected);
		
		$fechaDesde = "2007-12-10 10:12:45";
		$fechaHasta = "2008-12-10 23:22:52";
		$result = $this->model->dateDiff($fechaDesde, $fechaHasta);
		$expected = array(	"dias" 		=> "366",
							"horas" 	=> "13",
							"minutos" 	=> "10",
							"segundos" 	=> "7");
		$this->assertEqual($result, $expected);
		
		$fechaDesde = "2007-12-10";
		$fechaHasta = "2008-12-10 23:22:52";
		$result = $this->model->dateDiff($fechaDesde, $fechaHasta);
		$expected = array(	"dias" 		=> "366",
							"horas" 	=> "23",
							"minutos" 	=> "22",
							"segundos" 	=> "52");
		$this->assertEqual($result, $expected);

		$fechaDesde = "2007-12-10";
		$fechaHasta = "2008-12-10";
		$result = $this->model->dateDiff($fechaDesde, $fechaHasta);
		$expected = array(	"dias" 		=> "366",
							"horas" 	=> "0",
							"minutos" 	=> "0",
							"segundos" 	=> "0");
		$this->assertEqual($result, $expected);
	}

	
/**
 * Metodo que se ejecuta despues de cada test.
 *
 * @access public
 */
	function endTest() {
		unset($this->model);
	}
}

?>