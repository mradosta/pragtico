<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['AusenciasSeguimiento.id'] = array();
$campos['AusenciasSeguimiento.ausencia_id'] = array(	"lov"=>array("controller"	=>	"ausencias",
													"seleccionMultiple"	=> 	0,
													"camposRetorno"		=> 	array(	"Ausencia.desde",
																					"AusenciasMotivo.motivo")));
																				
$campos['AusenciasSeguimiento.desde'] = array();
$campos['AusenciasSeguimiento.hasta'] = array();
$campos['AusenciasSeguimiento.dias'] = array();
$campos['AusenciasSeguimiento.comprobante'] = array("label"=>"Presento Comprobante");
$campos['AusenciasSeguimiento.archivo'] = array("label"=>"Comprobante", "type"=>"file", "descargar"=>true, "mostrar"=>true);
$campos['AusenciasSeguimiento.estado'] = array();
$campos['AusenciasSeguimiento.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Seguimiento de Ausencias", "imagen"=>"seguimiento.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset, "opcionesForm"=>array("enctype"=>"multipart/form-data")));
?>