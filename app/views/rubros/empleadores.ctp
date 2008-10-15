<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Empleador'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Empleador", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Empleador", "field"=>"cuit", "valor"=>$v['cuit']);
	$fila[] = array("model"=>"Empleador", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"Empleador", "field"=>"telefono", "valor"=>$v['telefono']);
	$fila[] = array("model"=>"Empleador", "field"=>"email", "valor"=>$v['email']);
$cuerpo[] = $fila;
}

$url = array("controller"=>"empleadores_rubros", "action"=>"add", "EmpleadoresRubro.rubro_id"=>$this->data['Rubro']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Empleadores", "cuerpo"=>$cuerpo));

?>