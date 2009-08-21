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


    /** Set array with definitios values. */
    $definitions = array(   array(  'width' => 55,
                                    'title' => ($group_option == 'worker')?'Trabajador':'Coeficiente' . ' / Concepto',
                                    'option' => null),
                            array(  'width' => 15,
                                    'title' => 'Cantidad',
                                    'option' => 'decimal'),
                            array(  'width' => 15,
                                    'title' => 'Total',
                                    'option' => 'currency'),
                            array(  'width' => 15,
                                    'title' => 'Coeficiente',
                                    'option' => 'decimal'),
                            array(  'width' => 15,
                                    'title' => 'Total',
                                    'option' => 'currency'));

    $fila = 1;
    $documento->setCellValue('E' . $fila, date('Y-m-d'), 'bold');
    $fila+=2;
    $documento->setCellValue('A' . $fila, 'Listado de Totales por Concepto', 'bold');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Empresa: ' . $conditions['Liquidacion-empleador_id__'], 'bold');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Periodo: ' . $conditions['Liquidacion-periodo_largo'], 'bold');


    $fila+=2;
    /** Create headers */
    $column = 0;
    foreach ($definitions as $definition) {
        /** Set width columns. */
        if (!empty($definition['width'])) {
            $documento->setWidth($column, $definition['width']);
        }
        /** Set title columns. */
        $documento->setCellValue($column . ',' . $fila, $definition['title'], 'title');
        $column++;
    }


    $fila = 7;
    $total = 0;
    $flag = null;
    $inicio = 0;
    $flagCoeficiente = null;    

    /** Body */
    foreach ($data as $k => $detail) {

        $fila++;
        $documento->setCellValue('A' . $fila, $k, 'bold');
        $beginRow = $fila;
        foreach ($detail as $r) {
            $fila++;
            $documento->setCellValueFromArray(
                array(  '0,' . $fila => '    ' . $r['LiquidacionesDetalle']['concepto_nombre'],
                        '1,' . $fila => $r['LiquidacionesDetalle']['suma_cantidad'],
                        '2,' . $fila => array('value' => $r['LiquidacionesDetalle']['valor'], 'options' => 'currency'),
                        '3,' . $fila => $r['LiquidacionesDetalle']['coeficiente_valor'],
                        '4,' . $fila => array('value' => '=C' . $fila . '*' . 'D' . $fila, 'options' => 'currency')));
        }
        $fila++;
        $totals['C'][] = 'C' . $fila;
        $totals['E'][] = 'E' . $fila;
        $documento->setCellValueFromArray(
            array(  '2,' . $fila =>
                array('value' => '=SUM(C' . $beginRow . ':C' . ($fila - 1) . ')', 'options' => 'total'),
                    '4,' . $fila =>
                array('value' => '=SUM(E' . $beginRow . ':E' . ($fila - 1) . ')', 'options' => 'total')));
    }

    $fila+3;
    $documento->setCellValue('A' . $fila . ':E' . $fila, 'TOTALES', 'title');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Trabajadores', 'bold');
    $documento->setCellValue('E' . $fila, $totalWorkers, 'bold');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Liquidado', 'bold');
    $documento->setCellValue('E' . $fila, '=SUM('.implode('+', $totals['C']).')', 'total');
    $fila++;
    $documento->setCellValue('A' . $fila, 'Facturado', 'bold');
    $documento->setCellValue('E' . $fila, '=SUM('.implode('+', $totals['E']).')', 'total');
    $fila++;
    

    $this->set('fileFormat', $this->data['Condicion']['Liquidacion-formato']);
    $documento->save($fileFormat);
    
} else {

    if (!empty($grupos)) {
        $condiciones['Condicion.Liquidacion-grupo_id'] = array('options' => $grupos, 'empty' => true);
    }
    
    $condiciones['Condicion.Liquidacion-empleador_id'] = array('lov' => array(
        'controller'        => 'empleadores',
        'seleccionMultiple' => 0,
        'camposRetorno'     => array('Empleador.nombre')));
        
    $condiciones['Condicion.Liquidacion-trabajador_id'] = array(
            'lov'   => array(   'controller'   => 'trabajadores',
                                'seleccionMultiple'    => 0,
                                'camposRetorno' => array('Trabajador.cuil', 'Trabajador.nombre', 'Trabajador.apellido')));

    $condiciones['Condicion.Liquidacion-group_option'] = array('type' => 'radio', 'options' => $options);
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