<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Banco.id'] = array();
$campos['Banco.codigo'] = array();
$campos['Banco.nombre'] = array();
$campos['Banco.direccion'] = array();
$campos['Banco.telefono'] = array();
$campos['Banco.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$campos = null;
$campos['Sucursal.id'] = array();
$campos['Sucursal.codigo'] = array();
$campos['Sucursal.direccion'] = array();
$campos['Sucursal.telefono'] = array();
$campos['Sucursal.observacion'] = array();
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("fieldset"=>array("class"=>"detail", "legend"=>"Sucursal", "imagen"=>"sucursales.gif")));

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"bancos.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
$this->addScript($ajax->jsPredefinido(array("tipo"=>"detalle", "agregar"=>true, "quitar"=>true)));
?>