<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Empleador'] as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"desglose", "id"=>$v['Relacion']['id'], "update"=>"desglose_1", "imagen"=>array("nombre"=>"ausencias.gif", "alt"=>"Ausencias"), "url"=>'../relaciones/ausencias');
	$fila[] = array("tipo"=>"desglose", "id"=>$v['Relacion']['id'], "update"=>"desglose_2", "imagen"=>array("nombre"=>"conceptos.gif", "alt"=>"Conceptos"), "url"=>'../relaciones/conceptos');
	$fila[] = array("tipo"=>"desglose", "id"=>$v['Relacion']['id'], "update"=>"desglose_3", "imagen"=>array("nombre"=>"ropas.gif", "alt"=>"Ropa Entregada"), "url"=>'../relaciones/ropaEntregada');
	$fila[] = array("tipo"=>"desglose", "id"=>$v['Relacion']['id'], "update"=>"desglose_4", "imagen"=>array("nombre"=>"horas.gif", "alt"=>"Horas"), "url"=>'../relaciones/horas');
	$fila[] = array("tipo"=>"desglose", "id"=>$v['Relacion']['id'], "update"=>"desglose_5", "imagen"=>array("nombre"=>"descuentos.gif", "alt"=>"Descuentos"), "url"=>'../relaciones/descuentos');
	$fila[] = array("tipo"=>"desglose", "id"=>$v['id'], "update"=>"desglose_6", "imagen"=>array("nombre"=>"recibos.gif", "alt"=>"Recibos"), "url"=>'../empleadores/recibos');
	$fila[] = array("model"=>"Relacion", "field"=>"id", "valor"=>$v['Relacion']['id'], "write"=>$v['Relacion']['write'], "delete"=>$v['Relacion']['delete']);
	$fila[] = array("model"=>"Empleador", "field"=>"cuit", "valor"=>$v['cuit'], "class"=>"centro");
	$fila[] = array("model"=>"Empleador", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"Relacion", "field"=>"ingreso", "valor"=>$v['Relacion']['ingreso']);
	$fila[] = array("model"=>"Relacion", "field"=>"horas", "valor"=>$v['Relacion']['horas']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"relaciones", "action"=>"add", "Relacion.trabajador_id"=>$this->data['Trabajador']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Empleadores", "cuerpo"=>$cuerpo));

?>