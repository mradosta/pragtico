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
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Coeficiente'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"EmpleadoresCoeficiente", "field"=>"id", "valor"=>$v['EmpleadoresCoeficiente']['id'], "write"=>$v['EmpleadoresCoeficiente']['write'], "delete"=>$v['EmpleadoresCoeficiente']['delete']);
	$fila[] = array("model"=>"EmpleadoresCoeficiente", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"EmpleadoresCoeficiente", "field"=>"tipo", "valor"=>$v['tipo']);
	$fila[] = array("model"=>"EmpleadoresCoeficiente", "field"=>"valor", "valor"=>$v['EmpleadoresCoeficiente']['valor']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"empleadores_coeficientes", "action"=>"add", "EmpleadoresCoeficiente.empleador_id"=>$this->data['Empleador']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Coeficiente", "cuerpo"=>$cuerpo));

?>