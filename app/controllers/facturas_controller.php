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


	function reporte($facturaId) {

		$records = $this->Factura->report($facturaId);
		if (empty($records)) {
			$this->Session->setFlash('No se han encontrado facturas para el periodo seleccioando segun los criterios especificados.', 'error');
		} else {
			$this->set('data', $records);
			//$this->set('fileFormat', $this->data['Condicion']['Liquidacion-formato']);
			$this->set('groupParams', ClassRegistry::init('Grupo')->getParams($records['invoice']['group_id']));
			$this->layout = 'ajax';
		}
	}
	

	function prefacturar() {

		if (!empty($this->data)) {
			
			if (!empty($this->data['Formulario']['accion'])) {
				
				$data = $this->data;
				
				if (!empty($this->data['Condicion']['Liquidacion-periodo_completo'])
				   	&& ($period = $this->Util->format($this->data['Condicion']['Liquidacion-periodo_completo'], 'periodo'))) {
					unset($this->data['Condicion']['Liquidacion-periodo_completo']);

					if ($this->data['Formulario']['accion'] === 'generar') {
						$this->data['Condicion']['Liquidacion-ano'] = $period['ano'];
						$this->data['Condicion']['Liquidacion-mes'] = $period['mes'];
						if ($period['periodo'] !== 'M') {
							$this->data['Condicion']['Liquidacion-periodo'] = $period['periodo'];
						}

						if (!empty($this->data['Condicion']['Liquidacion-grupo_id'])) {
							$condiciones['(Liquidacion.group_id & ' . $this->data['Condicion']['Liquidacion-grupo_id'] . ') >'] = 0;
							$this->set('groupParams', ClassRegistry::init('Grupo')->getParams($this->data['Condicion']['Liquidacion-grupo_id']));
							unset($this->data['Condicion']['Liquidacion-grupo_id']);
						}

						$condiciones = $this->Paginador->generarCondicion($this->data);

						/** Delete user's unconfirmed Invoices */
						//$usuario = $this->Session->read('__Usuario');
						//$this->Factura->deleteAll(array('Factura.user_id' => $usuario['Usuario']['id'], 'Factura.estado' => 'Sin Confirmar'));
						if (!$this->Factura->getInvoice($condiciones)) {
							$this->Session->setFlash(__('Can\'t create invoices. Check search criterias', true), 'error');
							$resultados['registros'] = array();
						}
					} elseif ($this->data['Formulario']['accion'] === 'buscar') {
						
						$this->data['Condicion']['Factura-fecha__desde'] = $this->Util->format($period['desde'], 'date');
						$this->data['Condicion']['Factura-fecha__hasta'] = $this->Util->format($period['hasta'], 'date');

						if (!empty($this->data['Condicion']['Liquidacion-empleador_id'])) {
							$this->data['Condicion']['Factura-empleador_id'] = $this->data['Condicion']['Liquidacion-empleador_id'];
							unset($this->data['Condicion']['Relacion-empleador_id']);
						}
						unset($this->data['Condicion']['Liquidacion-empleador_id']);
						unset($this->data['Condicion']['Liquidacion-grupo_id']);
						unset($this->data['Condicion']['Liquidacion-estado']);
						unset($this->data['Condicion']['Liquidacion-tipo']);
						$condiciones = $this->Paginador->generarCondicion($this->data);
						$condiciones['Factura.estado'] = 'Sin Confirmar';
						$resultados = $this->Paginador->paginar($condiciones);

					} elseif ($this->data['Formulario']['accion'] === 'limpiar') {
						$resultados = $this->Paginador->paginar(array('Factura.estado' => 'Sin Confirmar'));
						$data = array();
					}
				} else {
					if (!empty($this->data['Formulario']['accion'])
						&& $this->data['Formulario']['accion'] === 'generar') {
						$this->Session->setFlash(__('Must enter a valid period', true), 'error');
						$resultados['registros'] = array();
					}
				}
			}
		}
		if (!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] === 'limpiar') {
			$this->data = array();
		}
		$this->data['Condicion']['Liquidacion-estado'] = 'Confirmada';
		
		$this->Factura->contain(array('Empleador', 'Area'));
		if (!isset($resultados)) {
			$resultados = $this->Paginador->paginar(array('Factura.estado' => 'Sin Confirmar'), array(), false);
		}
		
		if (!empty($data)) {
			$this->data = $data;
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


	function confirmar() {
		$ids = $this->Util->extraerIds($this->data['seleccionMultiple']);
		
		if (!empty($ids)) {
			$this->Factura->unbindModel(array('belongsTo' => array('Empleador')));
			if ($this->Factura->updateAll(array('Factura.estado' => "'Confirmada'"), array('Factura.id' => $ids, 'Factura.confirmable' => 'Si'))) {
				$this->Session->setFlash('Las facturas seleccionadas se confirmaron correctamente', 'ok');
			} else {
				$this->Session->setFlash('No pudieron confirmarse las facturas. Verifique.', 'error');
			}
		}
		$this->History->goBack();
	}
	
	function index() {
		$this->paginate['conditions'] = array('Factura.estado' => 'Confirmada');
		parent::index();
	}

}
?>