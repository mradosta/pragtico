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
$campos['Variable.id'] = array();
$campos['Variable.nombre'] = array();
$campos['Variable.formula'] = array();
$campos['Variable.formato'] = array();
$campos['Variable.descripcion'] = array();
$campos['Variable.ejemplo'] = array();
$fieldsets[] = array('campos' => $campos);

$fieldset = $appForm->pintarFieldsets($fieldsets, array('div' => array('class' => 'unica'), 'fieldset' => array('imagen' => 'variables.gif')));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->element('add/add', array('fieldset' => $fieldset, 'miga' => 'Variable.nombre'));
?>