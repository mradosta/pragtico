<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Actividad.id'] = array();
$campos['Actividad.codigo'] = array();
$campos['Actividad.nombre'] = array();
$campos['Actividad.tipo'] = array();
$campos['Actividad.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"actividades.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>