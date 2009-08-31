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

    $documento->create();
    $fila = 2;
    $documento->setCellValue('A' . $fila, 'Listado de Aportes Sindicales', 'bold');

    $documento->moveCurrentRow(7, false);
    
    $documento->doc->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);

    $documento->setCellValue('A', 'Cuil', 'title');
    $documento->setCellValue('B', 'Apellido', 'title');
    $documento->setCellValue('C', 'Nombre', 'title');
    $documento->setCellValue('D', 'Sexo', 'title');
    $documento->setCellValue('E', 'Estado Civil', 'title');
    $documento->setCellValue('F', 'F. Nacimiento', 'title');
    $documento->setCellValue('G', 'direccion', 'title');
    $documento->setCellValue('H', 'Numero', 'title');
    $documento->setCellValue('I', 'Cod. Postal', 'title');
    $documento->setCellValue('J', 'Empleador', 'title');
    $documento->setCellValue('K', 'F. Ingreso', 'title');
    $documento->setCellValue('L', 'F. Egreso', 'title');
    $documento->setCellValue('M', 'Categoria', 'title');
    $documento->setCellValue('N', 'Valor', 'title');
    $documento->setCellValue('O', 'Concepto', 'title');
    $documento->setCellValue('P', 'Valor', 'title');
    $documento->setCellValue('Q', 'Periodo', 'title');
    $documento->setCellValue('R', 'Dias Periodo', 'title');
    $documento->setCellValue('S', 'Remunerativo', 'title');
    $documento->setCellValue('T', 'No Remunerativo', 'title');

    /** Body */
    foreach ($data as $k => $detail) {

        $codeToNameMapper[$detail['LiquidacionesDetalle']['concepto_codigo']] = $detail['LiquidacionesDetalle']['concepto_nombre'];
        if (empty($total[$detail['LiquidacionesDetalle']['concepto_codigo']])) {
            $totals[$detail['LiquidacionesDetalle']['concepto_codigo']] = $detail['LiquidacionesDetalle']['valor'];
        } else {
            $totals[$detail['LiquidacionesDetalle']['concepto_codigo']] += $detail['LiquidacionesDetalle']['valor'];
        }
        $cuils[$detail['Liquidacion']['trabajador_cuil']] = null;
        
        $documento->setCellValueFromArray(
            array(  $detail['Liquidacion']['trabajador_cuil'],
                    $detail['Liquidacion']['trabajador_apellido'],
                    $detail['Liquidacion']['trabajador_nombre'],
                    $detail['Liquidacion']['Trabajador']['sexo'],
                    $detail['Liquidacion']['Trabajador']['estado_civil'],
                    $detail['Liquidacion']['Trabajador']['nacimiento'],
                    $detail['Liquidacion']['Trabajador']['direccion'],
                    $detail['Liquidacion']['Trabajador']['numero'],
                    $detail['Liquidacion']['Trabajador']['codigo_postal'],
                    $detail['Liquidacion']['empleador_nombre'],
                    $detail['Liquidacion']['relacion_ingreso'],
                    ($detail['Liquidacion']['relacion_egreso'] !== '0000-00-00')?$detail['Liquidacion']['relacion_egreso']:'',
                    $detail['Liquidacion']['convenio_categoria_nombre'],
                    array('value' => $detail['Liquidacion']['convenio_categoria_costo'], 'options' => 'currency'),
                    $detail['LiquidacionesDetalle']['concepto_nombre'],
                    array('value' => $detail['LiquidacionesDetalle']['valor'], 'options' => 'currency'),
                    $detail['Liquidacion']['ano'] . str_pad($detail['Liquidacion']['mes'], 2, '0', STR_PAD_LEFT) . $detail['Liquidacion']['periodo'],
                    '=DAY(DATE(' . $detail['Liquidacion']['ano'] . ', ' . $detail['Liquidacion']['mes'] . '+1, 0))',
                    array('value' => $detail['Liquidacion']['remunerativo'], 'options' => 'currency'),
                    array('value' => $detail['Liquidacion']['no_remunerativo'], 'options' => 'currency')));
    }

    $documento->moveCurrentRow(3);
    $documento->setCellValue('A' . $documento->getCurrentRow() . ':C' . $documento->getCurrentRow(), 'TOTALES', 'title');
    $documento->moveCurrentRow();
    $documento->setCellValue('B', 'Trabajadores:', array('bold', 'right'));
    $documento->setCellValue('C', count($cuils), 'bold');

    foreach ($totals as $conceptCode => $total) {
        $documento->moveCurrentRow();
        $documento->setCellValue('B', $codeToNameMapper[$conceptCode]. ':', array('bold', 'right'));
        $documento->setCellValue('C', $total, 'total');
    }
    $documento->save($fileFormat);
} else {

    $conditions['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => array('soloAAAAMM'));

    $conditions['Condicion.Bar-empleador_id'] = array( 'lov' => array(
            'controller'        => 'empleadores',
            'seleccionMultiple' => true,
            'camposRetorno'     => array('Empleador.cuit', 'Empleador.nombre')));
    
    $conditions['Condicion.Bar-convenio_id'] = array( 'lov' => array(
            'controller'        => 'convenios',
            'seleccionMultiple' => false,
            'camposRetorno'     => array('Convenio.numero', 'Convenio.nombre')));
    
    $options = array('title' => 'Sindicatos');
    echo $this->element('reports/conditions', array('aditionalConditions' => $conditions, 'options' => $options));
}
 
?>