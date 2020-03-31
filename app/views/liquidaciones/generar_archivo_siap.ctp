<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.views
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */

/**
* Especifico los campos para ingresar las condiciones.
*/
// $conditions['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => array('soloAAAAMM'));
$conditions['Condicion.Liquidacion-tipo'] = array('label' => 'Tipo', 'type' => 'select', 'multiple' => 'checkbox');
$conditions['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo Liquidacion', 'type' => 'periodo', 'periodo' => array('1Q', '2Q', 'M', '1S', '2S', 'A'));


$conditions['Condicion.Bar-empleador_id'] = array( 'lov' => array(
        'controller'        => 'empleadores',
        'seleccionMultiple' => false,
        'camposRetorno'     => array('Empleador.cuit', 'Empleador.nombre')));

$conditions['Condicion.Bar-version'] = array(
	'options'  		=>  'listable',
	'model' 		=> 'Siap',
	'order' 		=> array('Siap.version' => 'DESC'),
	'displayField' 	=> array('Siap.version'));

$options = array('title' => 'Generar archivo SICOSS / Libro Sueldo Digital', 'conditions' => array('Bar-file_format' => false));
echo $this->element('reports/conditions', array('aditionalConditions' => $conditions, 'options' => $options));

?>