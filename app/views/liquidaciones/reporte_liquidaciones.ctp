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

    $documento->create(array('password' => false, 'title' => 'Listado de Liquidaciones'));
    $documento->setCellValue('A', 'Centro Costo', array('title' => '30'));
    $documento->setCellValue('B', 'Empelador', array('title' => '30'));
    $documento->setCellValue('C', 'Area', array('title' => '30'));
    $documento->setCellValue('D', 'Trabajadores', array('title' => '15'));
    $documento->setCellValue('E', 'Remunerativo', array('title' => '15'));
    $documento->setCellValue('F', 'No Remunerativo', array('title' => '15'));

    /** Body */
    foreach ($data as $cc => $detail) {

        $documento->moveCurrentRow();
        $documento->setCellValue('A', $cc, 'bold');
        $initialRow = $documento->getCurrentRow() + 1;
        foreach ($detail as $employer => $areas) {

            $documento->setCellValue('B', $employer, 'bold');
            
            foreach ($areas as $area => $values) {
                
                $documento->setCellValueFromArray(
                    array(  '',
                            '',
                            $area,
                            $values['trabajadores'],
                            array('value' => $values['remunerativo'], 'options' => 'currency'),
                            array('value' => $values['no_remunerativo'], 'options' => 'currency')));
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