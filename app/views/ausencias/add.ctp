<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Ausencia.id'] = array();
$campos['Ausencia.relacion_id'] = array(	"lov"=>array("controller"	=>	"relaciones",
													"seleccionMultiple"	=> 	0,
													"camposRetorno"		=> 	array(	"Empleador.nombre",
																					"Trabajador.apellido")));
																					
$campos['Ausencia.ausencia_motivo_id'] = array(	"empty"			=> true,
												"options"		=> "listable",
												"order"			=> "AusenciasMotivo.motivo",
												"displayField"	=> "AusenciasMotivo.motivo",
												"groupField"	=> "AusenciasMotivo.tipo",
												"model"			=> "AusenciasMotivo",
												"label"			=> "Motivo");
$fieldsets[] = 	array("campos"=>$campos);


$campos = null;
$campos['AusenciasSeguimiento.id'] = array();
$campos['AusenciasSeguimiento.desde'] = array();
$campos['AusenciasSeguimiento.hasta'] = array();
$campos['AusenciasSeguimiento.dias'] = array();
$campos['AusenciasSeguimiento.comprobante'] = array("label"=>"Presento Comprobante");
$campos['AusenciasSeguimiento.archivo'] = array("label"=>"Comprobante", "type"=>"file", "descargar"=>true, "mostrar"=>true);
$campos['AusenciasSeguimiento.estado'] = array();
$campos['AusenciasSeguimiento.observacion'] = array();
$fieldsets[] = 	array("campos"=>$campos, "opciones"=>array("fieldset"=>array("class"=>"detail", "legend"=>"Seguimientos", "imagen"=>"seguimientos.gif")));

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Ausencias", "imagen"=>"ausencias.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset, "opcionesForm"=>array("enctype"=>"multipart/form-data")));
$ajax->jsPredefinido(array("tipo"=>"detalle", "agregar"=>true, "quitar"=>true));

?>