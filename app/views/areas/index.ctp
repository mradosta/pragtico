<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Area-empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																		"camposRetorno"	=>array("Empleador.cuit",
																								"Empleador.nombre")));
$condiciones['Condicion.Area-nombre'] = array();
$condiciones['Condicion.Area-direccion'] = array();
$condiciones['Condicion.Area-contacto'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"areas.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Area", "field"=>"id", "valor"=>$v['Area']['id'], "write"=>$v['Area']['write'], "delete"=>$v['Area']['delete']);
	$fila[] = array("model"=>"Empleador", "field"=>"nombre", "valor"=>$v['Empleador']['nombre']);
	$fila[] = array("model"=>"Area", "field"=>"nombre", "valor"=>$v['Area']['nombre']);
	$fila[] = array("model"=>"Area", "field"=>"direccion", "valor"=>$v['Area']['direccion']);
	$fila[] = array("model"=>"Area", "field"=>"telefono", "valor"=>$v['Area']['telefono']);
	$fila[] = array("model"=>"Area", "field"=>"contacto", "valor"=>$v['Area']['contacto']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>