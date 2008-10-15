<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Descuento'] as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"desglose", "id"=>$v['id'], "update"=>"desglose_1", "imagen"=>array("nombre"=>"descuentos.gif", "alt"=>"Detalle de los descuentos"), "url"=>'descuentos_detalle');
	$fila[] = array("model"=>"Descuento", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Descuento", "field"=>"alta", "valor"=>$v['alta']);
	$fila[] = array("model"=>"Descuento", "field"=>"desde", "valor"=>$v['desde']);
	$fila[] = array("model"=>"Descuento", "field"=>"monto", "valor"=>"$ " . $v['monto']);
	$fila[] = array("model"=>"Descuento", "field"=>"descontar", "valor"=>$v['descontar']);
	$fila[] = array("model"=>"Descuento", "field"=>"maximo", "valor"=>$v['maximo'] . " %");
	$fila[] = array("model"=>"Descuento", "field"=>"tipo", "valor"=>$v['tipo']);
	$fila[] = array("model"=>"Descuento", "field"=>"estado", "valor"=>$v['estado']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"descuentos", "action"=>"add", "Descuento.relacion_id"=>$this->data['Relacion']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Descuentos", "cuerpo"=>$cuerpo));

?>