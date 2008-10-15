<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Usuario.id'] = array();
$campos['Usuario.nombre'] = array();
if($this->action === "add") {
	$campos['Usuario.clave'] = array("type"=>"password");
}
$campos['Usuario.nombre_completo'] = array();
$campos['Usuario.email'] = array("label"=>"E-Mail");
$campos['Usuario.estado'] = array();
if($this->action === "add") {
	$campos['Usuario.rol_id'] = array("options"=>"listable", "model"=>"Rol", "displayField"=>array("Rol.nombre"), "order"=>array("Rol.nombre"));
}
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"usuarios.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>