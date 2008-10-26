<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los grupos de usuarios.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.controllers
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */

/**
 * La clase encapsula la logica de negocio asociada a los grupos de usuarios.
 *
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class GruposController extends AppController {

/**
 * usuarios.
 * Muestra via desglose usuarios pertenecientes a este grupo.
 */
	function usuarios($id) {
		$this->Grupo->contain(array("Usuario"));
		$this->data = $this->Grupo->read(null, $id);
	}


/**
 * parametros.
 * Muestra via desglose menus parametros a este grupo.
 */
	function parametros($id) {
		$this->Grupo->contain(array("GruposParametro"));
		$this->data = $this->Grupo->read(null, $id);
	}	
	
}
?>