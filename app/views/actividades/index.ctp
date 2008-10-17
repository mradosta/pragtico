<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.views
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Actividad-codigo'] = array();
$condiciones['Condicion.Actividad-nombre'] = array();
$condiciones['Condicion.Actividad-tipo'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"actividades.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Actividad", "field"=>"id", "valor"=>$v['Actividad']['id'], "write"=>$v['Actividad']['write'], "delete"=>$v['Actividad']['delete']);
	$fila[] = array("model"=>"Actividad", "field"=>"codigo", "valor"=>$v['Actividad']['codigo'], "class"=>"derecha");
	$fila[] = array("model"=>"Actividad", "field"=>"nombre", "valor"=>$v['Actividad']['nombre']);
	$fila[] = array("model"=>"Actividad", "field"=>"tipo", "valor"=>$v['Actividad']['tipo']);
	$fila[] = array("model"=>"Actividad", "field"=>"observacion", "valor"=>$v['Actividad']['observacion']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>