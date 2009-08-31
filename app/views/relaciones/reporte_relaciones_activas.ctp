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
    $documento->setCellValue('A' . $fila, 'Listado de Relaciones Activas', 'bold');
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

    /** Body */
    foreach ($data as $k => $record) {
            $fila++;
            $documento->setCellValueFromArray(
                array(  '0,' . $fila => $record['Trabajador']['cuil'],
                        '1,' . $fila => $record['Trabajador']['apellido'],
                        '2,' . $fila => $record['Trabajador']['nombre'],
                        '3,' . $fila => $record['Trabajador']['sexo'],
                        '4,' . $fila => $record['Trabajador']['estado_civil'],
                        '5,' . $fila => $record['Trabajador']['nacimiento'],
                        '6,' . $fila => $record['Trabajador']['direccion'],
                        '7,' . $fila => $record['Trabajador']['numero'],
                        '8,' . $fila => $record['Trabajador']['codigo_postal'],
                        '9,' . $fila => $record['Empleador']['nombre'],
                        '10,' . $fila => $record['Relacion']['ingreso'],
                        '11,' . $fila => ($record['Relacion']['egreso'] !== '0000-00-00')?$record['Relacion']['egreso']:''));
            
    }
    $documento->save($fileFormat);
} else {

    $conditions['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => array('soloAAAAMM'));
    $conditions['Condicion.Bar-con_fecha_egreso'] = array('label' => 'Fecha Egreso', 'type' => 'radio', 'options' => array('Si' => 'Si', 'No' => 'No'), 'default' => 'No');
    $conditions['Condicion.Bar-con_liquidacion_periodo'] = array('label' => 'Liquidacion en el Periodo', 'type' => 'radio', 'options' => array('Si' => 'Si', 'No' => 'No'), 'default' => 'No');
    
    $options = array('title' => 'Relaciones Activas');
    echo $this->element('reports/conditions', array('aditionalConditions' => $conditions, 'options' => $options));
}
 
?>