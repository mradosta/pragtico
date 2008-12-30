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
 
$cuerpoT1 = $cuerpoT2 = null;
foreach($datosIzquierda as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => "Concepto", 'field' => "id", 'valor' => $v['Concepto']['id']);
	$fila[] = array('model' => "Concepto", 'field' => "codigo", "class"=>"oculto", 'valor' => $v['Concepto']['codigo']);
	$fila[] = array('model' => "Concepto", 'field' => "nombre", 'valor' => $v['Concepto']['nombre']);
	$cuerpoT1[] = $fila;
}

foreach($datosDerecha as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => "Concepto", 'field' => "id", 'valor' => $v['Concepto']['id']);
	$fila[] = array('model' => "Concepto", 'field' => "codigo", "class"=>"oculto", 'valor' => $v['Concepto']['codigo']);
	$fila[] = array('model' => "Concepto", 'field' => "nombre", 'valor' => $v['Concepto']['nombre']);
	$cuerpoT2[] = $fila;
}

$extra = $formulario->input("RelacionesConcepto.relacion_id", array("type"=>"hidden", "value"=>$relacion['Relacion']['id']));

echo $this->renderElement("add/add_rapido", array(
				"cuerpoTablaIzquierda"		=> $cuerpoT1,
				"cuerpoTablaDerecha"		=> $cuerpoT2,
				"extra"						=> $extra,
				"encabezadosTablaIzquierda"	=> array("Nombre"),
				"encabezadosTablaDerecha"	=> array("Nombre"),
				"busqueda"					=> array("label"=>"Concepto"),
				"fieldset"					=> array(	"imagen"=>	'conceptos.gif',
														"legend"=>	"Asignar conceptos a la Relacion (" . $relacion['Empleador']['nombre'] . " - " . $relacion['Trabajador']['apellido'] . " " . $relacion['Trabajador']['nombre'] . ")")
				));
?>