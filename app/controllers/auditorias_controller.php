<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las auditorias.
 * Cada operacion de escritura (add/edit) o eliminacion (delete) deja un log.
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
 * La clase encapsula la logica de negocio asociada a las auditorias.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class AuditoriasController extends AppController {

/**
 * detalles.
 * Muestra via desglose los detalles de la auditoria (el registro modificado).
 */
	function detalles($id) {
		$this->data = $this->Auditoria->read(null, $id);
		$this->data['Auditoria']['data'] = unserialize($this->data['Auditoria']['data']);
	}
	
}
?>