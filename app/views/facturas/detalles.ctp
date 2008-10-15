<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['FacturasDetalle'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"FacturasDetalle", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"FacturasDetalle", "field"=>"valor", "valor"=>$v['valor']);
	$fila[] = array("model"=>"Coeficiente", "field"=>"nombre", "valor"=>$v['Coeficiente']['nombre']);
	$fila[] = array("model"=>"FacturasDetalle", "field"=>"subtotal", "valor"=>$v['subtotal'], "tipoDato"=>"moneda");
	$fila[] = array("model"=>"FacturasDetalle", "field"=>"valor", "valor"=>$formato->format($v['valor'], array("tipo"=>"number")));
	$fila[] = array("model"=>"FacturasDetalle", "field"=>"total", "valor"=>$v['total'], "tipoDato"=>"moneda");
	$cuerpo[] = $fila;
}

echo $this->renderElement("desgloses/agregar", array("titulo"=>"Detalle", "cuerpo"=>$cuerpo));

?>