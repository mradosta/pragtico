<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Sucursal.id'] = array();
$campos['Sucursal.banco_id'] = array("options"=>"listable", "displayField"=>array("Banco.nombre"), "model"=>"Banco");
$campos['Sucursal.codigo'] = array();
$campos['Sucursal.direccion'] = array();
$campos['Sucursal.telefono'] = array();
$campos['Sucursal.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"sucursales.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>