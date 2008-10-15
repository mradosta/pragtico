<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Coeficiente.id'] = array();
$campos['Coeficiente.nombre'] = array();
$campos['Coeficiente.tipo'] = array();
$campos['Coeficiente.valor'] = array();
$campos['Coeficiente.descripcion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"coeficientes.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>