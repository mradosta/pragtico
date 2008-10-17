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
$campos = null;
$campos['Empleador.id'] = array();
$campos['Empleador.cuit'] = array();
$campos['Empleador.alta'] = array();
$campos['Empleador.nombre'] = array();
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("fieldset"=>array("legend"=>"Identificacion", "imagen"=>"identificacion.gif")));

$campos = null;
$campos['Empleador.direccion'] = array();
$campos['Empleador.codigo_postal'] = array();
$campos['Empleador.barrio'] = array();
$campos['Empleador.ciudad'] = array();
$campos['Empleador.localidad_id'] = array(	"lov"=>array("controller"	=>	"localidades",
														"seleccionMultiple"	=> 	0,
														"separadorRetorno"	=> 	", ",
														"camposRetorno"		=>	array(	"Provincia.nombre",
																						"Localidad.nombre")));
$campos['Empleador.pais'] = array();
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("fieldset"=>array("legend"=>"Ubicacion", "imagen"=>"ubicacion.gif")));

$campos = null;
$campos['Empleador.telefono'] = array();
$campos['Empleador.fax'] = array();
$campos['Empleador.pagina_web'] = array();
$campos['Empleador.email'] = array();
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("fieldset"=>array("legend"=>"Contacto", "imagen"=>"contacto.gif")));

$campos = null;
$campos['Empleador.redondear'] = array("aclaracion"=>"Indica si debe redondear la liquidacion.");
$campos['Empleador.facturar_por_area'] = array();
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("fieldset"=>array("legend"=>"Informacion Adicional", "imagen"=>"informacion_adicional.gif")));

$campos = null;
$campos['Empleador.corresponde_reduccion'] = array("aclaracion"=>"Indica si corresponde reduccion (SIAP).");
$campos['Empleador.actividad_id'] = array(	"aclaracion"=> "Se refiere a la actividad (SIAP).",
											"lov"		=>	array(	"controller"		=> 	"actividades",
																"seleccionMultiple"	=> 	0,
																	"camposRetorno"	=> 	array(	"Actividad.codigo",
																								"Actividad.nombre")));
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("fieldset"=>array("legend"=>"Afip", "imagen"=>"afip.gif")));


$campos = null;
$campos['Empleador.observacion'] = array();
if($this->action == "add") {
	$campos['Area.crear_area_general'] = array("type"=>"checkbox", "class"=>"checkbox", "checked"=>"true");
}
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("fieldset"=>array("legend"=>"Observaciones", "imagen"=>"observaciones.gif")));


/**
* Pinto el element add con todos los fieldsets que he definido.
*/
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"empleadores.gif")));
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));

/*
echo $formulario->codeBlock('
	jQuery("#EmpleadorCuit").mask("99-99999999-9");
');
*/
?>