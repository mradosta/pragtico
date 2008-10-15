<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Usuario.id'] = array("value"=>$usuario['Usuario']['id']);
$campos['Usuario.clave_anterior'] = array("type"=>"password", "label"=>"Clave Actual");
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Actual", "imagen"=>"cambiar_clave.gif")));

$campos = null;
$campos['Usuario.clave_nueva'] = array("type"=>"password", "label"=>"Nueva Clave");
$campos['Usuario.clave_nueva_reingreso'] = array("type"=>"password", "label"=>"Reingrese");
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Nueva", "imagen"=>"cambiar_clave.gif")));

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Cambio de clave para " . $usuario['Usuario']['nombre_completo'] . " (" . $usuario['Usuario']['nombre'] . ")", "imagen"=>"usuarios.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset, "opcionesForm"=>array("action"=>"cambiar_clave")));
?>