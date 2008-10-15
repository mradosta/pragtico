<?php
/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['PreferenciasValor'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"PreferenciasValor", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"PreferenciasValor", "field"=>"valor", "valor"=>$v['valor']);
	$fila[] = array("model"=>"PreferenciasValor", "field"=>"predeterminado", "valor"=>$v['predeterminado']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"preferencias_valores", "action"=>"add", "PreferenciasValor.preferencia_id"=>$this->data['Preferencia']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Valores", "cuerpo"=>$cuerpo));

?>