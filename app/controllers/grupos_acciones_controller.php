<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a la relacion entre grupos y acciones.
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
 * La clase encapsula la logica de negocio asociada a la relacion entre grupos y acciones.
 *
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class GruposAccionesController extends AppController {


	function actualizarTablaIzquierda() {
		$acciones = $this->GruposAccion->Accion->find("all", array("conditions"=>array("Controlador.id"=>$this->params['named']['partialText']), "order"=>array("Accion.nombre")));
		$data = $this->Util->combine($acciones, '{n}.Accion.id', array('%s - %s', '{n}.Controlador.nombre', '{n}.Accion.etiqueta'));
		$tablaSiemple = $this->Util->generarCuerpoTablaSimple($data);
		$this->set("cuerpo", $tablaSiemple['cuerpo']);
		$this->set("encabezados", $tablaSiemple['encabezados']);
		$this->render(".." . DS . "elements" . DS . "tablas_from_to" . DS . "tabla");
	}



	function autocomplete_buscar() {
		$acciones = $this->GruposAccion->Accion->Controlador->find("all", array("conditions"=>array("Controlador.nombre like"=>$this->params['url']['q'] . "%"), "order"=>array("Controlador.nombre")));
		$acciones = $this->Util->combine($acciones, '{n}.Controlador.id', '{n}.Controlador.nombre');
		$this->set("data", $this->Util->generarAutocomplete($acciones));
		$this->render("xx", "wsdl");
	}


/**
 * Realiza los seteos especificos (valores por defecto) al agregar y/o editar.
 */
	function __seteos() {
        $this->set("grupos", $this->GruposAccion->Grupo->find("list", array("recursive"=>-1, "fields"=>array("Grupo.nombre"))));
        $acciones = $this->GruposAccion->Accion->find("all", array("order"=>array("Controlador.nombre", "Accion.nombre")));
        $datosIzquierda = $this->Util->combine($acciones, '{n}.Accion.id', array('%s - %s', '{n}.Controlador.nombre', '{n}.Accion.etiqueta'));
        $this->set("datosIzquierda", $datosIzquierda);
        $datosDerecha = array();
        $this->set("datosDerecha", $datosDerecha);
	}


}
?>