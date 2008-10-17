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
$campos['AusenciasSeguimiento.id'] = array();
$campos['AusenciasSeguimiento.ausencia_id'] = array(	"lov"=>array("controller"	=>	"ausencias",
													"seleccionMultiple"	=> 	0,
													"camposRetorno"		=> 	array(	"Ausencia.desde",
																					"AusenciasMotivo.motivo")));
																				
$campos['AusenciasSeguimiento.desde'] = array();
$campos['AusenciasSeguimiento.hasta'] = array();
$campos['AusenciasSeguimiento.dias'] = array();
$campos['AusenciasSeguimiento.comprobante'] = array("label"=>"Presento Comprobante");
$campos['AusenciasSeguimiento.archivo'] = array("label"=>"Comprobante", "type"=>"file", "descargar"=>true, "mostrar"=>true);
$campos['AusenciasSeguimiento.estado'] = array();
$campos['AusenciasSeguimiento.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Seguimiento de Ausencias", "imagen"=>"seguimiento.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset, "opcionesForm"=>array("enctype"=>"multipart/form-data")));
?>