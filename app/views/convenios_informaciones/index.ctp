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
$condiciones['Condicion.ConveniosInformacion-convenio_id'] = array(	"lov"=>array("controller"	=>"convenios",
																			"camposRetorno"	=>array(	"Convenio.numero",
																										"Convenio.nombre")));
$condiciones['Condicion.ConveniosInformacion-informacion_id'] = array("options"=>"listable", "model"=>"Informacion", "empty"=>true, "displayField"=>array("Informacion.nombre"));
$condiciones['Condicion.ConveniosInformacion-valor'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Informacion del Convenio Colectivo", "imagen"=>"informaciones.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"ConveniosInformacion", "field"=>"id", "valor"=>$v['ConveniosInformacion']['id'], "write"=>$v['ConveniosInformacion']['write'], "delete"=>$v['ConveniosInformacion']['delete']);
	$fila[] = array("model"=>"Convenio", "field"=>"nombre", "valor"=>$v['Convenio']['nombre'], "nombreEncabezado"=>"Convenio");
	$fila[] = array("model"=>"Informacion", "field"=>"nombre", "valor"=>$v['Informacion']['nombre'], "nombreEncabezado"=>"Informacion");
	$fila[] = array("model"=>"ConveniosInformacion", "field"=>"valor", "valor"=>$v['ConveniosInformacion']['valor']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>