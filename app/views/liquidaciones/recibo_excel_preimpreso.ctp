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
 * @version         $Revision: 498 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2009-04-17 11:30:17 -0300 (Fri, 17 Apr 2009) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */

    $documento->create();
    $documento->setActiveSheet();
    $documento->activeSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $documento->activeSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    
    //$documento->activeSheet->getDefaultStyle()->getFont()->setName('Courier New');
    $documento->activeSheet->getDefaultStyle()->getFont()->setName('Arial');
    $documento->activeSheet->getDefaultStyle()->getFont()->setSize(8);

    $documento->activeSheet->getDefaultRowDimension()->setRowHeight(10);

    $documento->setWidth('A:AS', 4);

    $pageMargins = $documento->activeSheet->getPageMargins();
    $pageMargins->setTop(0.2);
    $pageMargins->setBottom(0);
    $pageMargins->setLeft(0.05);
    $pageMargins->setRight(0.2);


    $styleRight = array('style' => array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)));
    
    $styleCenter = array('style' => array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));

    $initialRow = 0;
    foreach ($this->data as $receipt) {

        for ($i = 0; $i <= 1; $i++) {
            $fila = $initialRow;
            $fila+=9;
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('B') -1 + ($i * 23)) . ',' . $fila, $receipt['Liquidacion']['empleador_nombre']);

            $fila+=3;
            if ($receipt['Liquidacion']['tipo'] !== 'Final') {
                $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('A') -1 + ($i * 23)) . ',' . $fila, substr($receipt['Liquidacion']['tipo'], 0, 3) . ' ' . $formato->format($receipt['Liquidacion']['ano'] . str_pad($receipt['Liquidacion']['mes'], 2, '0', STR_PAD_LEFT) . $receipt['Liquidacion']['periodo'], array('type' => 'periodoEnLetras', 'short' => true, 'case' => 'ucfirst')));
            }
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('D') -1 + ($i * 23)) . ',' . $fila, $formato->format($receipt['Liquidacion']['pago'], 'date'));
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('H') -1 + ($i * 23)) . ',' . $fila, sprintf('%s, %s', $receipt['Liquidacion']['trabajador_apellido'], $receipt['Liquidacion']['trabajador_nombre']));
            if (preg_match('/\d\d\-([0-9]+)\-\d/', $receipt['Liquidacion']['trabajador_cuil'], $matches)) {
                $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('Q') -1 + ($i * 23)) . ',' . $fila, " " . $matches[1], $styleRight);
                $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('U') -1 + ($i * 23)) . ',' . $fila, " " . $matches[1], $styleRight);
            }

            $fila+=3;
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('A') -1 + ($i * 23)) . ',' . $fila, $receipt['Liquidacion']['trabajador_cuil']);
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('F') -1 + ($i * 23)) . ',' . $fila, $formato->format($receipt['Liquidacion']['relacion_ingreso'], 'date'), $styleRight);
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('H') -1 + ($i * 23)) . ',' . $fila, $receipt['Liquidacion']['relacion_antiguedad'], $styleCenter);
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('I') -1 + ($i * 23)) . ',' . $fila, $receipt['Liquidacion']['convenio_categoria_nombre']);
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('R') -1 + ($i * 23)) . ',' . $fila, substr($receipt['Liquidacion']['trabajador_cbu'], 0, 3) . " " . substr($receipt['Liquidacion']['trabajador_cbu'], 8, 13));

            $fila+=4;
            foreach ($receipt['LiquidacionesDetalle'] as $detail) {
                if($detail['concepto_imprimir'] === 'Si' || ($detail['concepto_imprimir'] === 'Solo con valor') && abs($detail['valor']) > 0) {
                    if (abs($detail['valor_cantidad']) > 0) {
                        $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('B') -1 + ($i * 23)) . ',' . $fila, $detail['valor_cantidad']);
                    }
                    $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('E') -1 + ($i * 23)) . ',' . $fila, $detail['concepto_nombre']);
                    if ($detail['concepto_tipo'] !== 'Deduccion') {
                        $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('S') -1 + ($i * 23)) . ',' . $fila, $formato->format($detail['valor'], 'currency'), $styleRight);
                    } else {
                        $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('V') -1 + ($i * 23)) . ',' . $fila, $formato->format($detail['valor'], 'currency'), $styleRight);
                    }
                    $fila++;
                }
            }

            $fila = $initialRow + 47;
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('K') -1 + ($i * 23)) . ',' . $fila, $formato->format($receipt['Liquidacion']['no_remunerativo'], 'currency'), $styleRight);
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('O') -1 + ($i * 23)) . ',' . $fila, $formato->format($receipt['Liquidacion']['remunerativo'], 'currency'), $styleRight);
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('S') -1 + ($i * 23)) . ',' . $fila, $formato->format(($receipt['Liquidacion']['remunerativo'] + $receipt['Liquidacion']['no_remunerativo']), 'currency'), $styleRight);
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('V') -1 + ($i * 23)) . ',' . $fila, $formato->format($receipt['Liquidacion']['deduccion'], 'currency'), $styleRight);
            
            $fila+=2;
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('V') -1 + ($i * 23)) . ',' . $fila, $formato->format($receipt['Liquidacion']['total_pesos'], 'currency'), $styleRight);

            $fila+=3;
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('C') -1 + ($i * 23)) . ',' . $fila, $formato->format($receipt['Liquidacion']['total_pesos'], array('type' => 'numeroEnLetras', 'case' => 'ucfirst')));
            
            $fila+=4;
            if (!empty($receipt['Suss'])) {
                $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('M') -1 + ($i * 23)) . ',' . $fila, $formato->format($receipt['Suss']['periodo'], array('type' => 'periodoEnLetras', 'short' => true, 'case' => 'ucfirst')));
                $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('P') -1 + ($i * 23)) . ',' . $fila, $formato->format($receipt['Suss']['fecha'], 'date'));
                $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('T') -1 + ($i * 23)) . ',' . $fila, $receipt['Banco']['nombre']);
            }
            $fila+=2;
            $documento->setCellValue((PHPExcel_Cell::columnIndexFromString('P') -1 + ($i * 23)) . ',' . $fila, $formato->format($receipt['Liquidacion']['pago'], 'date') . ' - CORDOBA');
        }
        
        $documento->activeSheet->setBreak('A' . $fila, PHPExcel_Worksheet::BREAK_ROW);
        $initialRow = $fila;
    }

    //$documento->save('Excel2007', '/tmp/file');
    //$documento->save('Excel5', '/tmp/ff');
    $documento->save('Excel5');
    
?>