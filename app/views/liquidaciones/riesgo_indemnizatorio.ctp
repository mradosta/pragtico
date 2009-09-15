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
if (!empty($data)) {

    $documento->create(array('password' => false, 'title' => 'Riesgo Indemnizatorio'));
    
    $documento->setWidth('A', '50');
    $documento->setWidth('B', '20');
            
    $documento->setCellValue('A', 'Cuil: ', 'right');
    $documento->setCellValue('B', $relacion['Trabajador']['cuil'], 'bold');
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Trabajador: ', 'right');
    $documento->setCellValue('B', $relacion['Trabajador']['nombre'] .', ' . $relacion['Trabajador']['apellido'], 'bold');
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Ingreso: ', 'right');
    $documento->setCellValue('B', $relacion['Relacion']['ingreso'], 'bold');
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Egreso: ', 'right');
    $documento->setCellValue('B', $relacion['Relacion']['egreso'], 'bold');
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Antiguedad en Dias (Base a mes de 30 dias): ', 'right');
    $documento->setCellValue('B', '=DAYS360(B' . ($documento->getCurrentRow() - 2) .',B' . ($documento->getCurrentRow() - 1) . ')', 'bold');
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Empleador: ', 'right');
    $documento->setCellValue('B', $relacion['Empleador']['nombre'], 'bold');
    
    $documento->moveCurrentRow(4);
    $initialRow = $documento->getCurrentRow() + 1;
    foreach ($data as $v) {
        $documento->moveCurrentRow();
        $documento->setCellValue('A', $formato->format($v['Liquidacion']['ano'] . str_pad($v['Liquidacion']['mes'], 2, '0', STR_PAD_LEFT), array('type' => 'periodoEnLetras', 'short' => true)) . ' ', 'right');
        $documento->setCellValue('B', $v['Liquidacion']['total'], 'total');
    }
    $finalRow = $documento->getCurrentRow();
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Total: ', 'right');
    $documento->setCellValue('B', '=SUM(B' . $initialRow .':B' . $finalRow . ')', 'total');
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Promedio: ', 'right');
    $documento->setCellValue('B', '=B' . ($documento->getCurrentRow() - 1) . ' / ' . count($data), 'total');

    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Mayor: ', 'right');
    $documento->setCellValue('B', '=MAX(B' . $initialRow . ':B' . $finalRow . ')', 'total');

    $documento->moveCurrentRow(3);
    $documento->setCellValue('A', 'SAC Proporcional: ', 'right');
    $documento->setCellValue('B', '=MAX(B' . $initialRow . ':B' . $finalRow . ')', 'total');
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Preaviso: ', 'right');
    $documento->setCellValue('B', '=MAX(B' . $initialRow . ':B' . $finalRow . ')', 'total');

    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'SAC s/ Preaviso: ', 'right');
    $documento->setCellValue('B', '=MAX(B' . $initialRow . ':B' . $finalRow . ')', 'total');

    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Indemnizacion: ', 'right');
    $documento->setCellValue('B', '=MAX(B' . $initialRow . ':B' . $finalRow . ')', 'total');
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Integrativo Mes Despido: ', 'right');
    $documento->setCellValue('B', '=MAX(B' . $initialRow . ':B' . $finalRow . ')', 'total');
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'SAC s/ Integrativo: ', 'right');
    $documento->setCellValue('B', '=MAX(B' . $initialRow . ':B' . $finalRow . ')', 'total');
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Vacaciones No Gozadas: ', 'right');
    $documento->setCellValue('B', '=MAX(B' . $initialRow . ':B' . $finalRow . ')', 'total');
    
    $documento->moveCurrentRow();
    $documento->setCellValue('A', 'Liquidacion Final: ', 'right');
    $documento->setCellValue('B', '=MAX(B' . $initialRow . ':B' . $finalRow . ')', 'total');
    
    $documento->save($fileFormat);
} else {

    $conditions['Condicion.Bar-relacion_id'] = array( 'lov' => array(
            'controller'        => 'relaciones',
            'seleccionMultiple' => true,
                'camposRetorno' => array(   'Empleador.cuit',
                                            'Empleador.nombre',
                                            'Trabajador.cuil',
                                            'Trabajador.nombre',
                                            'Trabajador.apellido')));
            
    $options = array('title' => 'Riesgo Indemnizatorio');
    echo $this->element('reports/conditions', array('aditionalConditions' => $conditions, 'options' => $options));
}
 
?>