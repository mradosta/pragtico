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
foreach ($this->data['Accion'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"GruposAccion", "field"=>"id", "valor"=>$v['GruposAccion']['id'], "write"=>$v['GruposAccion']['write'], "delete"=>$v['GruposAccion']['delete']);
	$fila[] = array("model"=>"Menu", "field"=>"etiqueta", "valor"=>$v['etiqueta']);
	$fila[] = array("model"=>"Menu", "field"=>"orden", "valor"=>$v['orden']);
	$fila[] = array("model"=>"GruposAccion", "field"=>"estado", "valor"=>$v['GruposAccion']['estado']);
 	$fila[] = array("model"=>"Menu", "field"=>"controller", "valor"=>$v['controller']);
 	$fila[] = array("model"=>"Menu", "field"=>"action", "valor"=>$v['action']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"grupos_acciones", "action"=>"add", "GruposAccion.grupo_id"=>$this->data['Grupo']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Acciones", "cuerpo"=>$cuerpo));

?>