<?php

App::import("Helper", "Formato");
App::import("Helper", "Number");
App::import("Helper", "Time");


class FormatoTest extends CakeTestCase {
/**
 * setUp method
 *
 * @access public
 * @return void
 */
	function setUp() {
		$this->Formato = new FormatoHelper();
		$this->Formato->Number =& new NumberHelper();
		$this->Formato->Time =& new TimeHelper();
	}

/**
 * testformat method
 * 
 * @access public
 * @return void
 */
	function testformat() {
		
		$valor = "1000";
		$result = $this->Formato->format($valor);
		$expected = '1000,00';
		$this->assertEqual($expected, $result);

		$valor = "1000.353";
		$result = $this->Formato->format($valor);
		$expected = '1000,35';
		$this->assertEqual($expected, $result);

		$valor = "1";
		$result = $this->Formato->format($valor, "numero");
		$expected = '1,00';
		$this->assertEqual($expected, $result);

		$valor = "1301100.353";
		$result = $this->Formato->format($valor, array("places"=>"3"));
		$expected = '1301100,353';
		$this->assertEqual($expected, $result);

		$valor = "130.333";
		$result = $this->Formato->format($valor, array("type"=>"moneda", "places"=>3));
		$expected = "$ 130,333";
		$this->assertEqual($expected, $result);

		$valor = "130.333";
		$result = $this->Formato->format($valor, "moneda");
		$expected = "$ 130,33";
		$this->assertEqual($expected, $result);

		$valor = "";
		$result = $this->Formato->format($valor, array("type"=>"date"));
		$expected = date("Y-m-d");
		$this->assertEqual($expected, $result);
		
		$valor = "0000-00-00";
		$result = $this->Formato->format($valor, array("type"=>"date"));
		$expected = "";
		$this->assertEqual($expected, $result);

		$valor = "";
		$result = $this->Formato->format($valor, array("type"=>"date", "default"=>true));
		$expected = date("Y-m-d");
		$this->assertEqual($expected, $result);
		
		$valor = "";
		$result = $this->Formato->format($valor, array("type"=>"date", "format"=>"d/m/Y"));
		$expected = date("d/m/Y");
		$this->assertEqual($expected, $result);
		
		$valor = "1943-03-10";
		$result = $this->Formato->format($valor, array("type"=>"date", "default"=>false));
		$expected = "10/03/1943";
		$this->assertEqual($expected, $result);
		
		$valor = "";
		$result = $this->Formato->format($valor, array("type"=>"date", "default"=>false));
		$expected = "";
		$this->assertEqual($expected, $result);
		
		$valor = "15/10/2005";
		$result = $this->Formato->format($valor, array("type"=>"date"));
		$expected = "2005-10-15";
		$this->assertEqual($expected, $result);
		
		$valor = "15/10/2005 10:54";
		$result = $this->Formato->format($valor, array("type"=>"date"));
		$expected = "2005-10-15";
		$this->assertEqual($expected, $result);

		$valor = "2005-10-15";
		$result = $this->Formato->format($valor, array("type"=>"date"));
		$expected = "15/10/2005";
		$this->assertEqual($expected, $result);
		
		$valor = "2005-10-15 10:54";
		$result = $this->Formato->format($valor, array("type"=>"date"));
		$expected = "15/10/2005";
		$this->assertEqual($expected, $result);

		$valor = "15/10/2005";
		$result = $this->Formato->format($valor, array("type"=>"dateTime"));
		$expected = "2005-10-15 00:00:00";
		$this->assertEqual($expected, $result);

		$valor = "15/10/2005 10:54:32";
		$result = $this->Formato->format($valor, array("type"=>"dateTime"));
		$expected = "2005-10-15 10:54:32";
		$this->assertEqual($expected, $result);

		$valor = "2005-10-15";
		$result = $this->Formato->format($valor, array("type"=>"dateTime"));
		$expected = "15/10/2005 00:00:00";
		$this->assertEqual($expected, $result);
		
		$valor = "2005-10-15 10:54:32";
		$result = $this->Formato->format($valor, array("type"=>"dateTime"));
		$expected = "15/10/2005 10:54:32";
		$this->assertEqual($expected, $result);
		
		$valor = "2005-10-15 10:54:32";
		$result = $this->Formato->format($valor, array("type"=>"dateTime", "format"=>"H:i"));
		$expected = "15/10/2005 10:54";
		$this->assertEqual($expected, $result);
    
		$valor = "15/10/2005 10:54:32";
		$result = $this->Formato->format($valor, array("type"=>"dateTime", "format"=>"H:i"));
		$expected = "2005-10-15 10:54";
		$this->assertEqual($expected, $result);
    
		$valor = "";
		$result = $this->Formato->format($valor, array("type"=>"dateTime", "default"=>false));
		$expected = "";
		$this->assertEqual($expected, $result);
		
		$valor = "2005-10-15 10:54:32";
		$result = $this->Formato->format($valor, array("type"=>"ano"));
		$expected = "2005";
		$this->assertEqual($expected, $result);
    
		$valor = "2005-10-15 10:54:32";
		$result = $this->Formato->format($valor, array("type"=>"mes"));
		$expected = "10";
		$this->assertEqual($expected, $result);
    
		$valor = "2005-10-15 10:54:32";
		$result = $this->Formato->format($valor, array("type"=>"dia"));
		$expected = "15";
		$this->assertEqual($expected, $result);

		$valor = "2004-02-15";
		$result = $this->Formato->format($valor, array("type"=>"ultimoDiaDelMes"));
		$expected = "29";
		$this->assertEqual($expected, $result);
    
		$valor = "2008-12-15 13:34";
		$result = $this->Formato->format($valor, array("type"=>"ultimoDiaDelMes"));
		$expected = "31";
		$this->assertEqual($expected, $result);
		
		$valor = "2004-03-01";
		$result = $this->Formato->format($valor, array("type"=>"diaAnterior"));
		$expected = "29";
		$this->assertEqual($expected, $result);
		
		$valor = "2005-03-01";
		$result = $this->Formato->format($valor, array("type"=>"diaAnterior"));
		$expected = "28";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-12-15";
		$result = $this->Formato->format($valor, array("type"=>"diaAnterior"));
		$expected = "14";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-01-01";
		$result = $this->Formato->format($valor, array("type"=>"diaAnterior"));
		$expected = "31";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-12-15";
		$result = $this->Formato->format($valor, array("type"=>"mesAnterior"));
		$expected = "11";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-03-22";
		$result = $this->Formato->format($valor, array("type"=>"anoAnterior"));
		$expected = "2007";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-01-15";
		$result = $this->Formato->format($valor, array("type"=>"mesAnterior"));
		$expected = "12";
		$this->assertEqual($expected, $result);

		$valor = "2008-01-22";
		$result = $this->Formato->format($valor, array("type"=>"1QAnterior"));
		$expected = "2008011Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-03-12";
		$result = $this->Formato->format($valor, array("type"=>"1QAnterior"));
		$expected = "2008021Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-01-12";
		$result = $this->Formato->format($valor, array("type"=>"1QAnterior"));
		$expected = "2007121Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-01-22";
		$result = $this->Formato->format($valor, array("type"=>"2QAnterior"));
		$expected = "2007122Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-03-12";
		$result = $this->Formato->format($valor, array("type"=>"2QAnterior"));
		$expected = "2008022Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-03-22";
		$result = $this->Formato->format($valor, array("type"=>"2QAnterior"));
		$expected = "2008022Q";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-03-22";
		$result = $this->Formato->format($valor, array("type"=>"mensualAnterior"));
		$expected = "200802M";
		$this->assertEqual($expected, $result);
		
		$valor = "2008-01-22";
		$result = $this->Formato->format($valor, array("type"=>"mensualAnterior"));
		$expected = "200712M";
		$this->assertEqual($expected, $result);
		
    	$valor = "2008021Q";
		$result = $this->Formato->format($valor, array("type"=>"periodoEnLetras", "case"=>"ucfirst"));
		$expected = "Primera quincena de febrero de 2008";
		$this->assertEqual($expected, $result);
    
    	$valor = "200802";
		$result = $this->Formato->format($valor, array("type"=>"periodoEnLetras", "case"=>"ucfirst"));
		$expected = "Febrero de 2008";
		$this->assertEqual($expected, $result);

    	$valor = "20082";
		$result = $this->Formato->format($valor, array("type"=>"periodoEnLetras", "case"=>"upper"));
		$expected = "FEBRERO DE 2008";
		$this->assertEqual($expected, $result);

    	$valor = "200811";
		$result = $this->Formato->format($valor, "periodoEnLetras");
		$expected = "noviembre de 2008";
		$this->assertEqual($expected, $result);

		$valor = "2008-01-22";
		$result = $this->Formato->format($valor, array("type"=>"mesEnLetras", "case"=>"ucfirst"));
		$expected = "Enero";
		$this->assertEqual($expected, $result);

		$valor = "2008-01-22";
		$result = $this->Formato->format($valor, array("type"=>"mesEnLetras"));
		$expected = "enero";
		$this->assertEqual($expected, $result);

		$valor = "2008-01-22";
		$result = $this->Formato->format($valor, array("type"=>"mesEnLetras", "case"=>"upper"));
		$expected = "ENERO";
		$this->assertEqual($expected, $result);

		$valor = "200";
		$result = $this->Formato->format($valor, array("type"=>"numeroEnLetras", "option"=>"moneda", "case"=>"lower"));
		$expected = "pesos doscientos";
		$this->assertEqual($expected, $result);
		
		$valor = "1";
		$result = $this->Formato->format($valor, array("type"=>"numeroEnLetras", "option"=>"palabras", "case"=>"lower"));
		$expected = "uno";
		$this->assertEqual($expected, $result);
		
		$valor = "234,00";
		$result = $this->Formato->format($valor, array("type"=>"numeroEnLetras", "option"=>"moneda", "ceroCents"=>true, "case"=>"upper"));
		$expected = "PESOS DOSCIENTOS TREINTA Y CUATRO CON CERO CENTAVOS";
		$this->assertEqual($expected, $result);
		
		$valor = "103.21";
		$result = $this->Formato->format($valor, array("type"=>"numeroEnLetras", "option"=>"moneda"));
		$expected = "pesos ciento tres con veintiun centavos";
		$this->assertEqual($expected, $result);
		
		$valor = "2008.34";
		$result = $this->Formato->format($valor, array("type"=>"numeroEnLetras"));
		$expected = "dos mil ocho con treinta y cuatro";
		$this->assertEqual($expected, $result);
		
		$valor = "1008.345";
		$result = $this->Formato->format($valor, array("type"=>"numeroEnLetras", "case"=>"lower"));
		$expected = "mil ocho con treinta y cinco";
		$this->assertEqual($expected, $result);
		
		$valor = "1008.345";
		$result = $this->Formato->format($valor, array("places"=>3, "type"=>"numeroEnLetras", "case"=>"lower"));
		$expected = "mil ocho con trescientos cuarenta y cinco";
		$this->assertEqual($expected, $result);

    
    }
	
}


?>