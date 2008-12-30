<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las formas de pagos
 * con las que se cancelan los pagos que se le realizan a las relaciones laborales.
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
 * La clase encapsula la logica de acceso a datos asociada a las formas de pagos
 * con las que se cancelan los pagos que se le realizan a las relaciones laborales.
 *
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class PagosFormasController extends AppController {


	/**
	* Guarda las posibles formas que soporta un pago.
	*/
	var $formas = array();
	
	function add() {
		if (!empty($this->params['named']['PagosForma.forma']) && $this->params['named']['PagosForma.forma'] == "Cheque") {
			$this->formas = array('Cheque' => 'Cheque');
		}
		unset($this->passedArgs['PagosForma.forma']);
		unset($this->params['named']['PagosForma.forma']);
		
		if (!empty($this->params['named']['PagosForma.pago_id'])) {
			$pagoId = $this->params['named']['PagosForma.pago_id'];
		}
		else if (!empty($this->data['PagosForma']['pago_id'])) {
			$pagoId = $this->data['PagosForma']['pago_id'];
		}
		if (!empty($pagoId)) {
			$this->PagosForma->Pago->recursive = -1;
			$pago = $this->PagosForma->Pago->read(null, $pagoId);
			if ($pago['Pago']['estado'] != "Pendiente") {
				$this->Session->setFlash("El pago seleccionado se encuentra en estado " . $pago['Pago']['estado'] . " y solo a los pagos Pendientes se les puede agregar una forma.", "error");
				$this->History->goBack();
			}
			else {
				parent::add();
			}
		}
		else {
			$this->Session->setFlash("El pago seleccionado no existe.", "error");
			$this->History->goBack();
		}
	}


	function buscar_ultimo_numero_cheque($cuenta_id) {
		$ultimoNumeroCheque = $this->PagosForma->getUltimoNumeroCheque($cuenta_id);
		$this->set("ultimoNumeroCheque", $ultimoNumeroCheque + 1);
	}


	function revertir_pagos_forma($id) {
		if ($this->PagosForma->revertir($id)) {
			$this->Session->setFlash("La forma de pago se revirtio correctamente.", "ok");
		}
		else {
			$errores = $this->$this->PagosForma->getError();
			$this->Session->setFlash("No fue posible revertir la forma de pago.", "error", array("errores"=>$errores));
		}
		$this->History->goBack(2);
	}
	
	
/**
 * Realiza los seteos especificos (valores por defecto) al agregar y/o editar.
 */
	function __seteos() {
		if (!empty($this->params['named']['PagosForma.pago_id'])) {
			$pagoId = $this->params['named']['PagosForma.pago_id'];
		}
		else if (!empty($this->data['PagosForma']['pago_id'])) {
			$pagoId = $this->data['PagosForma']['pago_id'];
		}

		if (!empty($pagoId) && empty($this->data['PagosForma']['monto'])) {
			$this->PagosForma->Pago->contain("PagosForma");
			$total = 0;
			$pago = $this->PagosForma->Pago->read(null, $pagoId);
			if (!empty($pago['PagosForma'])) {
				foreach ($pago['PagosForma'] as $v) {
					$total += $v['monto'];
				}
			}
			$this->data['PagosForma']['monto'] = $pago['Pago']['monto'] - $total;
			$this->data['PagosForma']['pago_monto'] = $pago['Pago']['monto'];
			$this->data['PagosForma']['pago_acumulado'] = $total;
			$usuario = $this->Session->read("__Usuario");
			if ($this->action == "add" && $usuario['Usuario']['grupo_default_id'] > 0) {
				if (empty($this->data['PagosForma']['empleador_id'])) {
					$this->data['PagosForma']['empleador_id'] = $usuario['Usuario']['grupo_default']['empleador_id'];
					$this->data['PagosForma']['empleador_id__'] = $usuario['Usuario']['grupo_default']['Empleador']['cuit'] . " - " . $usuario['Usuario']['grupo_default']['Empleador']['nombre'];
				}
				if (empty($this->data['PagosForma']['fecha_pago'])) {
					$this->data['PagosForma']['fecha_pago'] = $this->Util->dateAdd();
				}
			}
			if (empty($this->formas)) {
				if ($pago['Pago']['moneda'] == "Pesos") {
					$this->formas = array('Deposito' => 'Deposito', 'Cheque' => 'Cheque', 'Efectivo' => 'Efectivo', 'Otro' => 'Otro');
				}
				else {
					$this->formas = array('Beneficios' => 'Beneficios');
				}
			}
		}
		$this->set("formas", $this->formas);
		$this->data['PagosForma']['fecha'] = date("d/m/Y");
	}

}
?>