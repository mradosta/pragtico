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
$campos['Convenio.id'] = array();
$campos['Convenio.numero'] = array();
$campos['Convenio.nombre'] = array();
$campos['Convenio.archivo'] = array("type"=>"file", "descargar"=>true);
$campos['Convenio.actualizacion'] = array("label"=>"Ultima Actualizacion");
$campos['Convenio.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"convenios.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset, "opcionesForm"=>array("enctype"=>"multipart/form-data"), "migaEdit" => $this->data[0]['Convenio']['nombre']));
?>