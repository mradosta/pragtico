<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los roles.
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
 * La clase encapsula la logica de negocio asociada a los roles.
 *
 * Se refiere a las entidades bancarias.
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class RolesController extends AppController {

/**
 * usuarios.
 * Muestra via desglose usuarios pertenecientes a un rol.
 */
	function usuarios($id) {
		$this->Rol->contain(array("Usuario"));
		$this->data = $this->Rol->read(null, $id);
	}

/**
 * menus.
 * Muestra via desglose menus relacionados a un rol.
 */
	function menus($id) {
		$this->Rol->contain(array("Menu"));
		$this->data = $this->Rol->read(null, $id);
	}	
}	
?>