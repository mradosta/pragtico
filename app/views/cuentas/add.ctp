<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Cuenta.id'] = array();
$campos['Cuenta.empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																"seleccionMultiple"	=> 	0,
																	"camposRetorno"	=>	array(	"Empleador.cuit",
																								"Empleador.nombre")));
$campos['Cuenta.sucursal_id'] 	= array("label"=>"Banco",
											"lov"=>array("controller"		=>	"sucursales",
														"seleccionMultiple"	=> 	0,
														"camposRetorno"		=>	array(	"Banco.nombre",
																						"Sucursal.direccion")));


$campos['Cuenta.tipo'] = array();
$campos['Cuenta.numero'] = array();
$campos['Cuenta.cbu'] = array();
$campos['Cuenta.identificador'] = array();
$fieldsets[] = array("campos"=>$campos);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"cuenta del empleador", "imagen"=>"cuentas.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>