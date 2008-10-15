<?php
/**
 * Este es un helper CakePHP que sirve para crear archivos rtf.
 *
 * @author MRadosta <mradosta AT pragmatia.com>
 * @from 11/07/2006
 */

 App::import('Vendor', "class", true, array(APP . "vendors" . DS . "phprtflite" . DS . "rtf"), "Rtf.php");

class RtfHelper extends Rtf {

	var $helpers = array();

	function test() {
		$sect = $this->addSection();
		$sect->writeText('<i>Hello <b>World</b></i>.', new Font(12), new ParFormat('center'));

		$this->sendRtf('Hello World');
	}
	
	//function RtfHelper(){
	//	$this->Cezpdf();
	//}

}