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
    $documento->create(array('password' => 'PaXXHttBXG66'));

    $fila = 1;
    $documento->setCellValue('I' . $fila, date('Y-m-d'), array('bold', 'right'));
    $fila+=2;
    $documento->setCellValue('A' . $fila, 'Listado de Liquidaciones Confirmadas', 'bold');

    $fila = 7;
    $total = 0;
    $flag = null;
    $inicio = 0;
    $flagCoeficiente = null;    


    $documento->setWidth('A', 30);
    $documento->setWidth('B', 15);
    $documento->setWidth('C', 15);
    $documento->setWidth('D', 30);
    $documento->setWidth('E', 30);
    $documento->setWidth('F', 15);
    $documento->setWidth('G', 15);
    $documento->setWidth('H', 15);
    $documento->setWidth('I', 30);
    
    $documento->setCellValue('A' . $fila, 'Empleador', 'title');
    $documento->setCellValue('B' . $fila, 'Periodo', 'title');
    $documento->setCellValue('C' . $fila, 'Cuil', 'title');
    $documento->setCellValue('D' . $fila, 'Apellido', 'title');
    $documento->setCellValue('E' . $fila, 'Nombre', 'title');
    $documento->setCellValue('F' . $fila, 'Pesos', 'title');
    $documento->setCellValue('G' . $fila, 'Beneficios', 'title');
    $documento->setCellValue('H' . $fila, 'Total', 'title');
    $documento->setCellValue('I' . $fila, 'Cuenta', 'title');
            
    /** Body */
    $startRow = $fila + 1;
    foreach ($data as $detail) {

        $fila++;
        $account = '';
        if (preg_match('/(\d\d\d)(\d\d\d\d)\d(\d\d\d\d\d\d\d\d\d\d\d\d\d)\d/', $detail['Liquidacion']['trabajador_cbu'], $matches)) {
            unset($matches[0]);
            $account = implode(' ', $matches);
        }

        $documento->setCellValueFromArray(
            array(  '0,' . $fila => $detail['Liquidacion']['empleador_nombre'],
                    '1,' . $fila => array('value'      => $formato->format($detail['Liquidacion'], 'periodo'),
                                          'options'    => 'center'),
                    '2,' . $fila => array('value'      => $detail['Liquidacion']['trabajador_cuil'],
                                          'options'    => 'center'),
                    '3,' . $fila => $detail['Liquidacion']['trabajador_apellido'],
                    '4,' . $fila => $detail['Liquidacion']['trabajador_nombre'],
                    '5,' . $fila => array('value'      => $detail['Liquidacion']['total_pesos'],
                                           'options'   => 'currency'),
                    '6,' . $fila => array('value'      => $detail['Liquidacion']['total_beneficios'],
                                           'options'   => 'currency'),
                    '7,' . $fila => array('value'      => $detail['Liquidacion']['total'],
                                           'options'   => 'currency'),
                    '8,' . $fila => array('value'      => $account,
                                           'options'   => 'center')));
    }
    $endRow = $fila;
    
    $fila+=3;
    $documento->setCellValue('A' . $fila . ':I' . $fila, 'TOTALES', 'title');
    $fila++;
    $documento->setCellValue('B' . $fila, 'Liquidaciones:', array('bold', 'right'));
    $documento->setCellValue('D' . $fila, count($data), 'bold');
    $fila++;
    $documento->setCellValue('B' . $fila, 'Pesos:', array('bold', 'right'));
    $documento->setCellValue('D' . $fila, sprintf('=SUM(F%s:F%s)', $startRow, $endRow), 'total');
    $fila++;
    $documento->setCellValue('B' . $fila, 'Beneficios:', array('bold', 'right'));
    $documento->setCellValue('D' . $fila, sprintf('=SUM(G%s:G%s)', $startRow, $endRow), 'total');
    $fila++;
    $documento->setCellValue('B' . $fila, 'Total:', array('bold', 'right'));
    $documento->setCellValue('D' . $fila, sprintf('=SUM(H%s:H%s)', $startRow, $endRow), 'total');
    
    $appForm->addScript('console.log("xxxxxxxx");');
    $documento->save('Excel5');

}
?>