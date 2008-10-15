<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a la ropa que se le entrega
 * a cada trabajador de una relacion laboral.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.controllers
 * @since			Pragtico v 1.0.0
 * @version			1.0.0
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a la ropa que se le entrega
 * a cada trabajador de una relacion laboral.
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class RopasController extends AppController {


	var $helpers = array("Pdf");
	


/**
 * Prendas.
 * Muestra via desglose las prendas entregadas. 
 */
   	function prendas($id) {
		$this->Ropa->contain("RopasDetalle");
		$this->data = $this->Ropa->read(null, $id);
   	}


/**
 * imprimirOrden.
 * Genera una orden que el empleado pueda retirar la ropa.
 */
   	function imprimirOrden($id) {
		$this->layout = 'pdf';
		$this->data = $this->Ropa->read(null, $id);
   	}
}
?>
