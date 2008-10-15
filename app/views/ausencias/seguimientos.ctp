<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['AusenciasSeguimiento'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"AusenciasSeguimiento", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"AusenciasSeguimiento", "field"=>"desde", "valor"=>$v['desde']);
	$fila[] = array("model"=>"AusenciasSeguimiento", "field"=>"hasta", "valor"=>$v['hasta']);
	$fila[] = array("model"=>"AusenciasSeguimiento", "field"=>"dias", "valor"=>$v['dias']);
	$fila[] = array("model"=>"AusenciasSeguimiento", "field"=>"comprobante", "valor"=>$v['comprobante']);
	$fila[] = array("model"=>"AusenciasSeguimiento", "field"=>"estado", "valor"=>$v['estado']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"ausencias_seguimientos", "action"=>"add", "AusenciasSeguimiento.ausencia_id"=>$this->data['Ausencia']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Seguimientos", "cuerpo"=>$cuerpo));

?>