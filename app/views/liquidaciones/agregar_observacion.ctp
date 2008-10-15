<?php

/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Liquidacion.id'] = array();
$campos['Liquidacion.observacion'] = array();
$fieldset = $formulario->pintarFieldsets(array(array("campos"=>$campos)), array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Obervaciones", "imagen"=>"observaciones.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset, "opcionesForm"=>array("action"=>"saveMultiple")));

?>