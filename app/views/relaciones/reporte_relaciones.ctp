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
 * @version         $Revision: 528 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2009-05-20 16:56:44 -0300 (Wed, 20 May 2009) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
 
if (!empty($data)) {

    $documento->create(array('password' => false, 'orientation' => 'landscape', 'title' => 'Relaciones No Activas'));

    $documento->setCellValue('A', 'Cuit', array('title' => '15'));
    $documento->setCellValue('B', 'Empleador', array('title' => '30'));
    $documento->setCellValue('C', 'Cuil', array('title' => '15'));
    $documento->setCellValue('D', 'Apellido', array('title' => '20'));
    $documento->setCellValue('E', 'Nombre', array('title' => '25'));
    $documento->setCellValue('F', 'Area', array('title' => '30'));
    $documento->setCellValue('G', 'F. Ingreso', array('title' => '15'));
    $documento->setCellValue('H', 'F. Egreso', array('title' => '15'));
	$documento->setCellValue('I', 'Motivo Egreso', array('title' => '40'));



    /** Body */
    foreach ($data as $k => $record) {
        $documento->setCellValueFromArray(
            array(  $record['Relacion']['Empleador']['cuit'],
                    $record['Relacion']['Empleador']['nombre'],
					$record['Relacion']['Trabajador']['cuil'],
                    $record['Relacion']['Trabajador']['apellido'],
                    $record['Relacion']['Trabajador']['nombre'],
                    $record['Relacion']['Area']['nombre'],
                    $record['RelacionesHistorial']['inicio'],
                    $record['RelacionesHistorial']['fin'],
                    $record['EgresosMotivo']['motivo']
                 ));
    }

    $documento->save($fileFormat);
} else {

    $conditions['Condicion.Bar-empleador_id'] = array( 'lov' => array(
            'controller'        => 'empleadores',
            'seleccionMultiple' => true,
            'camposRetorno'     => array('Empleador.cuit', 'Empleador.nombre')));

    if ($this->params['named']['state'] == 'Activa') {
    	$conditions['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => array('soloAAAAMM'));
        $conditions['Condicion.Bar-con_fecha_egreso'] = array('label' => 'Fecha Egreso', 'type' => 'radio', 'options' => array('Si' => 'Si', 'No' => 'No'), 'default' => 'No');

        $conditions['Condicion.Bar-con_liquidacion_periodo'] = array('label' => 'Liquidacion en el Periodo', 'type' => 'radio', 'options' => array('Si' => 'Si', 'No' => 'No'), 'default' => 'No');
    } else {
		$conditions['Condicion.Bar-desde'] = array('label' => 'Desde', 'type' => 'date');
		$conditions['Condicion.Bar-hasta'] = array('label' => 'Hasta', 'type' => 'date');
	}

    $options = array('title' => 'Relaciones ' . Inflector::pluralize($this->params['named']['state']));
    echo $this->element('reports/conditions', array('aditionalConditions' => $conditions, 'options' => $options));
}
 
?>