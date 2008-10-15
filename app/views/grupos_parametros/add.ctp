<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['GruposParametro.id'] = array();
$campos['GruposParametro.grupo_id'] = array("options"=>$grupos);

$campos['GruposParametro.nombre'] = array();
$campos['GruposParametro.valor'] = array();
$campos['GruposParametro.descripcion'] = array();

$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Parametro del Grupo", "imagen"=>"parametros.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>