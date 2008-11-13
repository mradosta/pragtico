<?php
/**
 * Este archivo contiene los datos de un fixture para los casos de prueba.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.tests.fixtures
 * @since			Pragtico v 1.0.0
 * @version			$Revision: 54 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2008-10-23 23:14:28 -0300 (Thu, 23 Oct 2008) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase para un fixture para un caso de prueba.
 *
 * @package app.tests
 * @subpackage app.tests.fixtures
 */
class LiquidacionFixture extends CakeTestFixture {


/**
 * El nombre de este Fixture.
 *
 * @var array
 * @access public
 */
    var $name = 'Liquidacion';


/**
 * La definicion de la tabla.
 *
 * @var array
 * @access public
 */
    var $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'primary'),
        'fecha' => array('type' => 'date', 'null' => false),
        'ano' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '4'),
        'mes' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '2'),
        'periodo' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '50'),
        'tipo' => array('type' => 'string', 'null' => false, 'default' => 'Normal', 'length' => '17'),
        'estado' => array('type' => 'string', 'null' => false, 'default' => 'Sin Confirmar', 'length' => '13'),
        'observacion' => array('type' => 'text', 'null' => false, 'default' => ''),
        'relacion_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'relacion_ingreso' => array('type' => 'date', 'null' => false),
        'relacion_horas' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '2'),
        'relacion_basico' => array('type' => 'float', 'null' => false, 'default' => '', 'length' => '10,2'),
        'relacion_area_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'trabajador_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'trabajador_cuil' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '13'),
        'trabajador_nombre' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '150'),
        'trabajador_apellido' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '150'),
        'empleador_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'empleador_cuit' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '13'),
        'empleador_nombre' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '150'),
        'empleador_direccion' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '150'),
        'convenio_categoria_convenio_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'convenio_categoria_nombre' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '150'),
        'convenio_categoria_costo' => array('type' => 'float', 'null' => false, 'default' => '', 'length' => '10,2'),
        'convenio_categoria_jornada' => array('type' => 'string', 'null' => false, 'default' => '', 'length' => '8'),
        'remunerativo' => array('type' => 'float', 'null' => false, 'default' => '', 'length' => '10,2'),
        'no_remunerativo' => array('type' => 'float', 'null' => false, 'default' => '', 'length' => '10,2'),
        'deduccion' => array('type' => 'float', 'null' => false, 'default' => '', 'length' => '10,2'),
        'total_pesos' => array('type' => 'float', 'null' => false, 'default' => '', 'length' => '10,2'),
        'total_beneficios' => array('type' => 'float', 'null' => false, 'default' => '', 'length' => '10,2'),
        'total' => array('type' => 'float', 'null' => false, 'default' => '', 'length' => '10,2'),
        'factura_id' => array('type' => 'integer', 'null' => '1', 'default' => '', 'length' => '11'),
        'created' => array('type' => 'datetime', 'null' => false),
        'modified' => array('type' => 'datetime', 'null' => false),
        'user_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'role_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'group_id' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
        'permissions' => array('type' => 'integer', 'null' => false, 'default' => '', 'length' => '11', 'key' => 'index'),
    );


/**
 * Los registros.
 *
 * @var array
 * @access public
 */
    var $records = array(
        array(
            'id' => '1',
            'fecha' => '2008-10-30',
            'ano' => '2008',
            'mes' => '9',
            'periodo' => '2Q',
            'tipo' => 'Normal',
            'estado' => 'Confirmada',
            'observacion' => '',
            'relacion_id' => '3612',
            'relacion_ingreso' => '2008-02-01',
            'relacion_horas' => '8',
            'relacion_basico' => '0.00',
            'relacion_area_id' => '435',
            'trabajador_id' => '8064',
            'trabajador_cuil' => '20-17625327-5',
            'trabajador_nombre' => 'Jorge',
            'trabajador_apellido' => 'Albarracin',
            'empleador_id' => '237',
            'empleador_cuit' => '20-13193399-2',
            'empleador_nombre' => 'REYNAGA, AURELIO',
            'empleador_direccion' => 'Jujuy y Salta, casa 20',
            'convenio_categoria_convenio_id' => '9',
            'convenio_categoria_nombre' => 'AYUDANTE',
            'convenio_categoria_costo' => '7.54',
            'convenio_categoria_jornada' => 'Por Hora',
            'remunerativo' => '429.78',
            'no_remunerativo' => '30.00',
            'deduccion' => '106.97',
            'total_pesos' => '352.81',
            'total_beneficios' => '0.00',
            'total' => '352.81',
            'factura_id' => '',
            'created' => '2008-10-30 10:55:13',
            'modified' => '2008-10-30 10:55:13',
            'user_id' => '2',
            'role_id' => '3',
            'group_id' => '1',
            'permissions' => '496',
        ),
        array(
            'id' => '2',
            'fecha' => '2008-10-30',
            'ano' => '2008',
            'mes' => '9',
            'periodo' => '2Q',
            'tipo' => 'Normal',
            'estado' => 'Sin Confirmar',
            'observacion' => '',
            'relacion_id' => '3613',
            'relacion_ingreso' => '2008-06-05',
            'relacion_horas' => '8',
            'relacion_basico' => '0.00',
            'relacion_area_id' => '435',
            'trabajador_id' => '8065',
            'trabajador_cuil' => '20-29030685-0',
            'trabajador_nombre' => 'Franco',
            'trabajador_apellido' => 'Albarracin',
            'empleador_id' => '237',
            'empleador_cuit' => '20-13193399-2',
            'empleador_nombre' => 'REYNAGA, AURELIO',
            'empleador_direccion' => 'Jujuy y Salta, casa 20',
            'convenio_categoria_convenio_id' => '9',
            'convenio_categoria_nombre' => 'AYUDANTE',
            'convenio_categoria_costo' => '7.54',
            'convenio_categoria_jornada' => 'Por Hora',
            'remunerativo' => '399.62',
            'no_remunerativo' => '30.00',
            'deduccion' => '101.39',
            'total_pesos' => '328.23',
            'total_beneficios' => '0.00',
            'total' => '328.23',
            'factura_id' => '',
            'created' => '2008-10-30 10:55:14',
            'modified' => '2008-10-30 10:55:14',
            'user_id' => '2',
            'role_id' => '3',
            'group_id' => '1',
            'permissions' => '496',
        ),
        array(
            'id' => '4',
            'fecha' => '2008-11-03',
            'ano' => '2008',
            'mes' => '10',
            'periodo' => 'M',
            'tipo' => 'Normal',
            'estado' => 'Sin Confirmar',
            'observacion' => '',
            'relacion_id' => '7',
            'relacion_ingreso' => '2006-08-01',
            'relacion_horas' => '8',
            'relacion_basico' => '0.00',
            'relacion_area_id' => '1',
            'trabajador_id' => '7137',
            'trabajador_cuil' => '20-27077986-8',
            'trabajador_nombre' => 'Pablo Ricardo',
            'trabajador_apellido' => 'Abrate',
            'empleador_id' => '1',
            'empleador_cuit' => '30-70981114-9',
            'empleador_nombre' => 'R.P.B. S.A.',
            'empleador_direccion' => 'Obispo Oro N° 344',
            'convenio_categoria_convenio_id' => '1',
            'convenio_categoria_nombre' => 'Auxiliar Especializado A',
            'convenio_categoria_costo' => '1348.92',
            'convenio_categoria_jornada' => 'Mensual',
            'remunerativo' => '1375.90',
            'no_remunerativo' => '225.23',
            'deduccion' => '403.69',
            'total_pesos' => '1197.44',
            'total_beneficios' => '0.00',
            'total' => '1197.44',
            'factura_id' => '',
            'created' => '2008-11-03 13:24:38',
            'modified' => '2008-11-03 13:24:38',
            'user_id' => '1',
            'role_id' => '1',
            'group_id' => '0',
            'permissions' => '496',
        ),
    );
}

?>