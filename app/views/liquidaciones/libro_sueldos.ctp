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
 * @version			$Revision: 236 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2009-01-27 11:26:49 -0200 (mar, 27 ene 2009) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
if (!empty($data)) {

	$documento->create(array('password' => 'PaXXHttBXG66'));
	$documento->doc->getActiveSheet()->getDefaultStyle()->getFont()->setName('Courier New');
	$documento->doc->getActiveSheet()->getDefaultStyle()->getFont()->setSize(6);

	$documento->doc->getActiveSheet()->getDefaultRowDimension()->setRowHeight(10);
	$documento->doc->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$documento->doc->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	
	/**
	* Issue reported
	* http://phpexcel.codeplex.com/WorkItem/View.aspx?WorkItemId=9560
	*/
	$pageMargins = $documento->doc->getActiveSheet()->getPageMargins();
	$pageMargins->setBottom(0.2);
	$pageMargins->setLeft(0.2);
	$pageMargins->setRight(0.2);

	if (empty($groupParams)) {
		//$left = sprintf("&L%s\n%s - %s\nCP: %s - %s - %s\nCUIT: %s", $employer['Empleador']['nombre'], $employer['Empleador']['direccion'], $employer['Empleador']['barrio'], $employer['Empleador']['codigo_postal'], $employer['Empleador']['ciudad'], $employer['Empleador']['pais'], $employer['Empleador']['cuit']);
		$left = '';
		$center = "&CLibro Especial de Sueldos - Art. 52 Ley 20744";
	} else {
		$left = sprintf("&L%s\n%s - %s\nCP: %s - %s - %s\nCUIT: %s",
			$groupParams['nombre_fantasia'],
			$groupParams['direccion'],
			$groupParams['barrio'],
			$groupParams['codigo_postal'],
			$groupParams['ciudad'],
			$groupParams['pais'],
			$groupParams['cuit']);
		$center = "&CLibro Especial de Sueldos - Art. 52 Ley 20744" . $groupParams['libro_sueldos_encabezado'];
	}
	//$right = '&RHoja &P';
	$documento->doc->getActiveSheet()->getHeaderFooter()->setOddHeader($left . $center);
	
	$styleBoldCenter = array('style' => array(
		'font'		=> array('bold' => true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
		'borders' 	=> array( 'bottom'     => array('style' => PHPExcel_Style_Border::BORDER_DOTTED))));
	$styleBoldRight = array('style' => array('font'		=> array(
		'bold' 		=> true),
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)));
	$styleBold = array('style' => array('font' => array(
		'bold' 		=> true)));
	$styleRight = array('style' => array(
		'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)));
	$styleBorderBottom = array('style' => array(
		'borders' => array( 'bottom'     => array('style' => PHPExcel_Style_Border::BORDER_DASHDOT))));
	

	
	$documento->setWidth('A', 30);
	$documento->setWidth('B', 10);
	$documento->setWidth('C', 10);
	$documento->setWidth('D', 3);
	$documento->setWidth('E', 30);
	$documento->setWidth('F', 10);
	$documento->setWidth('G', 10);
	$documento->setWidth('H', 3);
	$documento->setWidth('I', 30);
	$documento->setWidth('J', 10);
	$documento->setWidth('K', 10);


	$fila = 0;
	$employerFlag = null;
	$pageCount = $startPage - 1;
	foreach ($data as $k => $record) {
		
		if ($employerFlag !== $record['Relacion']['Empleador']['cuit']) {
			$employerFlag = $record['Relacion']['Empleador']['cuit'];

			$recordCount = 0;
			$documento->doc->getActiveSheet()->setBreak('A' . $fila, PHPExcel_Worksheet::BREAK_ROW);
			$fila++;
			$pageCount++;
			$documento->setCellValue('K' . $fila, 'Hoja ' . $pageCount);
			
			$fila+=2;
			$documento->setCellValue('A' . $fila, 'Empresa Usuario:');
			$documento->setCellValue('B' . $fila, $record['Relacion']['Empleador']['nombre'], $styleBold);
			$documento->setCellValue('I' . $fila, 'Periodo: ' . $formato->format($periodo, array('type' => 'periodoEnLetras', 'short' => true, 'case' => 'ucfirst')), $styleBold);
			
			$fila++;
			$documento->setCellValue('A' . $fila, 'CUIT:');
			$documento->setCellValue('B' . $fila, $record['Relacion']['Empleador']['cuit'], $styleBold);
			
			$fila++;
			$documento->setCellValue('A' . $fila, 'Direccion:');
			$documento->setCellValue('B' . $fila, $record['Relacion']['Empleador']['direccion']);
			
			$fila+=3;
		}
		$recordCount++;
		
		$fila++;
		$documento->setCellValue('A' . $fila, 'CUIL: ' . $record['Relacion']['Trabajador']['cuil']);
		$documento->setCellValue('E' . $fila, 'Apellido y Nombre: ' . $record['Relacion']['Trabajador']['apellido'] . ' ' . $record['Relacion']['Trabajador']['nombre']);
		$documento->setCellValue('I' . $fila, 'Categoria: ' . $record['Liquidacion']['convenio_categoria_nombre']);

		$fila++;
		$documento->setCellValue('A' . $fila, 'Legajo: ' . $record['Relacion']['legajo']);
		$documento->setCellValue('E' . $fila, 'Contrato: ' . $record['Relacion']['Modalidad']['nombre']);
		$documento->setCellValue('I' . $fila, 'Suel/Jorn.: $' . number_format($record['Relacion']['ConveniosCategoria']['costo'], 2, '.', ''));

		$fila++;
		$documento->setCellValue('A' . $fila, 'Ingreso: ' . $formato->format($record['Relacion']['ingreso'], 'date'));
		$egreso = $formato->format($record['Relacion']['egreso'], 'date');
		$documento->setCellValue('E' . $fila, 'Baja: ' . $egreso);
		if (empty($egreso)) {
			$documento->setCellValue('I' . $fila, 'Estado: Activo');
		} else {
			$documento->setCellValue('I' . $fila, 'Estado: Inactivo');
		}

		
		$fila++;
		$documento->setCellValue('A' . $fila . ':C' . $fila, 'Remunerativo', $styleBoldCenter);
		$documento->setCellValue('B' . $fila, '', $styleBoldCenter);
		$documento->setCellValue('C' . $fila, '', $styleBoldCenter);
		$documento->setCellValue('E' . $fila . ':G' . $fila, 'Deduccion', $styleBoldCenter);
		$documento->setCellValue('F' . $fila, '', $styleBoldCenter);
		$documento->setCellValue('G' . $fila, '', $styleBoldCenter);
		$documento->setCellValue('I' . $fila . ':K' . $fila, 'No Remunerativo', $styleBoldCenter);
		$documento->setCellValue('J' . $fila, '', $styleBoldCenter);
		$documento->setCellValue('K' . $fila, '', $styleBoldCenter);
		
		$fila++;
		$documento->setCellValue('A' . $fila, 'Descripcion');
		$documento->setCellValue('B' . $fila, 'Cantidad', $styleRight);
		$documento->setCellValue('C' . $fila, 'Importe', $styleRight);
		
		$documento->setCellValue('E' . $fila, 'Descripcion');
		$documento->setCellValue('F' . $fila, 'Cantidad', $styleRight);
		$documento->setCellValue('G' . $fila, 'Importe', $styleRight);
		
		$documento->setCellValue('I' . $fila, 'Descripcion');
		$documento->setCellValue('J' . $fila, 'Cantidad', $styleRight);
		$documento->setCellValue('K' . $fila, 'Importe', $styleRight);

		$detailFlag = null;
		$initialRow = $fila;
		$maxCount = 0;
		foreach ($record['LiquidacionesDetalle'] as $detail) {

			if($detail['concepto_imprimir'] === 'Si' || ($detail['concepto_imprimir'] === 'Solo con valor' && abs($detail['valor']) > 0)) {
				if ($detailFlag !== $detail['concepto_tipo']) {
					$detailFlag = $detail['concepto_tipo'];
					$fila = $initialRow;
					$count = 0;

					if ($count > $maxCount) {
						$maxCount = $count;
					}
				}
				$fila++;
				$count++;

				if ($detail['concepto_tipo'] === 'Remunerativo') {
					$documento->setCellValue('A' . $fila, $detail['concepto_nombre']);
					$documento->setCellValue('B' . $fila, $detail['valor_cantidad']);
					$documento->setCellValue('C' . $fila, '$' . $detail['valor'], $styleRight);
				} elseif ($detail['concepto_tipo'] === 'Deduccion') {
					$documento->setCellValue('E' . $fila, $detail['concepto_nombre']);
					$documento->setCellValue('F' . $fila, $detail['valor_cantidad']);
					$documento->setCellValue('G' . $fila, '$' . $detail['valor'], $styleRight);
				} elseif ($detail['concepto_tipo'] === 'No Remunerativo') {
					$documento->setCellValue('I' . $fila, $detail['concepto_nombre']);
					$documento->setCellValue('J' . $fila, $detail['valor_cantidad']);
					$documento->setCellValue('K' . $fila, '$' . $detail['valor'], $styleRight);
				}
			}
		}
		
		if ($count > $maxCount) {
			$maxCount = $count;
		}
		$fila = $initialRow + $maxCount + 1;
		$documento->setCellValue('A' . $fila, 'Totales');
		$documento->setCellValue('C' . $fila, '$ ' . $record['Liquidacion']['remunerativo'], $styleBoldRight);
		$documento->setCellValue('G' . $fila, '$ ' . $record['Liquidacion']['deduccion'], $styleBoldRight);
		$documento->setCellValue('K' . $fila, '$ ' . $record['Liquidacion']['no_remunerativo'], $styleBoldRight);

		$fila++;
		$documento->setCellValue('K' . $fila, 'Total Neto $ ' . $record['Liquidacion']['total_pesos'], $styleBoldRight);

		$fila++;
		$documento->setCellValue('A' . $fila, '', $styleBorderBottom);
		$documento->setCellValue('B' . $fila, '', $styleBorderBottom);
		$documento->setCellValue('C' . $fila, '', $styleBorderBottom);
		$documento->setCellValue('D' . $fila, '', $styleBorderBottom);
		$documento->setCellValue('E' . $fila, '', $styleBorderBottom);
		$documento->setCellValue('F' . $fila, '', $styleBorderBottom);
		$documento->setCellValue('G' . $fila, '', $styleBorderBottom);
		$documento->setCellValue('H' . $fila, '', $styleBorderBottom);
		$documento->setCellValue('I' . $fila, '', $styleBorderBottom);
		$documento->setCellValue('J' . $fila, '', $styleBorderBottom);
		$documento->setCellValue('K' . $fila, '', $styleBorderBottom);
		$fila++;

		if ($recordCount === 4 && $k < count($data) - 1) {
			$recordCount = 0;
			$documento->doc->getActiveSheet()->setBreak('A' . $fila, PHPExcel_Worksheet::BREAK_ROW);
			$fila++;
			$pageCount++;
			$documento->setCellValue('K' . $fila, 'Hoja ' . $pageCount);
			$fila++;
		}
	}
	$documento->save($fileFormat);
	
} else {
	if (!empty($grupos)) {
		$condiciones['Condicion.Liquidacion-grupo_id'] = array('options' => $grupos);
	}
	$condiciones['Condicion.Liquidacion-empleador_id'] = array(	'lov' => array(
			'controller'		=>	'empleadores',
			'seleccionMultiple' => false,
			'camposRetorno'		=> array('Empleador.cuit', 'Empleador.nombre')));
	$condiciones['Condicion.Liquidacion-periodo'] = array('type' => 'periodo', 'periodo' => array('1Q', '2Q', 'M', '1S', '2S'));
	$condiciones['Condicion.Liquidacion-tipo'] = array('label' => 'Tipo', 'multiple' => 'checkbox', 'type' => 'select');
	$condiciones['Condicion.Liquidacion-formato'] = array('type' => 'radio', 'options' => array('Excel5' => 'Excel', 'Excel2007' => 'Excel 2007'), 'value' => 'Excel2007');
	$condiciones['Condicion.Bar-start_page'] = array('label' => 'Hoja Inicial', 'type' => 'text', 'value' => '1');

	$fieldsets[] = array('campos' => $condiciones);
	$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('legend' => 'Generar Libro Sueldos','imagen' => 'archivo.gif')));

	$accionesExtra['opciones'] = array('acciones' => array());
	$botonesExtra[] = 'limpiar';
	$botonesExtra[] = $appForm->submit('Generar', array('title' => 'Genera el Libro de Sueldos', 'onclick'=>'document.getElementById("accion").value="generar"'));

	echo $this->element('index/index', array(
						'opcionesTabla' => array('tabla' => array('omitirMensajeVacio' => true)),
						'botonesExtra'	=> array('opciones' => array('botones' => $botonesExtra)),
						'accionesExtra'	=> $accionesExtra,
						'opcionesForm'	=> array('action' => 'libro_sueldos'),
						'condiciones'	=> $fieldset,
						'cuerpo'		=> null));
}
 
?>