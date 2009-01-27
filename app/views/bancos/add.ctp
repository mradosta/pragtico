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
$campos['Banco.id'] = array();
$campos['Banco.codigo'] = array();
$campos['Banco.nombre'] = array();
$campos['Banco.observacion'] = array();
$fieldsets[] = array('campos' => $campos);

$campos = null;
$campos['Sucursal.id'] = array();
$campos['Sucursal.codigo'] = array();
$campos['Sucursal.direccion'] = array();
$campos['Sucursal.telefono'] = array();
$campos['Sucursal.observacion'] = array();
$fieldsets[] = array('campos' => $campos, 'opciones' => array('fieldset' => array("class"=>"detail", 'legend' => "Sucursal", 'imagen' => 'sucursales.gif')));

$fieldset = $appForm->pintarFieldsets($fieldsets, array('div' => array('class' => 'unica'), 'fieldset' => array('imagen' => 'bancos.gif')));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
$miga = array('format' 	=> '(%s) %s', 
			  'content' => array('Banco.codigo', 'Banco.nombre'));

echo $this->element('add/add', array('fieldset' => $fieldset, 'miga' => $miga));
$this->addScript($ajax->jsPredefinido(array('tipo' => 'detalle', 'agregar' => true, 'quitar' => true)));
?>