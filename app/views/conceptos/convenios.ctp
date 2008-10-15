<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Convenio'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"ConveniosConcepto", "field"=>"id", "valor"=>$v['ConveniosConcepto']['id'], "write"=>$v['ConveniosConcepto']['write'], "delete"=>$v['ConveniosConcepto']['delete']);
	$fila[] = array("model"=>"Convenio", "field"=>"numero", "valor"=>$v['numero']);
	$fila[] = array("model"=>"Convenio", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"ConveniosConcepto", "field"=>"formula", "valor"=>$v['ConveniosConcepto']['formula']);
	$fila[] = array("model"=>"ConveniosConcepto", "field"=>"desde", "valor"=>$v['ConveniosConcepto']['desde']);
	$fila[] = array("model"=>"ConveniosConcepto", "field"=>"hasta", "valor"=>$v['ConveniosConcepto']['hasta']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"convenios_conceptos", "action"=>"add", "ConveniosConcepto.concepto_id"=>$this->data['Concepto']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Convenios", "cuerpo"=>$cuerpo));

?>