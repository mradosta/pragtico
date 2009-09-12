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

    $documento->create(array('password' => false, 'title' => 'Resumen de Liquidacion'));

    /** Set array with definitios values. */
    $definitions = array(   array(  'width' => 60,
                                    'title' => ($group_option == 'worker')?'Trabajador':'Coeficiente' . ' / Concepto',
                                    'option' => null),
                            array(  'width' => 15,
                                    'title' => 'Cant.',
                                    'option' => 'decimal'),
                            array(  'width' => 15,
                                    'title' => 'Total',
                                    'option' => 'currency'),
                            array(  'width' => 15,
                                    'title' => 'Coeficiente',
                                    'option' => 'decimal'),
                            array(  'width' => 15,
                                    'title' => 'Total',
                                    'option' => 'currency'));

    $fila = 4;
    $documento->setCellValue('A' . $fila, 'Empresa: ' . $conditions['Bar-empleador_id__'], 'bold');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Periodo: ' . $conditions['Bar-periodo_largo'], 'bold');


    $fila+=2;
    /** Create headers */
    $column = 0;
    foreach ($definitions as $definition) {
        /** Set title columns. */
        $documento->setCellValue($column . ',' . $fila, $definition['title'], array('title' => $definition['width']));
        $column++;
    }


    $fila = 7;
    $total = 0;
    $flag = null;
    $inicio = 0;
    $flagCoeficiente = null;    
    $extraTotals['Remunerativo'] = 0;
    $extraTotals['No Remunerativo'] = 0;
    $extraTotals['Deduccion'] = 0;
    
            
    /** Body */
    foreach ($data as $k => $detail) {

        $fila++;
        $documento->setCellValue('A' . $fila, $k, 'bold');
        $beginRow = $fila;
        foreach ($detail as $r) {
            
            $extraTotals[$r['LiquidacionesDetalle']['concepto_tipo']] += $r['LiquidacionesDetalle']['valor'];
            
            if ($r['LiquidacionesDetalle']['concepto_tipo'] === 'Deduccion') {
                $r['LiquidacionesDetalle']['valor'] = $r['LiquidacionesDetalle']['valor'] * -1;
            }
            
            $fila++;
            $documento->setCellValueFromArray(
                array(  '0,' . $fila => '    ' . $r['LiquidacionesDetalle']['concepto_nombre'],
                        '1,' . $fila => $r['LiquidacionesDetalle']['suma_cantidad'],
                        '2,' . $fila => array('value' => $r['LiquidacionesDetalle']['valor'], 'options' => 'currency'),
                        '3,' . $fila => $r['LiquidacionesDetalle']['coeficiente_valor'],
                        '4,' . $fila => array('value' => '=C' . $fila . '*' . 'D' . $fila, 'options' => 'currency')));
        }
        $fila++;
        $totals['C'][] = 'C' . $fila;
        $totals['E'][] = 'E' . $fila;
        $documento->setCellValueFromArray(
            array(  '2,' . $fila =>
                array('value' => '=SUM(C' . $beginRow . ':C' . ($fila - 1) . ')', 'options' => 'total'),
                    '4,' . $fila =>
                array('value' => '=SUM(E' . $beginRow . ':E' . ($fila - 1) . ')', 'options' => 'total')));
    }

    $fila+=3;
    $documento->setCellValue('A' . $fila . ':E' . $fila, 'TOTALES', 'title');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Trabajadores', 'bold');
    $documento->setCellValue('E' . $fila, $totalWorkers, 'bold');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Liquidado', 'bold');
    $documento->setCellValue('E' . $fila, '=SUM('.implode('+', $totals['C']).')', 'total');
    
    foreach ($extraTotals as $t => $v) {
        $fila++;
        $documento->setCellValue('A' . $fila, '    ' . $t, 'bold');
        $documento->setCellValue('E' . $fila, $v, 'total');
    }
    
    $fila++;
    $documento->setCellValue('A' . $fila, 'A Facturar', 'bold');
    $documento->setCellValue('E' . $fila, '=SUM('.implode('+', $totals['E']).')', 'total');
    $fila++;


    $documento->save($fileFormat);
    
} else {

    $conditions['Condicion.Bar-empleador_id'] = array( 'lov' => array(
            'controller'        => 'empleadores',
            'seleccionMultiple' => 0,
            'camposRetorno'     => array('Empleador.cuit', 'Empleador.nombre')));
    
    $conditions['Condicion.Bar-trabajador_id'] = array(
            'lov'   => array(   'controller'   => 'trabajadores',
                                'seleccionMultiple'    => 0,
                                'camposRetorno' => array('Trabajador.cuil', 'Trabajador.nombre', 'Trabajador.apellido')));

    $conditions['Condicion.Bar-group_option'] = array('type' => 'radio', 'options' => $options);
    
    $conditions['Condicion.Bar-tipo'] = array('label' => 'Tipo', 'multiple' => 'checkbox', 'type' => 'select', 'options' => $types);

    $conditions['Condicion.Bar-estado'] = array('label' => 'Estado', 'multiple' => 'checkbox', 'type' => 'select', 'options' => $states);
    
    $conditions['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => array('1Q', '2Q', 'M', '1S', '2S', 'F'));

    $options = array('title' => 'Resumen de Liquidacion');
    echo $this->element('reports/conditions', array('aditionalConditions' => $conditions, 'options' => $options));
    

}
 
?>