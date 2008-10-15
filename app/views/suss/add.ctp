<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Suss.id'] = array();
$campos['Suss.empleador_id'] = array(	"lov"=>array(	"controller"		=> 	"empleadores",
														"seleccionMultiple"	=> 	0,
														"camposRetorno"		=> 	array(	"Empleador.cuit",
																						"Empleador.nombre")));
$campos['Suss.fecha'] = array("label"=>"Fecha de Pago");
$campos['Suss.banco_id'] = array("options"=>"listable", "model"=>"Banco", "empty"=>true, "displayField"=>array("Banco.nombre"));
$campos['Suss.periodo'] = array("type"=>"periodo", "periodo"=>array("soloAAAAMM"), "aclaracion"=>"De la forma AAAAMM");
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"suss.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>