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
 
if(!empty($data)) {
	$documento->create();
	$fila = $filaInicio = 8;

	/**
	* Pongo el titulo de la planilla.
	*/
	$documento->setCellValue('E1:M3', 'Solicitud de Tarjetas de Debito - ' . date('d/m/Y'),
		array('style'=>array('font'		=> array('bold' => true, 'size' => 14),
							'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))
			));

	/**
	* Agrego el logo de Pragtico.
	*/
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setName('Pragtico');
	$objDrawing->setDescription('Pragtico');
	$objDrawing->setPath(WWW_ROOT . 'img' . DS . 'logo_pragtico.jpg');
	$objDrawing->setCoordinates('B1');
	$objDrawing->setHeight(130);
	$objDrawing->setWidth(260);
	$objDrawing->getShadow()->setVisible(true);
	$objDrawing->setWorksheet($documento->doc->getActiveSheet());


	/**
	* Pongo las columnas en auto ajuste del ancho.
	*/
	$documento->doc->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$documento->doc->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$documento->doc->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$documento->doc->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

	/**
	* Pongo los titulos de las columnas.
	*/
	$estiloTituloColumna =
		array(
			'font'    => array(
				'bold'      => true
			),
			'alignment' => array(
				'vertical' 	 => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'fill' => array(
				'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation'   => 90,
				'startcolor' => array(
					'argb' => 'FFA0A0A0'
				),
				'endcolor'   => array(
					'argb' => 'FFFFFFFF'
				)
			)
		);

	$documento->setCellValue('A' . $fila, 'Cuil', array('style' => $estiloTituloColumna));
	$documento->setCellValue('B' . $fila, 'Tipo Documento', array('style' => $estiloTituloColumna));
    $documento->setCellValue('C' . $fila, 'Numero Documento', array('style' => $estiloTituloColumna));
    $documento->setCellValue('D' . $fila, 'Apellido', array('style' => $estiloTituloColumna));
    $documento->setCellValue('E' . $fila, 'Nombre', array('style' => $estiloTituloColumna));
    $documento->setCellValue('F' . $fila, 'Dicreccion', array('style' => $estiloTituloColumna));
    $documento->setCellValue('G' . $fila, 'Localidad', array('style' => $estiloTituloColumna));
    $documento->setCellValue('H' . $fila, 'Provincia', array('style' => $estiloTituloColumna));
    $documento->setCellValue('I' . $fila, 'Sexo', array('style' => $estiloTituloColumna));
    $documento->setCellValue('J' . $fila, 'Estado Civil', array('style' => $estiloTituloColumna));
    $documento->setCellValue('K' . $fila, 'Ingreso', array('style' => $estiloTituloColumna));
    $documento->setCellValue('L' . $fila, 'Nacimiento', array('style' => $estiloTituloColumna));
    $documento->setCellValue('M' . $fila, 'Codigo Postal', array('style' => $estiloTituloColumna));
	
    
	foreach($data as $record) {
        $fila++;
        
        $documento->setCellValue('A' . $fila, $record['Trabajador']['cuil']);
        $documento->setCellValue('B' . $fila, $record['Trabajador']['tipo_documento']);
        $documento->setCellValue('C' . $fila, $record['Trabajador']['numero_documento']);
        $documento->setCellValue('D' . $fila, $record['Trabajador']['apellido']);
        $documento->setCellValue('E' . $fila, $record['Trabajador']['nombre']);
        $documento->setCellValue('F' . $fila, $record['Trabajador']['direccion']);
        $documento->setCellValue('G' . $fila, $record['Localidad']['nombre']);
        $documento->setCellValue('H' . $fila, $record['Localidad']['Provincia']['nombre']);
        $documento->setCellValue('I' . $fila, $record['Trabajador']['sexo']);
        $documento->setCellValue('J' . $fila, $record['Trabajador']['estado_civil']);
        $documento->setCellValue('K' . $fila, (!empty($record['Relacion']['ingreso']))?$formato->format($record['Relacion']['ingreso'], 'date'):'');
        $documento->setCellValue('L' . $fila, $formato->format($record['Trabajador']['nacimiento'], 'date'));
        $documento->setCellValue('M' . $fila, $record['Trabajador']['codigo_postal']);
        
	}

    $documento->save('Excel5');
}

?>