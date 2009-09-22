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

    $documento->create(array('password' => false, 'orientation' => 'landscape', 'title' => 'Listado de Ausencias Liquidadas'));
    $documento->setCellValue('A', 'Empleador', array('title' => '30'));
    $documento->setCellValue('B', 'Cuil', array('title' => '20'));
    $documento->setCellValue('C', 'Apellido', array('title' => '20'));
    $documento->setCellValue('D', 'Nombre', array('title' => '20'));
    $documento->setCellValue('E', 'Motivo', array('title' => '40'));
    $documento->setCellValue('F', 'Inicio', array('title' => '15'));
    $documento->setCellValue('G', 'Dias Conf.', array('title' => '15'));
    $documento->setCellValue('H', 'Cant. Liq.', array('title' => '15'));
    $documento->setCellValue('I', 'Liquidado', array('title' => '20'));
    
    /** Body */
    foreach ($data as $k => $detail) {

        /** Because of naming convenion in absences model, can get concept code */
        $conceptCode = null;
        $tmpName = 'ausencias_' . strtolower($detail['Ausencia']['AusenciasMotivo']['tipo']);
        $conceptCode[] = $tmpName;
        if ($tmpName == 'ausencias_accidente') {
            $conceptCode[] = $tmpName . '_art';
        }

        $val = $days = 0;
        foreach ($detail['Liquidacion']['LiquidacionesDetalle'] as $d) {
            if (in_array($d['concepto_codigo'], $conceptCode)) {
                $val += $d['valor'];
                $days += $d['valor_cantidad'];
            }
        }

        if (empty($totals[$detail['Ausencia']['AusenciasMotivo']['motivo']])) {
            $totals[$detail['Ausencia']['AusenciasMotivo']['motivo']]['days'] = $detail['AusenciasSeguimiento']['dias'];
            $totals[$detail['Ausencia']['AusenciasMotivo']['motivo']]['value'] = $val;
        } else {
            $totals[$detail['Ausencia']['AusenciasMotivo']['motivo']]['days'] += $detail['AusenciasSeguimiento']['dias'];
            $totals[$detail['Ausencia']['AusenciasMotivo']['motivo']]['value'] += $val;
        }

        $documento->setCellValueFromArray(
            array(  $detail['Ausencia']['Relacion']['Empleador']['nombre'],
                    $detail['Ausencia']['Relacion']['Trabajador']['cuil'],
                    $detail['Ausencia']['Relacion']['Trabajador']['apellido'], $detail['Ausencia']['Relacion']['Trabajador']['nombre'],
                    $detail['Ausencia']['AusenciasMotivo']['motivo'],
                    $detail['Ausencia']['desde'],
                    $detail['AusenciasSeguimiento']['dias'],
                    $days,
                    array('value' => $val, 'options' => 'currency')));
    }
    
    $documento->moveCurrentRow(3);
    $documento->setCellValue('A' . $documento->getCurrentRow() . ':D' . $documento->getCurrentRow(), 'TOTALES', 'title');
    foreach ($totals as $label => $total) {
        $documento->moveCurrentRow();
        $documento->setCellValue('B', $label. ':', array('bold', 'right'));
        $documento->setCellValue('C', $total['days'], array('bold', 'right'));
        $documento->setCellValue('D', $total['value'], array('bold', 'currency'));
    }

    $documento->save($fileFormat);
} else {

    $conditions['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => array('soloAAAAMM'));

    $conditions['Condicion.Bar-empleador_id'] = array( 'lov' => array(
            'controller'        => 'empleadores',
            'seleccionMultiple' => true,
            'camposRetorno'     => array('Empleador.cuit', 'Empleador.nombre')));
    
    $options = array('title' => 'Ausencias Liquidadas');
    echo $this->element('reports/conditions', array('aditionalConditions' => $conditions, 'options' => $options));
}
 
?>