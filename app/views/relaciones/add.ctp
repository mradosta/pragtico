<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.views
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
/**
* Especifico los campos de ingreso de datos.
*/

/**
* Datos del trabajador.
*/
$campos = null;
$campos['Relacion.trabajador_id'] = array(	"lov"=>array("controller"		=> 	"trabajadores",
														"seleccionMultiple"	=> 	0,
															"camposRetorno"	=> 	array(	"Trabajador.cuil",
																					 	"Trabajador.nombre",
																					 	"Trabajador.apellido")));

$campos['Relacion.convenios_categoria_id'] = array(	"label"	=>	"Categoria",
													"lov"	=>	array(	"controller"		=> 	"convenios_categorias",
																		"seleccionMultiple"	=> 	0,
																			"camposRetorno"	=> 	array(	"Convenio.nombre",
																										"ConveniosCategoria.nombre")));

$campos['Relacion.ingreso'] = array();
$campos['Relacion.horas'] = array("label"=>"Horas de Trabajo");
$campos['Relacion.basico'] = array("label"=>"Basico $", "aclaracion"=>"Si lo deja en cero, se utilizara el basico de convenio.");
$campos['Relacion.estado'] = array();
$campos['Relacion.antiguedad_reconocida'] = array();
$fieldsets[] = array('campos' => $campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Trabajador", 'imagen' => 'trabajadores.gif')));


/**
* Datos del empleador.
*/
$campos = null;
$campos['Relacion.id'] = array();
$campos['Relacion.empleador_id'] = array(	"lov"=>array("controller"		=> 	"empleadores",
														"seleccionMultiple"	=> 	0,
														"camposRetorno"		=> 	array(	"Empleador.cuit",
																						"Empleador.nombre")));
$campos['Relacion.area_id'] = array("type"=>"relacionado", "verificarRequerido"=>"forzado", "valor"=>"Area.nombre", "relacion"=>"Relacion.empleador_id", "url"=>"relaciones/areas_relacionado");
$campos['Relacion.legajo'] = array("aclaracion"=>"Si lo deja en blanco, se utilizara el numero de documento del Trabajador.");
if($this->action == "add") {
	$campos['Relacion.recibo_id'] = array("type"=>"relacionado", "valor"=>"Recibo.nombre", "relacion"=>"Relacion.empleador_id", "url"=>"relaciones/recibos_relacionado");
}
$fieldsets[] = array('campos' => $campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Empleador", 'imagen' => 'empleadores.gif')));


/**
* Datos de la afip.
*/
$campos = null;
$campos['Relacion.situacion_id'] = array(	"aclaracion"=>	"Se refiere a la situacion que se informara (SIAP).",
											"lov"		=>	array(	"controller"		=> 	"situaciones",
																	"seleccionMultiple"	=> 	0,
																		"camposRetorno"	=> 	array(	"Situacion.codigo",
																									"Situacion.nombre")));
$campos['Relacion.actividad_id'] = array(	"aclaracion"=>"Se refiere a la actividad (SIAP).",
											"lov"		=>	array(	"controller"		=> 	"actividades",
																	"seleccionMultiple"	=> 	0,
																		"camposRetorno"	=> 	array(	"Actividad.codigo",
																									"Actividad.nombre")));
$campos['Relacion.modalidad_id'] = array(	"lov"	=>	array(	"controller"		=> 	"modalidades",
																"seleccionMultiple"	=> 	0,
																	"camposRetorno"	=> 	array(	"Modalidad.codigo",
																								"Modalidad.nombre")));
$fieldsets[] = array('campos' => $campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Afip", 'imagen' => 'afip.gif')));


/**
* Datos de la desvinculacion.
*/
$campos = null;
$campos['Relacion.egreso'] = array();
$campos['Relacion.observacion'] = array();
$fieldsets[] = array('campos' => $campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Desvinculacion", 'imagen' => 'fin_relacion_laboral.gif')));

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Relacion Laboral", 'imagen' => 'relaciones.gif')));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array('imagen' => 'trabajadores.gif')));
$miga = array('format' 	=> '%s %s (%s)', 
			  'content' => array('Trabajador.apellido', 'Trabajador.nombre', 'Empleador.nombre'));
echo $this->element("add/add", array('fieldset' => $fieldset, "miga" => $miga));


?>