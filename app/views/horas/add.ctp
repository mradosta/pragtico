<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Hora.id'] = array();
$campos['Hora.periodo'] = array("type"=>"periodo");
$campos['Hora.relacion_id'] = array(	"label"=>"Relacion",
											"lov"=>array("controller"	=> 	"relaciones",
													"seleccionMultiple"	=> 	0,
														"camposRetorno"	=> 	array(	"Trabajador.nombre",
																					"Trabajador.apellido",
																					"Empleador.nombre")));
$campos['Hora.cantidad'] = array();
$campos['Hora.tipo'] = array();
if($this->action == "add") {
	$campos['Hora.estado'] = array("options"=>$estados);
}
else {
	$campos['Hora.estado'] = array();
}
$campos['Hora.observacion'] = array();
$fieldset = $formulario->pintarFieldsets(array(array("campos"=>$campos)), array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"horas manual", "imagen"=>"horas.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));

?>