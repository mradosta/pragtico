<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los descuentos.
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
 * La clase encapsula la logica de negocio asociada a los descuentos.
 *
 * Se refiere a los posibles descuentos que puede tener un trabajador de una relacion laboral,
 * puede ser un vale, un embargo, un prestamo, etc.
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class DescuentosController extends AppController {


/**
 * detalles.
 * Muestra via desglose los detalles de un descuento.
 */
	function detalles($id) {
		$this->Descuento->contain("DescuentosDetalle");
		$this->data = $this->Descuento->read(null, $id);
	}

}
?>