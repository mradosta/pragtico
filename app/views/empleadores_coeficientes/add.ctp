<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['EmpleadoresCoeficiente.id'] = array();
$campos['EmpleadoresCoeficiente.empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																"seleccionMultiple"	=> 	0,
																	"camposRetorno"	=>	array(	"Empleador.cuit",
																								"Empleador.nombre")));
$campos['EmpleadoresCoeficiente.coeficiente_id'] = array(	"lov"=>array("controller"	=>	"coeficientes",
																	"seleccionMultiple"	=> 	0,
																		"camposRetorno"	=>	array(	"Coeficiente.nombre",
																									"Coeficiente.valor")));
$campos['EmpleadoresCoeficiente.valor'] = array("aclaracion"=>"Indica el valor a sumar o restar al coeficiente.");
$campos['EmpleadoresCoeficiente.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"coeficiente del empleador", "imagen"=>"coeficientes.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>