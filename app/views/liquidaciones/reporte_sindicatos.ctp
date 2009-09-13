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

    $documento->create(array('password' => false, 'title' => 'Listado de Aportes Sindicales'));
    $documento->setCellValue('A', 'Cuil', array('title' => '25'));
    $documento->setCellValue('B', 'Apellido', array('title' => '30'));
    $documento->setCellValue('C', 'Nombre', array('title' => '30'));
    $documento->setCellValue('D', 'Sexo', array('title' => '20'));
    $documento->setCellValue('E', 'Estado Civil', array('title' => '20'));
    $documento->setCellValue('F', 'F. Nacimiento', array('title' => '15'));
    $documento->setCellValue('G', 'Direccion', array('title' => '30'));
    $documento->setCellValue('H', 'Numero', array('title' => '10'));
    $documento->setCellValue('I', 'Cod. Postal', array('title' => '10'));
    $documento->setCellValue('J', 'Area', array('title' => '35'));
    $documento->setCellValue('K', 'Empleador', array('title' => '30'));
    $documento->setCellValue('L', 'F. Ingreso', array('title' => '15'));
    $documento->setCellValue('M', 'F. Egreso', array('title' => '15'));
    $documento->setCellValue('N', 'Categoria', array('title' => '30'));
    $documento->setCellValue('O', 'Valor', array('title' => '15'));
    $documento->setCellValue('P', 'Concepto', array('title' => '50'));
    $documento->setCellValue('Q', 'Valor', array('title' => '15'));
    $documento->setCellValue('R', 'Periodo', array('title' => '15'));
    $documento->setCellValue('S', 'Dias Periodo', array('title' => '10'));
    $documento->setCellValue('T', 'Remunerativo', array('title' => '20'));
    $documento->setCellValue('U', 'No Remunerativo', array('title' => '20'));

    /** Body */
    foreach ($data as $k => $detail) {

        $codeToNameMapper[$detail['LiquidacionesDetalle']['concepto_codigo']] = $detail['LiquidacionesDetalle']['concepto_nombre'];
        if (empty($totals[$detail['LiquidacionesDetalle']['concepto_codigo']])) {
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
                    $detail['Liquidacion']['Area']['nombre'],
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

    $t['Trabajadores'] = array(count($cuils) => array('bold', 'right'));
    foreach ($totals as $conceptCode => $total) {
        $t[$codeToNameMapper[$conceptCode]] = $total;
    }
    $documento->setTotals($t);
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