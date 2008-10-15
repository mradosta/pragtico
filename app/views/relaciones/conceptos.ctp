<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['RelacionesConcepto'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"RelacionesConcepto", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Concepto", "field"=>"codigo", "valor"=>$v['Concepto']['codigo']);
	$fila[] = array("model"=>"Concepto", "field"=>"nombre", "valor"=>$v['Concepto']['nombre']);
 	$fila[] = array("model"=>"RelacionesConcepto", "field"=>"formula", "valor"=>$v['formula']);
	$cuerpo[] = $fila;
}

$url[] = array("controller"=>"relaciones_conceptos", "action"=>"add", "RelacionesConcepto.relacion_id"=>$this->data['Relacion']['id']);
$url[] = array("controller"=>"relaciones_conceptos", "action"=>"add_rapido", "RelacionesConcepto.relacion_id"=>$this->data['Relacion']['id'], "texto"=>"Carga Rapida");
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Conceptos", "cuerpo"=>$cuerpo));

?>