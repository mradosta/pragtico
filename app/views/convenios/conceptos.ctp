<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Concepto'] as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"accion", "valor"=>$formulario->link($formulario->image("asignar.gif", array("alt"=>"Asignar este concepto a todos los Trabajadores", "title"=>"Asignar este concepto a todos los Trabajadores")), array("action"=>"manipular_concepto/agregar", "convenio_id"=>$this->data['Convenio']['id'], "concepto_id"=>$v['id']), array(), "Asignara este concepto a todos los trabajadores de todos los empleadores que tengan el convenio colectivo '" . $this->data['Convenio']['nombre'] . "'. Desea continuar?"));
	$fila[] = array("tipo"=>"accion", "valor"=>$formulario->link($formulario->image("quitar.gif", array("alt"=>"Quitara este concepto de todos los Trabajadores", "title"=>"Quitara este concepto de todos los Trabajadores")), array("action"=>"manipular_concepto/quitar", "convenio_id"=>$this->data['Convenio']['id'], "concepto_id"=>$v['id']), array(), "Quitara este concepto de todos los trabajadores de todos los empleadores que tengan el convenio colectivo '" . $this->data['Convenio']['nombre'] . "'. Desea continuar?"));
	$fila[] = array("model"=>"ConveniosConcepto", "field"=>"id", "valor"=>$v['ConveniosConcepto']['id'], "write"=>$v['ConveniosConcepto']['write'], "delete"=>$v['ConveniosConcepto']['delete']);
	$fila[] = array("model"=>"ConveniosConcepto", "field"=>"codigo", "valor"=>$v['codigo']);
	$fila[] = array("model"=>"ConveniosConcepto", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"ConveniosConcepto", "field"=>"formula", "valor"=>$v['ConveniosConcepto']['formula']);
	$cuerpo[] = $fila;
}

$url[] = array("controller"=>"convenios_conceptos", "action"=>"add", "ConveniosConcepto.convenio_id"=>$this->data['Convenio']['id']);
$url[] = array("controller"=>"convenios_conceptos", "action"=>"add_rapido", "ConveniosConcepto.convenio_id"=>$this->data['Convenio']['id'], "texto"=>"Carga Rapida");
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Conceptos", "cuerpo"=>$cuerpo));

?>