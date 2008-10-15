<?php
/**
* Especifico los campos de ingreso de datos.
*/

/**
* Por bug, se confunde con el nombre de la condicion, por eso le pongo un label.
*/
$campos = null;
$campos['Condicion.id'] = array("label"=>"id"); 
$campos['Condicion.codigo'] = array("label"=>"Codigo");
$campos['Condicion.nombre'] = array("label"=>"Nombre");
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"condiciones.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>