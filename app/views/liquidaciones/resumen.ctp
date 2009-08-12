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
	
    $fila = 1;
    if ($desagregado === 'Si') {
        $documento->setCellValue('D' . $fila, date('Y-m-d'), 'bold');
    } else {
        $documento->setCellValue('C' . $fila, date('Y-m-d'), 'bold');
    }
    $fila = 3;
    $documento->setCellValue('A' . $fila, 'Listado de Totales por Concepto', 'bold');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Empresa: ' . $conditions['Liquidacion-empleador_id__'], 'bold');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Periodo: ' . $conditions['Liquidacion-periodo_largo'], 'bold');

    $fila+=2;
    if ($desagregado === 'Si') {
        $documento->setWidth('A', 55);
        $documento->setWidth('B', 55);
        $documento->setWidth('C', 13);
        $documento->setWidth('D', 13);
        $documento->setCellValue('A' . $fila, 'Trabajador', 'title');
        $documento->setCellValue('B' . $fila, 'Concepto', 'title');
        $documento->setCellValue('C' . $fila, 'Cantidad', 'title');
        $documento->setCellValue('D' . $fila, 'Total', 'title');
        $documento->setCellValue('E' . $fila, 'Coeficiente', 'title');
        $documento->setCellValue('F' . $fila, 'Total', 'title');
    } else {
        $documento->setWidth('A', 65);
        $documento->setWidth('B', 13);
        $documento->setWidth('C', 13);
        $documento->setCellValue('A' . $fila, 'Concepto', 'title');
        $documento->setCellValue('B' . $fila, 'Cantidad', 'title');
        $documento->setCellValue('C' . $fila, 'Total', 'title');
        $documento->setCellValue('D' . $fila, 'Coeficiente', 'title');
        $documento->setCellValue('E' . $fila, 'Total', 'title');
    }

    $fila = 7;
    $total = 0;
    $flag = null;
    foreach ($data as $detail) {

        if (abs($detail['LiquidacionesDetalle']['valor']) > 0) {
            $fila++;

            if ($detail['LiquidacionesDetalle']['concepto_tipo'] === 'Deduccion') {
                $detail['LiquidacionesDetalle']['valor'] = $detail['LiquidacionesDetalle']['valor'] * -1;
            }

            if ($desagregado === 'Si') {
                if ($flag !== $detail['Liquidacion']['id']) {
                    if ($flag != null) {
                        $fila++;
                    }
                    $flag = $detail['Liquidacion']['id'];
                    $documento->setCellValue('A' . $fila, $detail['Liquidacion']['trabajador_apellido'] . ', ' . $detail['Liquidacion']['trabajador_nombre']);
                }
                $documento->setCellValue('B' . $fila, $detail['LiquidacionesDetalle']['concepto_nombre']);
                $documento->setCellValue('C' . $fila, $detail['LiquidacionesDetalle']['suma_cantidad'], 'decimal');
                $documento->setCellValue('D' . $fila, $detail['LiquidacionesDetalle']['valor'], 'currency');
                $documento->setCellValue('E' . $fila, $detail['LiquidacionesDetalle']['coeficiente_valor'], 'decimal');
                $documento->setCellValue('F' . $fila, $detail['LiquidacionesDetalle']['suma_cantidad'] * $detail['LiquidacionesDetalle']['coeficiente_valor'], 'currency');
            } else {
                $documento->setCellValue('A' . $fila, $detail['LiquidacionesDetalle']['concepto_nombre']);
                $documento->setCellValue('B' . $fila, $detail['LiquidacionesDetalle']['suma_cantidad'], 'decimal');
                $documento->setCellValue('C' . $fila, $detail['LiquidacionesDetalle']['valor'], 'currency');
                $documento->setCellValue('D' . $fila, $detail['LiquidacionesDetalle']['coeficiente_valor'], 'decimal');
                $documento->setCellValue('E' . $fila, $detail['LiquidacionesDetalle']['suma_cantidad'] * $detail['LiquidacionesDetalle']['coeficiente_valor'], 'currency');
            }
            $total += $detail['LiquidacionesDetalle']['valor'];
        }
    }

    $fila+=2;
    if ($desagregado === 'Si') {
        $documento->setCellValue('A' . $fila . ':D' . $fila, 'TOTALES', 'title');
        $fila++;
        $documento->setCellValue('B' . $fila, 'Pesos / Beneficios', array('right', 'bold'));
        $documento->setCellValue('D' . $fila, $total, 'total');
        $fila++;
        $documento->setCellValue('B' . $fila, 'Empleados', array('right', 'bold'));
        $documento->setCellValue('D' . $fila, $workers[0]['Liquidacion']['cantidad'], 'total');
    } else {
        $documento->setCellValue('A' . $fila . ':C' . $fila, 'TOTALES', 'title');
        $fila++;
        $documento->setCellValue('A' . $fila, 'Pesos / Beneficios', array('right', 'bold'));
        $documento->setCellValue('C' . $fila, $total, 'total');
        $fila++;
        $documento->setCellValue('A' . $fila, 'Empleados', array('right', 'bold'));
        $documento->setCellValue('C' . $fila, $workers[0]['Liquidacion']['cantidad'], 'total');
    }

    $this->set('fileFormat', $this->data['Condicion']['Liquidacion-formato']);
    $documento->save($fileFormat);
	
} else {

    if (!empty($grupos)) {
        $condiciones['Condicion.Liquidacion-grupo_id'] = array('options' => $grupos, 'empty' => true);
    }
	
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
    $condiciones['Condicion.Liquidacion-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => array('1Q', '2Q', 'M', '1S', '2S'));
	$condiciones['Condicion.Liquidacion-estado'] = array('type' => 'select', 'multiple' => 'checkbox');
    $condiciones['Condicion.Liquidacion-formato'] = array('type' => 'radio', 'options' => array('Excel5' => 'Excel', 'Excel2007' => 'Excel 2007'), 'value' => 'Excel2007');

	$fieldsets[] = array('campos' => $condiciones);
	$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('imagen' => 'resumen.gif', 'legend' => "Resumen")));

    $accionesExtra['opciones'] = array('acciones' => array());
    $botonesExtra[] = 'limpiar';
    $botonesExtra[] = $appForm->submit('Generar', array('title' => 'Genera el Resumen de Liquidacion', 'onclick'=>'document.getElementById("accion").value="generar"'));

    echo $this->element('index/index', array(
                        'opcionesTabla' => array('tabla' => array('omitirMensajeVacio' => true)),
                        'botonesExtra'  => array('opciones' => array('botones' => $botonesExtra)),
                        'accionesExtra' => $accionesExtra,
                        'opcionesForm'  => array('action' => 'resumen'),
                        'condiciones'   => $fieldset,
                        'cuerpo'        => null));
    

}
 
?>