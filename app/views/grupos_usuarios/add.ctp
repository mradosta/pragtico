<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['GruposUsuario.id'] = array();
$campos['GruposUsuario.usuario_id'] = array(	"lov"=>array("controller"	=>	"usuarios",
														"seleccionMultiple"	=> 	0,
															"camposRetorno"	=>	array(	"Usuario.nombre",
																						"Usuario.nombre_completo")));
$campos['GruposUsuario.grupo_id'] = array("options"=>"listable", "model"=>"Grupo", "displayField"=>"Grupo.nombre", "empty"=>true);
$campos['GruposUsuario.estado'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"relacion entre Usuario y Grupo", "imagen"=>"usuarios.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>