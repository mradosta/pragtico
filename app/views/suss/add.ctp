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
$campos['Suss.id'] = array();
$campos['Suss.empleador_id'] = array(	"lov"=>array(	"controller"		=> 	"empleadores",
														"seleccionMultiple"	=> 	0,
														"camposRetorno"		=> 	array(	"Empleador.cuit",
																						"Empleador.nombre")));
$campos['Suss.fecha'] = array("label"=>"Fecha de Pago");
$campos['Suss.banco_id'] = array('options' => 'listable', "model"=>"Banco", "empty"=>true, "displayField"=>array("Banco.nombre"));
$campos['Suss.periodo'] = array("type"=>"periodo", "periodo"=>array("soloAAAAMM"), 'aclaracion' => "De la forma AAAAMM");
$fieldsets[] = array('campos' => $campos);

$fieldset = $appForm->pintarFieldsets($fieldsets, array('div' => array('class' => 'unica'), 'fieldset' => array('imagen' => 'suss.gif')));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
$miga = array('format' 	=> '%s (%s)', 
			  'content' => array('Banco.nombre', 'Suss.fecha'));
echo $this->element('add/add', array('fieldset' => $fieldset, 'miga' => $miga));
?>