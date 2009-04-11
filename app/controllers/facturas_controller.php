<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a la facturacion.
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
 * La clase encapsula la logica de negocio asociada a la facturacion.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class FacturasController extends AppController {

	//var $uses = array("Factura", "Empleador");
	var $helpers = array('Documento');


	function resumen() {
		//if (!isset($this->data['Resumen']['tipo'])) {
		//	$this->data['Resumen']['tipo'] = "resumido";
		//}
		//d($this->data);
		if (!empty($this->data['Condicion']['Liquidacion-periodo']) && !empty($this->data['Condicion']['Liquidacion-empleador_id'])) {
			$period = $this->Util->format($this->data['Condicion']['Liquidacion-periodo'], 'periodo');
			
			if (!empty($this->data['Condicion']['Liquidacion-grupo_id'])) {
				$this->set('groupParams', ClassRegistry::init('Grupo')->getParams($this->data['Condicion']['Liquidacion-grupo_id']));
				unset($this->data['Condicion']['Liquidacion-grupo_id']);
			}
			
			if (!empty($period)) {
				$this->data['Condicion']['Liquidacion-ano'] = $period['ano'];
				$this->data['Condicion']['Liquidacion-mes'] = $period['mes'];
				$this->data['Condicion']['Liquidacion-periodo'] = $period['periodo'];
				$condiciones = $this->Paginador->generarCondicion($this->data);
				
				$records = $this->Factura->report($condiciones, $this->data['Resumen']['tipo']);

				if (empty($records)) {
					$this->Session->setFlash('No se han encontrado liquidaciones para el periodo seleccioando segun los criterios especificados.', 'error');
				} else {
					$this->set('data', $records);
					//$this->set('fileFormat', $this->data['Condicion']['Liquidacion-formato']);
					$this->layout = 'ajax';
				}
			}
		} else {
			$this->set('grupos', $this->Util->getUserGroups());
		}
		$this->set("tipos", array("summarized" => "Resumido", "detailed" => "Detallado"));
	}
	
	
	function prefacturar() {
		if (!empty($this->data['Condicion']['Liquidacion-periodo_completo'])) {
			$period = $this->Util->format($this->data['Condicion']['Liquidacion-periodo_completo'], 'periodo');

			if (!empty($period)) {
				
				$this->data['Condicion']['Liquidacion-ano'] = $period['ano'];
				$this->data['Condicion']['Liquidacion-mes'] = $period['mes'];
				if ($period['periodo'] !== 'M') {
					$this->data['Condicion']['Liquidacion-periodo'] = $period['periodo'];
				}
				$condiciones = $this->Paginador->generarCondicion($this->data);
				unset($condiciones['Liquidacion.periodo_completo']);
				if (!empty($this->data['Condicion']['Liquidacion-grupo_id'])) {
					$condiciones['(Liquidacion.group_id & ' . $this->data['Condicion']['Liquidacion-grupo_id'] . ') >'] = 0;
					$this->set('groupParams', ClassRegistry::init('Grupo')->getParams($this->data['Condicion']['Liquidacion-grupo_id']));
					unset($condiciones['Liquidacion.grupo_id']);
				}
				
				/** Deletes user's unconfirmed invoices */
				$usuario = $this->Session->read('__Usuario');
				/*
				if (!$this->Factura->deleteAll(array(
					'Factura.user_id' => $usuario['Usuario']['id'],
					'Factura.estado' => 'Sin Confirmar'))) {
					$this->Session->setFlash(__('Can\'t delete previous invoices. Call Administrator', true), 'error');
					$this->redirect(array('action' => 'prefacturar'));
				}
				*/


				if (!$this->Factura->getInvoice($condiciones)) {
					$this->Session->setFlash(__('Can\'t create invoices. Check search criterias', true), 'error');
					$resultados['registros'] = array();
				} else {
					$this->Session->del('filtros.' . $this->name . '.' . $this->action);
					$data = $this->data;
					$this->data = array();
					$resultados = $this->Paginador->paginar(array(
						'Factura.user_id' => $usuario['Usuario']['id'],
						'Factura.estado' => 'Sin Confirmar'));
					$this->data = $data;
				}
			}
			//if (!empty($this->data['Condicion'])) {
			//	$this->data['Condicion']['Liquidacion-periodo'] = $this->data['Condicion']['Liquidacion-ano'] . $this->data['Condicion']['Liquidacion-mes'] . $this->data['Condicion']['Liquidacion-periodo'];
			//}
		} else {
			$resultados = $this->Paginador->paginar(array('Factura.estado' => 'Sin Confirmar'));
		}
		$this->set('registros', $resultados['registros']);
		$this->set('grupos', $this->Util->getUserGroups());
	}
	
/*
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
*/
	
/**
 * Detalles
 * Muestra via desglose los detalles de una factura.
 */
	function detalles($id) {
		$this->Factura->contain("FacturasDetalle.Coeficiente");
		$this->data = $this->Factura->read(null, $id);
	}

}
?>