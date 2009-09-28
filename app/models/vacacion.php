<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las vacaciones.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.models
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a las vacaciones.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Vacacion extends AppModel {

    var $permissions = array('permissions' => 496, 'group' => 'default', 'role' => 'all');

/**
 * Los modificaciones al comportamiento estandar de app_controller.php
 *
 * @var array
 * @access public
*/
    var $modificadores = array( 'index' => 
            array('contain' => array('Relacion' => array('Empleador', 'Trabajador'))),
                                'edit'  =>
            array('contain' => array('Relacion' => array('Empleador', 'Trabajador'))));

    var $validate = array(
        'desde' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar la fecha de inicio de las vacaciones.'),
			array(
				'rule'      => VALID_DATE,
				'message'	=> 'Debe especificar una fecha valida.')
				
        ),
        'hasta' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar la fecha de fin de las vacaciones.'),
			array(
				'rule'      => VALID_DATE,
				'message'	=> 'Debe especificar una fecha valida.')
				
        ),
        'relacion_id' => array(
			array(
				'rule'      => VALID_NOT_EMPTY,
				'message'	=> 'Debe seleccionar la relacion laboral que toma las vacaciones.')
        )        
	);
	
	var $belongsTo = array(	'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'));



    function getVacaciones($relacion, $periodo) {


        $vacaciones = $this->find('all',
                array('fields'      => array('Vacacion.id', 'Vacacion.dias'),
                      'conditions'  => array(
                            'Vacacion.desde >='    => $periodo['periodo']['desde'],
                            'Vacacion.desde <='    => $periodo['periodo']['hasta'],
                            'Vacacion.estado'       => 'Confirmada',
                            'Vacacion.relacion_id'  => $relacion['Relacion']['id']),
                      'recursive'   => -1));

        $vacacionesLiquidadas = $this->find('all',
                array('fields'      => array('SUM(Vacacion.dias) AS total'),
                      'conditions'  => array(
                            'Vacacion.desde >='    => $periodo['periodo']['ano'] . '-01-01',
                            'Vacacion.desde <='    => $periodo['periodo']['ano'] . '-12-31',
                            'Vacacion.estado'       => 'Liquidada',
                            'Vacacion.relacion_id'  => $relacion['Relacion']['id']),
                      'recursive'   => -1));

        $variables = $conceptos = $auxiliares = array();
        $days = 0;
        if (!empty($vacaciones)) {
            
            foreach ($vacaciones as $vacacion) {
                
                $days += $vacacion['Vacacion']['dias'];
                $auxiliar = null;
                $auxiliar['id'] = $vacacion['Vacacion']['id'];
                $auxiliar['estado'] = 'Liquidada';
                $auxiliar['permissions'] = '288';
                $auxiliar['liquidacion_id'] = '##MACRO:liquidacion_id##';
                $auxiliares[] = array('save'=>serialize($auxiliar), 'model' => 'Vacacion');
            }

            $variables = array(
                '#dias_vacaciones_confirmados'  => $days,
                '#dias_vacaciones_liquidados'   => (empty($vacacionesLiquidadas[0]['Vacacion']['total']))?0:$vacacionesLiquidadas[0]['Vacacion']['total']);

            $conceptos[] = ClassRegistry::init('Concepto')->findConceptos('ConceptoPuntual',
                    array(  'relacion'          => $relacion,
                            'codigoConcepto'    => 'vacaciones'));
            
        }
        return array('conceptos' => $conceptos, 'variables' => $variables, 'auxiliar' => $auxiliares);
    }
    
}
?>