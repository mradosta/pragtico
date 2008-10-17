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
$campos['Area.id'] = array();
$campos['Area.empleador_id'] = array(	"lov"=>array(	"controller"		=>	"empleadores",
														"seleccionMultiple"	=> 	0,
														"camposRetorno"		=>	array(	"Empleador.cuit",
																						"Empleador.nombre")));
$campos['Area.nombre'] = array();
$campos['Area.contacto'] = array();
$campos['Area.direccion'] = array();
$campos['Area.telefono'] = array();
$campos['Area.fax'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"areas.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>