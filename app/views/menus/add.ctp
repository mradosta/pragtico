<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Menu.id'] = array();
$campos['Menu.nombre'] = array();
$campos['Menu.etiqueta'] = array();
$campos['Menu.ayuda'] = array();
$campos['Menu.imagen'] = array();
$campos['Menu.orden'] = array();
$campos['Menu.controller'] = array();
$campos['Menu.action'] = array();
$campos['Menu.parent_id'] = array("options"=>"listable", "displayField"=>array("Menu.etiqueta"), "conditions"=>array("Menu.parent_id is null"), "order"=>array("Menu.nombre"), "empty"=>true, "label"=>"Padre");
$campos['Menu.estado'] = array();
$fieldsets[] = array("campos"=>$campos);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"menus.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>