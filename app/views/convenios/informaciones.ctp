<?php
/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['ConveniosInformacion'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"ConveniosInformacion", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Informacion", "field"=>"nombre", "valor"=>$v['Informacion']['nombre']);
	$fila[] = array("model"=>"ConveniosInformacion", "field"=>"valor", "valor"=>$v['valor']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"convenios_informaciones", "action"=>"add", "ConveniosInformacion.convenio_id"=>$this->data['Convenio']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Informacion Adicional", "cuerpo"=>$cuerpo));

?>