<?php
/**
 * Este archivo contiene un caso de prueba.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.tests.cases.components
 * @since           Pragtico v 1.0.0
 * @version         $Revision: 54 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2008-10-23 23:14:28 -0300 (Thu, 23 Oct 2008) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */


App::import('Component', array('Paginador', 'Session', 'Util'));

require_once(APP . "tests" . DS . "cases" . DS . "controllers" . DS . "fake_test_controller.test.php");
require_once(APP . "tests" . DS . "cases" . DS . "models" . DS . "fake.test.php");


/**
 * Caso de prueba para el Component Paginador.
 *
 * @package app.tests
 * @subpackage app.tests.cases.components
 */
class PaginadorComponentTestCase extends CakeTestCase {

/**
 * El component que probare.
 *
 * @var array
 * @access public
 */
    var $PaginadorComponentTest;
    
    
/**
 * Controller que usare en este caso de prueba.
 *
 * @var array
 * @access public
 */
    var $controller;


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
    	$this->PaginadorComponentTest =& new PaginadorComponent();
    	$this->PaginadorComponentTest->Util = new UtilComponent();
    	$this->controller = new FakeTestController();
    	$this->controller->FakeTest = new FakeTest();
    	$this->controller->Session = &new SessionComponent();

    	/**
    	* Me aseguro que no existan datos en la session.
    	*/
    	$this->controller->Session->destroy();
    }


/**
 * Pruebo la generacion de condiciones.
 *
 * @access public
 */
    function testGenerarCondicion() {
    	$this->controller->data['Condicion']['FakeTest-id'] = "1";
    	$this->controller->data['Condicion']['FakeTest-campo_string'] = "texto string";
    	$this->controller->data['Condicion']['FakeTest-campo_text'] = "texto text";
    	$this->controller->data['Condicion']['FakeTest-campo_integer'] = "145";
    	$this->controller->data['Condicion']['FakeTest-campo_decimal'] = "145.456";
    	$this->controller->data['Condicion']['FakeTest-campo_fecha'] = "21/10/2008";
    	$this->controller->data['Condicion']['FakeTest-campo_fechahora'] = "22/10/2008 18:45:43";
    	$this->controller->action = "index";
    	
    	$this->PaginadorComponentTest->startup(&$this->controller);
		$expected = array(
			"FakeTest.id" 						=> "1",
			"FakeTest.campo_string like" 		=> "%texto string%",
			"FakeTest.campo_text like" 			=> "%texto text%",
			"FakeTest.campo_integer" 			=> "145",
			"FakeTest.campo_decimal" 			=> "145.456",
			"FakeTest.campo_fecha" 				=> "2008-10-21",
			"FakeTest.campo_fechahora" 			=> "2008-10-22 18:45:43"
		);
		
		$result = $this->PaginadorComponentTest->generarCondicion();
		$this->assertEqual($result, $expected);


		$result = $this->controller->Session->read("filtros." . $this->controller->name . "." . $this->controller->action);
		$expected = array("condiciones" => $expected, "valoresLov" => array());
		$this->assertEqual($result, $expected);


		$expected = array(
			"FakeTest-id" 						=> "1",
			"FakeTest-campo_string" 			=> "texto string",
			"FakeTest-campo_text" 				=> "texto text",
			"FakeTest-campo_integer" 			=> "145",
			"FakeTest-campo_decimal" 			=> "145.456",
			"FakeTest-campo_fecha" 				=> "21/10/2008",
			"FakeTest-campo_fechahora" 			=> "22/10/2008 18:45:43"
		);
		$result = $this->controller->data;
		$this->assertEqual($result, array("Condicion"=>$expected));
	}
	

/**
 * Pruebo la generacion de condiciones de rango de campos fecha.
 *
 * @access public
 */
    function testGenerarCondicionRangoFecha() {
    	$this->controller->data['Condicion']['FakeTest-id'] = "1";
    	$this->controller->data['Condicion']['FakeTest-campo_fecha__desde'] = "21/10/2008";
    	$this->controller->data['Condicion']['FakeTest-campo_fecha__hasta'] = "25/10/2008";
    	$this->controller->action = "index";
    	
    	$this->PaginadorComponentTest->startup(&$this->controller);
		$expected = array(
			"FakeTest.id" 						=> "1",
			"FakeTest.campo_fecha >=" 			=> "2008-10-21",
			"FakeTest.campo_fecha <=" 			=> "2008-10-25"
		);
		
		$result = $this->PaginadorComponentTest->generarCondicion();
		$this->assertEqual($result, $expected);

		
		$result = $this->controller->Session->read("filtros." . $this->controller->name . "." . $this->controller->action);
		$expected = array("condiciones" => $expected, "valoresLov" => array());
		$this->assertEqual($result, $expected);

		
		$expected = array(
			"FakeTest-id" 								=> "1",
			"FakeTest-campo_fecha__desde" 			=> "21/10/2008",
			"FakeTest-campo_fecha__hasta" 			=> "25/10/2008"
		);
		$result = $this->controller->data;
		$this->assertEqual($result, array("Condicion"=>$expected));
	}


/**
 * Pruebo la generacion de condiciones de rango de campos fechaHora.
 *
 * @access public
 */
    function testGenerarCondicionRangoFechaHora() {
    	$this->controller->data['Condicion']['FakeTest-campo_fechahora__desde'] = "21/10/2008 22:34:56";
    	$this->controller->data['Condicion']['FakeTest-campo_fechahora__hasta'] = "25/10/2008 12:34:56";
    	$this->controller->action = "index";
    	
    	$this->PaginadorComponentTest->startup(&$this->controller);
		$expected = array(
			"FakeTest.campo_fechahora >=" 			=> "2008-10-21 22:34:56",
			"FakeTest.campo_fechahora <=" 			=> "2008-10-25 12:34:56"
		);
		
		$result = $this->PaginadorComponentTest->generarCondicion();
		$this->assertEqual($result, $expected);


		$result = $this->controller->Session->read("filtros." . $this->controller->name . "." . $this->controller->action);
		$expected = array("condiciones" => $expected, "valoresLov" => array());
		$this->assertEqual($result, $expected);

		$expected = array(
			"FakeTest-campo_fechahora__desde" 			=> "21/10/2008 22:34:56",
			"FakeTest-campo_fechahora__hasta" 			=> "25/10/2008 12:34:56"
		);
		$result = $this->controller->data;
		$this->assertEqual($result, array("Condicion"=>$expected));
	}


/**
 * Pruebo la generacion de condiciones de rango de campos fecha y fechaHora.
 *
 * @access public
 */
    function testGenerarCondicionRangoFechaHoraMixto() {
    	$this->controller->data['Condicion']['FakeTest-campo_fecha__desde'] = "21/10/2008";
    	$this->controller->data['Condicion']['FakeTest-campo_fechahora__hasta'] = "25/10/2008 12:34:56";
    	$this->controller->action = "index";
    	
    	$this->PaginadorComponentTest->startup(&$this->controller);
		$expected = array(
			"FakeTest.campo_fecha >=" 			=> "2008-10-21",
			"FakeTest.campo_fechahora <=" 			=> "2008-10-25 12:34:56"
		);
		
		$result = $this->PaginadorComponentTest->generarCondicion();
		$this->assertEqual($result, $expected);


		$result = $this->controller->Session->read("filtros." . $this->controller->name . "." . $this->controller->action);
		$expected = array("condiciones" => $expected, "valoresLov" => array());
		$this->assertEqual($result, $expected);

		$expected = array(
			"FakeTest-campo_fecha__desde" 				=> "21/10/2008",
			"FakeTest-campo_fechahora__hasta" 			=> "25/10/2008 12:34:56"
		);
		$result = $this->controller->data;
		$this->assertEqual($result, array("Condicion"=>$expected));
	}


/**
 * Metodo que se ejecuta despues de cada test.
 *
 * @access public
 */
	function endTest() {
		unset($this->controller);
		ClassRegistry::flush();
	}
	
}


?>