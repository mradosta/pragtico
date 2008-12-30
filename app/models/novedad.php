<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las novedades.
 * Una novedad es un ingreso de datos al sistema no confirmado aun.
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
 * La clase encapsula la logica de acceso a datos asociada a las novedades.
 * Una novedad es un ingreso de datos al sistema no confirmado aun.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Novedad extends AppModel {

/**
 * El orden por defecto.
 *
 * @var array
 * @access public
*/
	var $order = array('periodo', 'tipo');


/**
 * Los modificaciones al comportamiento estandar de app_controller.php
 *
 * @var array
 * @access public
*/
	var $modificadores = array(	'index'	=> 
			array('contain'	=> array('Relacion' => array('Empleador', 'Trabajador'))),
								'edit'	=>
			array('contain'	=> array('Relacion'	=> array('Empleador', 'Trabajador'))));

/**
 * Los permisos por defecto con los que se guardaran los datos de este model.
 *
 * @var integer
 * @access protected
 */
	//protected $__permissions = 122;
	
/**
 * Las opciones validadas de formatos de planillas que se podran generar e importar.
 *
 * @var array
 * @access public
 */
	var $opciones = array('formato'=>array('Excel5' => 'Excel', 'Excel2007' => 'Excel 2007'));
	
	var $belongsTo = array(	'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'));


/**
 * Based on novelty type, mark the existance of a previewsly informed novelty.
 */
	function afterFind($results, $primary = false) {
		if ($primary) {
			foreach ($results as $k => $v) {
				$existe = false;
				if (isset($v['Novedad']['tipo'])) {
					if ($v['Novedad']['tipo'] === 'Concepto') {
						$periodo = $this->format($v['Novedad']['periodo'], 'periodo');
						$conditions = array('RelacionesConcepto.concepto_id' =>	array_shift(explode(':', $v['Novedad']['subtipo'])),
											'RelacionesConcepto.relacion_id' =>	$v['Novedad']['relacion_id'],
											array('OR'	=> array(	'RelacionesConcepto.desde' => '0000-00-00',
																	'RelacionesConcepto.desde <=' => $periodo['desde'])),
											array('OR'	=> array(	'RelacionesConcepto.hasta' => '0000-00-00',
																	'RelacionesConcepto.hasta >=' => $periodo['hasta']))
										   );
								
						$existe = $this->Relacion->RelacionesConcepto->find('first', array(
													'recursive'		=> -1,
													'conditions' 	=> $conditions));
						
						/**
						* En caso de que exista, si dentro de la formula tiene la variable, 
						* lo marco como que no existe porque luego la reemplazare.
						*/
						if (!empty($existe['RelacionesConcepto']['formula']) && strpos($existe['RelacionesConcepto']['formula'], '#valor_novedad') !== false) {
							$existe = false;
						}
					} elseif ($v['Novedad']['tipo'] === 'Horas') {
						$Hora = ClassRegistry::init('Hora');
						$find = null;
						$find['Hora.tipo'] = $v['Novedad']['subtipo'];
						$find['Hora.periodo'] = $v['Novedad']['periodo'];
						$find['Hora.relacion_id'] = $v['Novedad']['relacion_id'];
						$existe = $Hora->find('first', array(	'recursive' 	=> -1, 
											 					'checkSecurity'	=> false,
											 					'conditions' 	=> $find));
					} elseif ($v['Novedad']['tipo'] === 'Ausencias') {
						$Ausencia = ClassRegistry::init('Ausencia');
						$find = null;
						$periodo = $this->format($v['Novedad']['periodo'], 'periodo');
						$find['Ausencia.desde >='] = $periodo['desde'];
						$find['Ausencia.desde <='] = $periodo['hasta'];
						//$find['Ausencia.ausencia_motivo_id'] = array_shift(explode(':', $v['Novedad']['subtipo']));
						$find['Ausencia.relacion_id'] = $v['Novedad']['relacion_id'];
						$existe = $Ausencia->find('first', array(	'recursive' 	=> -1, 
											 						'checkSecurity'	=> false,
											 						'conditions' 	=> $find));
					} elseif ($v['Novedad']['tipo'] === 'Vales') {
						$Descuento = ClassRegistry::init('Descuento');
						$find = null;
						$periodo = $this->format($v['Novedad']['periodo'], 'periodo');
						$find['Descuento.tipo'] = 'Vale';
						$find['Descuento.desde >='] = $periodo['desde'];
						$find['Descuento.desde <='] = $periodo['hasta'];
						$find['Descuento.relacion_id'] = $v['Novedad']['relacion_id'];
						$existe = $Descuento->find('first', array(	'recursive' 	=> -1, 
											 						'checkSecurity'	=> false,
											 						'conditions' 	=> $find));
					}
				}
				$results[$k]['Novedad']['existe'] = false;
				if (!empty($existe)) {
					$results[$k]['Novedad']['existe'] = true;
				}
			}
		}
		return parent::afterFind($results, $primary);
	}
	
/**
 * Graba las novedades provenientes desde la planilla.
 * Maneja transacciones.
 *
 * @param array $datos Los datos a grabar.
 * @param array $periodo El periodo al cual se asignaran los datos de las novedades.
 * @return boolean True si fue posible guardar las novedades ingresadas, false en otro caso
 * @access public 
 */
 	function grabar($datos, $periodo) {
		if (!preg_match(VALID_PERIODO, $periodo) || empty($datos) || !is_array($datos)) {
			return false;
		}
		
		$predefinidos = $this->getIngresosPosibles('predefinidos');
		
		foreach ($datos as $relacion_id => $data) {
			foreach ($data as $tipo => $registros) {
				foreach ($registros as $subTipo => $registro) {
				
					$save = null;
					$save['Novedad']['id'] = null;
					$save['Novedad']['periodo'] = $periodo;
					$save['Novedad']['relacion_id'] = $relacion_id;
					
					if (!in_array($tipo, $predefinidos)) {
						/**
						* Busco el id del concepto correspondiente al nombre que importe desde la planilla.
						*/
						$this->Relacion->RelacionesConcepto->Concepto->recursive = -1;
						$concepto = $this->Relacion->RelacionesConcepto->Concepto->findByNombre($tipo);
						if (empty($concepto['Concepto']['id'])) {
							continue;
						}
						//$save['Novedad']['data'] = '#valor_planilla:' . $registro;
						$save['Novedad']['data'] = $registro;
						$save['Novedad']['tipo'] = 'Concepto';
						$save['Novedad']['subtipo'] = $concepto['Concepto']['id'] . ':' . $tipo;
					} else {
						$save['Novedad']['concepto_id'] = null;
						$save['Novedad']['tipo'] = $tipo;
						$save['Novedad']['subtipo'] = $subTipo;
						$save['Novedad']['data'] = $registro;
						
						/**
						* Le doy un tratamiento especial a las ausencias, para ya dejar el motivo
						* especificado.
						*/
						if ($tipo === 'Ausencias') {
							if ($subTipo === 'Dias') {
								continue;
							} else {
								$save['Novedad']['data'] = $registros['Dias'];
								
								if (empty($registro)) {
									$save['Novedad']['subtipo'] = 1;
								} else {
									$this->Relacion->Ausencia->AusenciasMotivo->recursive = -1;
									$motivo = $this->Relacion->Ausencia->AusenciasMotivo->findByMotivo($registro);
									if (!empty($motivo)) {
										$save['Novedad']['subtipo'] = $motivo['AusenciasMotivo']['id'] . ':' . $registro;
									} else {
										$save['Novedad']['subtipo'] = '1:Justificada (sin especificar)';
									}
								}
							}
						}
					}
					
					$saveAll[] = $save;
				}
			}
		}
		return $this->saveAll($saveAll);
	}


/**
 * Distribuye las novedades en las diferecntes tablas (horas, ausencias, descuentos) o crea los conceptos necesarios.
 *
 * @param array $ids Los ids de las novedades a distribuir en cada tabla.
 * @return mixed Cantidad de novedades distribuidas. False en caso de error o que no hayn podido confirmarse todos los ids.
 * @access public
 */
	function distribuir($ids) {
		$novedades = $this->find('all', 
				array('conditions' 	=> array('Novedad.id' => $ids), 
					  'recursive'	=> -1));
		$c = $i = $ii = 0;
		
		foreach ($novedades as $novedad) {
			$periodo = $this->format($novedad['Novedad']['periodo'], 'periodo');
			switch ($novedad['Novedad']['tipo']) {
				case 'Horas':
					$saves[$i]['Hora']['id'] = null;
					$saves[$i]['Hora']['tipo'] = $novedad['Novedad']['subtipo'];
					$saves[$i]['Hora']['cantidad'] = $novedad['Novedad']['data'];
					$saves[$i]['Hora']['estado'] = 'Confirmada';
					$saves[$i]['Hora']['relacion_id'] = $novedad['Novedad']['relacion_id'];
					$saves[$i]['Hora']['periodo'] = $periodo['periodoCompleto'];
					$saves[$i]['Hora']['observacion'] = 'Ingresado desde planilla';
				break;
				case 'Ausencias':
					$saves[$i]['Ausencia']['id'] = null;
					$saves[$i]['Ausencia']['desde'] = $this->format($periodo['desde'], 'date');
					$saves[$i]['Ausencia']['ausencia_motivo_id'] = array_shift(explode(':', $novedad['Novedad']['subtipo']));
					$saves[$i]['Ausencia']['relacion_id'] = $novedad['Novedad']['relacion_id'];
					$saves[$i]['AusenciasSeguimiento'][$ii]['dias'] = $novedad['Novedad']['data'];
					$saves[$i]['AusenciasSeguimiento'][$ii]['observacion'] = 'Ingresado desde planilla';
					$saves[$i]['AusenciasSeguimiento'][$ii]['estado'] = 'Confirmado';
					$ii++;
				break;
				case 'Vales':
					$saves[$i]['Descuento']['id'] = null;
					$saves[$i]['Descuento']['alta'] = $this->format($periodo['desde'], 'date');
					$saves[$i]['Descuento']['desde'] = $saves[$i]['Descuento']['alta'];
					$saves[$i]['Descuento']['relacion_id'] = $novedad['Novedad']['relacion_id'];
					$saves[$i]['Descuento']['monto'] = $novedad['Novedad']['data'];
					$saves[$i]['Descuento']['tipo'] = 'Vale';
					$saves[$i]['Descuento']['descontar'] = array('1');
					$saves[$i]['Descuento']['concurrencia'] = 'Permite superponer';
					$saves[$i]['Descuento']['estado'] = 'Activo';
					$saves[$i]['Descuento']['observacion'] = 'Ingresado desde planilla';
				break;
				case 'Concepto':
					$saves[$i]['RelacionesConcepto']['desde'] = $this->format($periodo['desde'], 'date');
					$saves[$i]['RelacionesConcepto']['hasta'] = $this->format($periodo['hasta'], 'date');
					$saves[$i]['RelacionesConcepto']['relacion_id'] = $novedad['Novedad']['relacion_id'];
					$saves[$i]['RelacionesConcepto']['concepto_id'] = array_shift(explode(':', $novedad['Novedad']['subtipo']));
					$saves[$i]['RelacionesConcepto']['observacion'] = 'Ingresado desde planilla';
					
					$find = $this->Relacion->RelacionesConcepto->find('first', 
							array(	'recursive' 			=> -1,
									'conditions' => array(
										'RelacionesConcepto.relacion_id' 	=> $saves[$i]['RelacionesConcepto']['relacion_id'],
		   								'RelacionesConcepto.concepto_id'	=> $saves[$i]['RelacionesConcepto']['concepto_id'])
									));
					
					if (empty($find)) {
						$saves[$i]['RelacionesConcepto']['id'] = null;
						$formula = '=' . $novedad['Novedad']['data'];
					}
					if (empty($find['RelacionesConcepto']['formula'])) {
						$saves[$i]['RelacionesConcepto']['id'] = $find['RelacionesConcepto']['id'];
						$formula = '=' . $novedad['Novedad']['data'];
					}
					else {
						$saves[$i]['RelacionesConcepto']['id'] = $find['RelacionesConcepto']['id'];
						$formula = preg_replace('/(.*)(#valor_novedad):?([0-9]+|)(.*)/', '${1}${2}:' . $novedad['Novedad']['data'] .'$4', $find['RelacionesConcepto']['formula']);
					}
					
					$saves[$i]['RelacionesConcepto']['formula'] = $formula;
				break;
			}
			$i++;
		}
		
		$this->begin();
		foreach ($saves as $save) {
			$keys = array_keys($save);
			if ($this->Relacion->{$keys[0]}->save($save)	) {
				$c++;
			}
		}
		
		if ($i === $c) {
			$this->deleteAll(array('Novedad.id'=>$ids), false, false, false);
			$this->commit();
			return $i;
		}
		else {
			$this->rollback();
			return false;
		}
	}


/**
 * Obtiene un listado de los posibles campos que puedo ingresar por novedades.
 *
 * @return Array con los posibles campos que debo ingresar.
 * @access public.
 */
	function getIngresosPosibles($tipo = 'todos') {
		$predefinidos[] = 'Horas';
		$predefinidos[] = 'Ausencias';
		$predefinidos[] = 'Vales';
		if ($tipo === 'todos') {
			$Concepto = new Concepto();
			$conceptos = $Concepto->find('all', array('conditions'=>array('Concepto.novedad' => 'Si'), 'recursive'=>-1));
			return array_merge($predefinidos, Set::extract('/Concepto/nombre', $conceptos));
		}
		elseif ($tipo === 'predefinidos') {
			return $predefinidos;	
		}
	}

}
?>