<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Controlador.id'] = array();
$campos['Controlador.nombre'] = array();
$campos['Controlador.etiqueta'] = array();
$campos['Controlador.ayuda'] = array();
$campos['Controlador.estado'] = array();
$campos['Controlador.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"controladores.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>