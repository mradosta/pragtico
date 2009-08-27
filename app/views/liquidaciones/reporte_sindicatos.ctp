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

    $fila = 1;
    $documento->setCellValue('E' . $fila, date('Y-m-d'), 'bold');
    $fila+=2;
    $documento->setCellValue('A' . $fila, 'Listado de Aportes Sindicales', 'bold');
    $fila++;
    //$documento->setCellValue('A' . $fila, 'Empresa: ' . $conditions['Liquidacion-empleador_id__'], 'bold');
    $fila++;
    //$documento->setCellValue('A' . $fila, 'Periodo: ' . $conditions['Liquidacion-periodo_largo'], 'bold');


    $fila = 7;
    $total = 0;
    $flag = null;
    $inicio = 0;
    $flagCoeficiente = null;    


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

    $documento->setCellValue('A' . $fila, 'Cuil', 'title');
    $documento->setCellValue('B' . $fila, 'Apellido', 'title');
    $documento->setCellValue('C' . $fila, 'Nombre', 'title');
    $documento->setCellValue('D' . $fila, 'Sexo', 'title');
    $documento->setCellValue('E' . $fila, 'Estado Civil', 'title');
    $documento->setCellValue('F' . $fila, 'F. Nacimiento', 'title');
    $documento->setCellValue('G' . $fila, 'direccion', 'title');
    $documento->setCellValue('H' . $fila, 'Numero', 'title');
    $documento->setCellValue('I' . $fila, 'Cod. Postal', 'title');
    $documento->setCellValue('J' . $fila, 'Empleador', 'title');
    $documento->setCellValue('K' . $fila, 'F. Ingreso', 'title');
    $documento->setCellValue('L' . $fila, 'F. Egreso', 'title');
    $documento->setCellValue('M' . $fila, 'Categoria', 'title');
    $documento->setCellValue('N' . $fila, 'Valor', 'title');
    $documento->setCellValue('O' . $fila, 'Periodo', 'title');
    $documento->setCellValue('P' . $fila, 'Dias Periodo', 'title');
    $documento->setCellValue('Q' . $fila, 'Remunerativo', 'title');
    $documento->setCellValue('R' . $fila, 'No Remunerativo', 'title');
            
    /** Body */
    $receiptId = $data[0]['LiquidacionesDetalle']['liquidacion_id'];
    $totalRemunerativo = $totalNoRemunerativo = 0;
    foreach ($data as $k => $detail) {

        if ($detail['LiquidacionesDetalle']['concepto_tipo'] === 'Remunerativo') {
            $totalRemunerativo += $detail['LiquidacionesDetalle']['valor'];
        } elseif ($detail['LiquidacionesDetalle']['concepto_tipo'] === 'No Remunerativo') {
            $totalNoRemunerativo += $detail['LiquidacionesDetalle']['valor'];
        }

        if ($receiptId != $detail['LiquidacionesDetalle']['liquidacion_id']) {
            $fila++;
            $documento->setCellValueFromArray(
                array(  '0,' . $fila => $detail['Liquidacion']['trabajador_cuil'],
                        '1,' . $fila => $detail['Liquidacion']['trabajador_apellido'],
                        '2,' . $fila => $detail['Liquidacion']['trabajador_nombre'],
                        '3,' . $fila => $detail['Liquidacion']['Trabajador']['sexo'],
                        '4,' . $fila => $detail['Liquidacion']['Trabajador']['estado_civil'],
                        '5,' . $fila => $detail['Liquidacion']['Trabajador']['nacimiento'],
                        '6,' . $fila => $detail['Liquidacion']['Trabajador']['direccion'],
                        '7,' . $fila => $detail['Liquidacion']['Trabajador']['numero'],
                        '8,' . $fila => $detail['Liquidacion']['Trabajador']['codigo_postal'],
                        '9,' . $fila => $detail['Liquidacion']['empleador_nombre'],
                        '10,' . $fila => $detail['Liquidacion']['relacion_ingreso'],
                        '11,' . $fila => ($detail['Liquidacion']['relacion_egreso'] !== '0000-00-00')?$detail['Liquidacion']['relacion_egreso']:'',
                        '12,' . $fila => $detail['Liquidacion']['convenio_categoria_nombre'],
                        '13,' . $fila => $detail['Liquidacion']['convenio_categoria_costo'],
                        '14,' . $fila => $detail['Liquidacion']['ano'] . str_pad($detail['Liquidacion']['mes'], 2, '0', STR_PAD_LEFT) . $detail['Liquidacion']['periodo'],
                        '15,' . $fila => '=DAY(DATE(' . $detail['Liquidacion']['ano'] . ', ' . $detail['Liquidacion']['mes'] . '+1, 0))',
                        '16,' . $fila => array('value' => $totalRemunerativo, 'options' => 'currency'),
                        '17,' . $fila => array('value' => $totalNoRemunerativo, 'options' => 'currency')));
            $totalRemunerativo = $totalNoRemunerativo = 0;
            $receiptId = $detail['LiquidacionesDetalle']['liquidacion_id'];
        }
    }
    $fileFormat = 'Excel5';
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