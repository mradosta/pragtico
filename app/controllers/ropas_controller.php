<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a la ropa que se le entrega
 * a cada trabajador de una relacion laboral.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.controllers
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a la ropa que se le entrega
 * a cada trabajador de una relacion laboral.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class RopasController extends AppController {


/**
 * Prendas.
 * Muestra via desglose las prendas entregadas. 
 */
   	function prendas($id) {
		$this->Ropa->contain("RopasDetalle");
		$this->data = $this->Ropa->read(null, $id);
   	}

}
?>
