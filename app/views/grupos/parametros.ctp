<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['GruposParametro'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"GruposParametro", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"GruposParametro", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"GruposParametro", "field"=>"valor", "valor"=>$v['valor']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"grupos_parametros", "action"=>"add", "GruposParametro.grupo_id"=>$this->data['Grupo']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Parametros", "cuerpo"=>$cuerpo));

?>