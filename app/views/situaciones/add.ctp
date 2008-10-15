<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Situacion.id'] = array();
$campos['Situacion.codigo'] = array();
$campos['Situacion.nombre'] = array();
$campos['Situacion.descripcion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"situaciones.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>