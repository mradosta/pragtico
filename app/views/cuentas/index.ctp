<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Cuenta-empleador_id'] 	= array(	"lov"=>array("controller"	=>	"empleadores",
																		"camposRetorno"	=>array("Empleador.cuit",
																								"Empleador.nombre")));
$condiciones['Condicion.Cuenta-sucursal_id'] 	= array("label"=>"Banco",
															"lov"=>array("controller"		=>	"sucursales",
																		"camposRetorno"		=>	array(	"Banco.nombre",
																										"Sucursal.direccion")));
$condiciones['Condicion.Cuenta-tipo'] = array();
$condiciones['Condicion.Cuenta-numero'] = array();

$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Cuentas de los Empleadores", "imagen"=>"cuentas.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Cuenta", "field"=>"id", "valor"=>$v['Cuenta']['id'], "write"=>$v['Cuenta']['write'], "delete"=>$v['Cuenta']['delete']);
	$fila[] = array("model"=>"Empleador", "field"=>"nombre", "nombreEncabezado"=>"Empleador", "valor"=>$v['Empleador']['nombre']);
	$fila[] = array("model"=>"Banco", "field"=>"nombre", "nombreEncabezado"=>"Banco", "valor"=>$v['Sucursal']['Banco']['nombre']);
	$fila[] = array("model"=>"Sucursal", "field"=>"direccion", "nombreEncabezado"=>"Sucursal", "valor"=>$v['Sucursal']['direccion']);
	$fila[] = array("model"=>"Cuenta", "field"=>"tipo", "valor"=>$v['Cuenta']['tipo']);
	$fila[] = array("model"=>"Cuenta", "field"=>"numero", "valor"=>$v['Cuenta']['numero']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>