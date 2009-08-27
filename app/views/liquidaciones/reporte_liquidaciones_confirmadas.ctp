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
    $documento->doc->getActiveSheet()->getDefaultStyle()->getFont()->setName('Courier New');
    $documento->doc->getActiveSheet()->getDefaultStyle()->getFont()->setSize(6);

    $documento->doc->getActiveSheet()->getDefaultRowDimension()->setRowHeight(10);
    $documento->doc->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
    $documento->doc->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


    $fila = 1;
    $documento->setCellValue('E' . $fila, date('Y-m-d'), 'bold');
    $fila+=2;
    $documento->setCellValue('A' . $fila, 'Listado de Liquidaciones Confirmadas', 'bold');
    $fila++;
    //$documento->setCellValue('A' . $fila, 'Empresa: ' . $conditions['Liquidacion-empleador_id__'], 'bold');
    $fila++;
    //$documento->setCellValue('A' . $fila, 'Periodo: ' . $conditions['Liquidacion-periodo_largo'], 'bold');


    $fila = 7;
    $total = 0;
    $flag = null;
    $inicio = 0;
    $flagCoeficiente = null;    


    $documento->setWidth('A', 30);
    $documento->setWidth('B', 15);
    $documento->setWidth('C', 20);
    $documento->setWidth('D', 20);
    $documento->setWidth('E', 15);
    $documento->setWidth('F', 15);
    $documento->setWidth('G', 15);
    $documento->setWidth('H', 30);
    
    $documento->setCellValue('A' . $fila, 'Empleador', 'title');
    $documento->setCellValue('B' . $fila, 'Cuil', 'title');
    $documento->setCellValue('C' . $fila, 'Apellido', 'title');
    $documento->setCellValue('D' . $fila, 'Nombre', 'title');
    $documento->setCellValue('E' . $fila, 'Pesos', 'title');
    $documento->setCellValue('F' . $fila, 'Beneficios', 'title');
    $documento->setCellValue('G' . $fila, 'Total', 'title');
    $documento->setCellValue('H' . $fila, 'Cuenta', 'title');
            
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
                    '1,' . $fila => $detail['Liquidacion']['trabajador_cuil'],
                    '2,' . $fila => $detail['Liquidacion']['trabajador_apellido'],
                    '3,' . $fila => $detail['Liquidacion']['trabajador_nombre'],
                    '4,' . $fila => array('value'      => $detail['Liquidacion']['total_pesos'],
                                           'options'    => 'currency'),
                    '5,' . $fila => array('value'      => $detail['Liquidacion']['total_beneficios'],
                                           'options'    => 'currency'),
                    '6,' . $fila => array('value'      => $detail['Liquidacion']['total'],
                                           'options'    => 'currency'),
                    '7,' . $fila => $account));
    }
    $endRow = $fila;
    
    $fila+=3;
    $documento->setCellValue('A' . $fila . ':H' . $fila, 'TOTALES', 'title');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Liquidaciones', 'bold');
    $documento->setCellValue('H' . $fila, count($data), 'bold');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Pesos', 'bold');
    $documento->setCellValue('H' . $fila, sprintf('=SUM(E%s:E%s)', $startRow, $endRow), 'total');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Beneficios', 'bold');
    $documento->setCellValue('H' . $fila, sprintf('=SUM(F%s:F%s)', $startRow, $endRow), 'total');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Total', 'bold');
    $documento->setCellValue('H' . $fila, sprintf('=SUM(G%s:G%s)', $startRow, $endRow), 'total');
    
    $documento->save('Excel5');
}
?>