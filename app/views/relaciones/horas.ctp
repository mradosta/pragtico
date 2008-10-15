<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Hora'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Hora", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Hora", "field"=>"periodo", "valor"=>$v['periodo']);
	$fila[] = array("model"=>"Hora", "field"=>"cantidad", "valor"=>$v['cantidad']);
 	$fila[] = array("model"=>"Hora", "field"=>"tipo", "valor"=>$v['tipo']);
 	$fila[] = array("model"=>"Hora", "field"=>"estado", "valor"=>$v['estado']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"horas", "action"=>"add", "Hora.relacion_id"=>$this->data['Relacion']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Horas", "cuerpo"=>$cuerpo));

?>