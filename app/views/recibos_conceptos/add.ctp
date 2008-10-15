<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['RecibosConcepto.id'] = array();
$campos['RecibosConcepto.recibo_id'] = array(	"lov"=>array("controller"	=>	"recibos",
														"seleccionMultiple"	=> 	0,
															"camposRetorno"	=>	array("Recibo.nombre")));

$campos['RecibosConcepto.concepto_id'] = array(	"lov"=>array("controller"	=>	"conceptos",
														"seleccionMultiple"	=> 	0,
															"camposRetorno"	=>	array(	"Concepto.codigo",
																						"Concepto.nombre")));
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Conceptos de un Recibo", "imagen"=>"recibos_conceptos.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>