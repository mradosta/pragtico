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

App::import('Component', 'Util');

/**
 * La clase encapsula el caso de prueba.
 *
 * @package		pragtico
 * @subpackage	app.tests.cases.behaviors
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
	    $result = $this->UtilComponentTest->format($valor, array("type"=>"mesEnLetras", "case"=>"ucfirst"));
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