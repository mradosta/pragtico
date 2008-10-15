<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['RolesUsuario.id'] = array();
$campos['RolesUsuario.usuario_id'] = array(	"lov"=>array("controller"	=>	"usuarios",
														"seleccionMultiple"	=> 	0,
															"camposRetorno"	=>	array(	"Usuario.nombre",
																						"Usuario.nombre_completo")));
$campos['RolesUsuario.rol_id'] = array("options"=>"listable", "model"=>"Rol", "displayField"=>"Rol.nombre", "empty"=>true);
$campos['RolesUsuario.estado'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"relacion entre Usuario y Rol", "imagen"=>"usuarios.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>