<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Grupo.id'] = array();
$campos['Grupo.nombre'] = array();
$campos['Grupo.empleador_id'] = array(	"lov"		=> array("controller"		=> 	"empleadores",
															"seleccionMultiple"	=> 	0,
															"camposRetorno"		=> 	array(	"Empleador.cuit",
																							"Empleador.nombre")));
$campos['Grupo.estado'] = array();
$campos['Grupo.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"grupos.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>