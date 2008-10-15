<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['ConveniosConcepto.id'] = array();
$campos['ConveniosConcepto.convenio_id'] = array("lov"=>array(	"controller"		=>	"convenios",
																"seleccionMultiple"	=> 	0,
																"camposRetorno"		=>	array(	"Convenio.numero",
																								"Convenio.nombre")));

$campos['ConveniosConcepto.concepto_id'] = array("lov"=>array(	"controller"		=>	"conceptos",
																"seleccionMultiple"	=> 	0,
																"camposRetorno"		=>	array(	"Concepto.codigo",
																								"Concepto.nombre")));
																					
$campos['ConveniosConcepto.desde'] = array();
$campos['ConveniosConcepto.hasta'] = array();
$campos['ConveniosConcepto.formula'] = array();
$campos['ConveniosConcepto.observacion'] = array();
																					
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"concepto del convenio colectivo", "imagen"=>"conceptos.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>