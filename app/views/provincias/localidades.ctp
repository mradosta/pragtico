<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Localidad'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Localidad", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Localidad", "field"=>"codigo", "valor"=>$v['codigo']);
	$fila[] = array("model"=>"Localidad", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"Localidad", "field"=>"codigo_zona", "valor"=>$v['codigo_zona']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"localidades", "action"=>"add", "Localidad.provincia_id"=>$this->data['Provincia']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Localidades", "cuerpo"=>$cuerpo));

?>