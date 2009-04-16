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

	var $belongsTo = array(	'Empleador' =>
                        array('className'   => 'Empleador',
                              'foreignKey' 	=> 'empleador_id'));



	function __getSaveArray($employerId, $saveDatailsTmp, $confirmable) {
		$total = 0;
		foreach ($saveDatailsTmp as $tmp) {
			$saveDatails[] = $tmp;
			$total += $tmp['total'];
		}

		$saveMaster['empleador_id'] = $employerId;
		$saveMaster['fecha'] = date('d/m/Y');
		$saveMaster['estado'] = 'Sin Confirmar';
		$saveMaster['total'] = $total;
		$saveMaster['confirmable'] = $confirmable;
		return array_merge(array('Factura' => $saveMaster), array('FacturasDetalle' => $saveDatails));
	}

	
	function getInvoice($conditions = null) {

		if (empty($conditions)) {
			return false;
		}

		$confirmable = 'No';
		if ($conditions['Liquidacion.estado'] === 'Confirmada') {
			$confirmable = 'Si';
		}
		
		$conditions = array_merge($conditions, array('Liquidacion.factura_id' => null));
		$data = $this->Liquidacion->find('all',
			array(	'conditions' 	=> $conditions,
					'order' 		=> array('Liquidacion.empleador_id'),
				 	'contain'		=> array('LiquidacionesDetalle')));

		if (!empty($data)) {
			
			$saveMaster = $saveDatails = null;
			$employerId = null;
			$receipts = null;
			foreach ($data as $receipt) {

				$receipts[] = $receipt['Liquidacion']['id'];

				if ($employerId !== $receipt['Liquidacion']['empleador_id']) {
					$employerId = $receipt['Liquidacion']['empleador_id'];
					if (!empty($saveDatailsTmp)) {
						$save[] = $this->__getSaveArray($employerId, $saveDatails, $confirmable);
						$saveMaster = $saveDatails = null;
					}
				}

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
			}
			$save[] = $this->__getSaveArray($employerId, $saveDatails, $confirmable);
		} else {
			return false;
		}

		if ($this->appSave($save)) {
			return $this->Liquidacion->updateAll(array('Liquidacion.factura_id' => $this->id), array('Liquidacion.id' => $receipts));
		} else {
			return false;
		}
	}
	
	//function report($conditions = null, $type = 'summarized') {
		function report($conditions = null, $type = 'summarized') {

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

							$detail['coeficiente_tipo']
									
							if (empty($trabajador)) {
								$details[$receipt['trabajador_id']]['Trabajador'] = array(
									'legajo'	=> $receipt['relacion_legajo'], 
									'nombre'	=> $receipt['trabajador_nombre'],
									'apellido'	=> $receipt['trabajador_apellido']);
							}
							$details[$receipt['trabajador_id']]['Concepto'][$detail['concepto_codigo']] = array(
								'Descripcion'		=> $detail['concepto_nombre'],
								'Cantidad'			=> $detail['valor_cantidad'],
								'V. Unit.'			=> 0,
								'S. Bruto.'			=> $detail['valor'],
								'Total Fact.'		=> ($detail['valor'] * $detail['coeficiente_valor']),
								'Total Fact. NR'	=> 0,
								'Total Fact. TK'	=> 0);
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
	

	
	function resumenx($condiciones = null, $tipo = "resumido") {
		
		if ($tipo == "resumido") {
			$sql = "
				select
							Empleador.id,
							Empleador.cuit,
							Empleador.nombre,
							LiquidacionesDetalle.concepto_codigo,
							LiquidacionesDetalle.concepto_nombre,
							LiquidacionesDetalle.concepto_orden,
							LiquidacionesDetalle.coeficiente_valor,
							sum(LiquidacionesDetalle.valor) as total,
							sum(LiquidacionesDetalle.valor_cantidad) as cantidad,
							count(1) as cuenta
				from 		empleadores Empleador,
							liquidaciones Liquidacion,
							liquidaciones_detalles LiquidacionesDetalle 
				where		1=1
				and			Liquidacion.id = LiquidacionesDetalle.liquidacion_id
				and			Empleador.id = Liquidacion.empleador_id
				and			(LiquidacionesDetalle.concepto_imprimir = 'Si'
							or LiquidacionesDetalle.concepto_imprimir = 'Solo con valor')
				and			";
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			$sql .= $db->conditions($condiciones, true, false);
			$sql .= " group by
							Empleador.id,
							Empleador.cuit,
							Empleador.nombre,
							LiquidacionesDetalle.concepto_codigo,
							LiquidacionesDetalle.concepto_nombre,
							LiquidacionesDetalle.concepto_orden,
							LiquidacionesDetalle.coeficiente_valor
				order by	Empleador.nombre,
							LiquidacionesDetalle.concepto_orden
			";

			$r = $this->query($sql);
			foreach ($r as $v) {
				$data = null;
				$data['nombre'] = $v['LiquidacionesDetalle']['concepto_nombre'];
				$data['coeficiente'] = $v['LiquidacionesDetalle']['coeficiente_valor'];
				$data['total'] = $v['0']['total'];
				$data['cantidad'] = $v['0']['cantidad'];

				if (!isset($return[$v['Empleador']['id']])) {
					$return[$v['Empleador']['id']]['cuit'] = $v['Empleador']['cuit'];
					$return[$v['Empleador']['id']]['nombre'] = $v['Empleador']['nombre'];
				}
				$return[$v['Empleador']['id']]['Concepto'][] = $data;
			}
			return array_values($return);
		}
		elseif ($tipo == "detallado") {
			$sql = "
				select
							Empleador.id,
							Empleador.cuit,
							Empleador.nombre,
							Trabajador.id,
							Trabajador.legajo,
							Trabajador.cuil,
							Trabajador.nombre,
							Trabajador.apellido,
							Area.nombre,
							LiquidacionesDetalle.concepto_codigo,
							LiquidacionesDetalle.concepto_nombre,
							LiquidacionesDetalle.concepto_orden,
							LiquidacionesDetalle.coeficiente_valor,
							LiquidacionesDetalle.valor as total,
							LiquidacionesDetalle.valor_cantidad as cantidad
				from 		empleadores Empleador,
							trabajadores Trabajador,
							liquidaciones Liquidacion,
							liquidaciones_detalles LiquidacionesDetalle,
							relaciones Relacion left join areas Area on (Area.id = Relacion.area_id)
				where		1=1
				and			Liquidacion.id = LiquidacionesDetalle.liquidacion_id
				and			Empleador.id = Liquidacion.empleador_id
				and			Trabajador.id = Liquidacion.trabajador_id
				and			Relacion.id = Liquidacion.relacion_id
				and			(LiquidacionesDetalle.concepto_imprimir = 'Si'
							or LiquidacionesDetalle.concepto_imprimir = 'Solo con valor')
				and			";
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			$sql .= $db->conditions($condiciones, true, false);
			$sql .= "
				order by	Empleador.nombre,
							Trabajador.apellido,
							Trabajador.nombre,
							LiquidacionesDetalle.concepto_orden
			";

			$r = $this->query($sql);
			foreach ($r as $v) {
				$data = null;
				$data['nombre'] = $v['LiquidacionesDetalle']['concepto_nombre'];
				$data['coeficiente'] = $v['LiquidacionesDetalle']['coeficiente_valor'];
				$data['total'] = $v['LiquidacionesDetalle']['total'];
				$data['cantidad'] = $v['LiquidacionesDetalle']['cantidad'];

				if (!isset($return[$v['Empleador']['id']])) {
					$return[$v['Empleador']['id']]['cuit'] = $v['Empleador']['cuit'];
					$return[$v['Empleador']['id']]['nombre'] = $v['Empleador']['nombre'];
				}
				if (!isset($return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']])) {
					$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['legajo'] = $v['Trabajador']['legajo'];
					$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['cuil'] = $v['Trabajador']['cuil'];
					$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['apellido'] = $v['Trabajador']['apellido'];
					$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['nombre'] = $v['Trabajador']['nombre'];
					$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['area'] = $v['Area']['nombre'];
				}
				$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['Concepto'][] = $data;
			}
			return array_values($return);
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