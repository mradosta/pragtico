<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Ropa'] as $k=>$v) {
	$fila = null;
	$accionImprimir = $formulario->link($formulario->image("print.gif", array("alt"=>"Imprimir Orden de Ropa", "title"=>"Imprimir Orden de Ropa")), "../ropas/imprimirOrden/" . $v['id'], array("target"=>"_blank"));
	$fila[] = array("tipo"=>"accion", "valor"=>$accionImprimir);
	$fila[] = array("tipo"=>"desglose", "id"=>$v['id'], "update"=>"desglose_1", "imagen"=>array("nombre"=>"prendas.gif", "alt"=>"Prendas entregadas"), "url"=>'../ropas/prendas');
	$fila[] = array("model"=>"Ropa", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Ropa", "field"=>"fecha", "valor"=>$v['fecha']);
	$fila[] = array("model"=>"Ropa", "field"=>"observacion", "valor"=>$v['observacion']);
	$cuerpo[] = $fila;
}


$url = array("controller"=>"ropas", "action"=>"add", "Ropa.relacion_id"=>$this->data['Relacion']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Entrega de ropa", "cuerpo"=>$cuerpo));

?>