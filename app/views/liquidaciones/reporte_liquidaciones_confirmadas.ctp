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
    $documento->create(array('password' => false, 'orientation' => 'landscape', 'title' => 'Listado de Liquidaciones Confirmadas'));

    $total = 0;
    $flag = null;
    $inicio = 0;
    $flagCoeficiente = null;    


    $documento->setCellValue('A', 'Empleador', array('title' => 30));
    $documento->setCellValue('B', 'Periodo', array('title' => 10));
    $documento->setCellValue('C', 'Cuil', array('title' => 25));
    $documento->setCellValue('D', 'Apellido', array('title' => 30));
    $documento->setCellValue('E', 'Nombre', array('title' => 30));
    $documento->setCellValue('F', 'Pesos', array('title' => 20));
    $documento->setCellValue('G', 'Beneficios', array('title' => 20));
    $documento->setCellValue('H', 'Total', array('title' => 20));
    $documento->setCellValue('I', 'Cuenta', array('title' => 25));
            
    /** Body */
    $startRow = $documento->getCurrentRow() + 1;
    foreach ($data as $detail) {

        $account = '';
        if (preg_match('/(\d\d\d)(\d\d\d\d)\d(\d\d\d\d\d\d\d\d\d\d\d\d\d)\d/', $detail['Liquidacion']['trabajador_cbu'], $matches)) {
            unset($matches[0]);
            $account = implode(' ', $matches);
        }

        $documento->setCellValueFromArray(
            array(  $detail['Liquidacion']['empleador_nombre'],
                    array('value' => $formato->format($detail['Liquidacion'], 'periodo'), 'options' => 'center'),
                    array('value' => $detail['Liquidacion']['trabajador_cuil'], 'options' => 'center'),
                    $detail['Liquidacion']['trabajador_apellido'],
                    $detail['Liquidacion']['trabajador_nombre'],
                    array('value' => $detail['Liquidacion']['total_pesos'], 'options' => 'currency'),
                    array('value' => $detail['Liquidacion']['total_beneficios'], 'options' => 'currency'),
                    array('value' => $detail['Liquidacion']['total'], 'options' => 'currency'),
                    array('value' => $account, 'options' => 'center')));
    }
    $endRow = $documento->getCurrentRow();

    $t['Liquidaciones'] = count($data);
    $t['Pesos'] = sprintf('=SUM(F%s:F%s)', $startRow, $endRow);
    $t['Beneficios'] = sprintf('=SUM(G%s:G%s)', $startRow, $endRow);
    $t['Total'] = sprintf('=SUM(H%s:H%s)', $startRow, $endRow);
    $documento->setTotals($t);
    
    $documento->moveCurrentRow(4);
    $documento->setCellValue('A', 'Observaciones:', 'bold');
    $documento->moveCurrentRow(1);
    $styleArray = array(
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_DOTTED,
                'color' => array('argb' => '00000000'),
            ),
        ),
    );
    $documento->activeSheet->getStyle('A' . $documento->getCurrentRow() . ':I' . ($documento->getCurrentRow() + 6))->applyFromArray($styleArray);
    $documento->activeSheet->getStyle('A' . $documento->getCurrentRow() . ':I' . ($documento->getCurrentRow() + 6))->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
    $documento->save('Excel5');

}
?>