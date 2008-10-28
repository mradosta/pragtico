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
 * @subpackage		app.tests.cases.helpers
 * @since			Pragtico v 1.0.0
 * @version			$Revision: 54 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2008-10-23 23:14:28 -0300 (Thu, 23 Oct 2008) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */

App::import("Helper", "Formato");
App::import("Helper", "Number");
App::import("Helper", "Time");

/**
 * Caso de prueba para el Helper Formato.
 *
 * @package app.tests
 * @subpackage app.tests.cases.helpers
 */

class FormatoTest extends CakeTestCase {

/**
 * Helper que usare en este caso de prueba.
 *
 * @var array
 * @access public
 */
	var $formato;

	
/**
 * Metodo que se ejecuta antes de cada test.
 *
 * @access public
 */
	function startTest() {
		$this->formato = new FormatoHelper();
		$this->formato->Number =& new NumberHelper();
		$this->formato->Time =& new TimeHelper();
	}


/**
 * Pruebo el formateo de datos.
 * 
 * @access public
 * @return void
 */
	function testformat() {
		
		$valor = "1000";
		$result = $this->formato->format($valor);
		$expected = '1000,00';
		$this->assertEqual($expected, $result);

		$valor = "1000.353";
		$result = $this->formato->format($valor);
		$expected = '1000,35';
		$this->assertEqual($expected, $result);

		$valor = "1";
		$result = $this->formato->format($valor, "numero");
		$expected = '1,00';
		$this->assertEqual($expected, $result);

		$valor = "1301100.353";
		$result = $this->formato->format($valor, array("places"=>"3"));
		$expected = '1301100,353';
		$this->assertEqual($expected, $result);

		$valor = "130.333";
		$result = $this->formato->format($valor, array("type"=>"moneda", "places"=>3));
		$expected = "$ 130,333";
		$this->assertEqual($expected, $result);

		$valor = "130.333";
		$result = $this->formato->format($valor, "moneda");
		$expected = "$ 130,33";
		$this->assertEqual($expected, $result);

		$valor = "";
		$result = $this->formato->format($valor, array("type"=>"date"));
		$expected = date("Y-m-d");
		$this->assertEqual($expected, $result);
		
		$valor = "0000-00-00";
		$result = $this->formato->format($valor, array("type"=>"date"));
		$expected = "";
		$this->assertEqual($expected, $result);

		$valor = "";
		$result = $this->formato->format($valor, array("type"=>"date", "default"=>true));
		$expected = date("Y-m-d");
		$this->assertEqual($expected, $result);
		
		$valor = "";
		$result = $this->formato->format($valor, array("type"=>"date", "format"=>"d/m/Y"));
		$expected = date("d/m/Y");
		$this->assertEqual($expected, $result);
		
		$valor = "1943-03-10";
		$result = $this->formato->format($valor, array("type"=>"date", "default"=>false));
		$expected = "10/03/1943";
		$this->assertEqual($expected, $result);
		
		$valor = "";
		$result = $this->formato->format($valor, array("type"=>"date", "default"=>false));
		$expected = "";
		$this->assertEqual($expected, $result);
		
		$valor = "15/10/2005";
		$result = $this->formato->format($valor, array("type"=>"date"));
		$expected = "2005-10-15";
		$this->assertEqual($expected, $result);
		
		$valor = array("dia"=>"15", "mes"=>"10", "ano"=>"2005");
		$result = $this->formato->format($valor, array("type"=>"date"));
		$expected = "15/10/2005";
		$this->assertEqual($expected, $result);
		
		$valor = "15/10/2005 10:54";
		$result = $this->formato->format($valor, array("type"=>"date"));
		$expected = "2005-10-15";
		$this->assertEqual($expected, $result);

		$valor = "2005-10-15";
		$result = $this->formato->format($valor, array("type"=>"date"));
		$expected = "15/10/2005";
		$this->assertEqual($expected, $result);
		
		$valor = "2005-10-15 10:54";
		$result = $this->formato->format($valor, array("type"=>"date"));
		$expected = "15/10/2005";
		$this->assertEqual($expected, $result);

		$valor = "15/10/2005";
		$result = $this->formato->format($valor, array("type"=>"dateTime"));
		$expected = "2005-10-15 00:00:00";
		$this->assertEqual($expected, $result);

		$valor = "15/10/2005 10:54:32";
		$result = $this->formato->format($valor, array("type"=>"dateTime"));
		$expected = "2005-10-15 10:54:32";
		$this->assertEqual($expected, $result);

		$valor = "2005-10-15";
		$result = $this->formato->format($valor, array("type"=>"dateTime"));
		$expected = "15/10/2005 00:00:00";
		$this->assertEqual($expected, $result);
		
		$valor = "2005-10-15 10:54:32";
		$result = $this->formato->format($valor, array("type"=>"dateTime"));
		$expected = "15/10/2005 10:54:32";
		$this->assertEqual($expected, $result);
		
		$valor = "2005-10-15 10:54:32";
		$result = $this->formato->format($valor, array("type"=>"dateTime", "format"=>"H:i"));
		$expected = "15/10/2005 10:54";
		$this->assertEqual($expected, $result);
    
		$valor = "15/10/2005 10:54:32";
		$result = $this->formato->format($valor, array("type"=>"dateTime", "format"=>"H:i"));
		$expected = "2005-10-15 10:54";
		$this->assertEqual($expected, $result);
    
		$valor = "";
		$result = $this->formato->format($valor, array("type"=>"dateTime", "default"=>false));
		$expected = "";
		$this->assertEqual($expected, $result);
		
		$valor = "2005-10-15 10:54:32";
		$result = $this->formato->format($valor, array("type"=>"ano"));
		$expected = "2005";
		$this->assertEqual($expected, $result);
    
		$valor = "2005-10-15 10:54:32";
		$result = $this->formato->format($valor, array("type"=>"mes"));
		$expected = "10";
		$this->assertEqual($expected, $result);
    
		$valor = "2005-10-15 10:54:32";
		$result = $this->formato->format($valor, array("type"=>"dia"));
		$expected = "15";
		$this->assertEqual($expected, $result);

		$valor = "2004-02-15";
		$result = $this->formato->format($valor, array("type"=>"ultimoDiaDelMes"));
		$expected = "29";
		$this->assertEqual($expected, $result);
    
		$valor = "2008-12-15 13:34";
		$result = $this->formato->format($valor, array("type"=>"ultimoDiaDelMes"));
		$expected = "31";
		$this->assertEqual($expected, $result);
		
		$valor = "2004-03-01";
		$result = $this->formato->format($valor, array("type"=>"diaAnterior"));
		$expected = "29";
		$this->assertEqual($expected, $result);
		
		$valor = "2005-03-01";
		$result = $this->formato->format($valor, array("type"=>"diaAnterior"));
		$expected = "28";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-12-15";
		$result = $this->formato->format($valor, array("type"=>"diaAnterior"));
		$expected = "14";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-01-01";
		$result = $this->formato->format($valor, array("type"=>"diaAnterior"));
		$expected = "31";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-12-15";
		$result = $this->formato->format($valor, array("type"=>"mesAnterior"));
		$expected = "11";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-03-22";
		$result = $this->formato->format($valor, array("type"=>"anoAnterior"));
		$expected = "2007";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-01-15";
		$result = $this->formato->format($valor, array("type"=>"mesAnterior"));
		$expected = "12";
		$this->assertEqual($expected, $result);

		$valor = "2008-01-22";
		$result = $this->formato->format($valor, array("type"=>"1QAnterior"));
		$expected = "2008011Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-03-12";
		$result = $this->formato->format($valor, array("type"=>"1QAnterior"));
		$expected = "2008021Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-01-12";
		$result = $this->formato->format($valor, array("type"=>"1QAnterior"));
		$expected = "2007121Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-01-22";
		$result = $this->formato->format($valor, array("type"=>"2QAnterior"));
		$expected = "2007122Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-03-12";
		$result = $this->formato->format($valor, array("type"=>"2QAnterior"));
		$expected = "2008022Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-03-22";
		$result = $this->formato->format($valor, array("type"=>"2QAnterior"));
		$expected = "2008022Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-03-22";
		$result = $this->formato->format($valor, array("type"=>"mensualAnterior"));
		$expected = "200802M";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-01-22";
		$result = $this->formato->format($valor, array("type"=>"mensualAnterior"));
		$expected = "200712M";
		$this->assertEqual($expected, $result);
		
    	$valor = "2008021Q";
		$result = $this->formato->format($valor, array("type"=>"periodoEnLetras", "case"=>"ucfirst"));
		$expected = "Primera quincena de febrero de 2008";
		$this->assertEqual($expected, $result);
    
    	$valor = "200802";
		$result = $this->formato->format($valor, array("type"=>"periodoEnLetras", "case"=>"ucfirst"));
		$expected = "Febrero de 2008";
		$this->assertEqual($expected, $result);

    	$valor = "20082";
		$result = $this->formato->format($valor, array("type"=>"periodoEnLetras", "case"=>"upper"));
		$expected = "FEBRERO DE 2008";
		$this->assertEqual($expected, $result);

    	$valor = "200811";
		$result = $this->formato->format($valor, "periodoEnLetras");
		$expected = "noviembre de 2008";
		$this->assertEqual($expected, $result);

		$valor = "2008-01-22";
		$result = $this->formato->format($valor, array("type"=>"mesEnLetras", "case"=>"ucfirst"));
		$expected = "Enero";
		$this->assertEqual($expected, $result);

		$valor = "2008-01-22";
		$result = $this->formato->format($valor, array("type"=>"mesEnLetras"));
		$expected = "enero";
		$this->assertEqual($expected, $result);

		$valor = "2008-01-22";
		$result = $this->formato->format($valor, array("type"=>"mesEnLetras", "case"=>"upper"));
		$expected = "ENERO";
		$this->assertEqual($expected, $result);

		$valor = "all";
		$result = $this->formato->format($valor, array("type"=>"mesEnLetras", "case"=>"upper"));
		$expected = Array(
			"1" => "ENERO",
			"2" => "FEBRERO",
			"3" => "MARZO",
			"4" => "ABRIL",
			"5" => "MAYO",
			"6" => "JUNIO",
			"7" => "JULIO",
			"8" => "AGOSTO",
			"9" => "SETIEMBRE",
			"10" => "OCTUBRE",
			"11" => "NOVIEMBRE",
			"12" => "DICIEMBRE");
		$this->assertEqual($expected, $result);

		$valor = "200808M";
		$result = $this->formato->format($valor, array("type"=>"periodo"));
		$expected = Array (
			"periodoCompleto" => "200808M",
			"ano" => "2008",
			"mes" => "08",
			"periodo" => "M",
			"desde" => "2008-08-01",
			"hasta" => "2008-08-31");
		$this->assertEqual($expected, $result);
		
		$valor = "2008082Q";
		$result = $this->formato->format($valor, array("type"=>"periodo"));
		$expected = Array (
			"periodoCompleto" => "2008082Q",
			"ano" => "2008",
			"mes" => "08",
			"periodo" => "2Q",
			"desde" => "2008-08-16",
			"hasta" => "2008-08-31");
		$this->assertEqual($expected, $result);
		
		$valor = "2008081Q";
		$result = $this->formato->format($valor, "periodo");
		$expected = Array (
			"periodoCompleto" => "2008081Q",
			"ano" => "2008",
			"mes" => "08",
			"periodo" => "1Q",
			"desde" => "2008-08-01",
			"hasta" => "2008-08-15");
		$this->assertEqual($expected, $result);

		$valor = "2008081QQ";
		$result = $this->formato->format($valor, "periodo");
		$expected =false;
		$this->assertFalse($result);
		
		$valor = "200";
		$result = $this->formato->format($valor, array("type"=>"numeroEnLetras", "option"=>"moneda", "case"=>"lower"));
		$expected = "pesos doscientos";
		$this->assertEqual($expected, $result);
		
		$valor = "1";
		$result = $this->formato->format($valor, array("type"=>"numeroEnLetras", "option"=>"palabras", "case"=>"lower"));
		$expected = "uno";
		$this->assertEqual($expected, $result);
		
		$valor = "234,00";
		$result = $this->formato->format($valor, array("type"=>"numeroEnLetras", "option"=>"moneda", "ceroCents"=>true, "case"=>"upper"));
		$expected = "PESOS DOSCIENTOS TREINTA Y CUATRO CON CERO CENTAVOS";
		$this->assertEqual($expected, $result);
		
		$valor = "103.21";
		$result = $this->formato->format($valor, array("type"=>"numeroEnLetras", "option"=>"moneda"));
		$expected = "pesos ciento tres con veintiun centavos";
		$this->assertEqual($expected, $result);
		
		$valor = "2008.34";
		$result = $this->formato->format($valor, array("type"=>"numeroEnLetras"));
		$expected = "dos mil ocho con treinta y cuatro";
		$this->assertEqual($expected, $result);
		
		$valor = "1008.345";
		$result = $this->formato->format($valor, array("type"=>"numeroEnLetras", "case"=>"lower"));
		$expected = "mil ocho con treinta y cinco";
		$this->assertEqual($expected, $result);
		
		$valor = "1008.345";
		$result = $this->formato->format($valor, array("places"=>3, "type"=>"numeroEnLetras", "case"=>"lower"));
		$expected = "mil ocho con trescientos cuarenta y cinco";
		$this->assertEqual($expected, $result);

    }
	

/**
 * Metodo que se ejecuta despues de cada test.
 *
 * @access public
 */
	function endTest() {
		unset($this->formato);
		ClassRegistry::flush();
	}
	
}


?>