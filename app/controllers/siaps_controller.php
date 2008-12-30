<?php
/**
 * Este archivo contiene toda la logica de negocio asociada al SIAP.
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
 * La clase encapsula la logica de negocio asociada al SIAP.
 *
 * Se refiere a las versiones de SIAP.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class SiapsController extends AppController {
	

/**
 * detalles.
 * Muestra via desglose los detalles de esta version de SIAP.
 */
	function detalles($id) {
		$this->data = $this->Siap->read(null, $id);
	}

}
?>