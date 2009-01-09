<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las ausencias.
 * Las ausencias son cuando un trabajador no se presenta a trabajar a un empleador (una relacion laboral).
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
 * La clase encapsula la logica de acceso a datos asociada a las actividades.
 *
 * Se refiere a cuando un trabajador no se presenta a trabajar para con un empleador (una relacion laboral).
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Ausencia extends AppModel {

	var $modificadores = array(	'index'=>array('contain'=>array('Relacion' => array('Empleador', 'Trabajador'),
																'AusenciasMotivo',
																'AusenciasSeguimiento')),
								'add'  	=> array(								
										'valoresDefault'=>array('desde' => array('date' => 'd/m/Y'))),
								'edit'=>array('contain'=>array(	'Relacion'=>array('Empleador','Trabajador'),
																'AusenciasSeguimiento')));
	
	var $validate = array( 
        'relacion_id__' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar la relacion laboral en la que se produjo la ausencia.')
        ),
        'ausencia_motivo_id' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe seleccionar el motivo de la ausencia.')
        ),
        'desde' => array(
			array(
				'rule'		=> VALID_DATE, 
				'message'	=> 'Debe especificar una fecha valida.'),
			array(
				'rule'		=> VALID_NOT_EMPTY, 
				'message'	=> 'Debe especificar la fecha desde la que se inicio la ausencia.'),
        ),
	);

	var $belongsTo = array(	'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'),
							'Liquidacion' =>
                        array('className'    => 'Liquidacion',
                              'foreignKey'   => 'liquidacion_id'),
							'AusenciasMotivo' =>
                        array('className'    => 'AusenciasMotivo',
                              'foreignKey'   => 'ausencia_motivo_id'));

	var $hasMany = array(	'AusenciasSeguimiento' =>
                        array('className'    => 'AusenciasSeguimiento',
							  'dependent'	 => true,
                              'foreignKey'   => 'ausencia_id'));

	
/**
 * Agrego un nuevo campo el calculo del total de dias que duro la ausencia 
 * (salen de la suma de los dias de seguimiento confirmados).
 * El seguimiento son los dias adicionales que agrega un medico, por ejemplo.
 *
 * @param array $results Los resultados que retorno alguna query.
 * @param boolean $primary Indica si este resultado viene de una query principal o de una query que
 *						   es generada por otra (recursive > 1)
 * @return array array $results Los mismos resultados que ingresaron con el campo dias (campo calculado).
 * @access public
 */	
	function afterFind($results, $primary = false) {
		if ($primary) {
			foreach ($results as $k=>$ausencia) {
				if (isset($ausencia['Ausencia']['id'])) {
					if (isset($ausencia['AusenciasSeguimiento'])) {
						
						$results[$k]['Ausencia']['dias'] = array_sum(Set::extract('/AusenciasSeguimiento[estado=Confirmado]/dias', $ausencia));
					}
				}
			}
		}
		else {
			if (!empty($results[0]['Ausencia'][0])) {
				foreach ($results as $k => $v) {
					foreach ($v as $k1 => $v1) {
						foreach ($v1 as $k2 => $ausencia) {
							if (!isset($ausencia['AusenciasSeguimiento'])) {
								$ausenciasSeguimiento = $this->AusenciasSeguimiento->find('all', 
																array(	'recursive'	=> -1, 
																		'conditions'=> 
																				array(	'AusenciasSeguimiento.ausencia_id'	=> $ausencia['id'],
																						'AusenciasSeguimiento.estado'		=> 'Confirmado')));
							}
							$results[$k]['Ausencia'][$k2]['dias'] = array_sum(Set::extract('/AusenciasSeguimiento/dias', $ausenciasSeguimiento));
						}
					}
				}
			}
		}
		return parent::afterFind($results, $primary);
	}
	


/**
 * Dada un ralacion y un periodo retorna los dias ausencias que esten confirmadas.
 *
 * @param array $relacion Una relacion laboral.
 * @param array $perido Un periodo.
 * @return array Array con la contidad de ausencias justificadas e injustificadas que hubo en el periodo.
 * @access public.
 */
	function getAusencias($relacion, $periodo) {

		$r = $this->find('all',
			array('contain'		=> array(	'AusenciasMotivo',
											'AusenciasSeguimiento'	=> array('conditions' => 
															array(	'AusenciasSeguimiento.estado'	=> 'Confirmado'))),
			'conditions'		=> array(	'Ausencia.relacion_id' 	=> $relacion['Relacion']['id'],
											array('AND' => array('Ausencia.desde >='		=> $periodo['desde'],
																 'Ausencia.desde <='		=> $periodo['hasta'])))));
		
		$return['Justificada'] = 0;
		$return['Injustificada'] = 0;
		if (!empty($r)) {
			foreach ($r as $k => $ausencia) {
				$diff = $this->dateDiff($periodo['hasta'], $ausencia['Ausencia']['desde']);
				if ($ausencia['Ausencia']['dias'] > ($diff['dias'] + 1)) {
					$dias = $diff['dias'] + 1;
				} else {
					$dias = $ausencia['Ausencia']['dias'];
				}
				$return[$ausencia['AusenciasMotivo']['tipo']] += $dias;
			}
		}
		return $return;
	}
	

}
?>