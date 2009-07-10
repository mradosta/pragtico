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
 * @version			$Revision: 528 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2009-05-20 16:56:44 -0300 (Wed, 20 May 2009) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
if (!empty($data)) {
	$documento->create(array('password' => 'PaXXHttBXG66'));
	$documento->doc->getActiveSheet()->getDefaultStyle()->getFont()->setName('Courier New');
	$documento->doc->getActiveSheet()->getDefaultStyle()->getFont()->setSize(6);

	$documento->doc->getActiveSheet()->getDefaultRowDimension()->setRowHeight(10);
	$documento->doc->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
	$documento->doc->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


	if (!empty($groupParams)) {
		$documento->doc->getActiveSheet()->getHeaderFooter()->setOddHeader(
			sprintf("&L%s\n%s - %s\nCP: %s - %s - %s\nCUIT: %s",
				$groupParams['nombre_fantasia'],
				$groupParams['direccion'],
				$groupParams['barrio'],
				$groupParams['codigo_postal'],
				$groupParams['ciudad'],
				$groupParams['pais'],
				$groupParams['cuit']));
	}
	
	/*
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
	

	*/
	/*
	if ($data['Type'] == 'summarized') {
		$documento->setWidth('A', 100);
		$documento->setWidth('B', 10);

		$fila = 1;
		foreach ($data['Total'] as $k => $v) {
			$documento->setCellValue('A' . $fila, $k);
			$documento->setCellValue('B' . $fila, $v);
			$fila++;
		}
	} elseif ($data['Type'] == 'detailed') {
		*/
	
		$styleBoldCenter = array('style' => array(
			'font'		=> array('bold' => true),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
			'borders' 	=> array('bottom'     => array('style' => PHPExcel_Style_Border::BORDER_DOTTED))));
	
		$styleBoldRight = array('style' => array(
			'font'		=> array('bold' => true),
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)));
		
		$styleBold = array('style' => array('font' => array(
			'bold' 		=> true)));
		
		$styleRight = array('style' => array(
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)));
	
        $fila = 1;
        if ($desagregado === 'Si') {
            $documento->setCellValue('D' . $fila, date('d/m/Y'), $styleBold);
        } else {
            $documento->setCellValue('C' . $fila, date('d/m/Y'), $styleBold);
        }
        $fila = 3;
        $documento->setCellValue('A' . $fila, 'Listado de Totales por Concepto', $styleBold);
        $fila++;
        $documento->setCellValue('A' . $fila, 'Empresa: ' . $conditions['Liquidacion-empleador_id__'], $styleBold);
        $fila++;
        $documento->setCellValue('A' . $fila, 'Periodo: ' . $conditions['Liquidacion-periodo_largo'], $styleBold);

        if ($desagregado === 'Si') {
            $documento->setWidth('A', 65);
            $documento->setWidth('B', 65);
            $documento->setWidth('C', 13);
            $documento->setWidth('D', 13);
            $documento->setCellValue('A' . $fila, 'Trabajador', $styleBoldCenter);
            $documento->setCellValue('B' . $fila, 'Concepto', $styleBoldCenter);
            $documento->setCellValue('C' . $fila, 'Cantidad', $styleBoldCenter);
            $documento->setCellValue('D' . $fila, 'Total', $styleBoldCenter);
        } else {
            $documento->setWidth('A', 65);
            $documento->setWidth('B', 13);
            $documento->setWidth('C', 13);
            $documento->setCellValue('A' . $fila, 'Concepto', $styleBoldCenter);
            $documento->setCellValue('B' . $fila, 'Cantidad', $styleBoldCenter);
            $documento->setCellValue('C' . $fila, 'Total', $styleBoldCenter);
        }
         
        $fila = 7;
        $total = 0;
		foreach ($data as $detail) {
            $fila++;

            if ($desagregado === 'Si') {
                $documento->setCellValue('A' . $fila, $detail['Liquidacion']['trabajador_apellido'] . ', ' . $detail['Liquidacion']['trabajador_nombre']);
                $documento->setCellValue('B' . $fila, $detail['LiquidacionesDetalle']['concepto_nombre']);
                $documento->setCellValue('C' . $fila, $detail['LiquidacionesDetalle']['suma_cantidad'], $styleRight);
                $documento->activeSheet->getStyle('D' . $fila)->getNumberFormat()->setFormatCode('"$ "0.00');
                $documento->setCellValue('D' . $fila, $detail['LiquidacionesDetalle']['valor'], $styleRight);
            } else {
                $documento->setCellValue('A' . $fila, $detail['LiquidacionesDetalle']['concepto_nombre']);
                $documento->setCellValue('B' . $fila, $detail['LiquidacionesDetalle']['suma_cantidad'], $styleRight);
                $documento->activeSheet->getStyle('C' . $fila)->getNumberFormat()->setFormatCode('"$ "0.00');
                $documento->setCellValue('C' . $fila, $detail['LiquidacionesDetalle']['valor'], $styleRight);
            }
            $total += $detail['LiquidacionesDetalle']['valor'];
		}

		$fila+=2;
		$documento->setCellValue('A' . $fila . ':C' . $fila, 'TOTALES', $styleBoldCenter);
		$fila++;
        if ($desagregado === 'Si') {
            $documento->setCellValue('B' . $fila, 'Total', $styleBold);
            $documento->activeSheet->getStyle('D' . $fila)->getNumberFormat()->setFormatCode('"$ "0.00');
            $documento->setCellValue('D' . $fila, $total, $styleRight);
            $fila++;
            $documento->setCellValue('B' . $fila, 'Total de Empleados', $styleBold);
            $documento->setCellValue('D' . $fila, $workers[0]['Liquidacion']['cantidad'], $styleRight);
        } else {
            $documento->setCellValue('A' . $fila, 'Total', $styleBold);
            $documento->activeSheet->getStyle('C' . $fila)->getNumberFormat()->setFormatCode('"$ "0.00');
            $documento->setCellValue('C' . $fila, $total, $styleRight);
            $fila++;
            $documento->setCellValue('A' . $fila, 'Total de Empleados', $styleBold);
            $documento->setCellValue('C' . $fila, $workers[0]['Liquidacion']['cantidad'], $styleRight);
        }

        $this->set('fileFormat', $this->data['Condicion']['Liquidacion-formato']);
        $documento->save($fileFormat);
	
} else {

//	if (!empty($grupos)) {
//		$condiciones['Condicion.Liquidacion-grupo_id'] = array('options' => $grupos);
//	}
	
	$condiciones['Condicion.Liquidacion-empleador_id'] = array('lov' => array(
		'controller'		=> 'empleadores',
		'seleccionMultiple'	=> 0,
		'camposRetorno'		=> array('Empleador.nombre')));
        
    $condiciones['Condicion.Liquidacion-trabajador_id'] = array(
            'lov'   => array(   'controller'   => 'trabajadores',
                                'seleccionMultiple'    => 0,
                                'camposRetorno' => array('Trabajador.cuil', 'Trabajador.nombre', 'Trabajador.apellido')));

    $condiciones['Condicion.Liquidacion-desagregado'] = array('type' => 'radio', 'options' => array('Si' => 'Si', 'No' => 'No'));
    $condiciones['Condicion.Liquidacion-tipo'] = array('label' => 'Tipo', 'type' => 'select');
    $condiciones['Condicion.Liquidacion-periodo_largo'] = array('type' => 'periodo', 'label' => 'Periodo');
	$condiciones['Condicion.Liquidacion-estado'] = array('type' => 'select', 'multiple' => 'checkbox');
    $condiciones['Condicion.Liquidacion-formato'] = array('type' => 'radio', 'options' => array('Excel5' => 'Excel', 'Excel2007' => 'Excel 2007'), 'value' => 'Excel2007');

	$fieldsets[] = array('campos' => $condiciones);
	$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('imagen' => 'resumen.gif', 'legend' => "Resumen")));


	$botonesExtra = $appForm->submit("Generar", array("title"=>"Genera el Resumen de Liquidacion"));
	$accionesExtra['opciones'] = array("acciones"=>array());
	$opcionesTabla =  array("tabla"=>array(	"omitirMensajeVacio"=>true));

	echo $this->element('index/index', array("opcionesForm"=>array("action"=>"resumen"), "opcionesTabla"=>$opcionesTabla, "accionesExtra"=>$accionesExtra, "botonesExtra"=>array('opciones' => array("botones"=>array("limpiar", $botonesExtra))), "condiciones"=>$fieldset));

}
 
?>