<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Recibo-empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																			"camposRetorno"	=>array("Empleador.cuit",
																									"Empleador.nombre")));

$condiciones['Condicion.Recibo-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"recibos.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"desglose", "id"=>$v['Recibo']['id'], "update"=>"desglose1", "imagen"=>array("nombre"=>"detalles.gif", "alt"=>"Conceptos (Detalle del Recibo)"), "url"=>'conceptos');
	$fila[] = array("model"=>"Recibo", "field"=>"id", "valor"=>$v['Recibo']['id'], "write"=>$v['Recibo']['write'], "delete"=>$v['Recibo']['delete']);
	$fila[] = array("model"=>"Empleador", "field"=>"nombre", "valor"=>$v['Empleador']['nombre'], "nombreEncabezado"=>"Empleador");
	$fila[] = array("model"=>"Recibo", "field"=>"nombre", "valor"=>$v['Recibo']['nombre']);
	$fila[] = array("model"=>"Recibo", "field"=>"descripcion", "valor"=>$v['Recibo']['descripcion']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>