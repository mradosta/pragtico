<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Sucursal'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Sucursal", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Sucursal", "field"=>"codigo", "valor"=>$v['codigo']);
	$fila[] = array("model"=>"Sucursal", "field"=>"direccion", "valor"=>$v['direccion']);
	$fila[] = array("model"=>"Sucursal", "field"=>"telefono", "valor"=>$v['telefono']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"sucursales", "action"=>"add", "Sucursal.banco_id"=>$this->data['Banco']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Sucursales", "cuerpo"=>$cuerpo));

?>