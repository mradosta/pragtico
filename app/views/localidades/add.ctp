<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Localidad.id'] = array();
$campos['Localidad.provincia_id'] = array("options"=>"listable", "model"=>"Provincia", "displayField"=>array("Provincia.nombre"));
$campos['Localidad.codigo'] = array();
$campos['Localidad.nombre'] = array();
$campos['Localidad.codigo_zona'] = array("aclaracion"=>"Indica el codigo de la zona para AFIP.");
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"localidades.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>