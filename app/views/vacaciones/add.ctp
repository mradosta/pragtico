<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Vacacion.id'] = array();
$campos['Vacacion.relacion_id'] = array(	"label"=>"Relacion",
											"lov"=>array("controller"	=>	"relaciones",
													"seleccionMultiple"	=> 	0,
														"camposRetorno"	=>	array(	"Trabajador.nombre",
																					"Trabajador.apellido",
																					"Empleador.nombre")));
$campos['Vacacion.desde'] = array();
$campos['Vacacion.hasta'] = array();
$campos['Vacacion.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Vacaciones", "imagen"=>"vacaciones.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>