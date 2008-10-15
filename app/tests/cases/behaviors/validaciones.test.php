<?php 
App::import('Behavior', 'Validaciones');
App::import('Core', array('AppModel', 'Model'));
require_once(ROOT . DS . CAKE . "tests" . DS . "cases" . DS . "libs" . DS . "model" . DS . "models.php");


class ValidacionesTest extends ValidacionesBehavior {
    var $name = 'Validaciones';
    var $useDbConfig = 'test_suite';
}

class ValidacionesTestCase extends CakeTestCase {
    var $fixtures = array( 'core.user' );


/**
 * Method executed before each test
 *
 * @access public
 */
	function startTest() {
		$this->User =& ClassRegistry::init('User');
		$this->User->Behaviors->attach('Validaciones');
	}
	

/**
 * Method executed after each test
 *
 * @access public
 */
	function endTest() {
		unset($this->User);
		ClassRegistry::flush();
	}
	
    function testValidCuitCuil() {

		$this->User->validate = array(
			'user' => array(
				array('rule'	=> 'validCuitCuil')
			)
        );
		$this->User->data = array("User"=>array("user"=>"sssssss"));
		//$this->User->data = array("User"=>array("user"=>"20-27959940-4"));
		$this->User->data = array("User"=>array("user"=>"sssssss"));
		
		$expected = true;
		$result = $this->User->validates();
		
		$this->assertEqual($result, $expected);
    }
}
?>