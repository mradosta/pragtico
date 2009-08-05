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

    var $paginate = array(
        'order' => array(
            'Liquidacion.ano'       => 'desc',
            'Liquidacion.mes'       => 'desc',
            'Liquidacion.periodo'   => 'desc'
        )
    );
    
	var $components = array('Formulador');
	var $helpers = array('Documento');
	
    function resumen() {

        if (!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] === 'generar') {
            if (empty($this->data['Condicion']['Liquidacion-empleador_id'])
                && empty($this->data['Condicion']['Liquidacion-grupo_id'])) {
                $this->Session->setFlash('Debe seleccionar por lo menos un Empleador y/o un Grupo.', 'error');
            } elseif (empty($this->data['Condicion']['Liquidacion-empleador_id'])) {
                $this->Session->setFlash('Debe seleccionar un empleador.', 'error');
            } elseif (empty($this->data['Condicion']['Liquidacion-periodo_largo']) || $this->Util->format($this->data['Condicion']['Liquidacion-periodo_largo'], 'periodo') === false) {
                $this->Session->setFlash('Debe especificar un periodo valido.', 'error');
            } else {
                $saveConditions = $this->data['Condicion'];

                $fileFormat = $this->data['Condicion']['Liquidacion-formato'];
                unset($this->data['Condicion']['Liquidacion-formato']);

                $periodo = $this->Util->format($this->data['Condicion']['Liquidacion-periodo_largo'], 'periodo');
                unset($this->data['Condicion']['Liquidacion-periodo_largo']);

                if (!empty($this->data['Condicion']['Liquidacion-desagregado'])) {
                    $desagregado = $this->data['Condicion']['Liquidacion-desagregado'];
                    unset($this->data['Condicion']['Liquidacion-desagregado']);
                } else {
                    $desagregado = 'No';
                }
                
                $conditions = array_merge($this->Paginador->generarCondicion(false),
                    array(  'Liquidacion.periodo'       => $periodo['periodo'],
                            'Liquidacion.ano'           => $periodo['ano'],
                            'Liquidacion.mes'           => $periodo['mes']));
                if (!empty($this->data['Condicion']['Liquidacion-grupo_id'])) {
                    $conditions['(Liquidacion.group_id & ' . $this->data['Condicion']['Liquidacion-grupo_id'] . ') >'] = 0;
                    unset($conditions['Liquidacion.grupo_id']);
                    unset($this->data['Condicion']['Liquidacion-grupo_id']);
                }

                $this->Liquidacion->Behaviors->detach('Permisos');
                $this->Liquidacion->Behaviors->detach('Util');
                $workers = $this->Liquidacion->find('all', array(
                        'conditions'    => $conditions,
                        'fields'        => array('COUNT(Liquidacion.trabajador_id) AS cantidad'),
                        'recursive'     => -1));

                $this->data['Condicion'] = $saveConditions;
                if (empty($workers[0]['Liquidacion']['cantidad'])) {
                    $this->Session->setFlash('No se han encontrado liquidaciones confirmadas para el periodo seleccionado segun los criterios especificados.', 'error');
                } else {

                    $this->Liquidacion->LiquidacionesDetalle->Behaviors->detach('Permisos');
                    $this->Liquidacion->LiquidacionesDetalle->Behaviors->detach('Util');
                    $conditions['LiquidacionesDetalle.concepto_imprimir !='] = 'No';
                    if ($desagregado === 'Si') {
                        $data = $this->Liquidacion->LiquidacionesDetalle->find('all', array(
                                'conditions'    => $conditions,
                                'contain'       => 'Liquidacion',
                                'order'         => 'Liquidacion.relacion_id, LiquidacionesDetalle.concepto_orden',
                                'fields'        => array('Liquidacion.trabajador_nombre',
                                                        'Liquidacion.trabajador_apellido',
                                                        'LiquidacionesDetalle.concepto_nombre',
                                                        'LiquidacionesDetalle.concepto_tipo',
                                                        'COUNT(LiquidacionesDetalle.concepto_nombre) AS cantidad',
                                                        'SUM(LiquidacionesDetalle.valor_cantidad) AS suma_cantidad',
                                                        'SUM(LiquidacionesDetalle.valor) AS valor'),
                                'group'         => array('Liquidacion.relacion_id', 'LiquidacionesDetalle.concepto_nombre'),
                                'contain'       => array('Liquidacion')));
                    } else {
                        $data = $this->Liquidacion->LiquidacionesDetalle->find('all', array(
                                'conditions'    => $conditions,
                                'order'         => 'LiquidacionesDetalle.concepto_orden',
                                'fields'        => array('LiquidacionesDetalle.concepto_nombre',
                                                        'LiquidacionesDetalle.concepto_tipo',
                                                        'COUNT(LiquidacionesDetalle.concepto_nombre) AS cantidad',
                                                        'SUM(LiquidacionesDetalle.valor_cantidad) AS suma_cantidad',
                                                        'SUM(LiquidacionesDetalle.valor) AS valor'),
                                'group'         => array('LiquidacionesDetalle.concepto_nombre'),
                                'contain'       => array('Liquidacion')));
                    }
                    
                    if (!empty($this->data['Condicion']['Liquidacion-grupo_id'])) {
                        $this->set('groupParams', ClassRegistry::init('Grupo')->getParams($this->data['Condicion']['Liquidacion-grupo_id']));
                    }

                    $this->set('data', $data);
                    $this->set('workers', $workers);
                    $this->set('fileFormat', $fileFormat);
                    $this->set('conditions', $this->data['Condicion']);
                    $this->set('desagregado', $desagregado);
                    $this->History->skip();
                }
            }
        }
        $this->set('grupos', $this->Util->getUserGroups());
    }

	function libro_sueldos() {
		if (!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] === 'generar') {
			if (empty($this->data['Condicion']['Liquidacion-empleador_id'])
				&& empty($this->data['Condicion']['Liquidacion-grupo_id'])) {
				$this->Session->setFlash('Debe seleccionar por lo menos un Empleador y/o un Grupo.', 'error');
			} elseif (empty($this->data['Condicion']['Liquidacion-periodo_largo']) || $this->Util->format($this->data['Condicion']['Liquidacion-periodo_largo'], 'periodo') === false) {
				$this->Session->setFlash('Debe especificar un periodo valido.', 'error');
			} else {
				$periodo = $this->Util->format($this->data['Condicion']['Liquidacion-periodo_largo'], 'periodo');
                
                $conditions = array('Liquidacion.estado'        => 'Confirmada',
                                    'Liquidacion.tipo'          => $this->data['Condicion']['Liquidacion-tipo'],
                                    'Liquidacion.periodo'       => $periodo['periodo'],
                                    'Liquidacion.ano'           => $periodo['ano'],
                                    'Liquidacion.mes'           => $periodo['mes']);
                
                if (!empty($this->data['Condicion']['Liquidacion-empleador_id'])) {
                    $conditions['Liquidacion.empleador_id'] = $this->data['Condicion']['Liquidacion-empleador_id'];
                }

                if (!empty($this->data['Condicion']['Liquidacion-grupo_id'])) {
                    $conditions['(Liquidacion.group_id & ' . $this->data['Condicion']['Liquidacion-grupo_id'] . ') >'] = 0;
                }

				$this->Liquidacion->Behaviors->detach('Permisos');
				$this->Liquidacion->Behaviors->detach('Util');
				$liquidaciones = $this->Liquidacion->find('all',
						array(	'contain'		=> array(
									'LiquidacionesDetalle' => array('order' => 'LiquidacionesDetalle.concepto_tipo'),
									'Relacion' => array('Trabajador', 'Empleador', 'Modalidad', 'ConveniosCategoria.ConveniosCategoriasHistorico')),
								'conditions'	=> $conditions,
							 	'order'			=> array('Liquidacion.empleador_nombre')));

				if (empty($liquidaciones)) {
					$this->Session->setFlash('No se han encontrado liquidaciones confirmadas para el periodo seleccionado segun los criterios especificados.', 'error');
				} else {
                    if (!empty($this->data['Condicion']['Liquidacion-grupo_id'])) {
                        $this->set('groupParams', ClassRegistry::init('Grupo')->getParams($this->data['Condicion']['Liquidacion-grupo_id']));
                    }
                    if (!empty($this->data['Condicion']['Liquidacion-empleador_id'])) {
                        $this->Liquidacion->Relacion->Empleador->recursive = -1;
                        $this->set('employer', $this->Liquidacion->Relacion->Empleador->findById($this->data['Condicion']['Liquidacion-empleador_id']));                    
                    }
                    $this->set('startPage', $this->data['Condicion']['Bar-start_page']);
                    $this->set('periodo', $periodo['periodoCompleto']);
					$this->set('data', $liquidaciones);
					$this->set('fileFormat', $this->data['Condicion']['Liquidacion-formato']);
                    $this->History->skip();
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

		$periodo = $this->Util->format($this->data['Condicion']['Liquidacion-periodo_largo'], 'periodo');
		if (!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] === 'generar') {
			
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
			} elseif ($this->data['Condicion']['Liquidacion-tipo'] !== 'final') {
				$message = __('Invalid Period', true);
			}

			if (empty($this->data['Condicion']['Relacion-empleador_id']) &&
					empty($this->data['Condicion']['Relacion-trabajador_id']) &&
					empty($this->data['Condicion']['Relacion-id'])) {
						$message = "Debe seleccionar un empleador, un trabajador o una relacion laboral.";
			}

			if (!empty($message)) {
				$this->Session->setFlash($message, 'error');
				$this->redirect(array('action' => 'preliquidar'));
			}


			/** Search for the relations */
			$condiciones = null;
			$condiciones = $this->Paginador->generarCondicion();
			unset($condiciones['Liquidacion.tipo']);
			unset($condiciones['Liquidacion.periodo_largo']);
			unset($condiciones['Liquidacion.periodo_vacacional']);
			unset($condiciones['Liquidacion.estado']);
            if ($this->data['Condicion']['Liquidacion-tipo'] !== 'final') {
                $condiciones['Relacion.ingreso <='] = $periodo['hasta'];
            }
            
            $condiciones['Relacion.estado'] = 'Activa';
            if ($this->data['Condicion']['Liquidacion-tipo'] !== 'especial') {
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
                        'recursive'     => -1,
                        'fields'        => 'relacion_id',
                        'conditions'    => $condicionesLiquidacion));
                $confirmadas = Set::extract('/Liquidacion/relacion_id', $liquidaciones);
                if (!empty($confirmadas)) {
                    $condiciones['NOT'] = array('Relacion.id' => $confirmadas);
                }
            }

            $condiciones['(Relacion.group_id & ' . User::get('preferencias/grupo_default_id') . ') >'] = 0;
			$relaciones = $this->Liquidacion->Relacion->find('all',
					array(	'contain'		=> array(	'ConveniosCategoria',
														'Trabajador.ObrasSocial',
														'Empleador'),
							'conditions'	=> $condiciones));

			if (empty($relaciones)) {
				$this->Session->setFlash('No se encontraron relaciones para liquidar. Verifique que no se haya liquidado y confirmado previamente o los criterios de busqueda no son correctos.', 'error');
				$this->redirect(array('action' => 'preliquidar'));
			}

			/** Delete user's unconfirmed liquidations */
            $this->Liquidacion->setSecurityAccess('readOwnerOnly');
			if (!$this->Liquidacion->deleteAll(array(
                'Liquidacion.user_id'   => User::get('id'),
                'Liquidacion.estado'    => 'Sin Confirmar'), true, false, true)) {
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
			$variables = Set::combine(ClassRegistry::init('Variable')->find('all', array(
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

                if ($this->data['Condicion']['Liquidacion-tipo'] === 'final' && $relacion['Relacion']['liquidacion_final'] === 'No') {
                    continue;
                }

                /** For finished relations, only allow last period receipt */
                if (!empty($relacion['Relacion']['egreso']) && $relacion['Relacion']['egreso'] !== '0000-00-00' && $this->data['Condicion']['Liquidacion-tipo'] === 'final') {
                    $periodo['hasta'] = $relacion['Relacion']['egreso'];
                        /*
                        $tmpPeriod = explode('-', $relacion['Relacion']['egreso']);
                        App::import('Vendor', 'dates', 'pragmatia');
                        if ($periodo['periodo'] === 'M') {
                            $tmpPeriodHasta = $periodo['ano'] . '-' . $periodo['mes'] . '-' . Dates::daysInMonth($periodo['ano'], $periodo['mes']);
                        } elseif ($periodo['periodo'] === '1Q') {
                            $tmpPeriodHasta = $periodo['ano'] . '-' . $periodo['mes'] . '-15';
                        } elseif ($periodo['periodo'] === '2Q') {
                            $tmpPeriodHasta = $periodo['ano'] . '-' . $periodo['mes'] . '-' . Dates::daysInMonth($periodo['ano'], $periodo['mes']);
                        }
                        $periodo['hasta'] = $tmpPeriodHasta;
                        */
                }
                
                $conveniosCategoriasHistoricoCondition['ConveniosCategoriasHistorico.convenios_categoria_id'] = $relacion['ConveniosCategoria']['id'];
                
                if ($this->data['Condicion']['Liquidacion-tipo'] !== 'final') {
                    $conveniosCategoriasHistoricoCondition['ConveniosCategoriasHistorico.desde <='] = $periodo['desde'];
                    $conveniosCategoriasHistoricoCondition['OR'] = array(
                        'ConveniosCategoriasHistorico.hasta >=' => $periodo['hasta'],
                        'ConveniosCategoriasHistorico.hasta'    => '0000-00-00');
                } else {
                    $conveniosCategoriasHistoricoCondition['ConveniosCategoriasHistorico.desde <='] = $relacion['Relacion']['egreso'];
                    $conveniosCategoriasHistoricoCondition['OR'] = array(
                        'ConveniosCategoriasHistorico.hasta >=' => $relacion['Relacion']['egreso'],
                        'ConveniosCategoriasHistorico.hasta'    => '0000-00-00');
                }
                
				$historico = $this->Liquidacion->Relacion->ConveniosCategoria->ConveniosCategoriasHistorico->find('first',
					array(
						'recursive'	 	=> -1,
	  					'checkSecurity'	=> false,
                        'order'         => 'ConveniosCategoriasHistorico.id',
						'conditions' 	=> $conveniosCategoriasHistoricoCondition));

				if (!empty($historico)) {
					$relacion['ConveniosCategoria']['costo'] = $historico['ConveniosCategoriasHistorico']['costo'];
				} else {
					$relacion['ConveniosCategoria']['costo'] = 0;
				}
				$this->Liquidacion->getReceipt($relacion, $periodo, $variables['#tipo_liquidacion']['valor'], $opciones);
			}
			$condiciones = array('Liquidacion.estado' => 'Sin Confirmar');
			$this->data['Condicion']['Liquidacion-estado'] = 'Sin Confirmar';			
		} else {
			$condiciones = $this->Paginador->generarCondicion();
			if ($periodo !== false) {
				$condiciones['Liquidacion.mes'] = $periodo['mes'];
				$condiciones['Liquidacion.ano'] = $periodo['ano'];
				$condiciones['Liquidacion.periodo'] = $periodo['periodo'];
			}
		}

		/** Take care of filtering saved or unconfirmed receipt */
		if (empty($condiciones['Liquidacion.estado'])) {
			$condiciones = array_merge($condiciones, array('Liquidacion.estado' => array('Guardada', 'Sin Confirmar')));
			$this->data['Condicion']['Liquidacion-estado'] = array('Guardada', 'Sin Confirmar');
		}

        $this->Liquidacion->setSecurityAccess('readOwnerOnly');
		$this->Liquidacion->contain(array(
				'Relacion.Trabajador',
				'Relacion.Empleador',
	 			'LiquidacionesError'));
        $this->paginate = array_merge($this->paginate, array('limit' => 15));
		$resultados = $this->Paginador->paginar(
			$condiciones,
			array('Liquidacion.periodo_largo', 'Liquidacion.periodo_vacacional'));
		
		$this->set('states', array('Guardada' => 'Guardada', 'Sin Confirmar' => 'Sin Confirmar'));
		$this->set('registros', $resultados['registros']);
	}


/**
 * Agrega datos que seran guardados en la tabla liquidaciones_auxiliares.
 *
 * @param array $auxiliar Los datos a guardar.
 * @return void.
 * @access private.
 */
	function guardar($id = null) {
		if (empty($id)) {
			if (!empty($this->params['data']['seleccionMultiple'])) {
				$id = $this->Util->extraerIds($this->params['data']['seleccionMultiple']);
			}
		}
		$this->Liquidacion->unbindModel(array('belongsTo' => array('Trabajador', 'Empleador', 'Relacion', 'Factura')));
		if ($this->Liquidacion->updateAll(
				array('Liquidacion.estado' => "'Guardada'"),
				array('Liquidacion.id' => $id))) {
			$this->Session->setFlash(sprintf('Se guardaron correctamente %s liquidacion/es', count($id)), 'ok');
		} else {
			$this->Session->setFlash('No fue posible guardar las liquidaciones seleccionadas', 'error');
		}
		$this->autorender = false;
	}


/**
 * recibo_html.
 * Muestra via desglose el recibo (detalle) de la preliquidacion.
 */
	function recibo_html($id = null) {
        $this->Liquidacion->setSecurityAccess('readOwnerOnly');
		$this->Liquidacion->contain('LiquidacionesDetalle');
		$this->data = $this->Liquidacion->read(null, $id);
	}

/**
 * recibo_html.
 * Muestra via desglose el recibo (detalle) de la preliquidacion.
 */
	function imprimir($id = null) {

        if ((!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] === 'preimpreso')
        || (!empty($this->params['named']['tipo']) && $this->params['named']['tipo'] === 'preimpreso')) {
            $render = 'recibo_excel_preimpreso';
        } else {
            $render = 'recibo_excel';
        }

		if (empty($id)) {
			if (!empty($this->params['data']['seleccionMultiple'])) {
				$id = $this->Util->extraerIds($this->params['data']['seleccionMultiple']);
			}
		}

		$this->data = null;
		$this->Liquidacion->contain('LiquidacionesDetalle');
		$this->Liquidacion->Empleador->Suss->contain('Banco');
		foreach ($this->Liquidacion->find('all', array('order' => array('Liquidacion.trabajador_apellido', 'Liquidacion.trabajador_nombre'), 'conditions' => array('Liquidacion.id' => $id))) as $receipt) {

            $ano = $receipt['Liquidacion']['ano'];
            $mes = $receipt['Liquidacion']['mes'];
            if ($receipt['Liquidacion']['mes'] == 1) {
                $ano--;
                $mes = 12;
            } else {
                $mes--;
            }
            $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
            
			$suss = $this->Liquidacion->Empleador->Suss->find('first',
				array('conditions' => array(
					'Suss.empleador_id' => $receipt['Liquidacion']['empleador_id'],
					'Suss.periodo'		=> $ano . $mes))
			);
			if (!empty($suss)) {
				$this->data[] = array_merge($receipt, $suss);
			} else {
				$this->data[] = $receipt;
			}
		}

        $this->render($render);        
	}

	
/**
 * recibo_html_debug.
 * Muestra via desglose el recibo (detalle) de la preliquidacion con informacion de debug.
 */
	function recibo_html_debug($id) {
        $this->Liquidacion->setSecurityAccess('readOwnerOnly');
		$this->Liquidacion->contain(array('LiquidacionesDetalle'));
		$this->data = $this->Liquidacion->read(null, $id);
	}


/**
 * errores.
 * Muestra via desglose los errores de la preliquidacion.
 */
	function errores($id) {
        $this->Liquidacion->setSecurityAccess('readOwnerOnly');
		$this->Liquidacion->contain(array('LiquidacionesError'));
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
            if (empty($this->data['Condicion']['Siap-empleador_id'])
				&& empty($this->data['Condicion']['Siap-grupo_id'])) {
				$this->Session->setFlash("Debe seleccionar un Empleador o un Grupo por lo menos.", "error");
			} elseif (empty($this->data['Condicion']['Siap-periodo']) || !preg_match('/^(20\d\d)(0[1-9]|1[012])$/', $this->data['Condicion']['Siap-periodo'], $periodo)) {
				$this->Session->setFlash("Debe especificar un periodo valido de la forma AAAAMM.", "error");
			} else {
				$periodo = $this->Util->format($this->data['Condicion']['Siap-periodo'], 'periodo');
				
                $conditions = array('Liquidacion.estado'        => 'Confirmada',
                                    'Liquidacion.ano'           => $periodo['ano'],
                                    'Liquidacion.mes'           => $periodo['mes']);

				if (!empty($this->data['Condicion']['Siap-empleador_id'])) {
                    $conditions['Liquidacion.empleador_id'] = $this->data['Condicion']['Siap-empleador_id'];
				}

                if (!empty($this->data['Condicion']['Siap-grupo_id'])) {
                    $conditions['(Liquidacion.group_id & ' . $this->data['Condicion']['Siap-grupo_id'] . ') >'] = 0;
                }
				
				$liquidaciones = $this->Liquidacion->find('all',
						array(	'checkSecurity'	=> false,
								'contain'		=> array(	'Empleador',
                                        'LiquidacionesDetalle' => array('conditions' => array('LiquidacionesDetalle.concepto_imprimir !=' => 'No')),
										'Relacion' 		=> array('Situacion', 'ConveniosCategoria', 'Modalidad', 'Ausencia' =>
												array('conditions' => array('Ausencia.desde >=' => $periodo['desde'], 'Ausencia.desde <=' => $periodo['hasta']))),
										'Trabajador' 	=> array('ObrasSocial', 'Condicion', 'Siniestrado', 'Localidad')),
								'conditions'	=> $conditions));
				
				if (!empty($liquidaciones)) {

                    $opcionesConcepto = $this->Liquidacion->LiquidacionesDetalle->Concepto->opciones;
                            
                    $ausenciasMotivo = $this->Liquidacion->Relacion->Ausencia->AusenciasMotivo->find('all', array('conditions' => array('NOT' => array('AusenciasMotivo.situacion_id' => null))));
                    $ausenciasMotivo = Set::combine($ausenciasMotivo, '{n}.AusenciasMotivo.id', '{n}.Situacion');
                    
                    
                    $data = ClassRegistry::init('Siap')->findById($this->data['Condicion']['Siap-version']);
                    foreach ($data['SiapsDetalle'] as $k => $v) {
                        $detalles[$v['elemento']] = $v;
                    }
                    
					/** Must sumarize. Can't do in by query because of contain. */
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

                    $remuneraciones = null;
                    $compone = null;
                    $cantidadHorasExtras = null;
					foreach ($liquidacionesOriginal as $liquidacion) {

                        $cantidadHorasExtras[$liquidacion['Liquidacion']['id']] = 0;
                        
                        foreach ($opcionesConcepto['remuneracion'] as $k => $v) {
                            $remuneraciones[$liquidacion['Liquidacion']['id']][$v] = 0;
                        }
                        foreach ($opcionesConcepto['compone'] as $k => $v) {
                            $compone[$liquidacion['Liquidacion']['id']][$k] = 0;
                        }

                        foreach ($liquidacion['LiquidacionesDetalle'] as $detalle) {
                            if (!empty($detalle['concepto_compone'])) {
                                $compone[$liquidacion['Liquidacion']['id']][$detalle['concepto_compone']] += $detalle['valor'];
                                if ($detalle['concepto_compone'] === 'Importe Horas Extras') {
                                    $cantidadHorasExtras[$liquidacion['Liquidacion']['id']] += $detalle['valor_cantidad'];
                                }

                            }
                            if (!empty($detalle['concepto_remuneracion'])) {
                                foreach ($opcionesConcepto['remuneracion'] as $k => $v) {
                                    if ($detalle['concepto_remuneracion'] & (int)$k) {
                                        $remuneraciones[$liquidacion['Liquidacion']['id']][$v] += $detalle['valor'];
                                    }
                                }
                            }
                        }
                        
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
						$campos['c2']['valor'] = $liquidacion['Trabajador']['apellido'] . ' ' . $liquidacion['Trabajador']['nombre'];
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
                        $campos['c14']['valor'] = $remuneraciones[$liquidacion['Liquidacion']['id']]['Remuneracion 1'];
						$campos['c20']['valor'] = $liquidacion['Trabajador']['Localidad']['nombre'];
                        $campos['c21']['valor'] = $remuneraciones[$liquidacion['Liquidacion']['id']]['Remuneracion 2'];
                        $campos['c22']['valor'] = $remuneraciones[$liquidacion['Liquidacion']['id']]['Remuneracion 3'];
						
						/** Viene expresado como una formula. */
                        $campos['c23']['valor'] = $this->Formulador->resolver(str_replace('c23', $remuneraciones[$liquidacion['Liquidacion']['id']]['Remuneracion 4'], $campos['c23']['valor']));
						
						if (!empty($liquidacion['Trabajador']['siniestrado_id'])) {
							$campos['c24']['valor'] = $liquidacion['Trabajador']['Siniestrado']['codigo'];
						}
						if ($liquidacion['Empleador']['corresponde_reduccion'] === "Si") {
							$campos['c25']['valor'] = "S";
						} else {
							$campos['c25']['valor'] = " ";
						}
						if ($liquidacion['Trabajador']['jubilacion'] === "Reparto") {
							$campos['c29']['valor'] = "1";
						} else {
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
							} elseif ($cantidadAusencias > 0) {
								$campos['c' . $campoNumero]['valor'] = $tmp['situacion'];
								$campos['c' . ($campoNumero + 1)]['valor'] = $tmp['dia'];
							}
						}
						
						$campos['c36']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
                        $campos['c37']['valor'] = $compone[$liquidacion['Liquidacion']['id']]['SAC'];
                        $campos['c38']['valor'] = $compone[$liquidacion['Liquidacion']['id']]['Importe Horas Extras'];
                        $campos['c39']['valor'] = $compone[$liquidacion['Liquidacion']['id']]['Plus Zona Desfavorable'];
                        $campos['c40']['valor'] = $compone[$liquidacion['Liquidacion']['id']]['Vacaciones'];
                        $campos['c42']['valor'] = $remuneraciones[$liquidacion['Liquidacion']['id']]['Remuneracion 5'];
						if ($liquidacion['Relacion']['ConveniosCategoria']['nombre'] === 'Fuera de convenio') {
							$campos['c43']['valor'] = '0';
						} else {
							$campos['c43']['valor'] = '1';
						}
                        $campos['c44']['valor'] = $remuneraciones[$liquidacion['Liquidacion']['id']]['Remuneracion 6'];
                        $campos['c46']['valor'] = $compone[$liquidacion['Liquidacion']['id']]['Adicionales'];
                        $campos['c47']['valor'] = $compone[$liquidacion['Liquidacion']['id']]['Premios'];
                        $campos['c48']['valor'] = $remuneraciones[$liquidacion['Liquidacion']['id']]['Remuneracion 8'];
                        $campos['c49']['valor'] = $remuneraciones[$liquidacion['Liquidacion']['id']]['Remuneracion 7'];
                        $campos['c50']['valor'] = $cantidadHorasExtras[$liquidacion['Liquidacion']['id']];
						$campos['c51']['valor'] = $liquidacion['Liquidacion']['no_remunerativo'];
						$lineas[] = $this->__generarRegistro($campos);
					}
					$this->set('archivo', array(
                        'contenido' => implode("\r\n", $lineas),
                        'nombre'    => 'SICOSS_' . $periodo['ano'] . '-' . $periodo['mes'] . '.txt'));
					$this->render('..' . DS . 'elements' . DS . 'txt', 'txt');
				} else {
					$this->Session->setFlash('No se han encontrado liquidaciones confirmadas para el periodo seleccioando segun los criterios especificados.', 'error');
				}
			}
		}
		
		$this->set('grupos', $this->Util->getUserGroups());
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
                if ($campo['tipo'] === 'decimal') {
                    $campo['valor'] = number_format($campo['valor'], 2, ',', '');
                }
				if ($campo['direccion_relleno'] === 'Derecha') {
					$t = str_pad($campo['valor'], $campo['longitud'], $campo['caracter_relleno'], STR_PAD_RIGHT);
				} elseif ($campo['direccion_relleno'] === 'Izquierda') {
					$t = str_pad($campo['valor'], $campo['longitud'], $campo['caracter_relleno'], STR_PAD_LEFT);
				} else {
					$t = $campo['valor'];
				}
                $v[] = substr($t, 0, $campo['longitud']);
			}
		}
		return implode('', $v);
	}


	function asignar_suss_deprecated() {
		if (!empty($this->data['Formulario']['accion'])) {
			if ($this->data['Formulario']['accion'] === 'asignar') {
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
		if (!empty($this->data['Condicion']['Liquidacion-periodo_completo'])) {
			$periodo = $this->Util->format($this->data['Condicion']['Liquidacion-periodo_completo'], 'periodo');
			if (!empty($periodo)) {
				$this->data['Condicion']['Liquidacion-ano'] = $periodo['ano'];
				$this->data['Condicion']['Liquidacion-mes'] = $periodo['mes'];
				$this->data['Condicion']['Liquidacion-periodo'] = $periodo['periodo'];
				unset($this->data['Condicion']['Liquidacion-periodo_completo']);
			}
		}
		$this->paginate['conditions'] = array('Liquidacion.estado' => 'Confirmada');
		parent::index();
	}


	function beforeRender() {
		if ($this->action === 'index') {
			$filters = $this->Session->read('filtros.' . $this->name . '.' . $this->action);
			if (!empty($filters['condiciones']['Liquidacion.ano']) && !empty($filters['condiciones']['Liquidacion.mes']) && !empty($filters['condiciones']['Liquidacion.periodo like'])) {
				$this->data['Condicion']['Liquidacion-periodo_completo'] = $filters['condiciones']['Liquidacion.ano'] . $filters['condiciones']['Liquidacion.mes'] . str_replace('%', '', $filters['condiciones']['Liquidacion.periodo like']);
			}
		}
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

            $this->Liquidacion->setSecurityAccess('readOwnerOnly');
            if ($this->Liquidacion->find('count', array(
                'conditions'    => array(
                    'Liquidacion.id'        => $ids,
                    'Liquidacion.estado'    => array('Sin Confirmar', 'Guardada'),
                    'Liquidacion.total >='  => 0))) != count($ids)) {
                    
                $this->Session->setFlash('Ha seleccionado liquidaciones para confirmar que no pueden ser confirmadas.', 'error');
                $this->History->goBack();
            }
            
            
			/**
			* En la tabla auxiliares tengo un array de los datos listos para guardar.
			* Puede haber campos que deben ser guardados y no tienen valor, estos debo ponerle valor actual,
			* por ejemplo, la fecha del dia que se confirma, y no la del dia que se pre-liquido.
			*/
            $this->Liquidacion->LiquidacionesAuxiliar->setSecurityAccess('readOwnerOnly');
			$auxiliares = $this->Liquidacion->LiquidacionesAuxiliar->find('all',
					array('recursive' => -1, 'conditions' => array('LiquidacionesAuxiliar.liquidacion_id' => $ids)));

			$c = 0;
            $db = ConnectionManager::getDataSource($this->Liquidacion->useDbConfig);
            $db->begin($this);
			$idsAuxiliares = null;
			foreach ($auxiliares as $v) {
				$model = $v['LiquidacionesAuxiliar']['model'];
				$idsAuxiliares[] = $v['LiquidacionesAuxiliar']['id'];
				$save = unserialize($v['LiquidacionesAuxiliar']['save']);
				
                foreach ($save as $campo => $valor) {
					preg_match('/^##MACRO:([a-z_]+)##(.*)$/', $valor, $matches);
					if (!empty($matches[1])) {
						switch($matches[1]) {
							case 'fecha_liquidacion':
								$save[$campo] = date("d/m/Y");
								break;
							case 'liquidacion_id':
								$save[$campo] = $v['LiquidacionesAuxiliar']['liquidacion_id'];
								break;
							case 'concepto_valor':
								$this->Liquidacion->LiquidacionesDetalle->recursive = -1;
                                $this->Liquidacion->LiquidacionesDetalle->setSecurityAccess('readOwnerOnly');
								$concepto = $this->Liquidacion->LiquidacionesDetalle->find('first',
									array('conditions' => array('LiquidacionesDetalle.liquidacion_id' => $v['LiquidacionesAuxiliar']['liquidacion_id'],
										  'LiquidacionesDetalle.concepto_id' => $save['concepto_id'])));
                                if (!empty($save['condition']) && !empty($matches[2])) {
                                    $save[$campo] = $concepto['LiquidacionesDetalle']['valor'] . $matches[2];
                                    unset($save['concepto_id']);
                                } else {
                                    $save[$campo] = $concepto['LiquidacionesDetalle']['valor'];
                                }
								break;
						}
					}
				}

                if (!empty($save['condition'])) {
                    if ($this->Formulador->resolver('=if(' . $save['condition'] . ', true, false)') == false) {
                        $c++;
                        continue;
                    }
                    unset($save['condition']);
                }
				$modelSave = ClassRegistry::init($model);
                /** Just the owner and group can just read. Nobody can edit or delete it */
                $save['permissions'] = '288';
				$save = array($model => $save);
				$modelSave->create($save);
				if ($modelSave->save($save, false)) {
					$c++;
                }
			}
                    
            /** If everything is ok, change state and permission so only owner and group can just read */
			if ($c === count($auxiliares)) {
				$this->Liquidacion->recursive = -1;
				if ($this->Liquidacion->updateAll(array(
                    'estado'        => "'Confirmada'",
                    'permissions'   => "'288'",
                    'modified'      => 'NOW()'),
                        array('Liquidacion.id' => $ids))) {
					/**
					 * Borro de la tabla auxiliar.
					 */
					if (!empty($idsAuxiliares)) {
						$this->Liquidacion->LiquidacionesAuxiliar->recursive = -1;;
						$this->Liquidacion->LiquidacionesAuxiliar->deleteAll(array('LiquidacionesAuxiliar.id' => $idsAuxiliares));
					}
					$db->commit($this);
					$this->Session->setFlash('Se confirmaron correctamente ' . count($ids) . ' liquidacion/es.', 'ok');
				} else {
					$db->rollback($this);
					$this->Liquidacion->__buscarError();
					$this->Session->setFlash('Ocurrio un error al intentar confirmar las liquidaciones.', 'error');
				}
			} else {
				$db->rollback($this);
				$this->Session->setFlash('Ocurrio un error al intentar confirmar las liquidaciones. No se puedieron actualizar los registros relacionados.', 'error');
			}
		}
		$this->History->goBack();
	}
	
}
?>