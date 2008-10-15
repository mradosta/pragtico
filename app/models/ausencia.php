<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las ausencias.
 * Las ausencias son cuando un trabajador no se presenta a trabajar a un empleador (una relacion laboral).
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.models
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a las actividades.
 *
 * Se refiere a cuando un trabajador no se presenta a trabajar para con un empleador (una relacion laboral).
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Ausencia extends AppModel {

	var $modificadores = array(	"index"=>array("contain"=>array("Relacion.Empleador",
																"Relacion.Trabajador",
																"AusenciasMotivo",
																"AusenciasSeguimiento")),
								"edit"=>array("contain"=>array(	"Relacion.Empleador",
																"Relacion.Trabajador",
																"AusenciasSeguimiento")));
	var $validate = array( 
        'relacion_id__' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe especificar la relacion laboral en la que se produjo la ausencia.')
        ),
        'ausencia_motivo_id' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe seleccionar el motivo de la ausencia.')
        )        
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
                              'foreignKey'   => 'ausencia_id'));


/**
 * Agrego un nuevo campo el calculo del total de dias que duro la ausencia (salen de la suma de los dias de seguimiento).
 * El seguimiento son los dias adicionales que agrega un medico, por ejemplo.
 *
 * @param array $results Los resultados que retorno alguna query.
 * @param boolean $primary Indica si este resultado viene de una query principal o de una query que
 *						   es generada por otra (recursive > 1)
 * @return array array $results Los mismos resultados que ingresaron con el campo dias (campo calculado).
 * @access public
 */	
	function afterFind($results, $primary = false) {
		if(!empty($results[0]['Ausencia'][0])) {
			foreach($results as $k=>$ausencias) {
				foreach($ausencias as $k1=>$ausencia) {
					foreach($ausencia as $k2=>$v2) {
						$ausenciasSeguimiento = $this->AusenciasSeguimiento->find("all", array("recursive"=>-1, "conditions"=>array("AusenciasSeguimiento.ausencia_id"=>$v2['id'], "AusenciasSeguimiento.estado"=>"Confirmado")));
						$results[$k][$k1][$k2] = am($results[$k][$k1][$k2], $this->__getDaysAndDates($ausenciasSeguimiento));
					}
				}
			}
		}
		elseif($primary) {
			foreach($results as $k=>$ausencia) {
				if(isset($ausencia['Ausencia']['id'])) {
					$ausenciasSeguimiento = $this->AusenciasSeguimiento->find("all", array("recursive"=>-1, "conditions"=>array("AusenciasSeguimiento.ausencia_id"=>$ausencia['Ausencia']['id'], "AusenciasSeguimiento.estado"=>"Confirmado")));
					$results[$k]['Ausencia'] = am($results[$k]['Ausencia'], $this->__getDaysAndDates($ausenciasSeguimiento));
				}
			}
		}
		return parent::afterFind($results, $primary);
	}


/**
 * Dado un array (results, proveniente de un find), agrega los dias confirmados de ausencias
 * y las fechas desde y hasta en las que se produjo la ausencia.
 */
	function __getDaysAndDates($results) {
		if(!empty($results)) {
			$total = 0;
			$fechaMin = "2100-01-01";
			$fechaMax = null;
			foreach($results as $k=>$result) {
				if(!empty($result['AusenciasSeguimiento'])) {
					$total += $result['AusenciasSeguimiento']['dias'];
					if($result['AusenciasSeguimiento']['desde'] < $fechaMin) {
						$fechaMin = $result['AusenciasSeguimiento']['desde'];
					}
					if($result['AusenciasSeguimiento']['hasta'] > $fechaMax) {
						$fechaMax = $result['AusenciasSeguimiento']['hasta'];
					}
				}
			}
			if($fechaMin == "2100-01-01") {
				$fechaMin = null;
			}
			return array("dias" => $total, "desde" => $fechaMin, "hasta" => $fechaMax);
		}
		else {
			return array("dias" => 0, "desde" => "0000-00-00", "hasta" => "0000-00-00");
		}
	}


/**
 * Dada un ralacion y un periodo retorna los dias ausencias que esten pendientes de liquidar.
 *
 * @return array vacio si no hay aucencias.
 */
	function buscarAusencia($opciones, $relacion) {

		$conditions = array(
			"conditions"=>	array(	"Ausencia.relacion_id" 	=> $relacion['Relacion']['id'],
									"Hora.liquidacion_id" 	=> null,
									"Hora.periodo" 			=> $opciones['periodo'],
									"Hora.estado"			=> "Pendiente"),
			"fields"	=>	array(	"Hora.tipo", "sum(Hora.cantidad) as total"),
			"recursive"	=>	-1,
			"group"		=> 	array("Hora.tipo")
		);
	}
	
/**
 * Dado un array (results, proveniente de un find), agrega los dias confirmados de ausencias
 * y las fechas desde y hasta en las que se produjo la ausencia.
 */
	function getDaysAndDates_deprecated($results, $primary = false) {
		foreach($results as $k=>$result) {
			$total = 0;
			$fechaMin = "2100-01-01";
			$fechaMax = null;
			if(!empty($result['AusenciasSeguimiento'])) {
				foreach($result['AusenciasSeguimiento'] as $k1=>$r) {
					if($r['estado'] == "Confirmado") {
						$total += $r['dias'];
						if($r['desde'] < $fechaMin) {
							$fechaMin = $r['desde'];
						}
						if($r['hasta'] > $fechaMax) {
							$fechaMax = $r['hasta'];
						}
					}
				}
			}
			if($fechaMin == "2100-01-01") {
				$fechaMin = null;
			}
			if($primary === true) {
				$results[$k]['Ausencia']['dias'] = $total;
				$results[$k]['Ausencia']['desde'] = $fechaMin;
				$results[$k]['Ausencia']['hasta'] = $fechaMax;
			}
			else {
				$results[$k]['dias'] = $total;
				$results[$k]['desde'] = $fechaMin;
				$results[$k]['hasta'] = $fechaMax;
			}
		}
		return $results;
	}

}
?>
