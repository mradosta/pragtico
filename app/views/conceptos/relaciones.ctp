<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Relacion'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"RelacionesConcepto", "field"=>"id", "valor"=>$v['RelacionesConcepto']['id'], "write"=>$v['RelacionesConcepto']['write'], "delete"=>$v['RelacionesConcepto']['delete']);
	$fila[] = array("model"=>"Empleador", "field"=>"cuit", "valor"=>$v['Empleador']['cuit'] . " - " . $v['Empleador']['nombre'], "nombreEncabezado"=>"Empleador");
	$fila[] = array("model"=>"Trabajador", "field"=>"cuil", "valor"=>$v['Trabajador']['cuil'] . " - " . $v['Trabajador']['nombre'] . " " . $v['Trabajador']['apellido'], "nombreEncabezado"=>"Trabajador");
	$fila[] = array("model"=>"RelacionesConcepto", "field"=>"formula", "valor"=>$v['RelacionesConcepto']['formula']);
	$fila[] = array("model"=>"RelacionesConcepto", "field"=>"desde", "valor"=>$v['RelacionesConcepto']['desde']);
	$fila[] = array("model"=>"RelacionesConcepto", "field"=>"hasta", "valor"=>$v['RelacionesConcepto']['hasta']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"relaciones_conceptos", "action"=>"add", "RelacionesConcepto.concepto_id"=>$this->data['Concepto']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Formulas de las Relaciones Laborales", "cuerpo"=>$cuerpo));

?>