<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las formas de pagos
 * con las que se cancelan los pagos que se le realizan a las relaciones laborales.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.models
 * @since			Pragtico v 1.0.0
 * @version			1.0.0
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a las formas de pagos
 * con las que se cancelan los pagos que se le realizan a las relaciones laborales.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class PagosForma extends AppModel {


	var $chequearBeforeSave = true;

	var $validate = array(
        'fecha' => array(
			array(
				'rule'	=> VALID_DATE, 
				'message'	=>'Debe especificar una fecha valida.')
        ),
        'forma' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar una forma de pago.')
        ),
        'cbu_numero' => array(
			array(
				'rule'	=> array('minLength', 22),
				'allowEmpty' => true,
				'message'=>'Debe ingresar los 22 numeros del CBU.'),
			array(
				'rule'	=> 'validarCbu',
				'message'	=>'El Cbu ingresado no es valido.')
        ),
        'cheque_numero' => array(
			array(
				'rule'	=> array('minLength', 8),
				'allowEmpty' => true,
				'message'=>'Debe ingresar los 8 numeros del cheque.')
        ),
        'monto' => array(
			array(
				'rule'	=> VALID_NUMBER_MAYOR_A_CERO, 
				'message'	=>'Debe especificar un monto mayor a cero.')
        )
	);

	var $belongsTo = array(	'Pago' =>
                        array('className'    => 'Pago',
                              'foreignKey'   => 'pago_id'),
							'Cuenta' =>
                        array('className'    => 'Cuenta',
                              'foreignKey'   => 'cuenta_id')
                        );


/**
 * Permite revertir una forma de pago.
 */
	function revertir($id) {
		$this->recursive = -1;
		$pagosForma = $this->findById($id);
		$pagosForma['PagosForma']['id'] = null;
		$pagosForma['PagosForma']['monto'] = $pagosForma['PagosForma']['monto'] * -1;
		$this->chequearBeforeSave = false;
		return $this->save($pagosForma, false);
	}


/**
 * Antes de borrar una forma de pago, actualizo el estado del pago.
 */
	function beforeDelete($cascade = true) {
		$pago = $this->findById($this->id);
		if(!empty($pago)) {
			if($this->Pago->save(array("Pago"	=>array("id"		=> $pago['Pago']['id'],
														"estado"	=> "Pendiente")), false)) {
				return parent::beforeDelete($cascade);							
			}
			die;
		}
		return false;
	}
	
	function beforeSave() {

		/**
		* Cuando revierto, no debo ejecutar ningun chequeo.
		*/
		if($this->chequearBeforeSave === false) {
			return true;
		}
		
		/**
		* Cada forma de pago tiene valores que no corresponden, me aseguro de quitarlos.
		*/
		switch($this->data['PagosForma']['forma']) {
			case "Efectivo":
			case "Beneficios":
			case "Otro":
				$this->data['PagosForma']['cheque_numero'] = "0";
				$this->data['PagosForma']['cuenta_id'] = null;
				$this->data['PagosForma']['cbu_numero'] = "0";
				break;
			case "Deposito en Cuenta":
				if(empty($this->data['PagosForma']['cbu_numero'])) {
					$this->invalidate('cbu_numero', "Debe ingresar el Numero de Cbu.");
					return false;
				}
				$this->data['PagosForma']['cheque_numero'] = "0";
				$this->data['PagosForma']['cuenta_id'] = null;
				break;
			case "Cheque":
				if(empty($this->data['PagosForma']['fecha_pago'])) {
					$this->invalidate('fecha_pago', "Debe ingresar La fecha de pago del Cheque.");
					return false;
				}
				if(empty($this->data['PagosForma']['cheque_numero'])) {
					$this->invalidate('cheque_numero', "Debe ingresar el Numero de Cheque.");
					return false;
				}
				if(empty($this->data['PagosForma']['cuenta_id'])) {
					$this->invalidate('cuenta_id', "Debe seleccionar la Cuenta Emisora del Cheque.");
				}
				$this->data['PagosForma']['cbu_numero'] = "0";
				break;
		}
		if(($this->data['PagosForma']['pago_acumulado'] + $this->data['PagosForma']['monto']) > $this->data['PagosForma']['pago_monto']) {
			$this->dbError['errorDescripcion'] = "El monto ingresado ($ " . $this->data['PagosForma']['monto'] . ") mas el acumulado ($ " . $this->data['PagosForma']['pago_acumulado'] . ") supera el Total del Pago ($ " . $this->data['PagosForma']['pago_monto'] . "). Verifique.";
			return false;
		}
		else if(($this->data['PagosForma']['pago_acumulado'] + $this->data['PagosForma']['monto']) == $this->data['PagosForma']['pago_monto']) {
			$save = array("id"=>$this->data['PagosForma']['pago_id'], "estado"=>"Imputado", "permissions"=>"292");
			if(!$this->Pago->save(array("Pago"=>$save))) {
				$this->dbError['errorDescripcion'] = "No fue posible actualizar el estado del Pago.";
				return false;
			}
		}
		/**
		* Permiso de solo lectura.
		*/
		$this->data['PagosForma']['permissions'] = "292";
		return parent::beforeSave();
	}


/**
 * Dada una cuenta, retorna el ultimo numero de cheque emitido desde dicha cuenta.
 */
	function getUltimoNumeroCheque($cuentaId) {
		if(!empty($cuentaId) && is_numeric($cuentaId)) {
			$chequeNumero = $this->find("first", array("conditions" => array("PagosForma.cuenta_id"=>$cuentaId, "PagosForma.forma"=>"Cheque"),
											"fields" => "MAX(cheque_numero) AS cheque_numero", "recursive" => -1, "seguridad"=>false));
			if(!empty($chequeNumero[0]['cheque_numero'])) {
				return $chequeNumero[0]['cheque_numero'];
			}
		}
		return 0;
	}

}
?>