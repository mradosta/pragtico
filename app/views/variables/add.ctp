<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Variable.id'] = array();
$campos['Variable.nombre'] = array();
$campos['Variable.formula'] = array();
$campos['Variable.formato'] = array();
$campos['Variable.descripcion'] = array();
$campos['Variable.ejemplo'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"variable.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>