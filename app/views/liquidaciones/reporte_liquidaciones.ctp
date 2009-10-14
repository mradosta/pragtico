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
    $documento->setCellValue('D', 'Trabajadores', array('title' => '10'));
    $documento->setCellValue('E', 'Remunerativo', array('title' => '15'));
    $documento->setCellValue('F', 'No Remunerativo', array('title' => '15'));
    $documento->setCellValue('G', 'Facturado', array('title' => '15'));
    $documento->setCellValue('H', 'Contribuciones', array('title' => '15'));
    $documento->setCellValue('I', 'ART Variable', array('title' => '15'));
    $documento->setCellValue('J', 'ART Fijo', array('title' => '15'));
    $documento->setCellValue('K', 'Resultado', array('title' => '15'));

    /** Body */
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
                            '=G' . ($documento->getCurrentRow() + 1) . '*' . $groupParams[$groupId]['porcentaje_contribuciones'] . '/100',
                            '=G' . ($documento->getCurrentRow() + 1) . '*' . $groupParams[$groupId]['porcentaje_art_variable'] . '/100',
                            '=D' . ($documento->getCurrentRow() + 1) . '*' . $groupParams[$groupId]['valor_art_fijo'],
                            '=G' . ($documento->getCurrentRow() + 1) . '-F' . ($documento->getCurrentRow() + 1) . '-H' . ($documento->getCurrentRow() + 1) . '-I' . ($documento->getCurrentRow() + 1) . '-J' . ($documento->getCurrentRow() + 1)
                    ));
            }
        }
        $documento->moveCurrentRow();
        $documento->setCellValue('D', '=SUM(D' . $initialRow . ':D' . ($documento->getCurrentRow() - 1) . ')', array('bold', 'right'));
        $documento->setCellValue('E', '=SUM(E' . $initialRow . ':E' . ($documento->getCurrentRow() - 1) . ')', 'total');
        $documento->setCellValue('F', '=SUM(F' . $initialRow . ':F' . ($documento->getCurrentRow() - 1) . ')', 'total');
    }

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