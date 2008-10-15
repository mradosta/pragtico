<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Suss'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Suss", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Suss", "field"=>"fecha", "valor"=>$v['fecha']);
	$fila[] = array("model"=>"Suss", "field"=>"periodo", "valor"=>$v['periodo']);
	$fila[] = array("model"=>"Banco", "field"=>"nombre", "valor"=>$v['Banco']['nombre']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"suss", "action"=>"add", "Suss.empleador_id"=>$this->data['Empleador']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Suss", "cuerpo"=>$cuerpo));

?>