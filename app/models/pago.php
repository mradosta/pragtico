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
			array('contain'	=> array('Relacion'	=> array('Empleador', 'Trabajador'))));
	
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
 *
 */
	function registrarPago($ids, $tipo) {

		$retorno = true;
		$tipo = ucfirst($tipo);
			
		if ($tipo == 'Deposito') {
			$this->contain(array('PagosForma', 'Relacion.Trabajador'));
		}
		else {
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
			if (($pagos[$id]['Pago']['moneda'] == 'Beneficios' && $tipo == 'Beneficios') || $pagos[$id]['Pago']['moneda'] == 'Pesos' && $tipo != 'Beneficios') {

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
			$banco = $cuenta['Sucursal']['Banco']['nombre'];
			/*
			$sql = '
			SELECT 		Pago.id,
						Pago.liquidacion_id,
						Pago.relacion_id,
						Pago.fecha,
						Pago.monto,
						Pago.pago,
						Pago.estado,
						Trabajador.tipo_cuenta,
						Trabajador.apellido,
						Trabajador.nombre,
						Trabajador.numero_documento,
						Trabajador.cuil,
						Trabajador.cbu
			FROM		pagos Pago,
						relaciones Relacion,
						trabajadores Trabajador,
						sucursales Sucursal,
						bancos Banco
			WHERE 		Pago.estado = \'Pendiente\'
			AND 		Pago.id IN (' . implode(', ', $opciones['pago_id']) . ')
			AND			Relacion.estado = \'Activa\'
			AND			Trabajador.id = Relacion.trabajador_id
			AND			Relacion.id = Pago.relacion_id
			AND			Sucursal.id = Trabajador.sucursal_id
			AND			Banco.id = Sucursal.banco_id
			AND			Banco.nombre = \'' . $banco . '\'';
			*/
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
								$rd = null;												
								$rd[] = 'D';
								$rd[] = str_pad($cuenta['Cuenta']['identificador'], 5, '0', STR_PAD_LEFT); //Numero de empresa
								$rd[] = $tipoCuentaTrabajador; //tipo de cuenta
								$rd[] = str_pad('X', 6, '0', STR_PAD_LEFT); //folio
								$rd[] = 'X'; //1 digito
								$rd[] = str_pad('X', 3, '0', STR_PAD_LEFT); //sucursal
								$rd[] = 'X'; //2 digito
								$rd[] = str_pad(substr($pago['Relacion']['Trabajador']['apellido'] . ' ' . $pago['Relacion']['Trabajador']['nombre'], 0, 20), 20, ' ', STR_PAD_RIGHT); //nombre
								$rd[] = str_pad(number_format($pago['Pago']['monto'], 2, '', ''), 14, '0', STR_PAD_LEFT); //importe
								$rd[] = str_pad('1', 2, '0', STR_PAD_LEFT); //concepto
								$rd[] = str_pad('', 11, ' ', STR_PAD_RIGHT); //libre
								$rds[] = implode('', $rd);
								d($rds);
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
							$contenido = implode('\n\r', $rds);
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
								}
								elseif ($cuenta['Cuenta']['tipo'] == 'Caja de Ahorro') {
									$tipoCuentaEmpleador = '9';
								}
								$rh[] = 'H';
								$rh[] = str_pad($cuenta['Cuenta']['identificador'], 5, '0', STR_PAD_LEFT); //Numero de empresa
								$rh[] = $tipoCuentaEmpleador; //tipo de cuenta
								$rh[] = str_pad('X', 6, '0', STR_PAD_LEFT); //folio
								$rh[] = 'X'; //1 digito
								$rh[] = str_pad('X', 3, '0', STR_PAD_LEFT); //sucursal
								$rh[] = 'X'; //2 digito
								$rh[] = str_pad(number_format($total, 2, '', ''), 14, '0', STR_PAD_LEFT); //importe total
								$rh[] = str_pad($fechaAcreditacion, 8, ' ', STR_PAD_RIGHT); //fecha acreditacion
								$rh[] = str_pad('', 25, ' ', STR_PAD_RIGHT); //libre
								$rhs = implode('', $rh);

								$rf[] = 'F';
								$rf[] = str_pad($cuenta['Cuenta']['identificador'], 5, '0', STR_PAD_LEFT); //Numero de empresa
								$rf[] = str_pad(count($rds), 7, '0', STR_PAD_LEFT); //cantidad registros
								$rf[] = str_pad('', 52, ' ', STR_PAD_RIGHT); //libre
								$rfs = implode('', $rf);

								$contenido = $rhs . '\n\r' . implode('\n\r', $rds) . '\n\r' . $rfs;
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

/**
 * Permite revertir un pago.
 */

	function revertir($id) {
		$this->begin();
		$return = true;
		$this->contain('PagosForma');
		$pago = $this->findById($id);
		foreach ($pago['PagosForma'] as $v) {
			if (!$this->PagosForma->revertir($v['id'])) {
				$return = false;
				break;
			}
		}
		/**
		* Si pude revertir todas las formas de pago, lo dejo nuevamente pendiente al pago.
		*/
		if ($return === true) {
			if ($this->save(array('Pago'=>array('id'=>$pago['Pago']['id'], 'estado' => 'Pendiente')))) {
				$this->commit();
				return true;
			}
		}
		$this->rollBack();
		return false;
	}

	function traerDetalleCambio($condiciones) {
			$fields = am($fieldsRelaciones, $fieldsEmpleadoresConcepto, $fieldsConveniosConcepto, $fieldsConceptos, $fieldCoeficientes, $fieldEmpleadoresCoeficiente);
			$table 	= 	'relaciones_conceptos';
			$joins	=	array(
							array(
								'alias' => 'EmpleadoresConcepto',
								'table' => 'empleadores_conceptos',
								'type' 	=> 'LEFT',
								'conditions' => array(
									array(	'RelacionesConcepto.concepto_id = EmpleadoresConcepto.concepto_id',
											'EmpleadoresConcepto.empleador_id'=> $relacion['Relacion']['empleador_id'] ))
							),
							array(
								'alias' => 'ConveniosConcepto',
								'table' => 'convenios_conceptos',
								'type' 	=> 'LEFT',
								'conditions' => array(
									array(	'RelacionesConcepto.concepto_id = ConveniosConcepto.concepto_id',
											'ConveniosConcepto.convenio_id' => $relacion['ConveniosCategoria']['convenio_id']))
							),
							array(
								'alias' => 'Concepto',
								'table' => 'conceptos',
								'type' 	=> 'LEFT',
								'conditions' => array(
									array(	'RelacionesConcepto.concepto_id = Concepto.id'))
							),
							array(
								'alias' => 'Coeficiente',
								'table' => 'coeficientes',
								'type' 	=> 'LEFT',
								'conditions' => array(
									array(	'Concepto.coeficiente_id = Coeficiente.id'))
							),
							array(
								'alias' => 'EmpleadoresCoeficiente',
								'table' => 'empleadores_coeficientes',
								'type' 	=> 'LEFT',
								'conditions' => array(
									array(	'Coeficiente.id = EmpleadoresCoeficiente.coeficiente_id',
											'EmpleadoresCoeficiente.empleador_id'	=> $relacion['Relacion']['empleador_id']))
							)							
						);
			$conditions = array(
							'RelacionesConcepto.relacion_id' => $relacion['Relacion']['id'],
							array('OR'	=> array(	'RelacionesConcepto.desde' => '0000-00-00',
												'RelacionesConcepto.desde <=' => $opciones['desde'])),
							array('OR'	=> array(	'RelacionesConcepto.hasta' => '0000-00-00',
												'RelacionesConcepto.hasta >=' => $opciones['hasta']))
						);	
		d($condiciones);
	}

	function xtraerDetalleCambio($condiciones) {
		$sql = '
			select		Empleador.cuit,
						Empleador.nombre,
						Trabajador.cuil,
						Trabajador.apellido,
						Trabajador.nombre,
						Trabajador.numero_documento,
						Pago.pago,
						Banco.nombre,
						Sucursal.codigo,
						substr(Pago.cbu, 9, 13) as cuenta,
						Liquidacion.periodo,
						sum(Pago.monto) as monto
			from		pagos Pago
							left join pagos_formas PagosForma on (Pago.id = PagosForma.pago_id)
							left join bancos Banco on (Banco.codigo = substr(Pago.cbu, 1, 3))
							left join sucursales Sucursal on (Sucursal.codigo = substr(Pago.cbu, 4, 4) and Banco.id = Sucursal.banco_id),
						relaciones Relacion,
						trabajadores Trabajador,
						empleadores Empleador,
						liquidaciones Liquidacion
			where		Relacion.id = Pago.relacion_id
			and			Trabajador.id = Relacion.trabajador_id
			and			Empleador.id = Relacion.empleador_id
			and			Liquidacion.id = Pago.liquidacion_id
			and			Liquidacion.estado = \'Confirmada\'
			and			Pago.estado = \'Imputado\'
			and			';

		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$sql .= $db->conditions($condiciones, true, false);
		$sql .= ' group by
						Empleador.cuit,
						Empleador.nombre,
						Trabajador.cuil,
						Trabajador.apellido,
						Trabajador.nombre,
						Trabajador.numero_documento,
						Pago.pago,
						Banco.nombre,
						Sucursal.codigo,
						substr(Pago.cbu, 9, 13),
						Liquidacion.periodo';
		$sql .= ' order by
						Trabajador.apellido,
						Trabajador.nombre';

		$r = $this->query($sql);
		d($r);
		$pagos = null;
		foreach ($r as $v) {
			$cuil = $v['Trabajador']['cuil'];
			$cuit = $v['Empleador']['cuit'];
			if (!isset($pagos[$cuit][$cuil])) {
				$pagos[$cuit][$cuil]['empleador'] = $v['Empleador']['nombre'];
				$pagos[$cuit][$cuil]['apellido'] = $v['Trabajador']['apellido'];
				$pagos[$cuit][$cuil]['nombre'] = $v['Trabajador']['nombre'];
				$pagos[$cuit][$cuil]['numero_documento'] = $v['Trabajador']['numero_documento'];
				$pagos[$cuit][$cuil]['banco'] = $v['Banco']['nombre'];
				$pagos[$cuit][$cuil]['sucursal'] = $v['Sucursal']['codigo'];
				$pagos[$cuit][$cuil]['cuenta'] = $v['0']['cuenta'];
				$pagos[$cuit][$cuil]['periodo'] = $v['Liquidacion']['periodo'];
				$pagos[$cuit][$cuil]['pesos'] = '0';
				$pagos[$cuit][$cuil]['beneficios'] = '0';
				$pagos[$cuit][$cuil]['total_pesos'] = 0;
				$pagos[$cuit][$cuil]['total_beneficios'] = 0;
			}
			if ($v['Pago']['pago'] == 'Beneficios') {
				$pagos[$cuit][$cuil]['beneficios'] = $v['0']['monto'];
				$pagos[$cuit][$cuil]['total_beneficios'] += $v['0']['monto'];
			}
			else {
				$pagos[$cuit][$cuil]['pesos'] = $v['0']['monto'];
				$pagos[$cuit][$cuil]['total_pesos'] += $v['0']['monto'] ;
			}
		}
		return $pagos;
	}
}
?>