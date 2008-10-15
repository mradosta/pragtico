<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Convenio.id'] = array();
$campos['Convenio.numero'] = array();
$campos['Convenio.nombre'] = array();
$campos['Convenio.archivo'] = array("type"=>"file", "descargar"=>true);
$campos['Convenio.actualizacion'] = array("label"=>"Ultima Actualizacion");
$campos['Convenio.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"convenios.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset, "opcionesForm"=>array("enctype"=>"multipart/form-data")));
?>