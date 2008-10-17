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
$campos['RelacionesConcepto.id'] = array();
$campos['RelacionesConcepto.relacion_id'] = array(	"lov"=>array("controller"	=>	"relaciones",
															"seleccionMultiple"	=> 	0,
															"camposRetorno"		=> 	array(	"Empleador.nombre",
																							"Trabajador.apellido")));
																				
$campos['RelacionesConcepto.concepto_id'] = array(	"lov"=>array("controller"	=>	"conceptos",
															"seleccionMultiple"	=> 	0,
															"camposRetorno"		=> 	array(	"Concepto.codigo",
																							"Concepto.nombre")));
$campos['RelacionesConcepto.desde'] = array();
$campos['RelacionesConcepto.hasta'] = array();
$campos['RelacionesConcepto.formula'] = array();
$campos['RelacionesConcepto.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"concepto de la relacion laboral", "imagen"=>"conceptos.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>