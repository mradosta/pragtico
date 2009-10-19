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

    $documento->create(array('password' => false, 'orientation' => 'landscape', 'title' => 'Listado de Liquidaciones'));
    $documento->setCellValue('A', 'Centro Costo', array('title' => '25'));
    $documento->setCellValue('B', 'Empelador', array('title' => '30'));
    $documento->setCellValue('C', 'Area', array('title' => '30'));
    $documento->setCellValue('D', 'Trabaj.', array('title' => '10'));
    $documento->setCellValue('E', 'Remuner.', array('title' => '15'));
    $documento->setCellValue('F', 'No Remuner.', array('title' => '15'));
    $documento->setCellValue('G', 'Facturado', array('title' => '15'));
    $documento->setCellValue('H', 'Contrib.', array('title' => '15'));
    $documento->setCellValue('I', 'ART Variable', array('title' => '15'));
    $documento->setCellValue('J', 'ART Fijo', array('title' => '15'));
    $documento->setCellValue('K', 'Resultado', array('title' => '15'));

    /** Body */
    $totalRows = array();
    foreach ($data as $cc => $detail) {

        $documento->moveCurrentRow();
        $documento->setCellValue('A', $cc, 'bold');
        $initialRow = $documento->getCurrentRow() + 1;
        foreach ($detail as $employer => $areas) {

            $documento->moveCurrentRow();
            $documento->setCellValue('B', $employer, 'bold');
            
            foreach ($areas as $area => $values) {

                list($areaName, $groupId) = explode('||', $area);
                
                $documento->setCellValueFromArray(
                    array(  '',
                            '',
                            $areaName,
                            $values['trabajadores'],
                            array('value' => $values['remunerativo'], 'options' => 'currency'),
                            array('value' => $values['no_remunerativo'], 'options' => 'currency'),
                            array('value' => $values['facturado'], 'options' => 'currency'),
                            array('value' => '=E' . ($documento->getCurrentRow() + 1) . '*' . $groupParams[$groupId]['porcentaje_contribuciones'] . '/100', 'options' => 'currency'),
                            array('value' => '=E' . ($documento->getCurrentRow() + 1) . '*' . $groupParams[$groupId]['porcentaje_art_variable'] . '/100', 'options' => 'currency'),
                            array('value' => '=D' . ($documento->getCurrentRow() + 1) . '*' . $groupParams[$groupId]['valor_art_fijo'], 'options' => 'currency'),
                            array('value' => '=G' . ($documento->getCurrentRow() + 1) . '-E' . ($documento->getCurrentRow() + 1) . '-F' . ($documento->getCurrentRow() + 1) . '-H' . ($documento->getCurrentRow() + 1) . '-I' . ($documento->getCurrentRow() + 1) . '-J' . ($documento->getCurrentRow() + 1), 'options' => 'currency')
                    ));
            }
        }
        $documento->moveCurrentRow();
        $documento->setCellValue('D', '=SUM(D' . $initialRow . ':D' . ($documento->getCurrentRow() - 1) . ')', array('bold', 'right'));
        $documento->setCellValue('E', '=SUM(E' . $initialRow . ':E' . ($documento->getCurrentRow() - 1) . ')', 'total');
        $documento->setCellValue('F', '=SUM(F' . $initialRow . ':F' . ($documento->getCurrentRow() - 1) . ')', 'total');
        $documento->setCellValue('G', '=SUM(G' . $initialRow . ':G' . ($documento->getCurrentRow() - 1) . ')', 'total');
        $documento->setCellValue('H', '=SUM(H' . $initialRow . ':H' . ($documento->getCurrentRow() - 1) . ')', 'total');
        $documento->setCellValue('I', '=SUM(I' . $initialRow . ':I' . ($documento->getCurrentRow() - 1) . ')', 'total');
        $documento->setCellValue('J', '=SUM(J' . $initialRow . ':J' . ($documento->getCurrentRow() - 1) . ')', 'total');
        $documento->setCellValue('K', '=SUM(K' . $initialRow . ':K' . ($documento->getCurrentRow() - 1) . ')', 'total');
        $totalRows[$cc] = $documento->getCurrentRow();
    }

    $documento->moveCurrentRow(3);
    $documento->setCellValue('A' . $documento->getCurrentRow() . ':K' . $documento->getCurrentRow(), 'RESUMEN', 'title');
    $initialResumeRow = $documento->getCurrentRow() + 1;
    foreach ($totalRows as $cc => $row) {
        $documento->moveCurrentRow();
        $documento->setCellValue('A', $cc, array('bold'));
        $documento->setCellValue('D', '=D' . $row, array('bold', 'right'));
        $documento->setCellValue('E', '=E' . $row, 'total');
        $documento->setCellValue('F', '=F' . $row, 'total');
        $documento->setCellValue('G', '=G' . $row, 'total');
        $documento->setCellValue('H', '=H' . $row, 'total');
        $documento->setCellValue('I', '=I' . $row, 'total');
        $documento->setCellValue('J', '=J' . $row, 'total');
        $documento->setCellValue('K', '=K' . $row, 'total');
    }
    $documento->moveCurrentRow();
    $documento->setCellValue('D', '=SUM(D' . $initialResumeRow . ':D' . ($documento->getCurrentRow() - 1) . ')', array('bold', 'right'));
    $documento->setCellValue('E', '=SUM(E' . $initialResumeRow . ':E' . ($documento->getCurrentRow() - 1) . ')', 'total');
    $documento->setCellValue('F', '=SUM(F' . $initialResumeRow . ':F' . ($documento->getCurrentRow() - 1) . ')', 'total');
    $documento->setCellValue('G', '=SUM(G' . $initialResumeRow . ':G' . ($documento->getCurrentRow() - 1) . ')', 'total');
    $documento->setCellValue('H', '=SUM(H' . $initialResumeRow . ':H' . ($documento->getCurrentRow() - 1) . ')', 'total');
    $documento->setCellValue('I', '=SUM(I' . $initialResumeRow . ':I' . ($documento->getCurrentRow() - 1) . ')', 'total');
    $documento->setCellValue('J', '=SUM(J' . $initialResumeRow . ':J' . ($documento->getCurrentRow() - 1) . ')', 'total');
    $documento->setCellValue('K', '=SUM(K' . $initialResumeRow . ':K' . ($documento->getCurrentRow() - 1) . ')', 'total');

    $documento->save($fileFormat);
} else {

    $conditions['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => array('soloAAAAMM'));

    $conditions['Condicion.Bar-empleador_id'] = array( 'lov' => array(
            'controller'        => 'empleadores',
            'seleccionMultiple' => true,
            'camposRetorno'     => array('Empleador.cuit', 'Empleador.nombre')));
    
    $options = array('title' => 'Liquidaciones', 'conditions' => array('Bar-grupo_id' => 'multiple'));
    echo $this->element('reports/conditions', array('aditionalConditions' => $conditions, 'options' => $options));
}
 
?>