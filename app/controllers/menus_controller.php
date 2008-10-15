<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los menus.
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
 * La clase encapsula la logica de negocio asociada a los menus.
 *
 * Se refiere a los menus que usa el sistema.
 * 
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class MenusController extends AppController {


/**
 * roles.
 * Muestra via desglose roles asociados a este menu.
 */
	function roles($id) {
		$this->Menu->contain(array("Rol"));
		$this->data = $this->Menu->read(null, $id);
	}

}
?>