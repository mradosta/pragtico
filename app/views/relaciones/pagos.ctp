<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Pago'] as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"desglose", "id"=>$v['liquidacion_id'], "update"=>"desglose_1", "imagen"=>array("nombre"=>"liquidar.gif", "alt"=>"liquidacion"), "url"=>'../liquidaciones/preliquidacion');
	$fila[] = array("model"=>"Pago", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Pago", "field"=>"fecha", "valor"=>$v['fecha']);
	$fila[] = array("model"=>"Pago", "field"=>"pago", "valor"=>$v['pago']);
	$fila[] = array("model"=>"Pago", "field"=>"monto", "valor"=>$formato->format($v['monto'], array("before"=>"$ ", "places"=>2)));
	$fila[] = array("model"=>"Pago", "field"=>"estado", "valor"=>$v['estado']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("desgloses/agregar", array("titulo"=>"Pagos", "cuerpo"=>$cuerpo));

?>