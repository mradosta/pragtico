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
    $documento->setCellValue('B', 'Empelador', array('title' => '45'));
    $documento->setCellValue('C', 'Trabajadores', array('title' => '20'));
    $documento->setCellValue('D', 'Remunerativo', array('title' => '20'));
    $documento->setCellValue('E', 'No Remunerativo', array('title' => '20'));

    /** Body */
    foreach ($data as $cc => $detail) {

        $documento->moveCurrentRow();
        $documento->setCellValue('A', $cc, 'bold');
        $initialRow = $documento->getCurrentRow() + 1;
        foreach ($detail as $employer => $values) {

            $documento->setCellValueFromArray(
                array(  '',
                        $employer,
                        $values['trabajadores'],
                        array('value' => $values['remunerativo'], 'options' => 'currency'),
                        array('value' => $values['no_remunerativo'], 'options' => 'currency')));
        }
        $documento->moveCurrentRow();
        $documento->setCellValue('D', '=SUM(D' . $initialRow . ':D' . ($documento->getCurrentRow() - 1) . ')', 'total');
        $documento->setCellValue('E', '=SUM(E' . $initialRow . ':E' . ($documento->getCurrentRow() - 1) . ')', 'total');
    }

/*    $t['Trabajadores'] = array(count($cuils) => array('bold', 'right'));
    foreach ($totals as $conceptCode => $total) {
        $t[$codeToNameMapper[$conceptCode]] = $total;
    }
    $documento->setTotals($t);*/
    $documento->save($fileFormat);
} else {

    $conditions['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => array('soloAAAAMM'));

    $conditions['Condicion.Bar-empleador_id'] = array( 'lov' => array(
            'controller'        => 'empleadores',
            'seleccionMultiple' => true,
            'camposRetorno'     => array('Empleador.cuit', 'Empleador.nombre')));
    
    $conditions['Condicion.Bar-relacion_id'] = array( 'lov' => array(
            'controller'        => 'relaciones',
            'seleccionMultiple' => true,
            'camposRetorno'     => array('Empleador.nombre', 'Trabajador.apellido', 'Trabajador.nombre')));
    
    $options = array('title' => 'Liquidaciones', 'conditions' => array('Bar-grupo_id' => 'multiple'));
    echo $this->element('reports/conditions', array('aditionalConditions' => $conditions, 'options' => $options));
}
 
?>