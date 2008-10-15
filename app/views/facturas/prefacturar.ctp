<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Liquidacion-empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																		"camposRetorno"	=>array("Empleador.nombre")));
$condiciones['Condicion.Liquidacion-periodo'] = array("type"=>"periodo");
$condiciones['Condicion.Liquidacion-estado'] = array("options"=>$estados, "type"=>"radio");

$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"liquidaciones.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$id = $v['Factura']['id'];
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose1", "imagen"=>array("nombre"=>"detalles.gif", "alt"=>"Detalles"), "url"=>'detalles');
	$fila[] = array("model"=>"Factura", "field"=>"id", "valor"=>$id, "write"=>$v['Factura']['write'], "delete"=>$v['Factura']['delete']);
	$fila[] = array("model"=>"Empleador", "field"=>"cuit", "valor"=>$v['Empleador']['cuit']);
	$fila[] = array("model"=>"Empleador", "field"=>"nombre", "valor"=>$v['Empleador']['nombre'], "nombreEncabezado"=>"Empleador");
	$fila[] = array("model"=>"Factura", "field"=>"fecha", "valor"=>$v['Factura']['fecha']);
	$fila[] = array("model"=>"Factura", "field"=>"estado", "valor"=>$v['Factura']['estado']);
	$fila[] = array("model"=>"Factura", "field"=>"total", "valor"=>$v['Factura']['total'], "tipoDato"=>"moneda");
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo, "opcionesForm"=>array("action"=>"prefacturar")));

?>