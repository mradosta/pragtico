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
 * @subpackage      app.tests
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */

App::import('Component', 'Util');

/**
 * La clase encapsula el caso de prueba.
 *
 * @package     pragtico
 * @subpackage  app.tests.cases.behaviors
 */
class UtilComponentTestCase extends CakeTestCase {

/**
 * El component que probare.
 *
 * @var array
 * @access private
 */
    var $UtilComponentTest;


/**
 * En el contructor de la clase, instancio el objeto.
 *
 * @return void.
 * @access private.
 */
    function __construct() {
    	$this->UtilComponentTest =& new UtilComponent();
    }


    function testDateDiff() {
		
        $result = $this->UtilComponentTest->dateDiff('2007-01-01', '2007-12-31');
        $expected = array('dias' => 365, 'horas' => 0, 'minutos' => 0, 'segundos' => 0);
        $this->assertEqual($expected, $result);
		
        $result = $this->UtilComponentTest->dateDiff('2007-01-01', '2007-01-02');
        $expected = array('dias' => 2, 'horas' => 0, 'minutos' => 0, 'segundos' => 0);
        $this->assertEqual($expected, $result);
		
        $result = $this->UtilComponentTest->dateDiff('2007-12-01', '2008-01-02');
        $expected = array('dias' => 33, 'horas' => 0, 'minutos' => 0, 'segundos' => 0);
        $this->assertEqual($expected, $result);

        $result = $this->UtilComponentTest->dateDiff('2008-12-01', '2009-01-02');
        $expected = array('dias' => 33, 'horas' => 0, 'minutos' => 0, 'segundos' => 0);
        $this->assertEqual($expected, $result);
		
        $result = $this->UtilComponentTest->dateDiff('2007-01-01', '2007-12-30');
        $expected = array('dias' => 364, 'horas' => 0, 'minutos' => 0, 'segundos' => 0);
        $this->assertEqual($expected, $result);
		
        $result = $this->UtilComponentTest->dateDiff('2009-01-01', '2009-12-31');
		$expected = array('dias' => 365, 'horas' => 0, 'minutos' => 0, 'segundos' => 0);
        $this->assertEqual($expected, $result);

        $result = $this->UtilComponentTest->dateDiff('2008-01-01', '2008-12-31');
        $expected = array('dias' => 366, 'horas' => 0, 'minutos' => 0, 'segundos' => 0);
        $this->assertEqual($expected, $result);

    }


/**
 * Compruebo que formatee correctamente.
 * Como ya esta desarrollado el caso de prueba del helper Formato, solo compruebo un caso.
 *
 * @return void.
 * @access public.
 */
	function testFormat() {
	
		$valor = "1000";
		$result = $this->UtilComponentTest->format($valor);
		$expected = '1000,00';
		$this->assertEqual($expected, $result);

		$valor = "all";
		$expected = array(	"1" 	=> "Enero",
							"2" 	=> "Febrero",
							"3" 	=> "Marzo",
							"4" 	=> "Abril",
							"5" 	=> "Mayo",
							"6" 	=> "Junio",
							"7" 	=> "Julio",
							"8" 	=> "Agosto",
							"9" 	=> "Setiembre",
							"10" 	=> "Octubre",
							"11" 	=> "Noviembre",
							"12" 	=> "Diciembre");
	    $result = $this->UtilComponentTest->format($valor, array("type" => "mesEnLetras", "case" => "ucfirst"));
	    $this->assertEqual($result, $expected);
	}
	

/**
 * Compruebo la funcion de extraer ids desde un array.
 *
 * @return void.
 * @access public.
 */
    function testExtraerIds() {
    	$expected = array(1, 2, 6, 8, 9);
    	$data = array(	"id_1"	=>	1,
    					"id_2"	=>	1,
    					"id_3"	=>	0,
    					"xid_4"	=>	1,
    					"id_5x"	=>	1,
    					"id_6"	=>	"1",
    					"id_7"	=>	"a",
    					"id_8"	=>	true,
    					"id_9"	=>	"true",
    					"id_10"	=>	false,
    					"id_11"	=>	"false",
    					"id"	=> 	1);
	    $result = $this->UtilComponentTest->extraerIds($data);
	    $this->assertEqual($result, $expected);
	}
	
}


?>