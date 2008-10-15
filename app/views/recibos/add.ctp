<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Recibo.id'] = array();
$campos['Recibo.empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
												"seleccionMultiple"	=> 	0,
													"camposRetorno"	=>	array(	"Empleador.cuit",
																				"Empleador.nombre")));
$campos['Recibo.nombre'] = array();
$campos['Recibo.descripcion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"recibos.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>