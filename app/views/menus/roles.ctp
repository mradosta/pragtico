<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Grupo'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"GruposMenu", "field"=>"id", "valor"=>$v['GruposMenu']['id'], "write"=>$v['GruposMenu']['write'], "delete"=>$v['GruposMenu']['delete']);
	$fila[] = array("model"=>"Grupo", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"Grupo", "field"=>"estado", "valor"=>$v['estado']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"grupos_menus", "action"=>"add", "GruposMenu.menu_id"=>$this->data['Menu']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Grupos", "cuerpo"=>$cuerpo));

?>