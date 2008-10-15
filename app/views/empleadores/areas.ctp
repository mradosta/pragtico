<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Area'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Area", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Area", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"Area", "field"=>"direccion", "valor"=>$v['direccion']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"areas", "action"=>"add", "Area.empleador_id"=>$this->data['Empleador']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Areas", "cuerpo"=>$cuerpo));

?>