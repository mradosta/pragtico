<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a la liquidacion de sueldos.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.controllers
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a la liquidacion de sueldos.
 *
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class LiquidacionesController extends AppController {


	var $components = array('Formulador');
	var $helpers = array('Documento');
	

	function libro_sueldos() {
		if (!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] === 'generar') {
			if (empty($this->data['Condicion']['Liquidacion-empleador_id'])
				&& empty($this->data['Condicion']['Liquidacion-grupo_id'])) {
				$this->Session->setFlash('Debe seleccionar un por lo menos un Empleador o un Grupo y el Empleador.', 'error');
			} elseif (empty($this->data['Condicion']['Liquidacion-periodo']) || $this->Util->format($this->data['Condicion']['Liquidacion-periodo'], 'periodo') === false) {
				$this->Session->setFlash('Debe especificar un periodo valido.', 'error');
			} else {
				$periodo = $this->Util->format($this->data['Condicion']['Liquidacion-periodo'], 'periodo');
				
				/** Search employers */
				//$this->Liquidacion->Relacion->Empleador->contain(array(''));<br>$this->Liquidacion->Relacion->Empleador->
				$this->Liquidacion->Relacion->Empleador->recursive = -1;
				$this->set('employer', $this->Liquidacion->Relacion->Empleador->findById($this->data['Condicion']['Liquidacion-empleador_id']));
				$empleadores = $this->data['Condicion']['Liquidacion-empleador_id'];
				if (!empty($this->data['Condicion']['Liquidacion-grupo_id'])) {

					$grupo = array_pop(ClassRegistry::init('Grupo')->find('all',
						array(	'conditions' 	=> array('Grupo.id' => $this->data['Condicion']['Liquidacion-grupo_id']),
								'contain'		=> array('GruposParametro.Parametro'))
					));

					$this->set('groupParams', Set::combine($grupo['GruposParametro'], '{n}.Parametro.nombre', '{n}.valor'));
					
					$empleadores = Set::extract('/Empleador/id', $this->Liquidacion->Relacion->Empleador->find('all', array(
							'recursive' 	=> -1,
							'conditions' 	=> array(
							'(Empleador.group_id & ' . $this->data['Condicion']['Liquidacion-grupo_id'] . ') >' => 0))
					));
				}
				
				$conditions = array('Liquidacion.empleador_id' 	=> $empleadores,
									'Liquidacion.estado'		=> 'Confirmada',
		 							'Liquidacion.ano'			=> $periodo['ano'],
		 							'Liquidacion.mes'			=> $periodo['mes']);

				$this->Liquidacion->Behaviors->detach('Permisos');
				$this->Liquidacion->Behaviors->detach('Util');
				$liquidaciones = $this->Liquidacion->find('all',
						array(	'contain'		=> array(
									'LiquidacionesDetalle' => array('order' => 'LiquidacionesDetalle.concepto_tipo'),
									'Relacion' => array('Trabajador', 'Empleador', 'Modalidad', 'ConveniosCategoria.ConveniosCategoriasHistorico')),
								'conditions'	=> $conditions,
							 	'order'			=> array('Liquidacion.empleador_nombre')));


				if (empty($liquidaciones)) {
					$this->Session->setFlash('No se han encontrado liquidaciones confirmadas para el periodo seleccioando segun los criterios especificados.', 'error');
				} else {
					$this->set('data', $liquidaciones);
					$this->set('fileFormat', $this->data['Condicion']['Liquidacion-formato']);
					$this->layout = 'ajax';
				}
			}
		}
		$this->set('grupos', $this->Util->getUserGroups());
	}

/**
 * PreLiquidar.
 * Me permite hacer una preliquidacion.
 */
	function preliquidar() {

		/**$this->__filasPorPagina();
		$this->paginate = array_merge($this->paginate,
				array('conditions' => array('Liquidacion.estado' => 'Sin Confirmar')));
		*/
		if (($this->data['Formulario']['accion'] === 'generar')) {
			
			$periodo = $this->Util->format($this->data['Condicion']['Liquidacion-periodo_largo'], 'periodo');
			if ($periodo !== false) {
				$message = null;
				if ($this->data['Condicion']['Liquidacion-tipo'] === 'normal' &&
						!in_array($periodo['periodo'], array('1Q', '2Q', 'M'))) {
					$message = __('Normal liquidation period should be of the form "YYYYMM[1Q|2Q|M]"', true);
				} elseif ($this->data['Condicion']['Liquidacion-tipo'] === 'holliday' &&
						!in_array($periodo['periodo'], array('1Q', '2Q', 'M'))) {
					$message = __('Holliday liquidation period should be of the form "YYYYMM[1Q|2Q|M]"', true);
				} elseif ($this->data['Condicion']['Liquidacion-tipo'] === 'holliday' &&
						!preg_match('/\d\d\d\d/', $this->data['Condicion']['Liquidacion-periodo_vacacional'])) {
					$message = __('Holliday period should be of the form "YYYY"', true);
				} elseif ($this->data['Condicion']['Liquidacion-tipo'] === 'sac' &&
						!in_array($periodo['periodo'], array('1S', '2S'))) {
					$message = __('Sac liquidation period should be of the form "YYYY[12]S"', true);
				}
			} elseif ($this->data['Condicion']['Liquidacion-tipo'] !== 'final_liquidation') {
				$message = __('Must enter a period', true);
			} else {
				$message = __('Invalid Period', true);
			}

			if (empty($this->data['Condicion']['Relacion-empleador_id']) &&
					empty($this->data['Condicion']['Relacion-trabajador_id']) &&
					empty($this->data['Condicion']['Relacion-id'])) {
						$message = "Debe seleccionar un empleador, un trabajador o una relacion laboral.";
			}

			if (!is_null($message)) {
				$this->Session->setFlash($message, 'error');
				$this->redirect(array('action' => 'preliquidar'));
			}

			
			/**
			 * De las liquidaciones que he seleccionado para pre-liquidar, verifico que no sean
			 * liquidaciones ya confirmadas para el mismo periodo del mismo tipo.
			 */
			$condicionesLiquidacion['Liquidacion.mes'] = $periodo['mes'];
			$condicionesLiquidacion['Liquidacion.ano'] = $periodo['ano'];
			$condicionesLiquidacion['Liquidacion.periodo'] = $periodo['periodo'];
			$condicionesLiquidacion['Liquidacion.tipo'] = $this->data['Condicion']['Liquidacion-tipo'];
			$condicionesLiquidacion['Liquidacion.estado'] = 'Confirmada';
			$liquidaciones = $this->Liquidacion->find('all', array(
					'recursive'		=> -1,
					'fields'		=> 'relacion_id',
					'conditions'	=> $condicionesLiquidacion));
			$confirmadas = Set::extract('/Liquidacion/relacion_id', $liquidaciones);
			
			
			/** Search for the relations */
			$condiciones = null;
			$condiciones = $this->Paginador->generarCondicion();
			unset($condiciones['Liquidacion.tipo']);
			unset($condiciones['Liquidacion.periodo_largo']);
			unset($condiciones['Liquidacion.periodo_vacacional']);
			$condiciones['Relacion.ingreso <='] = $periodo['hasta'];
			$condiciones['Relacion.estado'] = 'Activa';
			if (!empty($confirmadas)) {
				$condiciones['NOT'] = array('Relacion.id' => $confirmadas);
			}
			
			$relaciones = $this->Liquidacion->Relacion->find('all',
					array(	'contain'		=> array(	'ConveniosCategoria.ConveniosCategoriasHistorico',
														'Trabajador.ObrasSocial',
														'Empleador'),
							'conditions'	=> $condiciones));
			if (empty($relaciones)) {
				$this->Session->setFlash('No se encontraron relaciones para liquidar. Verifique si no se han liquidado y confirmado previamente o los criterios de busqueda no son correctos.', 'error');
				$this->redirect(array('action' => 'preliquidar'));
			}
			
			/** Delete user's unconfirmed liquidations */
			$usuario = $this->Session->read('__Usuario');
			$delete = array('Liquidacion.user_id' => $usuario['Usuario']['id'], 'Liquidacion.estado' => 'Sin Confirmar');
			if (!$this->Liquidacion->deleteAll($delete)) {
				$this->Session->setFlash(__('Can\'t delete previous liquidations. Call Administrator', true), 'error');
				$this->redirect(array('action' => 'preliquidar'));
			}

			/**
			 * Busco las informaciones de los convenios que pueden necesitarse en las formulas.
			 * Lo hago de esta forma, ya que busco todo junto y no uno por uno en cada relacion por una cuestion de performance,
			 * ya que seguramente las relaciones liquidadas tengas los mismos convenios.
			 */
			$informaciones = $this->Liquidacion->Relacion->ConveniosCategoria->Convenio->getInformacion(Set::extract('/ConveniosCategoria/convenio_id', $relaciones));

			/** Find all vars */
			$Variable = ClassRegistry::init('Variable');
			$variables = Set::combine($Variable->find('all', array(
					'recursive' => -1,
	 				'order' => false)), '{n}.Variable.nombre', '{n}.Variable');
			$variables['#tipo_liquidacion']['valor'] = $this->data['Condicion']['Liquidacion-tipo'];
			if (!empty($this->data['Condicion']['Liquidacion-periodo_vacacional'])) {
				$variables['#fecha_hasta_periodo_vacacional']['valor'] = sprintf('%d-12-31', $this->data['Condicion']['Liquidacion-periodo_vacacional']);
			}

			/** Make the liquidations if not done. */
			$ids = null;
			$opciones['variables'] = $variables;
			$opciones['informaciones'] = $informaciones;
			foreach ($relaciones as $relacion) {
				$this->Liquidacion->getReceipt($relacion, $periodo, $variables['#tipo_liquidacion']['valor'], $opciones);
			}
		}

		$this->Liquidacion->contain(array(
				'Relacion.Trabajador',
				'Relacion.Empleador',
	 			'LiquidacionesError'));

		$resultados = $this->Paginador->paginar(
				array('Liquidacion.estado' => 'Sin Confirmar'),
				array('Liquidacion.periodo_largo', 'Liquidacion.periodo_vacacional'));
		$this->set('registros', $resultados['registros']);
	}


/**
 * Agrega datos que seran guardados en la tabla liquidaciones_auxiliares.
 *
 * @param array $auxiliar Los datos a guardar.
 * @return void.
 * @access private.
 */

/**
 * recibo_html.
 * Muestra via desglose el recibo (detalle) de la preliquidacion.
 */
	function recibo_html($id = null) {
		$this->Liquidacion->contain('LiquidacionesDetalle');
		$this->data = $this->Liquidacion->read(null, $id);
	}

/**
 * recibo_html.
 * Muestra via desglose el recibo (detalle) de la preliquidacion.
 */
	function imprimir($id = null) {
		if (empty($id)) {
			if (!empty($this->params['data']['seleccionMultiple'])) {
				$id = $this->Util->extraerIds($this->params['data']['seleccionMultiple']);
			}
		}
		
		$this->Liquidacion->contain('LiquidacionesDetalle');
		$this->data = $this->Liquidacion->find('all', array('conditions' => array('Liquidacion.id' => $id)));
		$this->render('recibo_excel');
	}

	
/**
 * recibo_html_debug.
 * Muestra via desglose el recibo (detalle) de la preliquidacion con informacion de debug.
 */
	function recibo_html_debug($id) {
		$this->Liquidacion->contain(array("LiquidacionesDetalle"));
		$this->data = $this->Liquidacion->read(null, $id);
	}


/**
 * errores.
 * Muestra via desglose los errores de la preliquidacion.
 */
	function errores($id) {
		$this->Liquidacion->contain(array("LiquidacionesError"));
		$this->data = $this->Liquidacion->read(null, $id);
	}
	
	
	
	
	function agregar_observacion($id) {
		/**
		* Agrego una url a la History para que vuelva bien a donde debe, ya que no uso un edit  comun.
		*/
		$this->Liquidacion->recursive = -1;
		$this->data = $this->Liquidacion->read(null, $id);
	}


	function riesgo_indemnizatorio() {
	}



/**
 * Genera el archivo para importar en SIAP y generar el 931.
 * TODO: Deberia mover esto al controller siap.
 *
 * @return void.
 * @access public.
*/
	function generar_archivo_siap() {
		
		if (!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] === "generar" && !empty($this->data['Condicion']['Siap-version'])) {
			if (!empty($this->data['Condicion']['Siap-empleador_id'])
				&& !empty($this->data['Condicion']['Siap-grupo_id'])) {
				$this->Session->setFlash("Debe seleccionar el Empleador o el Grupo, pero no ambos.", "error");
			}
			elseif (empty($this->data['Condicion']['Siap-empleador_id'])
				&& empty($this->data['Condicion']['Siap-grupo_id'])) {
				$this->Session->setFlash("Debe seleccionar un Empleador o un Grupo por lo menos.", "error");
			}
			elseif (empty($this->data['Condicion']['Siap-periodo']) || !preg_match("/^(20\d\d)(0[1-9]|1[012])$/", $this->data['Condicion']['Siap-periodo'], $periodo)) {
				$this->Session->setFlash("Debe especificar un periodo valido de la forma AAAAMM.", "error");
			}
			else {
				$periodo = $this->Util->format($this->data['Condicion']['Siap-periodo'], 'periodo');
				
				/**
				* Busco los empleadores para los cuales debo generar el archivo.
				*/
				if (!empty($this->data['Condicion']['Siap-empleador_id'])) {
					$empleadores = $this->data['Condicion']['Siap-empleador_id'];
				}
				else {
					$empleadores = Set::extract("/Empleador/id", $this->Liquidacion->Relacion->Empleador->find("all", 
							array("recursive" 	=> -1,
								"conditions" 	=> array(
										"(Empleador.group_id & " . $this->data['Condicion']['Siap-grupo_id'] . ") >" => 0)
								)));
				}
				
				
				$ausenciasMotivo = $this->Liquidacion->Relacion->Ausencia->AusenciasMotivo->find('all', array('conditions' => array('NOT' => array('AusenciasMotivo.situacion_id' => null))));
				$ausenciasMotivo = Set::combine($ausenciasMotivo, '{n}.AusenciasMotivo.id', '{n}.Situacion');
				
				
				$Siap = ClassRegistry::init('Siap');
				$data = $Siap->findById($this->data['Condicion']['Siap-version']);
				foreach ($data['SiapsDetalle'] as $k => $v) {
					$detalles[$v['elemento']] = $v;
				}
				
				$conditions = array('Liquidacion.empleador_id' 	=> $empleadores,
									'Liquidacion.estado'		=> 'Confirmada',
		 							'Liquidacion.ano'			=> $periodo['ano'],
		 							'Liquidacion.mes'			=> $periodo['mes']);
				
				$liquidaciones = $this->Liquidacion->find('all',
						array(	'checkSecurity'	=> false,
								'contain'		=> array(	'Empleador',
										'Relacion' 		=> array('Situacion', 'ConveniosCategoria', 'Modalidad', 'Ausencia' =>
												array('conditions' => array('Ausencia.desde >=' => $periodo['desde'], 'Ausencia.desde <=' => $periodo['hasta']))),
										'Trabajador' 	=> array('ObrasSocial', 'Condicion', 'Siniestrado', 'Localidad')),
								'conditions'	=> $conditions));
				
				if (!empty($liquidaciones)) {
					
					/**
					* Must sumarize. Can't do in by query because of contain.
					*/
					$all = Set::extract('/Liquidacion/relacion_id', $liquidaciones);
					$unique = array_unique($all);
					$duplicates = Set::diff($unique, $all);
					
					$liquidacionesOriginal = $liquidaciones;
					$liquidaciones = Set::combine($liquidaciones, '{n}.Relacion.id', '{n}');
					
					$totales = array('remunerativo', 'no_remunerativo', 'deduccion', 'total_pesos', 'total_beneficios', 'total');
					foreach ($duplicates as $duplicateRelacionId) {
						foreach ($totales as $total) {
							$liquidaciones[$duplicateRelacionId]['Liquidacion'][$total] = 0;
						}
					}
					
					foreach ($liquidacionesOriginal as $liquidacion) {
						if (!in_array($liquidacion['Liquidacion']['relacion_id'], $duplicates)) {
							continue;
						}
						
						foreach ($totales as $total) {
							$liquidaciones[$liquidacion['Liquidacion']['relacion_id']]['Liquidacion'][$total] += $liquidacion['Liquidacion'][$total];
						}
					}
					
					$lineas = null;
					foreach ($liquidaciones as $liquidacion) {
						$campos = $detalles;
						$campos['c1']['valor'] = str_replace("-", "", $liquidacion['Trabajador']['cuil']);
						$campos['c2']['valor'] = $liquidacion['Trabajador']['apellido'] . " " . $liquidacion['Trabajador']['nombre'];
						if (!empty($liquidacion['Relacion']['situacion_id'])) {
							$campos['c5']['valor'] = $liquidacion['Relacion']['Situacion']['codigo'];
						}
						if (!empty($liquidacion['Trabajador']['condicion_id'])) {
							$campos['c6']['valor'] = $liquidacion['Trabajador']['Condicion']['codigo'];
						}
						if (!empty($liquidacion['Trabajador']['actividad_id'])) {
							$campos['c7']['valor'] = $liquidacion['Trabajador']['Actividad']['codigo'];
						}
						$campos['c8']['valor'] = $liquidacion['Trabajador']['Localidad']['codigo_zona'];
						if (!empty($liquidacion['Relacion']['modalidad_id'])) {
							$campos['c10']['valor'] = $liquidacion['Relacion']['Modalidad']['codigo'];
						}
						if (!empty($liquidacion['Trabajador']['obra_social_id'])) {
							$campos['c11']['valor'] = $liquidacion['Trabajador']['ObrasSocial']['codigo'];
						}
						$campos['c12']['valor'] = $liquidacion['Trabajador']['adherentes_os'];
						$campos['c13']['valor'] = $liquidacion['Liquidacion']['remunerativo'] + $liquidacion['Liquidacion']['no_remunerativo'];
						$campos['c14']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
						$campos['c20']['valor'] = $liquidacion['Trabajador']['Localidad']['nombre'];
						$campos['c21']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
						$campos['c22']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
						
						/**
						 * Viene expresado como una formula.
						 */
						$campos['c23']['valor'] = $this->Formulador->resolver(str_replace("c23", $liquidacion['Liquidacion']['remunerativo'], $campos['c23']['valor']));
						
						if (!empty($liquidacion['Trabajador']['siniestrado_id'])) {
							$campos['c24']['valor'] = $liquidacion['Trabajador']['Siniestrado']['codigo'];
						}
						if ($liquidacion['Empleador']['corresponde_reduccion'] === "Si") {
							$campos['c25']['valor'] = "S";
						}
						else {
							$campos['c25']['valor'] = " ";
						}
						if ($liquidacion['Trabajador']['jubilacion'] === "Reparto") {
							$campos['c29']['valor'] = "1";
						}
						else {
							$campos['c29']['valor'] = "0";
						}
						
						
						/**
						* Trabajo con la situacion de revista
						*/
						$cantidadAusencias = 0;
						$camposTmp = null;
						$camposTmp[0] = array();
						foreach ($liquidacion['Relacion']['Ausencia'] as $k => $ausencia) {
							if (isset($ausenciasMotivo[$ausencia['ausencia_motivo_id']])) {
								$cantidadAusencias++;
								$camposTmp[$k]['situacion'] = $ausenciasMotivo[$ausencia['ausencia_motivo_id']]['codigo'];
								$camposTmp[$k]['dia'] = array_pop(explode('-', $ausencia['desde']));
							}
						}	
							
						$campoNumero = 30;
						foreach ($camposTmp as $k => $tmp) {
							$campoNumero += ($k * 2);
							if ($cantidadAusencias < 3 && $k === 0) {
								$campos['c' . $campoNumero]['valor'] = "1";
								if ($liquidacion['Relacion']['ingreso'] <= $periodo['desde']) {
									$campos['c' . ($campoNumero + 1)]['valor'] = array_pop(explode('-', $periodo['desde']));
								} else {
									$campos['c' . ($campoNumero + 1)]['valor'] = array_pop(explode('-', $liquidacion['Relacion']['ingreso']));
								}
								$campoNumero = $campoNumero + 2;
							} 
							elseif ($cantidadAusencias > 0) {
								$campos['c' . $campoNumero]['valor'] = $tmp['situacion'];
								$campos['c' . ($campoNumero + 1)]['valor'] = $tmp['dia'];
							}
						}
						
						
						$campos['c36']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
						$campos['c42']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
						if ($liquidacion['Relacion']['ConveniosCategoria']['nombre'] === "Fuera de convenio") {
							$campos['c43']['valor'] = "0";
						}
						else {
							$campos['c43']['valor'] = "1";
						}
						$lineas[] = $this->__generarRegistro($campos);
					}
					$this->set("archivo", array("contenido"=>implode("\n\r", $lineas), "nombre" => "SIAP-" . $periodo['ano'] . "-" . $periodo['mes'] . ".txt"));
					$this->render(".." . DS . "elements" . DS . "txt", "txt");
				}
				else {
					$this->Session->setFlash("No se han encontrado liquidaciones confirmadas para el periodo seleccioando segun los criterios especificados.", "error");
				}
			}
		}
		
		$this->set("grupos", $this->Util->getUserGroups());
		/*
		$usuario = $this->Session->read('__Usuario');
		if (!empty($usuario['Grupo'])) {
			foreach ($usuario['Grupo'] as $grupo) {
				$grupos[$grupo['id']] = $grupo['nombre'];
			}
			$this->set("grupos", $grupos);
		}
		*/
	}
	
	
/**
 * Genera el una linea del archivo para importar en SIAP.
 *
 * @param array La descripcion del campo, donde me indica como debe comportarse.
 * @return string Una linea formateada para ser importada.
 * @access private.
*/
	function __generarRegistro($campos) {
		$v = array();
		if (!empty($campos)) {
			foreach ($campos as $campo) {
				if ($campo['direccion_relleno'] === "Derecha") {
					$v[] = str_pad($campo['valor'], $campo['longitud'], $campo['caracter_relleno'], STR_PAD_RIGHT);
				}
				elseif ($campo['direccion_relleno'] === "Izquierda") {
					$v[] = str_pad($campo['valor'], $campo['longitud'], $campo['caracter_relleno'], STR_PAD_LEFT);
				}
				else {
					$v[] = $campo['valor'];
				}
			}
		}
		return implode("", $v);
	}


	function asignar_suss() {
		if (!empty($this->data['Formulario']['accion'])) {
			if ($this->data['Formulario']['accion'] == "asignar") {
				if (!empty($this->data['Condicion']['Suss-fecha']) && !empty($this->data['Condicion']['Suss-banco_id']) && !empty($this->data['Condicion']['Suss-periodo'])) {
					if (preg_match("/^(20\d\d)(0[1-9]|1[012])$/", $this->data['Condicion']['Suss-periodo'], $periodo)) {
						d($periodo);
					}
					else {
						$this->Session->setFlash("El periodo ingresado no es valido. Debe tener la forma AAAAMM", "error");
					}
				}
				else {
					$this->Session->setFlash("Debe ingresar todos los datos requeridos.", "error");
				}
			}
			else {
				$this->History->goBack();
			}
		}
		$this->set("bancos", $this->Banco->find("list", array("fields"=>array("Banco.nombre"))));
	}
	
	
	
	function index() {
		if (!empty($this->data['Condicion']['Liquidacion-periodo'])) {
			if ($tmp = $this->Util->format($this->data['Condicion']['Liquidacion-periodo'], "periodo")) {
				$condiciones['Liquidacion.ano'] = $tmp['ano'];
				$condiciones['Liquidacion.mes'] = $tmp['mes'];
				$condiciones['Liquidacion.periodo'] = $tmp['periodo'];
			}
			unset($this->data['Condicion']['Liquidacion-periodo']);
		}
		$condiciones['Liquidacion.estado'] = "Confirmada";
		$this->paginate = array_merge($this->paginate, array('conditions' => $condiciones));
		parent::index();
	}

/**
 * pagos.
 * Muestra via desglose los Pagos generados por esta liquidacion.
 */
	function pagos($id) {
		$this->Liquidacion->contain(array("Pago"));
		$this->data = $this->Liquidacion->read(null, $id);
	}


/**
 * 
 * 
 */
	function __seteos() {
		$periodos["M"] = "Mensual";
		$periodos["1Q"] = "Primera Quincena";
		$periodos["2Q"] = "Segunda Quincena";
		$this->set("periodos", $periodos);
		$this->set("meses", $this->Util->format("all", array("type" => "mesEnLetras", "case" => "ucfirst")));
	}



	
	
/**
 * Permite confirmar liquidaciones.
 * Las liquidaciones estan en la tabla liquidaciones pero con estado "Sin Confirmar".
 *
 * Esto implica que:
 *		- Las horas liquidadas cambian a estado "Liquidada".
 *		- Las ausencias_seguimientos liquidadas cambian a estado "Liquidado".
 *		- Las liquidaciones cambian a estado "Liquidada".
 *		- Se generan los pagos pendientes.
 *		- Se agregan detalles de descuentos.
 *
 * @return void.
 * @access public.
 */
	function confirmar() {
		$ids = $this->Util->extraerIds($this->data['seleccionMultiple']);
		
		if (!empty($ids)) {
			/**
			* En la tabla auxiliares tengo un array de los datos listos para guardar.
			* Puede haber campos que deben ser guardados y no tienen valor, estos debo ponerle valor actual,
			* por ejemplo, la fecha del dia que se confirma, y no la del dia que se pre-liquido.
			*/
			$auxiliares = $this->Liquidacion->LiquidacionesAuxiliar->find("all", array("conditions"=>array("LiquidacionesAuxiliar.liquidacion_id"=>$ids)));
			$c = 0;
			$this->Liquidacion->begin();
			$idsAuxiliares = null;
			foreach ($auxiliares as $v) {
				$model = $v['LiquidacionesAuxiliar']['model'];
				$idsAuxiliares[] = $v['LiquidacionesAuxiliar']['id'];
				$save = unserialize($v['LiquidacionesAuxiliar']['save']);

				foreach ($save as $campo=>$valor) {
					preg_match('/^##MACRO:([a-z,_]+)##$/',$valor, $matches);
					if (!empty($matches[1])) {
						switch($matches[1]) {
							case 'fecha_liquidacion':
								$save[$campo] = date("d/m/Y");
								break;
							case 'liquidacion_id':
								$save[$campo] = $v['LiquidacionesAuxiliar']['liquidacion_id'];
								break;
						}
					}
				}
				
				$modelSave = ClassRegistry::init($model);
				$save = array($model => $save);
				$modelSave->create($save);
				if ($modelSave->save($save)) {
					$c++;
				}
			}
			

			/**
			 * Si lo anterior salio todo ok, continuo.
			 */
			if ($c === count($auxiliares)) {
				$this->Liquidacion->recursive = -1;
				if ($this->Liquidacion->updateAll(array('estado' => "'Confirmada'"), array('Liquidacion.id' => $ids))) {
					/**
					 * Borro de la tabla auxiliar.
					 */
					if (!empty($idsAuxiliares)) {
						$this->Liquidacion->LiquidacionesAuxiliar->recursive = -1;;
						$this->Liquidacion->LiquidacionesAuxiliar->deleteAll(array('LiquidacionesAuxiliar.id' => $idsAuxiliares));
					}
					$this->Liquidacion->commit();
					$this->Session->setFlash('Se confirmaron correctamente ' . count($ids) . ' liquidacion/es.', 'ok');
				} else {
					$this->Liquidacion->rollback();
					$this->Liquidacion->__buscarError();
					$this->Session->setFlash('Ocurrio un error al intentar confirmar las liquidaciones.', 'error');
				}
			} else {
				$this->Liquidacion->rollback();
				$this->Session->setFlash('Ocurrio un error al intentar confirmar las liquidaciones. No se puedieron actualizar los registros relacionados.', 'error');
			}
		}
		$this->History->goBack();
	}
	
}
?>