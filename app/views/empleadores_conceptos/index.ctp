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
$condiciones['Condicion.Relacion-empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																		"camposRetorno"	=>array("Empleador.cuit",
																								"Empleador.nombre")));
$condiciones['Condicion.Concepto-codigo'] = array();
$condiciones['Condicion.Concepto-nombre'] = array();
$condiciones['Condicion.Concepto-tipo'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Conceptos de los Empleadores", "imagen"=>"conceptos.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"EmpleadoresConcepto", "field"=>"id", "valor"=>$v['EmpleadoresConcepto']['id'], "write"=>$v['EmpleadoresConcepto']['write'], "delete"=>$v['EmpleadoresConcepto']['delete']);
	$fila[] = array("model"=>"Empleador", "field"=>"nombre", "nombreEncabezado"=>"Empleador", "valor"=>$v['Empleador']['cuit'] . " - " . $v['Empleador']['nombre']);
	$fila[] = array("model"=>"Concepto", "field"=>"codigo", "valor"=>$v['Concepto']['codigo']);
	$fila[] = array("model"=>"Concepto", "field"=>"nombre", "nombreEncabezado"=>"Concepto", "valor"=>$v['Concepto']['nombre']);
	$fila[] = array("model"=>"EmpleadoresConcepto", "field"=>"formula", "valor"=>$v['EmpleadoresConcepto']['formula']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>