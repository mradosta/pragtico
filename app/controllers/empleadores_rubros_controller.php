<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a la relacion entre un rubro y un empleador.
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
 * La clase encapsula la logica de negocio asociada a la relacion entre un rubro y un empleador.
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class EmpleadoresRubrosController extends AppController {


/**
 * Realiza los seteos especificos (valores por defecto) al agregar y/o editar.
 */
	function __seteos() {
        $this->set("rubros", $this->EmpleadoresRubro->Rubro->find("list", array("recursive"=>-1, "fields"=>array("Rubro.nombre"))));
	}

}
?>
