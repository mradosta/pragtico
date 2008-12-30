<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los rubros de las Empresas (Empleadores).
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
 * La clase encapsula la logica de negocio asociada a los rubros de las empresas.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class RubrosController extends AppController {


/**
 * empleadores.
 * Muestra via desglose los empleadores asociados a este rubro.
 */
	function empleadores($id) {
		$this->Rubro->contain("Empleador");
		$this->data = $this->Rubro->read(null, $id);
	}
 
}	
?>