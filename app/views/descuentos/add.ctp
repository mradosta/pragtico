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
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Descuento.id'] = array();
$campos['Descuento.relacion_id'] = array(	"label"=>"Relacion",
											"lov"=>array("controller"	=>	"relaciones",
													"seleccionMultiple"	=> 	0,
														"camposRetorno"	=>	array(	"Trabajador.nombre",
																					"Trabajador.apellido",
																					"Empleador.nombre")));
$campos['Descuento.alta'] = array();
$campos['Descuento.desde'] = array();
$campos['Descuento.descripcion'] = array("aclaracion"=>"Esta descripcion saldra impresa en el recibo.");
$campos['Descuento.monto'] = array("label"=>"Monto $", "aclaracion"=>"Se refiere al monto total a descontar.");
$campos['Descuento.cuotas'] = array();
$campos['Descuento.maximo'] = array("aclaracion"=>"Es el porcentaje a descontar con cada cuota sobre el SMVM.");
$campos['Descuento.descontar'] = array("type"=>"checkboxMultiple");
$campos['Descuento.concurrencia'] = array();
$campos['Descuento.tipo'] = array();
$campos['Descuento.estado'] = array();
$campos['Descuento.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"descuentos.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
$miga = array('format' 	=> '%s %s (%s)', 
			  'content' => array('Relacion.Trabajador.apellido', 'Relacion.Trabajador.nombre', 'Relacion.Empleador.nombre'));
echo $this->element("add/add", array("fieldset"=>$fieldset, "miga" => $miga));
?>