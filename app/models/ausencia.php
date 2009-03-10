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
			foreach ($results as $k => $ausencia) {
				if (isset($ausencia['Ausencia']['id'])) {
					if (isset($ausencia['AusenciasSeguimiento'])) {
						$results[$k]['Ausencia']['dias'] = array_sum(Set::extract('/AusenciasSeguimiento[estado!=Pendiente]/dias', $ausencia));
					}
				}
			}
		} else {
			if (!empty($results[0]['Ausencia'][0])) {
				foreach ($results as $k => $v) {
					foreach ($v as $k1 => $v1) {
						foreach ($v1 as $k2 => $ausencia) {
							if (!isset($ausencia['AusenciasSeguimiento'])) {
								$ausenciasSeguimiento = $this->AusenciasSeguimiento->find('all', 
																array(	'recursive'	=> -1, 
																		'conditions'=> 
																				array(	'AusenciasSeguimiento.ausencia_id'	=> $ausencia['id'],
																						'AusenciasSeguimiento.estado'		=> array('Confirmado', 'Liquidado'))));
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
 * Dada un ralacion y un periodo retorna los dias ausencias que esten confirmadas para el periodo.
 *
 * @param array $relacion Una relacion laboral.
 * @param array $perido Un periodo.
 * @return array Array con la contidad de ausencias justificadas e injustificadas que hubo en el periodo.
 * @access public.
 */
	function getAusencias($relacion, $periodo) {

		$r = $this->find('all', array(
				'contain'			=> array(	'AusenciasMotivo',
												'AusenciasSeguimiento'	=> array(
														'conditions' => array(	'AusenciasSeguimiento.estado'	=> 'Confirmado'))),
				'conditions'		=> array(	'Ausencia.relacion_id' 	=> $relacion['Relacion']['id'],
												array('AND' => array('Ausencia.desde >='	=> $periodo['desde'],
																	'Ausencia.desde <='		=> $periodo['hasta'])))));

		$ausencias['Accidente'] = 0;
		$ausencias['Justificada'] = 0;
		$ausencias['Injustificada'] = 0;
		$conceptos = $auxiliares = array();

		if (!empty($r)) {
			$Concepto = ClassRegistry::init('Concepto');
			
			foreach ($r as $k => $ausencia) {
				$diff = $this->dateDiff($ausencia['Ausencia']['desde'], $periodo['hasta']);

				foreach ($ausencia['AusenciasSeguimiento'] as $seguimiento) {
					if ($seguimiento['estado'] === 'Confirmado') {

						if ($seguimiento['dias'] > $diff['dias']) {

							$ausencias[$ausencia['AusenciasMotivo']['tipo']] += $diff['dias'];
							
							$auxiliar = null;
							$auxiliar['id'] = $seguimiento['id'];
							$auxiliar['estado'] = 'Liquidado';
							$auxiliar['liquidacion_id'] = '##MACRO:liquidacion_id##';
							$auxiliar['dias'] = $diff['dias'];
							$auxiliares[] = array(	'save' 	=> serialize($auxiliar),
													'model' => 'AusenciasSeguimiento');

							/** Debo desdoblar el seguimiento en dos partes:
							*  una ya liquidada (esta) y genero una nueva exactamente igual
							* con los dias que queron pendientes de este */
							$seguimiento['id'] = null;
							$seguimiento['dias'] = $seguimiento['dias'] - $diff['dias'];
							$auxiliares[] = array(	'save' 	=> serialize($seguimiento),
													'model' => 'AusenciasSeguimiento');
							break;
						} else {
							$ausencias[$ausencia['AusenciasMotivo']['tipo']] += $seguimiento['dias'];
							
							$auxiliar = null;
							$auxiliar['id'] = $seguimiento['id'];
							$auxiliar['estado'] = 'Liquidado';
							$auxiliar['liquidacion_id'] = '##MACRO:liquidacion_id##';
							$auxiliares[] = array(	'save' 	=> serialize($auxiliar),
													'model' => 'AusenciasSeguimiento');
						}
					}
				}
			}
			foreach (array_unique(Set::extract('/AusenciasMotivo/tipo', $r)) as $type) {
				$conceptos[] = $Concepto->findConceptos('ConceptoPuntual',
						array(	'relacion' 			=> $relacion,
								'codigoConcepto'	=> 'ausencia_' . strtolower($type)));
			}
		}

		return array('conceptos' 	=> $conceptos,
					 'variables' 	=> array('#ausencias_accidentes' 				=> $ausencias['Accidente'],
											 '#ausencias_justificadas_enfermedad' 	=> $ausencias['Justificada Enfermedad'],
											 '#ausencias_justificadas_licencia' 	=> $ausencias['Justificada Licencia'],
										  	 '#ausencias_injustificadas' 			=> $ausencias['Injustificada']),
					 'auxiliar' 	=> $auxiliares);
	}
	

}
?>