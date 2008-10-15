<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['EmpleadoresRubro.id'] = array();
$campos['EmpleadoresRubro.empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																"seleccionMultiple"	=> 	0,
																	"camposRetorno"	=>	array(	"Empleador.cuit",
																								"Empleador.nombre")));
$campos['EmpleadoresRubro.rubro_id'] = array("options"=>$rubros);
$fieldsets[] = array("campos"=>$campos);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"rubro del empleador", "imagen"=>"rubros.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>