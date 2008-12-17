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
$campos['Preferencia.id'] = array();
$campos['Preferencia.nombre'] = array();
$campos['Preferencia.descripcion'] = array();

$fieldsets[] = 	array("campos"=>$campos, "opciones"=>array("fieldset"=>array("legend"=>"Datos de la Preferencia", "imagen"=>"preferencias.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/

$campos = null;
$campos['PreferenciasValor.id'] = array("name"=>"data[PreferenciasValor][0][id]");
$campos['PreferenciasValor.valor'] = array("name"=>"data[PreferenciasValor][0][valor]");
$campos['PreferenciasValor.predeterminado'] = array("name"=>"data[PreferenciasValor][0][predeterminado]", "aclaracion"=>"Especifica cual sera el valor por defecto para esta preferencia.");
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("fieldset"=>array("class"=>"detail", "legend"=>"valor de la preferencia", "imagen"=>"valores.gif")));

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Preferencias", "imagen"=>"preferencias.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset, "migaEdit" => $this->data[0]['Preferencia']['nombre']));

$this->addScript($ajax->jsPredefinido(array("tipo"=>"detalle", "agregar"=>true, "quitar"=>true)));
?>