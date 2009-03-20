<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.views
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */

	$documento->create();
	$documento->setActiveSheet();
	$documento->activeSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$documento->activeSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	
	//$documento->activeSheet->getDefaultStyle()->getFont()->setName('Courier New');
	$documento->activeSheet->getDefaultStyle()->getFont()->setName('Arial');
	$documento->activeSheet->getDefaultStyle()->getFont()->setSize(8);

	$documento->activeSheet->getDefaultRowDimension()->setRowHeight(11);

	$documento->setWidth('A:AR', 4);

	$pageMargins = $documento->activeSheet->getPageMargins();
	$pageMargins->setTop(0.2);
	$pageMargins->setBottom(0.2);
	$pageMargins->setLeft(0.1);
	$pageMargins->setRight(0.2);


	$styleRight = array('style' => array(
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)));
	
	$styleCenter = array('style' => array(
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));

	$initialRow = 0;
	foreach ($this->data as $receipt) {

		for ($i = 0; $i <= 1; $i++) {
			$fila = $initialRow;
			$fila+=7;
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('B') + ($i * 22)) . ',' . $fila, sprintf('Usuario: %s - %s', $receipt['Liquidacion']['empleador_id'] ,$receipt['Liquidacion']['empleador_nombre']));

			$fila+=4;
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('A') + ($i * 22)) . ',' . $fila, substr($receipt['Liquidacion']['tipo'], 0, 3) . ' ' . $formato->format($receipt['Liquidacion']['ano'] . $receipt['Liquidacion']['mes'] . $receipt['Liquidacion']['periodo'], array('type' => 'periodoEnLetras', 'short' => true, 'case' => 'ucfirst')));
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('H') + ($i * 22)) . ',' . $fila, sprintf('%s, %s', $receipt['Liquidacion']['trabajador_apellido'], $receipt['Liquidacion']['trabajador_nombre']));
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('R') + ($i * 22)) . ',' . $fila, $receipt['Liquidacion']['trabajador_cuil'], $styleRight);
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('V') + ($i * 22)) . ',' . $fila, $receipt['Liquidacion']['relacion_legajo'], $styleRight);

			$fila+=3;
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('A') + ($i * 22)) . ',' . $fila, $receipt['Liquidacion']['trabajador_cuil']);
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('F') + ($i * 22)) . ',' . $fila, $formato->format($receipt['Liquidacion']['relacion_ingreso'], 'date'), $styleCenter);
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('I') + ($i * 22)) . ',' . $fila, $receipt['Liquidacion']['convenio_categoria_nombre']);

			$fila+=3;
			foreach ($receipt['LiquidacionesDetalle'] as $detail) {
				if($detail['concepto_imprimir'] === 'Si' || ($detail['concepto_imprimir'] === 'Solo con valor') && abs($detail['valor']) > 0) {
					$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('A') + ($i * 22)) . ',' . $fila, $detail['concepto_cantidad']);
					$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('E') + ($i * 22)) . ',' . $fila, $detail['concepto_nombre']);
					if ($detail['concepto_tipo'] !== 'Deduccion') {
						$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('S') + ($i * 22)) . ',' . $fila, $formato->format($detail['valor'], 'currency'), $styleRight);
					} else {
						$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('V') + ($i * 22)) . ',' . $fila, $formato->format($detail['valor'], 'currency'), $styleRight);
					}
					$fila++;
				}
			}

			$fila = 42;
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('N') + ($i * 22)) . ',' . $fila, $formato->format($receipt['Liquidacion']['remunerativo'], 'currency'), $styleRight);
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('S') + ($i * 22)) . ',' . $fila, $formato->format($receipt['Liquidacion']['deduccion'], 'currency'), $styleRight);
			
			$fila+=2;
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('V') + ($i * 22)) . ',' . $fila, $formato->format($receipt['Liquidacion']['total_pesos'], 'currency'), $styleRight);

			$fila+=2;
			$documento->setCellValue((PHPExcel_Cell::columnIndexFromString('C') + ($i * 22)) . ',' . $fila, $formato->format($receipt['Liquidacion']['total_pesos'], array('type' => 'numeroEnLetras', 'case' => 'ucfirst')));
		}
		
		$documento->activeSheet->setBreak('A' . $fila, PHPExcel_Worksheet::BREAK_ROW);
		$initialRow = $fila + 1;
	}
	
	//$documento->save('Excel2007', '/tmp/file');
	$documento->save('Excel5', '/tmp/file');
	//$documento->save('Excel5');
	
?>