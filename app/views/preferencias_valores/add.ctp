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
$campos['PreferenciasValor.id'] = array();
$campos['PreferenciasValor.preferencia_id'] = array(	"lov"=>array("controller"	=>	"preferencias",
																"seleccionMultiple"	=> 	0,
																	"camposRetorno"	=>	array("Preferencia.nombre")));
$campos['PreferenciasValor.valor'] = array();
$campos['PreferenciasValor.predeterminado'] = array();

$fieldsets[] = array('campos' => $campos);

$fieldset = $appForm->pintarFieldsets($fieldsets, array('div' => array('class' => 'unica'), 'fieldset' => array('legend' => "registro de valor de la preferencia", 'imagen' => 'preferencias_valores.gif')));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->element('add/add', array('fieldset' => $fieldset));
?>