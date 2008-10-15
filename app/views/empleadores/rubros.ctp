<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Rubro'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Rubro", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Rubro", "field"=>"nombre", "valor"=>$v['nombre']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"empleadores_rubros", "action"=>"add", "EmpleadoresRubro.empleador_id"=>$this->data['Empleador']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Rubros", "cuerpo"=>$cuerpo));

?>