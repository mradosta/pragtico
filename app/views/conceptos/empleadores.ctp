<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Empleador'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"EmpleadoresConcepto", "field"=>"id", "valor"=>$v['EmpleadoresConcepto']['id'], "write"=>$v['EmpleadoresConcepto']['write'], "delete"=>$v['EmpleadoresConcepto']['delete']);
	$fila[] = array("model"=>"Empleador", "field"=>"cuit", "valor"=>$v['cuit'], "class"=>"centro");
	$fila[] = array("model"=>"Empleador", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"EmpleadoresConcepto", "field"=>"formula", "valor"=>$v['EmpleadoresConcepto']['formula']);
	$fila[] = array("model"=>"EmpleadoresConcepto", "field"=>"desde", "valor"=>$v['EmpleadoresConcepto']['desde']);
	$fila[] = array("model"=>"EmpleadoresConcepto", "field"=>"hasta", "valor"=>$v['EmpleadoresConcepto']['hasta']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"empleadores_conceptos", "action"=>"add", "EmpleadoresConcepto.concepto_id"=>$this->data['Concepto']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Empleadores", "cuerpo"=>$cuerpo));

?>