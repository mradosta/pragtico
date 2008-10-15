<?php

App::import('Component', 'Util');

class UtilComponentTest extends UtilComponent {
    //var $name = 'Concepto';
    //var $useDbConfig = 'test_suite';
}


class UtilComponentTestCase extends CakeTestCase {
    var $UtilComponentTest;

    function UtilComponentTestCase() {
    	$this->UtilComponentTest =& new UtilComponentTest();
    }
    
    function testGetMeses() {
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
	    $result = $this->UtilComponentTest->getMeses();
	    $this->assertEqual($result, $expected);
	}
	
	
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