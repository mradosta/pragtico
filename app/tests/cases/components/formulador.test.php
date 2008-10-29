<?php

App::import('Component', 'Formulador');

/**
 * Creo un controller "ficticio". Solo para probar.
 */
class BarFooController {

} 

class FormuladorComponentTest extends FormuladorComponent {

}


class FormuladorComponentTestCase extends CakeTestCase {
    var $FormuladorComponentTest;

    function FormuladorComponentTestCase() {
    	$this->FormuladorComponentTest =& new FormuladorComponentTest();
    	$controller = new BarFooController();
		$this->FormuladorComponentTest->startup(&$controller);
    }


    function testResolverFechas() {

		$formula = '=date(2007, 12, 21)';
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1198195200';
		$this->assertEqual($expected, $result);

		$formula = '=datedif("2007-12-18", "2007-12-22", "D")';
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '4';
		$this->assertEqual($expected, $result);
		
		$formula = '=datedif(date(2007, 12, 18), date(2007, 12, 22), "D")';
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '4';
		$this->assertEqual($expected, $result);
	}

	
    function testResolverAlgebraica() {
    
		$formula = "=if('ax'='ak', if('j'='j', 3, 4), min(6,3)) + if('uz'='uz', 1, 2)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '4';
		$this->assertEqual($expected, $result);
		
		$formula = "=if('1z'='2z', min(10,20), max(3,5))";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '5';
		$this->assertEqual($expected, $result);

		$formula = "=min(2, if('ax'='ax', 1, 8), 6)";
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
		$formula = "=if('9aaBB11'='9aaBB22', if('s'='s', 1, 2), if('s'='s', if(1=2, 2, 5), 10))";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '5';
		$this->assertEqual($expected, $result);
		
		$formula = "=if('9aaBB11'='9aaBB11', if('s'='s', 1, 2), 0)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
	
		$formula = "=if('aaBB11'='AAbb22', 1, 0)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '0';
		$this->assertEqual($expected, $result);
		
		$formula = "=if('aaBB11'='aaBB11', 1, 0)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
		
		$formula = "=if(2<>3, 1, 1+1+2*2)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
		
		$formula = "=if(2<>2, 1, 1+1+2*2)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '6';
		$this->assertEqual($expected, $result);
		
		$formula = "=if(2=2, (1+1+2)*2, 3)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '8';
		$this->assertEqual($expected, $result);
		
		$formula = "=if(2<4, 1, 0)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '1';
		$this->assertEqual($expected, $result);
		
		$formula = "=if(2>2, 1, 3)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '3';
		$this->assertEqual($expected, $result);
		
		$formula = "=if(2=3, 1, 3)";
		$result = $this->FormuladorComponentTest->resolver($formula);
		$expected = '3';
		$this->assertEqual($expected, $result);
		
		$formula = "=if(2=2, 1)";
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

		$formula = "=max(2, 4, 6)";
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