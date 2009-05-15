<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las facturas.
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
 * La clase encapsula la logica de acceso a datos asociada a las facturas.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Factura extends AppModel {


	var $hasMany = array(	'Liquidacion',
							'FacturasDetalle' =>
                        array('dependent'   => true));

	var $belongsTo = array('Empleador', 'Area');



	function __preSave($employerId, $receiptIds, $areaId, $saveDatailsTmp, $conditions) {
		$total = 0;
		foreach ($saveDatailsTmp as $tmp) {
			$saveDatails[] = $tmp;
			$total += $tmp['total'];
		}
		
		$saveMaster['empleador_id'] = $employerId;
		$saveMaster['area_id'] = $areaId;
		$saveMaster['fecha'] = date('d/m/Y');
		$saveMaster['estado'] = 'Sin Confirmar';
		$saveMaster['total'] = $total;
		$saveMaster['confirmable'] = 'No';
		if ($conditions['Liquidacion.estado'] === 'Confirmada') {
			$saveMaster['confirmable'] = 'Si';
		}
		$saveMaster['ano'] = $conditions['Liquidacion.ano'];
		$saveMaster['mes'] = $conditions['Liquidacion.mes'];
		$saveMaster['periodo'] = 'M';
		if (!empty($conditions['Liquidacion.periodo like'])) {
			$saveMaster['periodo'] = str_replace('%', '', $conditions['Liquidacion.periodo like']);
		}
		$saveMaster['tipo'] = Inflector::humanize($conditions['Liquidacion.tipo']);

		$save = array_merge(array('Factura' => $saveMaster), array('FacturasDetalle' => $saveDatails));
		$this->create($save);
		
		if ($this->saveAll($save)) {
			return $this->Liquidacion->updateAll(array('Liquidacion.factura_id' => $this->id), array('Liquidacion.id' => $receiptIds));
		} else {
			return false;
		}
	}

	
	function getInvoice($conditions = null) {

		if (empty($conditions)) {
			return false;
		}

		$conditions = array_merge($conditions,
			array(	'OR' => array(
				'Liquidacion.factura_id' 	=> null,
					array(	'Factura.estado' 				=> 'Sin Confirmar',
							'Liquidacion.factura_id !=' 	=> null))));
		
		$data = $this->Liquidacion->find('all',
			array(	'conditions' 	=> $conditions,
					'order' 		=> array('Liquidacion.empleador_id', 'Liquidacion.relacion_area_id'),
				 	'contain'		=> array('Empleador', 'LiquidacionesDetalle', 'Factura')));

		if (!empty($data)) {
			
			$saveMaster = $saveDatails = null;
			$employerId = null;
			$areaId = null;
			$receiptIds = null;
			foreach ($data as $receipt) {

				foreach ($receipt['LiquidacionesDetalle'] as $detail) {
					if ($detail['coeficiente_tipo'] !== 'No Facturable' && ($detail['concepto_imprimir'] === 'Si' || ($detail['concepto_imprimir'] === 'Solo con valor') && abs($detail['valor']) > 0)) {
						if (!isset($saveDatails[$detail['coeficiente_nombre']])) {
							$saveDatails[$detail['coeficiente_nombre']]['coeficiente_id'] = $detail['coeficiente_id'];
							$saveDatails[$detail['coeficiente_nombre']]['coeficiente_nombre'] = $detail['coeficiente_nombre'];
							$saveDatails[$detail['coeficiente_nombre']]['coeficiente_tipo'] = $detail['coeficiente_tipo'];
							$saveDatails[$detail['coeficiente_nombre']]['coeficiente_valor'] = $detail['coeficiente_valor'];
							$saveDatails[$detail['coeficiente_nombre']]['subtotal'] = $detail['valor'];
							$saveDatails[$detail['coeficiente_nombre']]['total'] = $detail['valor'] * $detail['coeficiente_valor'];
						} else {
							$saveDatails[$detail['coeficiente_nombre']]['subtotal'] += $detail['valor'];
							$saveDatails[$detail['coeficiente_nombre']]['total'] += $detail['valor'] * $detail['coeficiente_valor'];
						}
					}
				}
				
				$receiptIds[] = $receipt['Liquidacion']['id'];
				if ($employerId !== $receipt['Liquidacion']['empleador_id']
				   	&& $receipt['Empleador']['facturar_por_area'] === 'No') {
					
					$employerId = $receipt['Liquidacion']['empleador_id'];
					$areaId = null;
					if (!empty($saveDatails)) {
						$this->__preSave($employerId, $receiptIds, $areaId, $saveDatails, $conditions);
						$saveMaster = $saveDatails = $receiptIds = null;;
					}
				} else if ($receipt['Empleador']['facturar_por_area'] === 'Si') {
					$employerId = $receipt['Liquidacion']['empleador_id'];
					if ($areaId !== $receipt['Liquidacion']['relacion_area_id']) {
						$areaId = $receipt['Liquidacion']['relacion_area_id'];
						if (!empty($saveDatails)) {
							$this->__preSave($employerId, $receiptIds, $areaId, $saveDatails, $conditions);
							$saveMaster = $saveDatails = $receiptIds = null;;
						}
					}
				}
			}
			return true;
		} else {
			return false;
		}
	}
	
	
	function report($invoiceId) {

		$invoice = $this->find('first', array(
			'conditions'	=> array('Factura.id' => $invoiceId),
			'contain'		=> array('Empleador', 'FacturasDetalle', 'Liquidacion.LiquidacionesDetalle')));

		$reportData = null;
		$reportData['Facturado Remunerativo'] = 0;
		$reportData['Facturado No Remunerativo'] = 0;
		$reportData['Facturado Beneficios'] = 0;
		$reportData['Liquidado Remunerativo'] = 0;
		$reportData['Liquidado No Remunerativo'] = 0;
				
		if (!empty($invoice)) {
			foreach ($invoice['Liquidacion'] as $receipt) {

				$trabajador = null;
				foreach ($receipt['LiquidacionesDetalle'] as $detail) {

					if ($detail['coeficiente_tipo'] !== 'No Facturable' && ($detail['concepto_imprimir'] === 'Si' || ($detail['concepto_imprimir'] === 'Solo con valor') && abs($detail['valor']) > 0)) {

						if (empty($trabajador)) {
							$details[$receipt['trabajador_id']]['Trabajador'] = array(
								'legajo'	=> $receipt['relacion_legajo'],
								'nombre'	=> $receipt['trabajador_nombre'],
								'apellido'	=> $receipt['trabajador_apellido']);
						}

						$t = $detail['valor'] * $detail['coeficiente_valor'];
						$t1 = $t2 = $t3 = 0;
						if (!isset($totals[$receipt['trabajador_id']]['Liquidado'])) {
							$totals[$receipt['trabajador_id']]['Liquidado'] = $detail['valor'];
						} else {
							$totals[$receipt['trabajador_id']]['Liquidado'] += $detail['valor'];
						}
						if ($detail['concepto_pago'] === 'Beneficios') {
							if (!isset($totals[$receipt['trabajador_id']]['Beneficios'])) {
								$totals[$receipt['trabajador_id']]['Beneficios'] = $t;
							} else {
								$totals[$receipt['trabajador_id']]['Beneficios'] += $t;
							}
							$t3 = $t;
							$reportData['Facturado Beneficios'] += $t;
						} elseif ($detail['concepto_tipo'] === 'Remunerativo') {
							if (!isset($totals[$receipt['trabajador_id']]['Remunerativo'])) {
								$totals[$receipt['trabajador_id']]['Remunerativo'] = $t;
							} else {
								$totals[$receipt['trabajador_id']]['Remunerativo'] += $t;
							}
							$t1 = $t;
							$reportData['Facturado Remunerativo'] += $t;
							$reportData['Liquidado Remunerativo'] += $detail['valor'];
						} elseif ($detail['concepto_tipo'] === 'No Remunerativo') {
							if (!isset($totals[$receipt['trabajador_id']]['No Remunerativo'])) {
								$totals[$receipt['trabajador_id']]['No Remunerativo'] = $t;
							} else {
								$totals[$receipt['trabajador_id']]['No Remunerativo'] += $t;
							}
							$t2 = $t;
							$reportData['Facturado No Remunerativo'] += $t;
							$reportData['Liquidado No Remunerativo'] += $detail['valor'];
						}

						$details[$receipt['trabajador_id']]['Concepto'][$detail['concepto_codigo']] = array(
							'Descripcion'				=> $detail['concepto_nombre'],
							'Cantidad'					=> $detail['valor_cantidad'],
							'Liquidado'					=> $detail['valor'],
							'Facturado Remunerativo'	=> $t1,
							'Facturado No Remunerativo'	=> $t2,
							'Facturado Beneficios'		=> $t3);

						$details[$receipt['trabajador_id']]['Totales'] = $totals[$receipt['trabajador_id']];
					}
				}
			}

			$reportData['Total de Empleados Facturados'] = count($details);
			$reportData['Iva'] = ($reportData['Facturado No Remunerativo'] + $reportData['Facturado Remunerativo'] + $reportData['Facturado Beneficios']) * 21 / 100;
			$reportData['Total'] = $reportData['Facturado No Remunerativo'] + $reportData['Facturado Remunerativo'] + $reportData['Facturado Beneficios'] + $reportData['Iva'];
			$reportData['Total Liquidado'] = $reportData['Liquidado Remunerativo'] + $reportData['Liquidado No Remunerativo'];

			return array(	'invoice'	=> $invoice['Factura'],
						 	'employer' 	=> $invoice['Empleador'],
						 	'details' 	=> $details,
	   						'totals' 	=> $reportData);
		} else {
			return array();
		}
	}
	
	//function report($conditions = null, $type = 'summarized') {
		function reportx($conditions = null, $type = 'summarized') {

		if ($type === 'summarized') {
			$contain = array('Empleador', 'FacturasDetalle');
		} elseif ($type === 'detailed') {
			$contain = array('Empleador', 'FacturasDetalle', 'Liquidacion.LiquidacionesDetalle');
		} else {
			return false;
		}

		$data = $this->find('all', array(
			'conditions'	=> $conditions,
			'contain'		=> $contain));

		if (!empty($data) && $type == 'summarized') {
			$result['remunerativo_a_facturar'] = 0;
			$result['no_remunerativo_a_facturar'] = 0;

			foreach($data as $invoice) {
				foreach ($invoice['FacturasDetalle'] as $detail) {
					if ($type === 'summarized') {
						if ($detail['coeficiente_tipo']  === 'Remunerativo') {
							$result['remunerativo_a_facturar'] += $detail['subtotal'];
						} elseif ($detail['coeficiente_tipo']  === 'No Remunerativo') {
							$result['no_remunerativo_a_facturar'] += $detail['subtotal'];
						}
					}
				}
			}
			return array('Type' => $type, 'Total' => $result, 'Empleador' => $data[0]['Empleador']);
		} elseif (!empty($data) && $type == 'detailed'){
			foreach ($data as $invoice) {				
				foreach ($invoice['Liquidacion'] as $receipt) {
					$trabajador = null;
					foreach ($receipt['LiquidacionesDetalle'] as $detail) {

						if ($detail['coeficiente_tipo'] !== 'No Facturable' && ($detail['concepto_imprimir'] === 'Si' || ($detail['concepto_imprimir'] === 'Solo con valor') && abs($detail['valor']) > 0)) {

							//$detail['coeficiente_tipo']
									
							if (empty($trabajador)) {
								$details[$receipt['trabajador_id']]['Trabajador'] = array(
									'legajo'	=> $receipt['relacion_legajo'], 
									'nombre'	=> $receipt['trabajador_nombre'],
									'apellido'	=> $receipt['trabajador_apellido']);
							}
							$details[$receipt['trabajador_id']]['Concepto'][$detail['concepto_codigo']] = array(
								'Descripcion'		=> $detail['concepto_nombre'],
								'Cantidad'			=> $detail['valor_cantidad'],
								'Monto Liq.'		=> $detail['valor'],
								'Fact. Rem.'		=> ($detail['valor'] * $detail['coeficiente_valor']),
								'Fact. No Rem.'		=> ($detail['valor'] * $detail['coeficiente_valor']),
								'Fact. Benef.'		=> ($detail['valor'] * $detail['coeficiente_valor']));
							$details[$receipt['trabajador_id']]['Totales'] = array(
								'title'				=> 'Totales del Empleado',
								'S. Bruto'			=> 0, 
								'Total Fact.'		=> 0, 
								'Total Fact. NR'	=> 0,
								'Total Fact. TK'	=> 0);
						}
					}
				}
			}
			d(array('Type' => $type, 'Details' => $details));
			return array('Type' => $type, 'Details' => $details);
		}else {
			return array();
		}
	}
	

	

	function prefacturar_deprecated($condiciones = null) {

		/**
		* Adecuo las condiciones.
		*/
		if ($periodo = $this->format($condiciones['Condicion']['Liquidacion-periodo'], 'periodo')) {
			$conditions['Liquidacion.mes'] = $periodo['mes'];
			$conditions['Liquidacion.ano'] = $periodo['ano'];
			unset($condiciones['Condicion']['Liquidacion-periodo']);
		} else {
			return false;
		}
		
		$empleadores = Set::extract("/Empleador/id", $this->Liquidacion->Empleador->find("all", 
				array("recursive" 	=> -1,
					"conditions" 	=> array(
							"(Empleador.group_id & " . $condiciones['Condicion']['Liquidacion-grupo_id'] . ") >" => 0)
					)));
		if (!empty($empleadores)) {
			unset($condiciones['Condicion']['Liquidacion-grupo_id']);
			$conditions['Liquidacion.empleador_id'] = $empleadores;
		} else {
			return false;
		}
		
		if (!empty($condiciones['Condicion']['Liquidacion-estado'])) {
			$conditions['Liquidacion.estado'] = $condiciones['Condicion']['Liquidacion-estado'];
		} else {
			return false;
		}
		
		/*
		d($this->Liquidacion->find('all', 
		  		array(	'conditions'		=> $conditions,
					  	'checkSecurity'		=> false,
					  	'contain'			=> array('LiquidacionesDetalle' => 
								array('conditions' => array("LiquidacionesDetalle.concepto_imprimir" => 
										array("Si", "Solo con valor")))))));
							  
		*/
			//array("OR"=>array(	"LiquidacionesDetalle.concepto_imprimir" => array("Si", "Solo con valor"))));		
		
		$query['fields']	= array("Liquidacion.id",
									"Empleador.id",
									"Empleador.nombre",
									"Coeficiente.id",
									"Coeficiente.nombre",
									"SUM(LiquidacionesDetalle.valor) AS subtotal",
									"IF(ISNULL(EmpleadoresCoeficiente.valor), Coeficiente.valor, Coeficiente.valor + EmpleadoresCoeficiente.valor) AS valor",
									"SUM(LiquidacionesDetalle.valor) * (IF(ISNULL(EmpleadoresCoeficiente.valor), Coeficiente.valor, Coeficiente.valor + EmpleadoresCoeficiente.valor)) AS total",);
		$query['group']		= array("Empleador.id", "Empleador.nombre", "Coeficiente.id", "Coeficiente.nombre");
		$query['order']		= array("Empleador.id");
		$query['joins']		= array(array(
									"table" => "liquidaciones_detalles",
									"type" 	=> "INNER"
								),array(
									"table" => "empleadores",
									"type" 	=> "INNER",
									"conditions"=> array("Empleador.id"=>DboSource::identifier("Liquidacion.empleador_id"))
								),array(
									"table" 	=> "coeficientes",
									"type" 		=> "LEFT",
									"conditions"=> array("Coeficiente.id"=>DboSource::identifier("LiquidacionesDetalle.coeficiente_id"))
								),array(
									"table" 	=> "empleadores_coeficientes",
									"type" 		=> "LEFT",
									"conditions"=> array("EmpleadoresCoeficiente.empleador_id"=>DboSource::identifier("Liquidacion.empleador_id"),
														 "EmpleadoresCoeficiente.coeficiente_id"=>DboSource::identifier("LiquidacionesDetalle.coeficiente_id"))
								));
		$query['conditions']= array_merge(	$conditions,
									array("OR"=>array(	"LiquidacionesDetalle.concepto_imprimir" => array("Si", "Solo con valor"))));

		$r = $this->query($this->generarSql($query, $this->Liquidacion));
		if (!empty($r)) {
			$niveles[0] = array("model" => "Empleador", "field" => "id");
			$niveles[1] = array("model" => "Coeficiente", "field" => "id");
			$r = $this->mapToKey($r, array("keyLevels"=>$niveles, "valor"=>array("model" => "0")));
			$ids = array();
			foreach ($r as $empleadorId => $v) {
				$saveEncabezado = null;
				$saveDetalle = null;
				$c = $total = 0;
				$saveEncabezado['fecha'] = date("d/m/Y");
				$saveEncabezado['empleador_id'] = $empleadorId;
				$saveEncabezado['estado'] = "Sin Confirmar";
				foreach ($v as $coeficienteId => $valores) {
					$saveDetalle[$c]['coeficiente_id'] = $coeficienteId;
					$saveDetalle[$c]['subtotal'] = $valores['subtotal'];
					$saveDetalle[$c]['valor'] = $valores['valor'];
					$saveDetalle[$c]['total'] = $valores['total'];
					$total+= $valores['total'];
					$c++;
				}
				$saveEncabezado['total'] = $total;
				//d($this->save(array("Factura"=>$saveEncabezado, "FacturasDetalle"=>$saveDetalle)));
				if ($saveEstado = $this->saveAll(array("Factura"=>$saveEncabezado, "FacturasDetalle"=>$saveDetalle))) {
					$ids[] = $this->id;
				}
			}
			return $ids;
		}
		return false;
	}

}
?>