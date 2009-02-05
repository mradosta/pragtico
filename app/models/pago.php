<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los pagos que se le realizan a las relaciones laborales.
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
 * La clase encapsula la logica de acceso a datos asociada a los pagos que se le realizan a las relaciones laborales.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Pago extends AppModel {

	var $modificadores = array(	'index' =>
			array('contain'	=> array('Liquidacion', 'PagosForma', 'Relacion'	=> array('Empleador', 'Trabajador'))));
	
	var $order = array('Pago.fecha' => 'desc');

	var $validate = array(
        'fecha' => array(
			array(
				'rule'		=> VALID_DATE, 
				'message'	=> 'Debe especificar una fecha valida.')
        )
	);

	var $belongsTo = array('Relacion', 'Liquidacion');
                              
	var $hasMany = array(	'PagosForma' =>
                        array('className'    => 'PagosForma',
                              'foreignKey'   => 'pago_id'));


/**
 * Calculate balance. Total payment - sum(partial payments).
 */
	function afterFind($results, $primary = false) {
		if ($primary === true && !empty($results[0]['Pago'])) {
			foreach ($results as $k => $result) {
				$results[$k]['Pago']['saldo'] = $results[$k]['Pago']['monto'] - $this->__getPartialPayments($result);
			}
		}
		return $results;
	}
	
	

/**
 *
 */
	function registrarPago($ids, $tipo) {

		$retorno = true;
		$tipo = ucfirst($tipo);
			
		if ($tipo === 'Deposito') {
			$this->contain(array('PagosForma', 'Relacion.Trabajador'));
		} else {
			$this->contain('PagosForma');
		}
		$pagosTmp = $this->find('all', array('conditions'=>array('Pago.id'=>$ids, 'Pago.estado' => 'Pendiente')));
		
		$ids = array();
		foreach ($pagosTmp as $pago) {
			$pagos[$pago['Pago']['id']] = $pago;
			$ids[] = $pago['Pago']['id'];
		}
		$c=0;
		foreach ($ids as $id) {
			if (($pagos[$id]['Pago']['moneda'] === 'Beneficios' && $tipo === 'Beneficios') || $pagos[$id]['Pago']['moneda'] === 'Pesos' && $tipo !== 'Beneficios') {

				
				$acumulado = $this->__getPartialPayments($pagos[$id]);
				/**
				* Determino si tiene la pagos parciales.
				*/
				$acumulado = 0;
				foreach ($pagos[$id]['PagosForma'] as $v) {
					$acumulado += $v['monto'];
				}
				$save = null;
				$save['id'] = null;
				$save['pago_id'] = $id;
				$save['forma'] = $tipo;
				$save['fecha'] = date('d/m/Y');
				$save['fecha_pago'] = date('d/m/Y');
				$save['permissions'] = '292';
				$save['monto'] = $pagos[$id]['Pago']['monto'] - $acumulado;
				/**
				* El beforeSave hara otra validacion, para lo cual necesitara estos datos.
				*/
				$save['pago_monto'] = $pagos[$id]['Pago']['monto'];
				$save['pago_acumulado'] = $acumulado;
				
				if ($tipo == 'Deposito') {
					$save['cbu_numero'] = $pagos[$id]['Relacion']['Trabajador']['cbu'];
				}
				
				/**
				* Cuando un pago esta imputado, ya no permito que sea borrado o modificado.
				*/
				$savePago['permissions'] = '292';
				$savePago['estado'] = 'Imputado';
				$savePago['id'] = $id;
				$this->begin();
				if ($this->save(array('Pago'=>$savePago)) && $this->PagosForma->save(array('PagosForma'=>$save))) {
					$this->commit();
					$c++;
				}
				else {
					$this->rollback();
				}
			}
		}
		return $c;
	}
 

/**
 * Gets the sum of partials payments.
 *
 * @param mixed $payment The payment with all it's partial payments or the paymentId.
 * @return 	double The sum of partial payments.
 *	
 * @access private
 */	
	function __getPartialPayments($payment) {
		if (!is_array($payment)) {
			$this->contain('PagosForma');
			$payment = $this->findById($payment);
		} 
		return array_sum(Set::extract('/PagosForma/monto', $payment));
	}

	
/**
 * Sets the payment state.
 *
 * @param array $payment The payment id.
 * @return 	boolean True on success, false in other case.
 *	
 * @access public
 */	
	function updateState($paymentId) {
		$save = null;
		
		/**
		 * Cuando un pago esta imputado, ya no permito que sea borrado o modificado.
		 */
		$save['id'] = $paymentId;
		$save['permissions'] = '292';
		
		$this->contain('PagosForma');
		$payment = $this->findById($paymentId);
		if ($payment['Pago']['monto'] == $this->__getPartialPayments($payment)) {
			$save['estado'] = 'Imputado';
		} else {
			$save['estado'] = 'Pendiente';
		}
		if ($this->save(array('Pago' => $save))) {
			return true;
		}
		return false;
	}
	
	
/**
 * Permite revertir un pago.
 */
	function revertir($id) {
		$this->begin();
		$save = null;
		$save['id'] = null;
		$save['monto'] = $this->__getPartialPayments($id) * -1;
		$save['forma'] = 'Efectivo';
		$save['observacion'] = 'Este pago ha sido revertido';
		$save['fecha'] = date('d/m/Y');
		$save['pago_id'] = $id;
		
		$this->begin();
		if ($this->PagosForma->save(array('PagosForma' => $save), false) && $this->updateState($id)) {
			$this->commit();
			return true;
		}
		$this->rollBack();
		return false;
	}	
	
	
/**
 * Genera el contenido del archivo para presentar en los bancos para la acreditacion de haberes.
 *
 * @param array $opciones Array con datos requeridos para la generacion del archivo.
 *						Son requeridos 	cuenta_id,
 *										pago_id,
 *										empleador_id
 *						Opcional		fecha_acreditacion
 * @return 	mixed 	array de dos componentes:
 *						contenido, con el contenido del archivo en caso de exito.
 *						banco, con nombre del banco para el cual se genero el archivo.
 *					false en caso de falla.
 * @access public
 */
	function generarSoporteMagnetico($opciones) {
	
		$contenido = $banco = false;
		
		if (!empty($opciones['cuenta_id']) && !empty($opciones['pago_id']) && !empty($opciones['empleador_id'])) {

			//$this->Relacion->Empleador->Cuenta->recursive = 2;
			$this->Relacion->Empleador->Cuenta->contain(array('Empleador', 'Sucursal.Banco'));
			$cuenta = $this->Relacion->Empleador->Cuenta->findById($opciones['cuenta_id']);
			$banco = $cuenta['Sucursal']['Banco']['codigo'];
			$banco = 'Galicia';
			
//BANCO DE GALICIA y BS. AS. S.A.                              007

//BANCO DE LA NACION ARGENTINA                         011 			
			$conditions = array(
					'Pago.estado'		=> 'Pendiente',
	 				'Pago.id'			=> $opciones['pago_id'],
					'Relacion.estado'	=> 'Activa');
	  				
			$pagos =  $this->find('all', 
			  		array(	'contain'		=> array('Relacion.Trabajador'),
						  	'conditions' 	=> $conditions));
			//$pagos = $this->query($sql);
			//$pagos = $this->find('all', array('conditions'=>array('Pago.estado' => 'Pendiente', 'Pago.id'=>$opciones['pago_id'])));
			//d($pagos);
			
			if (!empty($pagos)) {
			
				//$empleador = $this->Relacion->Empleador->findById($opciones['Empleador.id']);
				$total = 0;
				foreach ($pagos as $pago) {

					preg_match('/(\d\d\d)(\d\d\d\d)\d(\d\d\d\d\d\d\d\d\d\d\d\d\d)\d$/', $pago['Relacion']['Trabajador']['cbu'], $matches);
					if (!empty($matches[2]) && !empty($matches[3])) {
					
						$total += number_format($pago['Pago']['monto'], 2, '.', '');					
						switch ($banco) {
							case 'Santander-Rio':
								if ($pago['Relacion']['Trabajador']['tipo_cuenta'] === 'Cta. Cte.') {
									$tipoCuentaTrabajador = '2';
								} elseif ($pago['Relacion']['Trabajador']['tipo_cuenta'] === 'Caja de Ahorro') {
									$tipoCuentaTrabajador = '3';
								}
								$c = null;
								$c[] = '0'; // moneda
								$c[] = str_pad(substr($pago['Relacion']['Trabajador']['apellido'] . ' ' . $pago['Relacion']['Trabajador']['nombre'], 0, 15), 15, ' ', STR_PAD_RIGHT); //nombre
								$c[] = str_replace('-', '', $pago['Relacion']['Trabajador']['cuil']); //cuil
								$c[] = $matches[2]; // Sucursal
								$c[] = $tipoCuentaTrabajador; // Tipo de Cuenta
								$c[] = substr($matches[3], 2); // Cuenta
								$c[] = str_pad(number_format($pago['Pago']['monto'], 2, '', ''), 15, '0', STR_PAD_LEFT); // importe
								$rds[] = implode(';', $c);
								break;
							case 'Galicia':
								if ($pago['Relacion']['Trabajador']['tipo_cuenta'] === 'Cta. Cte.') {
									$tipoCuentaTrabajador = '0';
								}
								elseif ($pago['Relacion']['Trabajador']['tipo_cuenta'] === 'Caja de Ahorro') {
									$tipoCuentaTrabajador = '4';
								}
								//$pago['Relacion']['Trabajador']['cbu'] = '0070278430004005944782';
								//$pago['Relacion']['Trabajador']['cbu'] = '0070278430004005945518';
								$pago['Relacion']['Trabajador']['cbu'] = '0070278430004005946351';
								
								$rd = null;												
								$rd[] = 'D';
								$rd[] = str_pad($cuenta['Cuenta']['convenio'], 5, '0', STR_PAD_LEFT); //Numero de empresa (convenio)
								$rd[] = $tipoCuentaTrabajador; //tipo de cuenta
								$rd[] = str_pad(substr($pago['Relacion']['Trabajador']['cbu'], 13, 6), 6, '0', STR_PAD_LEFT); //folio (cuenta)
								$rd[] = substr($pago['Relacion']['Trabajador']['cbu'], 19, 1); //1 digito
								$rd[] = str_pad(substr($pago['Relacion']['Trabajador']['cbu'], 4, 3), 3, '0', STR_PAD_LEFT); //sucursal
								$rd[] = substr($pago['Relacion']['Trabajador']['cbu'], -2, 1); //2 digito
								$rd[] = strtoupper(str_pad(substr($pago['Relacion']['Trabajador']['apellido'] . ' ' . $pago['Relacion']['Trabajador']['nombre'], 0, 20), 20, ' ', STR_PAD_RIGHT)); //nombre
								$rd[] = str_pad(number_format($pago['Pago']['monto'], 2, '', ''), 14, '0', STR_PAD_LEFT); //importe
								$rd[] = str_pad('1', 2, '0', STR_PAD_LEFT); //concepto
								$rd[] = str_pad('', 11, ' ', STR_PAD_RIGHT); //libre
								$rds[] = implode('', $rd);
								break;
							case 'Nacion':
								$fechaAcreditacion = date('Ymd');
								if (!empty($opciones['fecha_acreditacion'])) {
									preg_match('/(\d\d)\/(\d\d)\/\d\d(\d\d)$/', $opciones['fecha_acreditacion'], $matches);
									if (!empty($matches[1]) && !empty($matches[2]) && !empty($matches[3])) {
										$fechaAcreditacion = $matches[1] . $matches[2] . $matches[3];
									}
								}
								$c = null;
								$c[] = str_pad($matches[2], 4, '0', STR_PAD_LEFT); // Sucursal
								$c[] = str_pad($matches[3], 10, '0', STR_PAD_LEFT); // Nro cuenta
								$c[] = '141'; // nadie sabe que es, pero debe ir este valor
								$c[] = $fechaAcreditacion; // fecha de acreditacion
								$c[] = 'CTRE0'; // nadie sabe que es, pero debe ir este valor
								$c[] = str_pad($pago['Relacion']['Trabajador']['numero_documento'], 8, '0', STR_PAD_LEFT); //dni
								$c[] = str_pad(number_format($pago['Pago']['monto'], 2, '', ''), 13, '0', STR_PAD_LEFT); //importe
								$c[] = str_pad(substr($pago['Relacion']['Trabajador']['apellido'] . ' ' . $pago['Relacion']['Trabajador']['nombre'], 0, 30), 30, ' ', STR_PAD_RIGHT); //nombre
								$c[] = '96'; // nadie sabe que es, pero debe ir este valor
								$c[] = str_pad($pago['Relacion']['Trabajador']['numero_documento'], 8, '0', STR_PAD_LEFT); //dni
								$rds[] = implode('', $c);
								break;
						}
					}
				}
				
				if (!empty($rds)) {
					switch ($banco) {
						case 'Santander-Rio':
						case 'Nacion':
							$contenido = implode("\r\n", $rds);
							break;
						case 'Galicia':
								$fechaAcreditacion = date('Ymd');
								if (!empty($opciones['fecha_acreditacion'])) {
									preg_match('/(\d\d)\/(\d\d)\/(\d\d\d\d)$/', $opciones['fecha_acreditacion'], $matches);
									if (!empty($matches[1]) && !empty($matches[2]) && !empty($matches[3])) {
										$fechaAcreditacion = $matches[3] . $matches[2] . $matches[1];
									}
								}
								if ($cuenta['Cuenta']['tipo'] == 'Cta. Cte.') {
									$tipoCuentaEmpleador = '0';
								} elseif ($cuenta['Cuenta']['tipo'] == 'Caja de Ahorro') {
									$tipoCuentaEmpleador = '9';
								}
								$rh[] = 'H';
								$rh[] = str_pad($cuenta['Cuenta']['convenio'], 5, '0', STR_PAD_LEFT); //Numero de empresa
								$rh[] = $tipoCuentaEmpleador; //tipo de cuenta
								$rh[] = str_pad(substr($cuenta['Cuenta']['cbu'], 13, 6), 6, '0', STR_PAD_LEFT); //folio (cuenta)
								$rh[] = substr($cuenta['Cuenta']['cbu'], 19, 1); //1 digito
								$rh[] = str_pad(substr($cuenta['Cuenta']['cbu'], 4, 3), 3, '0', STR_PAD_LEFT); //sucursal
								$rh[] = substr($cuenta['Cuenta']['cbu'], -2, 1); //2 digito
								$rh[] = str_pad(number_format($total, 2, '', ''), 14, '0', STR_PAD_LEFT); //importe total
								$rh[] = str_pad($fechaAcreditacion, 8, ' ', STR_PAD_RIGHT); //fecha acreditacion
								$rh[] = str_pad('', 25, ' ', STR_PAD_RIGHT); //libre
								$rhs = implode('', $rh);                         
								$rf[] = 'F';
								$rf[] = str_pad($cuenta['Cuenta']['convenio'], 5, '0', STR_PAD_LEFT); //Numero de empresa
								$rf[] = str_pad(count($rds), 7, '0', STR_PAD_LEFT); //cantidad registros
								$rf[] = str_pad('', 52, ' ', STR_PAD_RIGHT); //libre
								$rfs = implode('', $rf);

								$contenido = $rhs . "\r\n" . implode("\r\n", $rds) . "\r\n" . $rfs;
							break;
					}
				}
			}
			else {
				return false;
			}
		}
		return array('contenido'=>$contenido, 'banco'=>$banco);
	}

}
?>