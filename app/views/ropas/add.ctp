<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Ropa.id'] = array();
$campos['Ropa.fecha'] = array();
$campos['Ropa.relacion_id'] = array(	"lov"=>array(	"controller"		=>	"relaciones",
														"seleccionMultiple"	=> 	0,
															"camposRetorno"	=> 	array(	"Empleador.nombre",
																						"Trabajador.apellido")));

$campos['Ropa.observacion'] = array();
$fieldsets[] = 	array("campos"=>$campos, "opciones"=>array("fieldset"=>array("legend"=>"Datos de la Orden", "imagen"=>"ropas.gif")));

$campos = null;
$campos['RopasDetalle.id'] = array();
$campos['RopasDetalle.prenda'] = array("type"=>"select");
$campos['RopasDetalle.tipo'] = array();
$campos['RopasDetalle.color'] = array();
$campos['RopasDetalle.modelo'] = array();
$campos['RopasDetalle.tamano'] = array("label"=>"Tamaño / Numero");
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("fieldset"=>array("class"=>"detail", "legend"=>"prenda", "imagen"=>"prendas.gif")));

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Orden para la entrega de ropa", "imagen"=>"ropas.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
$this->addScript($ajax->jsPredefinido(array("tipo"=>"detalle", "agregar"=>true, "quitar"=>true)));
?>