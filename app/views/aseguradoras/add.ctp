<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Aseguradora.id'] = array();
$campos['Aseguradora.codigo'] = array();
$campos['Aseguradora.nombre'] = array();
$campos['Aseguradora.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"aseguradoras.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>