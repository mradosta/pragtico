<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las areas de los empleadores.
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
 * La clase encapsula la logica de negocio asociada a las areas de los empleadores.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class AreasController extends AppController {

/**
 * coeficientes.
 * Muestra via desglose los coeficientes de un area.
 */
	function coeficientes($id) {
		$this->Area->contain('Coeficiente');
		$this->data = $this->Area->read(null, $id);
	}	
	
}
?>