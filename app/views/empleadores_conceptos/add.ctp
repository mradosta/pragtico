<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['EmpleadoresConcepto.id'] = array();
$campos['EmpleadoresConcepto.empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																"seleccionMultiple"	=> 	0,
																	"camposRetorno"	=>	array(	"Empleador.cuit",
																								"Empleador.nombre")));
$campos['EmpleadoresConcepto.concepto_id'] = array(	"lov"=>array("controller"	=>	"conceptos",
															"seleccionMultiple"	=> 	0,
																"camposRetorno"	=>	array(	"Concepto.codigo",
																							"Concepto.nombre")));
$campos['EmpleadoresConcepto.desde'] = array();
$campos['EmpleadoresConcepto.hasta'] = array();
$campos['EmpleadoresConcepto.formula'] = array();
$campos['EmpleadoresConcepto.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"concepto del empleador", "imagen"=>"conceptos.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>