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
$conditions['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo Liquidacion', 'type' => 'periodo', 'periodo' => array('M'));


$conditions['Condicion.Bar-empleador_id'] = array( 'lov' => array(
        'controller'        => 'empleadores',
        'seleccionMultiple' => false,
        'camposRetorno'     => array('Empleador.cuit', 'Empleador.nombre')));

$conditions['Condicion.Bar-version'] = array(
	'options'  		=>  'listable',
	'model' 		=> 'Siap',
	'order' 		=> array('Siap.version' => 'DESC'),
	'displayField' 	=> array('Siap.version'));

$conditions['Condicion.Bar-numero'] = array('label' => 'Número de liquidación');

$botonesExtra[] = $appForm->submit('Asignar', array('id' => 'asignar', 'title'=>'Asigna un Número de Liquidacion (AFIP)', 'onclick'=>'document.getElementById("accion").value="asignar"'));

$options = array(
	'title' => 'Generar archivo SICOSS / Libro Sueldo Digital',
	'conditions' => array('Bar-file_format' => false),
	'botonesExtra'	=> $botonesExtra
);
echo $this->element('reports/conditions', array('aditionalConditions' => $conditions, 'options' => $options ));

?>