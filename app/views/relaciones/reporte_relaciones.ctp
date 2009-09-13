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

    $documento->create(array('password' => false, 'orientation' => 'portrait', 'title' => 'Relaciones ' . Inflector::pluralize($state)));

    $documento->setCellValue('A', 'Cuil', array('title' => '25'));
    $documento->setCellValue('B', 'Apellido', array('title' => '30'));
    $documento->setCellValue('C', 'Nombre', array('title' => '30'));
    $documento->setCellValue('D', 'Sexo', array('title' => '20'));
    $documento->setCellValue('E', 'Estado Civil', array('title' => '20'));
    $documento->setCellValue('F', 'F. Nacimiento', array('title' => '15'));
    $documento->setCellValue('G', 'Telefono', array('title' => '20'));
    $documento->setCellValue('H', 'Celular', array('title' => '20'));
    $documento->setCellValue('I', 'Direccion', array('title' => '30'));
    $documento->setCellValue('J', 'Numero', array('title' => '10'));
    $documento->setCellValue('K', 'Barrio', array('title' => '30'));
    $documento->setCellValue('L', 'Ciudad', array('title' => '30'));
    $documento->setCellValue('M', 'Localidad', array('title' => '30'));
    $documento->setCellValue('N', 'Provincia', array('title' => '30'));
    $documento->setCellValue('O', 'Cod. Postal', array('title' => '10'));
    $documento->setCellValue('P', 'Cuit', array('title' => '25'));
    $documento->setCellValue('Q', 'Empleador', array('title' => '30'));
    $documento->setCellValue('R', 'Area', array('title' => '35'));
    $documento->setCellValue('S', 'F. Ingreso', array('title' => '15'));
    $documento->setCellValue('T', 'F. Egreso', array('title' => '15'));
    $documento->setCellValue('U', 'Convenio', array('title' => '30'));
    $documento->setCellValue('V', 'Categoria', array('title' => '30'));
    $documento->setCellValue('W', 'Jornada', array('title' => '20'));
    $documento->setCellValue('X', 'Obra Social', array('title' => '70'));
    if ($state === 'Historica') {
        $documento->setCellValue('Y', 'Motivo Egreso', array('title' => '50'));
    }
    

    /** Body */
    foreach ($data as $k => $record) {
        $documento->setCellValueFromArray(
            array(  $record['Trabajador']['cuil'],
                    $record['Trabajador']['apellido'],
                    $record['Trabajador']['nombre'],
                    $record['Trabajador']['sexo'],
                    $record['Trabajador']['estado_civil'],
                    $record['Trabajador']['nacimiento'],
                    $record['Trabajador']['telefono'],
                    $record['Trabajador']['celular'],
                    $record['Trabajador']['direccion'],
                    $record['Trabajador']['numero'],
                    $record['Trabajador']['barrio'],
                    $record['Trabajador']['ciudad'],
                    $record['Trabajador']['Localidad']['nombre'],
                    $record['Trabajador']['Localidad']['Provincia']['nombre'],
                    $record['Trabajador']['codigo_postal'],
                    $record['Empleador']['cuit'],
                    $record['Empleador']['nombre'],
                    $record['Area']['nombre'],
                    $record['Relacion']['ingreso'],
                    ($record['Relacion']['egreso'] !== '0000-00-00')?$record['Relacion']['egreso']:'',
                    $record['ConveniosCategoria']['Convenio']['nombre'],
                    $record['ConveniosCategoria']['nombre'],
                    $record['ConveniosCategoria']['jornada'],
                    $record['Trabajador']['ObrasSocial']['nombre'],
                    (($state === 'Historica')?$record['EgresosMotivo']['motivo']:'')
                 ));
    }

    $documento->save($fileFormat);
} else {

    $conditions['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => array('soloAAAAMM'));
    
    $conditions['Condicion.Bar-empleador_id'] = array( 'lov' => array(
            'controller'        => 'empleadores',
            'seleccionMultiple' => true,
            'camposRetorno'     => array('Empleador.cuit', 'Empleador.nombre')));

    if ($this->params['named']['state'] == 'Activa') {
        $conditions['Condicion.Bar-con_fecha_egreso'] = array('label' => 'Fecha Egreso', 'type' => 'radio', 'options' => array('Si' => 'Si', 'No' => 'No'), 'default' => 'No');
    
        $conditions['Condicion.Bar-con_liquidacion_periodo'] = array('label' => 'Liquidacion en el Periodo', 'type' => 'radio', 'options' => array('Si' => 'Si', 'No' => 'No'), 'default' => 'No');
    }

    $options = array('title' => 'Relaciones ' . Inflector::pluralize($this->params['named']['state']));
    echo $this->element('reports/conditions', array('aditionalConditions' => $conditions, 'options' => $options));
}
 
?>