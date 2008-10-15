<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['ObrasSocial.id'] = array();
$campos['ObrasSocial.codigo'] = array();
$campos['ObrasSocial.nombre'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"obras_sociales.gif", "legend"=>"Obra Social")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>