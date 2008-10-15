<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Accion.id'] = array();
$campos['Accion.controlador_id'] = array("options"=>"listable", "model"=>"Controlador", "displayField"=>array("Controlador.nombre"), "empty"=>true);
$campos['Accion.nombre'] = array();
$campos['Accion.etiqueta'] = array();
$campos['Accion.ayuda'] = array();
$campos['Accion.estado'] = array();
$campos['Accion.seguridad'] = array("aclaracion"=>"Indica si debe chequearse la seguridad sobre esta accion.");
$campos['Accion.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"acciones.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>