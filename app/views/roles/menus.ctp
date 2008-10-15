<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Menu'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"RolesMenu", "field"=>"id", "valor"=>$v['RolesMenu']['id'], "write"=>$v['RolesMenu']['write'], "delete"=>$v['RolesMenu']['delete']);
	$fila[] = array("model"=>"Menu", "field"=>"etiqueta", "valor"=>$v['etiqueta']);
	$fila[] = array("model"=>"Menu", "field"=>"orden", "valor"=>$v['orden']);
	$fila[] = array("model"=>"RolesMenu", "field"=>"estado", "valor"=>$v['RolesMenu']['estado']);
 	$fila[] = array("model"=>"Menu", "field"=>"controller", "valor"=>$v['controller']);
 	$fila[] = array("model"=>"Menu", "field"=>"action", "valor"=>$v['action']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"roles_menus", "action"=>"add", "RolesMenu.rol_id"=>$this->data['Rol']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Menus", "cuerpo"=>$cuerpo));

?>