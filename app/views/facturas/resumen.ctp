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
 
if (!empty($data)) {

	$documento->create(array('password' => 'PaXXHttBXG66'));
	$documento->doc->getActiveSheet()->getDefaultStyle()->getFont()->setName('Courier New');
	$documento->doc->getActiveSheet()->getDefaultStyle()->getFont()->setSize(6);

	$documento->doc->getActiveSheet()->getDefaultRowDimension()->setRowHeight(10);
	$documento->doc->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$documento->doc->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	
	if (!empty($employer)) {
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
	$right = '&RHoja &P';
	
	$documento->doc->getActiveSheet()->getHeaderFooter()->setOddHeader($left . $center . $right);
	
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
	

	
	$documento->setWidth('A', 100);
	$documento->setWidth('B', 10);

	d($data);
	foreach ($data as $record) {
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

		if ($recordCount === 4) {
			$recordCount = 0;
			$documento->doc->getActiveSheet()->setBreak('A' .$fila, PHPExcel_Worksheet::BREAK_ROW);
			$fila+=2;
		}
	}
	$documento->save($fileFormat);
	
} else {

	if (!empty($grupos)) {
		$condiciones['Condicion.Liquidacion-grupo_id'] = array('options' => $grupos);
	}
	
	$condiciones['Condicion.Liquidacion-empleador_id'] = array(	"lov"=>array("controller"	=> "empleadores",
																			"camposRetorno"	=> array("Empleador.nombre")));
	$condiciones['Condicion.Liquidacion-periodo'] = array("type" => "periodo");
	$condiciones['Resumen.tipo'] = array("type"=>"radio", "options"=>$tipos);
	$condiciones['Condicion.Liquidacion-estado'] = array('type' => 'select', 'multiple' => 'checkbox', 'aclaracion' => 'Se refiere a que liquidaciones tomar como base para la prefacturacion.');

	$fieldsets[] = array('campos' => $condiciones);
	$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('imagen' => 'buscar.gif', 'legend' => "Resumen")));


	$botonesExtra = $appForm->submit("Generar", array("title"=>"Imprime el Resumen de Facturacion"));
	$accionesExtra['opciones'] = array("acciones"=>array());
	$opcionesTabla =  array("tabla"=>array(	"omitirMensajeVacio"=>true));

	echo $this->element('index/index', array("opcionesForm"=>array("action"=>"resumen"), "opcionesTabla"=>$opcionesTabla, "accionesExtra"=>$accionesExtra, "botonesExtra"=>array('opciones' => array("botones"=>array("limpiar", $botonesExtra))), "condiciones"=>$fieldset));

}
 
?>