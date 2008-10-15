<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Informacion.id'] = array();
$campos['Informacion.nombre'] = array();
$campos['Informacion.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"observaciones.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>