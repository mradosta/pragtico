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
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */


App::import('Component', 'Formulador');

require_once(APP . "tests" . DS . "cases" . DS . "controllers" . DS . "fake_test_controller.test.php");


/**
 * Caso de prueba para el Component Formulador.
 *
 * @package app.tests
 * @subpackage app.tests.cases.components
 */
class FormuladorComponentTestCase extends CakeTestCase {
	
/**
 * El component que probare.
 *
 * @var array
 * @access public
 */
    var $FormuladorComponentTest;

    
/**
 * Controller que usare en este caso de prueba.
 *
 * @var array
 * @access public
 */
    var $controller;
	
	
/**
 * startCase method.
 *
 * @access public
 */
	function startCase() {
    	$this->FormuladorComponentTest =& new FormuladorComponent();
    	$this->controller = new FakeTestController();
		$this->FormuladorComponentTest->startup(&$this->controller);
    }

	function testInformationFuncions() {

		$formula = "=if(isblank(0000-00-00), 1, 2)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
		
		$formula = "=if(isblank('0000-00-00'), 1, 2)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);

		$formula = "=if(isblank(H23), 1, 2)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
	}

	function testDivisionByZero() {
		
		$formula = "=if('mensual' = 'mensual', (1319.56 / 0), 1319.56)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '#N/A';
		$this->assertEqual($expected, $result);
		
		$formula = "=1319   /    0";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '#N/A';
		$this->assertEqual($expected, $result);
	}
	
	function testResolverNombreFormulas() {

		$formula = "=if('F.A.E.C. y S.'='N/A', 'Aporte Solidario', 'F.A.E.C. y S.')";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = 'F.A.E.C. y S.';
		$this->assertEqual($expected, $result);
		
		$formula = "=if 	('mensual'       ='mensual1', 'Basico',         'Horas')";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = 'Horas';
		$this->assertEqual($expected, $result);

		$formula = "=if('mensual'= 'mensual', 'Basico', 'Horas')";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = 'Basico';
		$this->assertEqual($expected, $result);
		
		$formula = "=if('mensual'='mensual',if ('test'='test1','test','test1'),'Horas')";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = 'test1';
		$this->assertEqual($expected, $result);
		
		$formula = "=if ('mensual' = 'mensual', 'Basico', 'Horas')";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = 'Basico';
		$this->assertEqual($expected, $result);
		
		$formula = "=if ('Fondo Social'='N/A', 'Aporte Solidario', 'Fondo Social')";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = 'Fondo Social';
		$this->assertEqual($expected, $result);
		
		$formula = "=if ('Fondo Social'='Fondo Social', 'Aporte Solidario', 'Fondo Social')";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = 'Aporte Solidario';
		$this->assertEqual($expected, $result);
	
		$formula = "=if ('N/A'='N/A', 'Aporte Solidario', 'Fondo Social')";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = 'Aporte Solidario';
		$this->assertEqual($expected, $result);
	}
	
	
    function testResolverFechas() {
		
		$formula = "=datedif(if('2008-02-10' > '2008-02-01', '2008-02-10'), if('2010-02-28' < '2008-02-29', '2010-02-29', '2008-02-29'))";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '19';
		$this->assertEqual($expected, $result);

		$formula = "=datedif(if('2009-02-10' > '2009-02-01', '2009-02-10'), if('2010-02-28' < '2009-02-28', '2010-02-28', '2009-02-28'))";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '18';
		$this->assertEqual($expected, $result);

		$formula = "=IF(AND(MONTH(date('2008-07-07'))>6,YEAR(date('2008-07-07'))=YEAR(date('2008-12-31');DAY(A2)>1)),INT(NETWORKDAYS(date('2008-07-07'),date('2008-12-31'))/20),IF(AND(MONTH(date('2008-07-07'))<6,YEAR(date('2008-07-07'))=YEAR(date('2008-12-31'))),14,IF((YEAR(date('2008-12-31'))-YEAR(date('2008-07-07')))<=5,14,IF((YEAR(date('2008-12-31'))-YEAR(date('2008-07-07')))<=10,21,IF((YEAR(date('2008-12-31'))-YEAR(date('2008-07-07')))<=15,28,35)))))";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '14';
		$this->assertEqual($expected, $result);
		
		$formula = "=date ( '2008-11-01')";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1225497600';
		$this->assertEqual($expected, $result);
		
		$formula = '=if (month(date(2008, 11, 01)) = 11, 1, 0)';
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
		
		$formula = '=date(2007, 12, 21)';
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1198195200';
		$this->assertEqual($expected, $result);

		$formula = "=datedif ('2007-12-18', '2007-12-22')";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '4';
		$this->assertEqual($expected, $result);
		
		$formula = '=datedif (date(2007, 12, 18), date(2007, 12, 22), "D")';
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '4';
		$this->assertEqual($expected, $result);
	}

	
    function testResolverAlgebraica() {
		
		$formula = "=if     ('ax'='ak', if ('j'='j', 3, 4), min(6,3)) + if    (  	5 >    4, 1, 2)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '4';
		$this->assertEqual($expected, $result);
    
		$formula = "=if ('ax'='ak', if ('j'='j', 3, 4), min(6,3)) + if ('uz'='uz', 1, 2)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '4';
		$this->assertEqual($expected, $result);
		
		$formula = "=if ('1z'='2z', min(10,20), max(3,5))";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '5';
		$this->assertEqual($expected, $result);

		$formula = "=min(2, if ('ax'='ax', 1, 8), 6)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
        
		$formula = "=1";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
		
		$formula = "=1+1";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '2';
		$this->assertEqual($expected, $result);
		
		$formula = "=(1+1)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '2';
		$this->assertEqual($expected, $result);

		$formula = "=(1+1)+2";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '4';
		$this->assertEqual($expected, $result);
		
		$formula = "=(1+1)*10";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '20';
		$this->assertEqual($expected, $result);
		
		$formula = "=(2*3)+10";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '16';
		$this->assertEqual($expected, $result);
		
		$formula = "=(2*3)+(4*4)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '22';
		$this->assertEqual($expected, $result);
		
		$formula = "=(10/2)+5+(2*3)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '16';
		$this->assertEqual($expected, $result);
		
		$formula = "=((1+1)*5)/((2*2)+1)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '2';
		$this->assertEqual($expected, $result);
	}

	function testResolverCondicional() {
		$formula = "=if ('9aaBB11'='9aaBB22', if ('s'='s', 1, 2), if ('s'='s', if (1=2, 2, 5), 10))";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '5';
		$this->assertEqual($expected, $result);
		
		$formula = "=if ('9aaBB11'='9aaBB11', if ('s'='s', 1, 2), 0)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
	
		$formula = "=if ('aaBB11'='AAbb22', 1, 0)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '0';
		$this->assertEqual($expected, $result);
		
		$formula = "=if ('aaBB11'='aaBB11', 1, 0)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
		
		$formula = "=if (2<>3, 1, 1+1+2*2)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
		
		$formula = "=if (2<>2, 1, 1+1+2*2)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '6';
		$this->assertEqual($expected, $result);
		
		$formula = "=if (2=2, (1+1+2)*2, 3)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '8';
		$this->assertEqual($expected, $result);
		
		$formula = "=if (2<4, 1, 0)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
		
		$formula = "=if (2>2, 1, 3)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '3';
		$this->assertEqual($expected, $result);
		
		$formula = "=if (2=3, 1, 3)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '3';
		$this->assertEqual($expected, $result);
		
		$formula = "=if (2=2, 1)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
		
	}		
	
	function testResolverStrings() {
		$formula = '=left("mi casa es verde", 2)';
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = 'mi';
		$this->assertEqual($expected, $result);
		
		$formula = '=right("mi casa es verde", 5)';
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = 'verde';
		$this->assertEqual($expected, $result);
	
		$formula = '=mid("mi casa es verde", 4, 4)';
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = 'casa';
		$this->assertEqual($expected, $result);
	
	}	

	function testResolverFuncionesDeGrupo() {

		$formula = '=max(2, 4, 6)';
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '6';
		$this->assertEqual($expected, $result);
		
		$formula = "=max(-2, -4, -6, 0)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '0';
		$this->assertEqual($expected, $result);
		
		$formula = "=max(-2, -4, -6)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '-2';
		$this->assertEqual($expected, $result);
		
		$formula = "=average(2, 4, 6)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '4';
		$this->assertEqual($expected, $result);
		
		$formula = "=min(2, 4, 6)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '2';
		$this->assertEqual($expected, $result);
		
		$formula = "=min(0, 2, 4, 6, -1)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '-1';
		$this->assertEqual($expected, $result);
		
		$formula = "=sum(0, 2, 4, 6, -1)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '11';
		$this->assertEqual($expected, $result);
		
		$formula = "=min(10,20,30,2) + min(100,200,300) + min(200,400,600)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '302';
		$this->assertEqual($expected, $result);
		
		$formula = "=2 + min(10,20,30,2) + max(3,5,7) + sum(5,6,1) -3";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '20';
		$this->assertEqual($expected, $result);
	
		$formula = "=2 + min(10,20,30,2) + max(3,5,7) + sum(5,6,1) -2 + min(2,3)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '23';
		$this->assertEqual($expected, $result);
	}	
	
}


?>