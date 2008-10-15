<?php
/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Ausencia'] as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"desglose", "id"=>$v['id'], "update"=>"desglose_1", "imagen"=>array("nombre"=>"seguimientos.gif", "alt"=>"Seguimientos"), "url"=>'../ausencias/seguimientos');
	$fila[] = array("model"=>"Ausencia", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"AusenciasMotivo", "field"=>"motivo", "valor"=>$v['AusenciasMotivo']['motivo']);
	$fila[] = array("model"=>"Ausencia", "field"=>"desde", "valor"=>$v['desde'], "tipoDato"=>"date");
	$fila[] = array("model"=>"Ausencia", "field"=>"hasta", "valor"=>$v['hasta'], "tipoDato"=>"date");
	$fila[] = array("model"=>"Ausencia", "field"=>"dias", "valor"=>$v['dias'], "tipoDato"=>"decimal");
	$cuerpo[] = $fila;
}


$url = array("controller"=>"ausencias", "action"=>"add", "Ausencia.relacion_id"=>$this->data['Relacion']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Ausencias", "cuerpo"=>$cuerpo));

?>