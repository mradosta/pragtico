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

    /** Must update receipts */
	function beforeDelete($casacade = true) {
        $this->recursive = -1;
        $invoice = $this->findById($this->id);
        if ($invoice['Factura']['estado'] === 'Sin Confirmar') {
            return $this->Liquidacion->updateAll(array('Liquidacion.factura_id' => null), array('Liquidacion.factura_id' => $this->id));
        }
        return false;
    }
    
	function getInvoice($conditions = null) {

		if (empty($conditions)) {
			return false;
		}

        /*
		$conditions = array_merge($conditions,
			array(	'OR' => array(
				'Liquidacion.factura_id' 	=> null,
					array(	'Factura.estado' 				=> 'Sin Confirmar',
							'Liquidacion.factura_id !=' 	=> null))));
		*/
        $conditions = array_merge($conditions, array('Liquidacion.factura_id' => null));
        $data = $this->Liquidacion->find('all',
			array(	'conditions' 	=> $conditions,
					'order' 		=> array('Liquidacion.empleador_id', 'Liquidacion.relacion_area_id'),
				 	'contain'		=> array('Empleador', 'LiquidacionesDetalle', 'Factura')));

		if (!empty($data)) {
			
			$saveMaster = $saveDatails = null;
			$employerId = null;
			$areaId = null;
			$receiptIds = null;
            $count = count($data) - 1;
			foreach ($data as $k => $receipt) {

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
				
                if ($receipt['Empleador']['facturar_por_area'] === 'No'
                    && $employerId !== $receipt['Liquidacion']['empleador_id']) {
					$employerId = $receipt['Liquidacion']['empleador_id'];
					$areaId = null;
                    if ($k > 0) {
						$this->__preSave($employerId, $receiptIds, $areaId, $saveDatails, $conditions);
						$saveMaster = $saveDatails = $receiptIds = null;;
					}
				} else if ($receipt['Empleador']['facturar_por_area'] === 'Si'
                    && $areaId !== $receipt['Liquidacion']['relacion_area_id']) {
                    if ($areaId !== null && !empty($saveDatails)) {
                        $this->__preSave($employerId, $receiptIds, $areaId, $saveDatails, $conditions);
                        $saveMaster = $saveDatails = $receiptIds = null;;
                    }
                    $employerId = $receipt['Liquidacion']['empleador_id'];
                    $areaId = $receipt['Liquidacion']['relacion_area_id'];
                } elseif ($count === $k) {
                    $receiptIds[] = $receipt['Liquidacion']['id'];
                    $this->__preSave($employerId, $receiptIds, $areaId, $saveDatails, $conditions);
                }
                $receiptIds[] = $receipt['Liquidacion']['id'];
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

                        $details[$receipt['trabajador_id']]['Totales'] = array_merge(array(
                                'Liquidado'      => 0,
                                'Remunerativo'   => 0,
                                'No Remunerativo'=> 0,
                                'Beneficios'     => 0), $totals[$receipt['trabajador_id']]);
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
	
}
?>