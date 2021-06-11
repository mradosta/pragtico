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
 * @version         $Revision: 1314 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2010-05-19 07:46:29 -0300 (Wed, 19 May 2010) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */

	$styleBorderBottom = array('style' => array(
			'borders' => array( 'bottom'     => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
	$styleBorderTop = array('style' => array(
			'borders' => array( 'top'     => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
	$styleBorderRight = array('style' => array(
			'borders' => array( 'right'     => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
	$styleBorderLeft = array('style' => array(
      'borders' => array( 'left'     => array('style' => PHPExcel_Style_Border::BORDER_THIN))));

	$styleThinBlackBorderOutline = array(
		'borders' => array(
			'outline' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => 'FF000000'),
			),
		),
	);

	$styleBorderVertical = array(
		'borders' => array(
			'vertical' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => 'FF000000'),
			),
		),
	);

	$styleBorders = array(
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				'color' => array('argb' => 'FF000000'),
			),
		),
	);

	$styleNormal = array('style' => array(
		'font'      => array('name' => 'Arial', 'size' => 8, 'bold' => false),
	));

	$styleBold = array('style' => array(
		'font'      => array('bold' => true),
	));

	$styleRight = array('style' => array(
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)));

	$styleRightBold = array('style' => array(
			'font'      => array('bold' => true),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)));

	$styleCenter = array('style' => array(
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));

	$styleCenterBold = array('style' => array(
			'font'      => array('bold' => true),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));

	$styleRowGrey = array('style' => array(
			'font'    => array(
				'bold' => false
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'fill' => array(
				'type'       => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array(
					'argb' => 'EFEFEFEF'
				),
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF000000'),
				),
			),
		)
	);


	$documento->create(array('header' => false, 'orientation' => 'landscape'));
	$isFirst = true;
	foreach ($this->data as $receipt) {

		$prevLastRow = 1;
		if (!$isFirst) {
			$documento->createNewSheet();
		}
		$isFirst = false;
		$documento->activeSheet->getDefaultStyle()->getFont()->setName('Arial');
		$documento->activeSheet->getDefaultStyle()->getFont()->setSize(8);
		$documento->activeSheet->getDefaultRowDimension()->setRowHeight(17);
		$documento->activeSheet->getDefaultColumnDimension()->setWidth(3.35);

		$pageMargins = $documento->activeSheet->getPageMargins();
		$pageMargins->setTop(0.2);
		$pageMargins->setBottom(0.2);
		$pageMargins->setLeft(0.05);
		$pageMargins->setRight(0.2);

		$spaces = -1;

		for ($i = 0; $i <= 1; $i++) {

			$documento->setCurrentRow($prevLastRow);
			$documento->addImage(($spaces + 1) . ',' . $documento->getCurrentRow(), 'logo_' . $receipt['Liquidacion']['empleador_cuit'] . '.png');
			$row = $documento->getCurrentRow();
			$documento->setCellValue(($spaces + 16) . ',' . $row . ':'. ($spaces + 26) .',' . $row, 'RECIBO DE HABERES', $styleBold);
			$documento->moveCurrentRow();
			$documento->activeSheet->getRowDimension(($documento->getCurrentRow() + 1))->setRowHeight(10);
			$row = ($documento->getCurrentRow() + 1);
			$documento->setCellValue(($spaces + 16) . ',' . $row . ':'. ($spaces + 26) .',' . $row, $receipt['Liquidacion']['empleador_nombre'], $styleNormal);
			$documento->moveCurrentRow();
			$row = ($documento->getCurrentRow() + 1);
			$documento->setCellValue(($spaces + 16) . ',' . $row . ':'. ($spaces + 26) .',' . $row, $receipt['Liquidacion']['empleador_direccion'], $styleNormal);
			$documento->moveCurrentRow();
			$documento->activeSheet->getRowDimension(($documento->getCurrentRow() + 1))->setRowHeight(10);
			$row = ($documento->getCurrentRow() + 1);
			$documento->setCellValue(($spaces + 16) . ',' . $row . ':'. ($spaces + 26) .',' . $row, 'C.U.I.T.: ' . $receipt['Liquidacion']['empleador_cuit'], $styleNormal);
			$documento->moveCurrentRow();
			$documento->activeSheet->getRowDimension(($documento->getCurrentRow() + 1))->setRowHeight(10);
			$row = ($documento->getCurrentRow() + 1);
			$documento->moveCurrentRow();
			$documento->activeSheet->getRowDimension(($documento->getCurrentRow() + 1))->setRowHeight(10);
			$row = ($documento->getCurrentRow() + 1);


			//$documento->moveCurrentRow();
		// $row = ($documento->getCurrentRow() + 1);
		// if ($i == 0) {
		// 	$documento->activeSheet->mergeCells('A' . $row . ':AA' . $row);
		// 	$documento->activeSheet->getStyle('A' . $row . ':AA' . $row)->applyFromArray($styleBorders);

		// } else {
		// 	$documento->activeSheet->mergeCells('AC' . $row . ':BC' . $row);
		// 	$documento->activeSheet->getStyle('AC' . $row . ':BC' . $row)->applyFromArray($styleBorders);
		// }


			//$documento->moveCurrentRow();
			$row = $documento->getCurrentRow() + 1;
			$documento->activeSheet->getRowDimension($row)->setRowHeight(10);
			$documento->setCellValue(($spaces + 1) . ',' . $row . ':' . ($spaces + 4) .',' . $row, 'Tipo de Liq.', $styleRowGrey);
			$documento->setCellValue(($spaces + 5) . ',' . $row . ':' . ($spaces + 8) .',' . $row, 'Período Abon.', $styleRowGrey);
			$documento->setCellValue(($spaces + 9) . ',' . $row . ':' . ($spaces + 12) .',' . $row, 'Fecha de Pago', $styleRowGrey);
			$documento->setCellValue(($spaces + 13) . ',' . $row . ':' . ($spaces + 21) .',' . $row, 'Apellido y Nombre', $styleRowGrey);
			$documento->setCellValue(($spaces + 22) . ',' . $row . ':' . ($spaces + 27) .',' . $row, 'Legajo', $styleRowGrey);

			$documento->moveCurrentRow();
			$row = $documento->getCurrentRow() + 1;
			$documento->setCellValue(($spaces + 1) . ',' . $row . ':' . ($spaces + 4) .',' . $row, $receipt['Liquidacion']['tipo']);
			$documento->setCellValue(($spaces + 5) . ',' . $row . ':' . ($spaces + 8) .',' . $row, $formato->format($receipt['Liquidacion']['ano'] . str_pad($receipt['Liquidacion']['mes'], 2, '0', STR_PAD_LEFT) . $receipt['Liquidacion']['periodo'], array('type' => 'periodoEnLetras', 'short' => true, 'case' => 'ucfirst')));
			$documento->setCellValue(($spaces + 9) . ',' . $row . ':' . ($spaces + 12) .',' . $row, $formato->format($receipt['Liquidacion']['pago'], 'date'));
			$documento->setCellValue(($spaces + 13) . ',' . $row . ':' . ($spaces + 21) .',' . $row, sprintf('%s, %s', $receipt['Liquidacion']['trabajador_apellido'], $receipt['Liquidacion']['trabajador_nombre']));
			$documento->setCellValue(($spaces + 22) . ',' . $row . ':' . ($spaces + 27) .',' . $row, $receipt['Liquidacion']['relacion_legajo']);

			$documento->moveCurrentRow();
			$row = $documento->getCurrentRow() + 1;
			$documento->activeSheet->getRowDimension($row)->setRowHeight(10);
			$documento->setCellValue(($spaces + 1) . ',' . $row . ':' . ($spaces + 5) .',' . $row, 'C.U.I.L.', $styleRowGrey);
			$documento->setCellValue(($spaces + 6) . ',' . $row . ':' . ($spaces + 9) .',' . $row, 'Fecha de Ingreso', $styleRowGrey);
			$documento->setCellValue(($spaces + 10) . ',' . $row . ':' . ($spaces + 12) .',' . $row, 'Antig. Rec.', $styleRowGrey);
			$documento->setCellValue(($spaces + 13) . ',' . $row . ':' . ($spaces + 22) .',' . $row, 'Categoría', $styleRowGrey);
			$documento->setCellValue(($spaces + 23) . ',' . $row . ':' . ($spaces + 27) .',' . $row, 'Sueldo Bruto', $styleRowGrey);

			$documento->moveCurrentRow();
			$row = $documento->getCurrentRow() + 1;
			$documento->setCellValue(($spaces + 1) . ',' . $row . ':' . ($spaces + 5) .',' . $row, $receipt['Liquidacion']['trabajador_cuil']);
			$documento->setCellValue(($spaces + 6) . ',' . $row . ':' . ($spaces + 9) .',' . $row, $formato->format($receipt['Liquidacion']['relacion_ingreso'], 'date'));
			$documento->setCellValue(($spaces + 10) . ',' . $row . ':' . ($spaces + 12) .',' . $row, $formato->format($receipt['Liquidacion']['relacion_antiguedad_reconocida'], 'date'));
			$documento->setCellValue(($spaces + 13) . ',' . $row . ':' . ($spaces + 22) .',' . $row, $receipt['Liquidacion']['convenio_categoria_nombre']);
			$documento->setCellValue(($spaces + 23) . ',' . $row . ':' . ($spaces + 27) .',' . $row, '$ ' . $receipt['Liquidacion']['relacion_basico'], $styleRight);


			$documento->moveCurrentRow();
			$row = $documento->getCurrentRow() + 1;
			$documento->activeSheet->getRowDimension($row)->setRowHeight(10);
			$documento->setCellValue(($spaces + 1) . ',' . $row . ':'. ($spaces + 19) .',' . $row, 'Cantidad / Concepto', $styleRowGrey);
			//$documento->setWidth(($spaces + 1), 9);

			// $documento->setCellValue(($spaces + 16) . ',' . $row . ':'. ($spaces + 19) .',' . $row, 'Valor Unitario', $styleRowGrey);
			$documento->setCellValue(($spaces + 16) . ',' . $row . ':'. ($spaces + 19) .',' . $row, '', $styleRowGrey);
			//$documento->setWidth(($spaces + 16), 6);
			$documento->setCellValue(($spaces + 20) . ',' . $row . ':'. ($spaces + 23) .',' . $row, 'Haberes', $styleRowGrey);
			$documento->setCellValue(($spaces + 24) . ',' . $row . ':'. ($spaces + 27) .',' . $row, 'Deducciones', $styleRowGrey);

			if ($i == 0) {
				$documento->activeSheet->getStyle('A' . ($row - 4) .':AA' . $row)->applyFromArray($styleBorders);
			} else {
				$documento->activeSheet->getStyle('AC' . ($row - 4) .':BC' . $row)->applyFromArray($styleBorders);
			}


			if ($receipt['Liquidacion']['estado'] != 'Confirmada') {
				$documento->addImage('H' . ($documento->getCurrentRow() + 5), 'invalid.png');
				$documento->addImage('AJ' .  ($documento->getCurrentRow() + 5), 'invalid.png');
			}


			$documento->moveCurrentRow(2);
			$c = 0;
			foreach ($receipt['LiquidacionesDetalle'] as $detail) {

			if ($detail['concepto_imprimir'] === 'Si' || ($detail['concepto_imprimir'] === 'Solo con valor') && abs($detail['valor']) > 0) {

				$c++;

				$documento->activeSheet->getRowDimension($documento->getCurrentRow())->setRowHeight(10);
				if (abs($detail['valor_cantidad']) > 0) {
											$documento->setCellValue(($spaces + 1) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 4) . ',' . $documento->getCurrentRow(), $detail['valor_cantidad']);
									} else {
					$documento->setCellValue(($spaces + 1) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 4) . ',' . $documento->getCurrentRow(), ' ');
				}

									$documento->setCellValue(($spaces + 5) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 19) . ',' . $documento->getCurrentRow(), $detail['concepto_nombre']);
				// $documento->setCellValue(($spaces + 16) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 19) . ',' . $documento->getCurrentRow(), $detail['valor']);
				$documento->setCellValue(($spaces + 16) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 19) . ',' . $documento->getCurrentRow(), '');
									if ($detail['concepto_tipo'] !== 'Deduccion') {
											$documento->setCellValue(($spaces + 20) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 23) . ',' . $documento->getCurrentRow(), $detail['valor'], 'currency');
					$documento->setCellValue(($spaces + 24) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), ' ');
									} else {
											$documento->setCellValue(($spaces + 24) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), $detail['valor'], 'currency');
					$documento->setCellValue(($spaces + 20) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 23) . ',' . $documento->getCurrentRow(), ' ');
									}


				$documento->activeSheet->getStyle('A' . $documento->getCurrentRow() . ':BC' . $documento->getCurrentRow())->applyFromArray($styleBorderVertical);

									$documento->moveCurrentRow();
							}
					}


		// Loop throught the empty line to fill the receipt
			for ($diff = 0; $diff <= (23 - $c); $diff++) {

				$documento->activeSheet->getRowDimension($documento->getCurrentRow())->setRowHeight(10);
				$documento->setCellValue(($spaces + 1) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 4) . ',' . $documento->getCurrentRow(), ' ');
				$documento->setCellValue(($spaces + 5) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 19) . ',' . $documento->getCurrentRow(), ' ');
				// $documento->setCellValue(($spaces + 16) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 19) . ',' . $documento->getCurrentRow(), ' ');
				$documento->setCellValue(($spaces + 20) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 23) . ',' . $documento->getCurrentRow(), ' ');
				$documento->setCellValue(($spaces + 24) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), ' ');

				$documento->activeSheet->getStyle('A' . $documento->getCurrentRow() . ':BC' . $documento->getCurrentRow())->applyFromArray($styleBorderVertical);

				$documento->moveCurrentRow();
			}



			$documento->activeSheet->getRowDimension($documento->getCurrentRow())->setRowHeight(10);
			$documento->setCellValue(($spaces + 1) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 10) . ',' . $documento->getCurrentRow(), ' ', $styleRowGrey);
			// $documento->setCellValue(($spaces + 5) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 10) . ',' . $documento->getCurrentRow(), 'Asignaciones Fliares', $styleRowGrey);
			$documento->setCellValue(($spaces + 11) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 15) . ',' . $documento->getCurrentRow(), 'No Rem.', $styleRowGrey);
			$documento->setCellValue(($spaces + 16) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 19) . ',' . $documento->getCurrentRow(), 'Remun', $styleRowGrey);
			$documento->setCellValue(($spaces + 20) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 23) . ',' . $documento->getCurrentRow(), 'Tot. Haberes', $styleRowGrey);
			$documento->setCellValue(($spaces + 24) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), 'Deducciones', $styleRowGrey);

			if ($i == 0) {
				$documento->activeSheet->getStyle('A' . $documento->getCurrentRow() . ':AA' . $documento->getCurrentRow())->applyFromArray($styleBorders);
			} else {
				$documento->activeSheet->getStyle('AC' . $documento->getCurrentRow() . ':BC' . $documento->getCurrentRow())->applyFromArray($styleBorders);
			}
			$documento->moveCurrentRow();
			$documento->setCellValue(($spaces + 1) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 10) . ',' . $documento->getCurrentRow(), ' ');
			// $documento->setCellValue(($spaces + 5) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 10) . ',' . $documento->getCurrentRow(), ' ');
			$documento->setCellValue(($spaces + 11) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 15) . ',' . $documento->getCurrentRow(), $receipt['Liquidacion']['no_remunerativo'], 'currency');
			$documento->setCellValue(($spaces + 16) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 19) . ',' . $documento->getCurrentRow(), $receipt['Liquidacion']['remunerativo'], 'currency');
			$documento->setCellValue(($spaces + 20) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 23) . ',' . $documento->getCurrentRow(), ($receipt['Liquidacion']['remunerativo'] + $receipt['Liquidacion']['no_remunerativo']), 'currency');
			$documento->setCellValue(($spaces + 24) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), $receipt['Liquidacion']['deduccion'], 'currency');


			if ($i == 0) {
				$documento->activeSheet->getStyle('A' . $documento->getCurrentRow() . ':AA' . $documento->getCurrentRow())->applyFromArray($styleBorders);
			} else {
				$documento->activeSheet->getStyle('AC' . $documento->getCurrentRow() . ':BC' . $documento->getCurrentRow())->applyFromArray($styleBorders);
			}


			$documento->moveCurrentRow();
			$documento->setCellValue(($spaces + 19) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 23) . ',' . $documento->getCurrentRow(), 'Neto a Cobrar ', $styleRowGrey);
			$documento->setCellValue(($spaces + 24) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), $receipt['Liquidacion']['total_pesos'], 'currency');

			if ($i == 0) {
				$documento->activeSheet->getStyle('S' . $documento->getCurrentRow() . ':AA' . $documento->getCurrentRow())->applyFromArray($styleBorders);
			} else {
				$documento->activeSheet->getStyle('AU' . $documento->getCurrentRow() . ':BC' . $documento->getCurrentRow())->applyFromArray($styleBorders);
			}


			$documento->moveCurrentRow();
			$documento->setCellValue(($spaces + 1) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 2) . ',' . $documento->getCurrentRow(), 'Son:');
			$documento->setCellValue(($spaces + 3) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), $formato->format($receipt['Liquidacion']['total_pesos'], array('type' => 'numeroEnLetras', 'case' => 'ucfirst')), $styleNormal);
			$documento->moveCurrentRow();
			$documento->setCellValue(($spaces + 14) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), 'Último Depósito Aporte Jubilatorio', $styleNormal);

			$documento->moveCurrentRow();
			$documento->activeSheet->getRowDimension($documento->getCurrentRow())->setRowHeight(10);
			$documento->setCellValue(($spaces + 1) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 13) . ',' . $documento->getCurrentRow(), 'CBU', $styleRowGrey);
			$documento->setCellValue(($spaces + 14) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 17) . ',' . $documento->getCurrentRow(), 'Período', $styleRowGrey);
			$documento->setCellValue(($spaces + 18) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 22) . ',' . $documento->getCurrentRow(), 'Fecha', $styleRowGrey);
			$documento->setCellValue(($spaces + 23) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), 'Banco', $styleRowGrey);

			if ($i == 0) {
				$documento->activeSheet->getStyle('A' . $documento->getCurrentRow() . ':AA' . $documento->getCurrentRow())->applyFromArray($styleBorders);
			} else {
				$documento->activeSheet->getStyle('AC' . $documento->getCurrentRow() . ':BC' . $documento->getCurrentRow())->applyFromArray($styleBorders);
			}



			if (empty($receipt['Suss']['periodo'])) {
				$receipt['Suss']['periodo'] = '';
			} else {
				$receipt['Suss']['periodo'] = $formato->format($receipt['Suss']['periodo'], array('type' => 'periodoEnLetras', 'short' => true, 'case' => 'ucfirst'));
			}
			if (empty($receipt['Suss']['fecha'])) {
				$receipt['Suss']['fecha'] = '';
			} else {
				$receipt['Suss']['fecha'] = $formato->format($receipt['Suss']['fecha'], 'date');
			}
			if (empty($receipt['Banco']['nombre'])) {
				$receipt['Banco']['nombre'] = '';
			}
			$documento->moveCurrentRow();
			$documento->setCellValue(($spaces + 1) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 13) . ',' . $documento->getCurrentRow(), ' ' . $receipt['Liquidacion']['trabajador_cbu']);
			$documento->setCellValue(($spaces + 14) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 17) . ',' . $documento->getCurrentRow(), $receipt['Suss']['periodo']);
			$documento->setCellValue(($spaces + 18) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 22) . ',' . $documento->getCurrentRow(), $receipt['Suss']['fecha']);
			$documento->setCellValue(($spaces + 23) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), $receipt['Banco']['nombre']);

			if ($i == 0) {
				$documento->activeSheet->getStyle('A' . $documento->getCurrentRow() . ':AA' . $documento->getCurrentRow())->applyFromArray($styleBorders);
			} else {
				$documento->activeSheet->getStyle('AC' . $documento->getCurrentRow() . ':BC' . $documento->getCurrentRow())->applyFromArray($styleBorders);
			}

			$documento->moveCurrentRow();
		// $documento->setCellValue(($spaces + 14) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 16) . ',' . $documento->getCurrentRow(), 'Lugar', $styleRightBold);
		// $documento->setCellValue(($spaces + 17) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), ' ');
		// if ($i == 0) {
		// 	$documento->activeSheet->getStyle('N' . $documento->getCurrentRow() . ':P' . $documento->getCurrentRow())->applyFromArray($styleBorderRight['style']);
		// } else {
		// 	$documento->activeSheet->getStyle('AP' . $documento->getCurrentRow() . ':AR' . $documento->getCurrentRow())->applyFromArray($styleBorderRight['style']);
		// }


			$documento->moveCurrentRow();
		// $documento->setCellValue(($spaces + 14) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 16) . ',' . $documento->getCurrentRow(), 'Fecha', $styleRightBold);
		// $documento->setCellValue(($spaces + 17) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 27) . ',' . $documento->getCurrentRow(), ' ');
		// if ($i == 0) {
		// 	$documento->activeSheet->getStyle('N' . $documento->getCurrentRow() . ':P' . $documento->getCurrentRow())->applyFromArray($styleBorderRight['style']);
		// 	$documento->activeSheet->getStyle('Q' . $documento->getCurrentRow() . ':AA' . $documento->getCurrentRow())->applyFromArray($styleBorderBottom['style']);
		// } else {
		// 	$documento->activeSheet->getStyle('AP' . $documento->getCurrentRow() . ':AR' . $documento->getCurrentRow())->applyFromArray($styleBorderRight['style']);
		// 	$documento->activeSheet->getStyle('AS' . $documento->getCurrentRow() . ':BC' . $documento->getCurrentRow())->applyFromArray($styleBorderBottom['style']);
		// }

			if ($i > 0) {
				$documento->addImage('AI' . ($documento->getCurrentRow() - 1), $groupParams['firma']);
			}
			$documento->moveCurrentRow();
			$documento->setCellValue(($spaces + 2) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 12) . ',' . $documento->getCurrentRow(), '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _');

			$documento->moveCurrentRow();
			$firma = 'Empleado';
			if ($i > 0) {
				$firma = 'Empleador';
			}
			$documento->setCellValue(($spaces + 2) . ',' . $documento->getCurrentRow() . ':'. ($spaces + 12) . ',' . $documento->getCurrentRow(), 'Firma / ' . $firma, $styleCenterBold);

//			$documento->setCurrentRow(4);
//
//			$documento->moveCurrentRow();
//
//			$spaces = 40;
//			$documento->setCellValue(($spaces + 1) . ':' . ($spaces + 5), $receipt['Liquidacion']['trabajador_cuil']);
//			$documento->activeSheet->mergeCells('A' . $fila . ':E' . $fila);

		/***************/
			$spaces += 28;
			$row = $documento->getCurrentRow();
			if ($i == 0) {

				$documento->activeSheet->getStyle('A' . $prevLastRow . ':AA' . $documento->getCurrentRow())->applyFromArray($styleThinBlackBorderOutline);
			} else {
				$documento->activeSheet->getStyle('AC' . $prevLastRow . ':BC' . $documento->getCurrentRow())->applyFromArray($styleThinBlackBorderOutline);
				$prevLastRow = $documento->getCurrentRow() + 1;
			}
		}


			// $documento->activeSheet->setBreak('A' . $documento->getCurrentRow(), PHPExcel_Worksheet::BREAK_ROW);
	}
	//d($documento->getCurrentRow());

	//$documento->activeSheet->getStyle('A9:AA13')->applyFromArray($styleBorders);
	//$documento->activeSheet->getStyle('AC9:BC13')->applyFromArray($styleBorders);
	$documento->save('Excel5');

?>