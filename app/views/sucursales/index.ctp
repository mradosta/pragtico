<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Sucursal-banco_id'] = array("options"=>"listable", "empty"=>true, "displayField"=>array("Banco.nombre"), "model"=>"Banco");
$condiciones['Condicion.Sucursal-codigo'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Sucursal", "imagen"=>"sucursales.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Sucursal", "field"=>"id", "valor"=>$v['Sucursal']['id'], "write"=>$v['Sucursal']['write'], "delete"=>$v['Sucursal']['delete']);
	$fila[] = array("model"=>"Banco", "field"=>"nombre", "valor"=>$v['Banco']['nombre'], "nombreEncabezado"=>"Banco");
	$fila[] = array("model"=>"Sucursal", "field"=>"codigo", "valor"=>$v['Sucursal']['codigo']);
	$fila[] = array("model"=>"Sucursal", "field"=>"direccion", "valor"=>$v['Sucursal']['direccion']);
	$fila[] = array("model"=>"Sucursal", "field"=>"telefono", "valor"=>$v['Sucursal']['telefono']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>