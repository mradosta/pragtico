<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Modalidad.id'] = array();
$campos['Modalidad.codigo'] = array();
$campos['Modalidad.nombre'] = array();
$campos['Modalidad.descripcion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"modalidades.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>