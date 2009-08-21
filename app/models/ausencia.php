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

    var $permissions = array('permissions' => 496, 'group' => 'default', 'role' => 'all');

	var $modificadores = array(	'index'=>array('contain'=>array('Relacion' => array('Empleador', 'Trabajador'),
																'AusenciasMotivo',
																'AusenciasSeguimiento')),
								'add'  	=> array(								
										'valoresDefault'=>array('desde' => array('date' => 'Y-m-d'))),
								'edit'=>array('contain'=>array(	'Relacion'=>array('Empleador','Trabajador'),
																'AusenciasSeguimiento')));
	
	var $validate = array( 
        'relacion_id' => array(
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
				'rule'		=> 'date',
				'message'	=> 'Debe especificar una fecha valida.',
                'allowEmpty'=> false))
	);

	var $belongsTo = array(	'Relacion',
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

		/** Try to find if there are accident absences before the period */
        $sql = "
            select      Ausencia.id
            from        ausencias Ausencia,
                        ausencias_motivos AusenciasMotivo,
                        ausencias_seguimientos AusenciasSeguimiento
            where       Ausencia.id = AusenciasSeguimiento.ausencia_id
            and         AusenciasMotivo.id = Ausencia.ausencia_motivo_id
            and         AusenciasSeguimiento.estado = 'Confirmado'
            and         Ausencia.relacion_id = " . $relacion['Relacion']['id'] . "
            and         Ausencia.desde <= '" . $periodo['hasta'] . "'
            group by    Ausencia.id
        ";
		$ausenciaIds = Set::extract('/Ausencia/id', $this->query($sql));
							
		$r = $this->find('all', array(
            'contain'		=> array(
                'AusenciasMotivo',
				'AusenciasSeguimiento'	=> array(
                    'conditions' => array(
                        'AusenciasSeguimiento.estado' => array('Confirmado', 'Liquidado')))),
            'conditions'	=> array(
                'Ausencia.relacion_id' 	=> $relacion['Relacion']['id'],
                array('OR' => array(
                    array(	'Ausencia.desde <='	=> $periodo['hasta'],
                            'Ausencia.id'		=> $ausenciaIds),
                    array(	'Ausencia.desde >='	=> $periodo['desde'],
                            'Ausencia.desde <='	=> $periodo['hasta']))))));

		$ausencias['Accidente'] = 0;
        $ausencias['Accidente ART'] = 0;
        $ausencias['Acumulado Remunerativo Accidente'] = 0;
        $ausencias['Dias Anteriores Accidente'] = 0;
        $nonWorkingDays['Maternidad'] = $ausencias['Maternidad'] = 0;
		$nonWorkingDays['Enfermedad'] = $ausencias['Enfermedad'] = 0;
		$nonWorkingDays['Licencia'] = $ausencias['Licencia'] = 0;
		$nonWorkingDays['Injustificada'] = $ausencias['Injustificada'] = 0;
        $art = 0;
        $conceptos = $auxiliares = array();

        if (!empty($r)) {
            App::import('Vendor', 'dates', 'pragmatia');
			$Concepto = ClassRegistry::init('Concepto');

			foreach ($r as $k => $ausencia) {

                /*
                if ($ausencia['Ausencia']['desde'] < $periodo['desde']) {
                    $diff = $this->dateDiff($periodo['desde'], $periodo['hasta']);
                } else {
                    $diff = $this->dateDiff($ausencia['Ausencia']['desde'], $periodo['hasta']);
                }
                */
                //$diff = $this->dateDiff($ausencia['Ausencia']['desde'], $periodo['hasta']);
                //d($diff);
                //$ausencia['AusenciasSeguimiento'][] = array('estado' => 'Confirmado', 'dias' => 10, 'id' => 1000);
                if ($ausencia['AusenciasMotivo']['tipo'] === 'Accidente') {
                    if (count(Set::extract('/AusenciasMotivo[tipo=Accidente]/id', $ausencia)) > 1) {
                        /** TODO: Revisar esto y hacerlo de una forma mas elegante */
                        echo 'ERROR, mas de una ausencias de tipo accidente cargadas.';
                        die;
                    }
                    $ausenciasArt = $ausencia;
                }

                $acumulado = 0;
                foreach ($ausencia['AusenciasSeguimiento'] as $seguimiento) {

                    $acumulado += $seguimiento['dias'];
                    if ($seguimiento['estado'] === 'Confirmado') {
                        if (Dates::dateAdd($ausencia['Ausencia']['desde'], $acumulado) > $periodo['hasta']) {

                            $diff = Dates::dateDiff(Dates::dateAdd($ausencia['Ausencia']['desde'], ($acumulado - $seguimiento['dias'])), $periodo['hasta']);
                            $diff['dias']--;
                                    
                            $ausencias[$ausencia['AusenciasMotivo']['tipo']] += $diff['dias'];
                            $auxiliar = null;
                            $auxiliar['id'] = $seguimiento['id'];
                            $auxiliar['estado'] = 'Liquidado';
                            $auxiliar['liquidacion_id'] = '##MACRO:liquidacion_id##';
                            
                            $auxiliar['dias'] = $seguimiento['dias'] - $diff['dias'];
                            $auxiliares[] = array(	'save' 	=> serialize($auxiliar),
                                                    'model' => 'AusenciasSeguimiento');

                            /** Debo desdoblar el seguimiento en dos partes:
                            *  una ya liquidada (esta) y genero una nueva exactamente igual
                            * con los dias que queron pendientes de este */
                            $seguimiento['id'] = null;
                            $seguimiento['dias'] = $seguimiento['dias'] - $auxiliar['dias'];
                            $auxiliares[] = array(	'save' 	=> serialize($seguimiento),
                                                    'model' => 'AusenciasSeguimiento');
                            //$acumulado += $auxiliar['dias'];
                            break;
                        } else {
                            $ausencias[$ausencia['AusenciasMotivo']['tipo']] += $seguimiento['dias'];
                            //$acumulado += $seguimiento['dias'];
                            
                            $auxiliar = null;
                            $auxiliar['id'] = $seguimiento['id'];
                            $auxiliar['estado'] = 'Liquidado';
                            $auxiliar['liquidacion_id'] = '##MACRO:liquidacion_id##';
                            $auxiliares[] = array(	'save' 	=> serialize($auxiliar),
                                                    'model' => 'AusenciasSeguimiento');
                        }
                    }
                }

                if (isset($nonWorkingDays[$ausencia['AusenciasMotivo']['tipo']])) {
                    if ($ausencia['Ausencia']['desde'] < $periodo['desde']) {
                        $nonWorkingDays[$ausencia['AusenciasMotivo']['tipo']] += Dates::getNonWorkingDays($periodo['desde'], Dates::dateAdd($periodo['desde'], $acumulado));
                    } else {
                        $nonWorkingDays[$ausencia['AusenciasMotivo']['tipo']] += Dates::getNonWorkingDays($ausencia['Ausencia']['desde'], Dates::dateAdd($ausencia['Ausencia']['desde'], $acumulado));
                    }
                }
			}

            foreach (array_unique(Set::extract('/AusenciasMotivo/tipo', $r)) as $type) {
                $conceptos[] = $Concepto->findConceptos('ConceptoPuntual',
                        array(  'relacion'          => $relacion,
                                'codigoConcepto'    => 'ausencias_' . strtolower($type)));
            }
                

            if (!empty($ausenciasArt)) {
                if ($ausenciasArt['Ausencia']['desde'] < $periodo['desde']) {
                    $diffTmp = Dates::dateDiff($ausenciasArt['Ausencia']['desde'], $periodo['desde']);
                    $daysBeforePeriod = $diffTmp['dias'] - 1;
                } else {
                    $daysBeforePeriod = 0;
                }

                /*
                $date = $this->dateAdd($ausenciasArt['Ausencia']['desde'], -365);
                $ausencias['Dias Accidentado'] = 365;
                if ($date < $relacion['Relacion']['ingreso']) {
                    $date = $relacion['Relacion']['ingreso'];
                    $diffDividendo = $this->dateDiff($relacion['Relacion']['ingreso'], $ausenciasArt['Ausencia']['desde']);
                    $ausencias['Dias Anteriores Accidente'] = $diffDividendo['dias'];
                }
                list($yearFrom, $monthFrom) = explode('-', $date);
                */

                list($yearTo, $monthTo, $dayTo) = explode('-', $ausenciasArt['Ausencia']['desde']);
                if ($periodo['periodo'] === '1Q') {
                    $fromDate = ($yearTo - 1) . '-' . $monthTo . '-16';
                    if ($monthTo > 1) {
                        $toDate = $yearTo .'-' . str_pad(($monthTo - 1), 2, '0', STR_PAD_LEFT) . '-' . str_pad(Dates::daysInMonth($yearTo, ($monthTo - 1)), 2, '0', STR_PAD_LEFT);
                    } else {
                        $toDate = $yearTo .'-12-31';
                    }
                } elseif ($periodo['periodo'] === '2Q') {
                    if ($monthTo === 12) {
                        $fromDate = ($yearTo - 1) . '-01-01';
                    } else {
                        $fromDate = ($yearTo - 1) . '-' . str_pad(($monthTo + 1), 2, '0', STR_PAD_LEFT) . '-01';
                    }
                    $toDate = $yearTo . '-' . $monthTo . '-15';
                } elseif ($periodo['periodo'] === 'M') {
                    if ($monthTo === 12) {
                        $fromDate = ($yearTo - 1) . '-01-01';
                    } else {
                        $fromDate = ($yearTo - 1) . '-' . str_pad(($monthTo + 1), 2, '0', STR_PAD_LEFT) . '-01';
                    }
                    $toDate = $yearTo . '-' . $monthTo . '-' . str_pad(Dates::daysInMonth($yearTo, $monthTo), 2, '0', STR_PAD_LEFT);
                }

                if ($fromDate < $relacion['Relacion']['ingreso']) {
                    $fromDate  = $relacion['Relacion']['ingreso'];
                }
                $diffDividendo = Dates::dateDiff($fromDate, $toDate);
                $ausencias['Dias Anteriores Accidente'] = $diffDividendo['dias'];


                $data = $this->Relacion->Liquidacion->LiquidacionesDetalle->find('all', array(
                    'checkSecurity' => false,
                    'contain'       => array('Liquidacion'),
                    'group'         => array('Liquidacion.id'),
                    'fields'        => array('sum(LiquidacionesDetalle.valor) as valor'),
                    'conditions'    => array(
                        'Liquidacion.estado'                        => 'Confirmada',
                        'Liquidacion.relacion_id'                   => $relacion['Relacion']['id'],
                        'LiquidacionesDetalle.concepto_tipo'        => 'Remunerativo',
                        'LiquidacionesDetalle.concepto_imprimir !=' => 'No',
                        'CONCAT(Liquidacion.ano, LPAD(Liquidacion.mes, 2, \'0\'), Liquidacion.periodo)' => Dates::getPeriods($fromDate, $toDate))));

                $ausencias['Acumulado Remunerativo Accidente'] = array_sum(Set::extract('/LiquidacionesDetalle/valor', $data));

                /** If more than 10 days, must create an ART accident and an accident */
                if ($daysBeforePeriod + $ausencias['Accidente'] > 10) {
                    if ($daysBeforePeriod > 10) {
                        $ausencias['Accidente ART'] = $ausencias['Accidente'];
                        $ausencias['Accidente'] = 0;
                    } else {
                        $ausencias['Accidente ART'] = $ausencias['Accidente'] - 10;
                        $ausencias['Accidente'] = 10 - $daysBeforePeriod;
                    }

                    $conceptos[] = $Concepto->findConceptos('ConceptoPuntual',
                            array(  'relacion'          => $relacion,
                                    'codigoConcepto'    => 'ausencias_accidente_art'));
                }
            }
        }

/*
        d(array('conceptos'    => $conceptos,
                     'variables'    => array(
        '#ausencias_accidente'                              => $ausencias['Accidente'],
        '#ausencias_maternidad'                             => $ausencias['Maternidad'],
        '#no_laborables_durante_ausencias_maternidad'       => $nonWorkingDays['Maternidad'],
        '#ausencias_accidente_art'                          => $ausencias['Accidente ART'],
        '#acumulado_remunerativo_accidente'                 => $ausencias['Acumulado Remunerativo Accidente'],
        '#dias_anteriores_accidente'                        => $ausencias['Dias Anteriores Accidente'],
        '#ausencias_enfermedad'                             => $ausencias['Enfermedad'],
        '#no_laborables_durante_ausencias_enfermedad'       => $nonWorkingDays['Enfermedad'],
        '#ausencias_licencia'                               => $ausencias['Licencia'],
        '#no_laborables_durante_ausencias_licencia'         => $nonWorkingDays['Licencia'],
        '#ausencias_injustificada'                          => $ausencias['Injustificada'],
        '#no_laborables_durante_ausencias_injustificada'    => $nonWorkingDays['Injustificada']),
                     'auxiliar'     => $auxiliares));
*/
		return array('conceptos' 	=> $conceptos,
					 'variables' 	=> array(
        '#ausencias_accidente'                              => $ausencias['Accidente'],
        '#ausencias_maternidad'                             => $ausencias['Maternidad'],
        '#no_laborables_durante_ausencias_maternidad'       => $nonWorkingDays['Maternidad'],
        '#ausencias_accidente_art'                          => $ausencias['Accidente ART'],
        '#acumulado_remunerativo_accidente'                 => $ausencias['Acumulado Remunerativo Accidente'],
        '#dias_anteriores_accidente'                        => $ausencias['Dias Anteriores Accidente'],
        '#ausencias_enfermedad'                             => $ausencias['Enfermedad'],
        '#no_laborables_durante_ausencias_enfermedad'       => $nonWorkingDays['Enfermedad'],
        '#ausencias_licencia'                               => $ausencias['Licencia'],
        '#no_laborables_durante_ausencias_licencia'         => $nonWorkingDays['Licencia'],
        '#ausencias_injustificada'                          => $ausencias['Injustificada'],
        '#no_laborables_durante_ausencias_injustificada'    => $nonWorkingDays['Injustificada']),
					 'auxiliar' 	=> $auxiliares);
	}
	

    function getAbsencesByType($types, $relacionId, $from, $to) {

        /** Try to find if the are absences before the period */
        $sql = "
            select      Ausencia.id,
                        Ausencia.desde,
                        AusenciasMotivo.tipo,
                        AusenciasSeguimiento.dias
            from        ausencias Ausencia,
                        ausencias_motivos AusenciasMotivo,
                        ausencias_seguimientos AusenciasSeguimiento
            where       Ausencia.id = AusenciasSeguimiento.ausencia_id
            and         AusenciasMotivo.id = Ausencia.ausencia_motivo_id
            and         AusenciasSeguimiento.estado = 'Liquidado'
            and         AusenciasMotivo.tipo in ('" . implode("', '", $types) . "')
            and         Ausencia.relacion_id = " . $relacionId . "
            and         Ausencia.desde <= '" . $to . "'";
        $r = $this->query($sql);

        foreach ($types as $type) {
            $ausencias[$type] = 0;
        }        
        if (!empty($r)) {
            foreach ($r as $k => $ausencia) {
                        
                if ($ausencia['Ausencia']['desde'] < $from) {
                    $diff = $this->dateDiff($from, $ausencia['Ausencia']['desde']);
                } else {
                    $diff = $this->dateDiff($ausencia['Ausencia']['desde'], $to);
                }

                foreach ($ausencia['AusenciasSeguimiento'] as $diasSeguimiento) {
                    if ($diasSeguimiento > $diff['dias']) {
                        $ausencias[$ausencia['Ausencia']['tipo']] += $diff['dias'];
                    }
                    $ausencias[$ausencia['AusenciasMotivo']['tipo']] += $diasSeguimiento;
                }
            }

            $diff = $this->dateDiff($from, $to);
            if ($ausencias[$ausencia['AusenciasMotivo']['tipo']] > $diff['dias']) {
                $ausencias[$ausencia['AusenciasMotivo']['tipo']] = $diff['dias'];
            }
        }
        return $ausencias;
    }

    
}
?>