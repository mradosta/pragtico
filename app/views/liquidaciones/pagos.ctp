<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Pago'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Pago", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Pago", "field"=>"fecha", "valor"=>$v['fecha']);
	$fila[] = array("model"=>"Pago", "field"=>"moneda", "valor"=>$v['moneda']);
	$fila[] = array("model"=>"Pago", "field"=>"monto", "valor"=>$v['monto'], "tipoDato"=>"moneda");
	$fila[] = array("model"=>"Pago", "field"=>"estado", "valor"=>$v['estado']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"Pagos", "action"=>"add", "Pago.liquidacion_id"=>$this->data['Liquidacion']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Pagos", "cuerpo"=>$cuerpo));

?>