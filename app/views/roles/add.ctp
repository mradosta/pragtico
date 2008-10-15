<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Rol.id'] = array();
$campos['Rol.nombre'] = array();
$campos['Rol.estado'] = array();
$campos['Rol.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Rol", "imagen"=>"roles.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>