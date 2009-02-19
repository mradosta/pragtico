<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los descuentos.
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
 * La clase encapsula la logica de acceso a datos asociada a los descuentos.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Descuento extends AppModel {

	var $order = array('Descuento.alta' => 'desc');
	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array(	'index'	=>
			array('contain'	=> array('Relacion' => array('Empleador', 'Trabajador'))),
								'edit'	=>
			array('contain'	=> array('Relacion'	=> array('Empleador', 'Trabajador'))),
								'add' 	=>
			array('valoresDefault'	=> array('alta'		=> array('date' => 'd/m/Y'),
											 'desde'	=> array('date' => 'd/m/Y'))));

	var $opciones = array('descontar'=> array(	'1'=>	'Con Cada Liquidacion',
												'2'=>	'Primera Quincena',
												'4'=>	'Segunda Quincena',
												'8'=>	'Sac',
												'16'=>	'Vacaciones',
												'32'=>	'Liquidacion Final',
											 	'64'=>	'Especial'));
							
	var $validate = array(
        'alta' => array(
			array(
				'rule'		=> VALID_DATE, 
				'message'	=> 'Debe ingresar una fecha valida.'),
			array(
				'rule'		=> VALID_NOT_EMPTY, 
				'message'	=> 'Debe ingresar una fecha.'),
        ),
        'desde' => array(
			array(
				'rule'		=> VALID_DATE, 
				'message'	=> 'Debe ingresar una fecha valida.'),
			array(
				'rule'		=> VALID_NOT_EMPTY, 
				'message'	=> 'Debe ingresar una fecha.'),
        ),
        'monto' => array(
			array(
				'rule'		=> VALID_NUMBER,
				'message'	=> 'Debe ingresar el monto a descontar.')
        ),
        'tipo' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe seleccionar el tipo de descuento.')
        ),
        'descripcion' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe ingresar la descripcion del descuento.')
        ),
        'relacion_id__' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar la relacion laboral a la cual realizar el descuento.')
        )
	);


	var $belongsTo = array(	'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'));
	
	var $hasMany = array(	'DescuentosDetalle' =>
					array('className'    => 'DescuentosDetalle',
						  'foreignKey'   => 'descuento_id'));



/**
 * getDescuentos
 * TODO: Puede que haya dos conceptos de embargo concurrentes, entoces se pisarian.
 * Esto implica un gran cambio en el liquidador. Revisar.		   
 * Dada un ralacion XXXXXXXXXX.
 * @return array vacio si no hay nada que descontar.
 */
	function getDescuentos($relacion, $opciones) {
		
	   switch($opciones['tipo']) {
			case 'normal':
				if ($opciones['periodo']['periodo'] === '1Q') {
					$descontar = 3;
				} elseif ($opciones['periodo']['periodo'] === '2Q' || $opciones['periodo']['periodo'] === 'M') {
					$descontar = 5;
				}
				break;
			case 'sac':
				$descontar = 9;
			break;
			case 'vacaciones':
				$descontar = 17;
			break;
			case 'liquidacion_final':
				$descontar = 33;
			break;
			case 'especial':
				$descontar = 1;
			break;
		}
		
		$r = $this->find('all',
			array(
				  	'contain'		=> 'DescuentosDetalle',
				  	'checkSecurity'	=> false,
					'conditions' 	=> array(
				'Descuento.relacion_id' 						=> $relacion['Relacion']['id'],
				'Descuento.desde >=' 							=> $opciones['periodo']['desde'],
 				'(Descuento.descontar & ' . $descontar . ') >' 	=> 0,
 				'Descuento.estado' 								=> 'Activo')
		));
		
		$concepto = $auxiliares = array();
		if (!empty($r)) {
			foreach ($r as $k => $v) {
				$cuotaDescontadas = count($v['DescuentosDetalle']);
				$totalDescontado = array_sum(Set::extract('/monto', $v['DescuentosDetalle']));
				$cuotaActual = $cuotaDescontadas + 1;
				switch ($v['Descuento']['tipo']) {
					case 'Prestamo':
					case 'Vale':
						$valorCuota = $v['Descuento']['monto'] / $v['Descuento']['cuotas'];
						$formula = '=' . $valorCuota;
						break;
					case 'Embargo':
						$valorCuota = $v['Descuento']['monto'] / $v['Descuento']['cuotas'];
						break;
					case 'Cuota Alimentaria':
						break;
				}

				
				/**
				* Establezco el maximo a descontar.
				*/
				if ($v['Descuento']['maximo'] > 0 && $valorCuota > $v['Descuento']['maximo']) {
					$valorCuota = $v['Descuento']['maximo'];
				}

				/**
				* Verifico que la cuota no sea mayor al saldo.
				*/
				$saldo = $v['Descuento']['monto'] - $totalDescontado;
				if ($saldo < $valorCuota) {
					$valorCuota = $saldo;
				}
				
				
				/**
				* Busco el codigo del concepto.
				*/
				$modelConcepto = ClassRegistry::init('Concepto');
				$codigoConcepto = strtolower($v['Descuento']['tipo']);
				$concepto = $modelConcepto->findConceptos('ConceptoPuntual', array_merge(array('relacion' => $relacion, 'codigoConcepto' => $codigoConcepto), $opciones));
				if (!empty($formula)) {
					$concepto[$codigoConcepto]['formula'] = $formula;
				}
				$concepto[$codigoConcepto]['debug'] = 'Tipo:' . $codigoConcepto . ', Monto Total:$' . $v['Descuento']['monto'] . ', Total de Cuotas:' . $v['Descuento']['cuotas'] . ', Cuotas Descontadas:' . $cuotaDescontadas . ', Saldo:$' . $saldo . ', Cuota a Descontar en esta Liquidacion:' . $cuotaActual . ', Valor esta Cuota:$' . $valorCuota;
				$concepto[$codigoConcepto]['valor_cantidad'] = '0';
				if ($v['Descuento']['tipo'] === 'Prestamo') {
					$concepto[$codigoConcepto]['nombre'] = $v['Descuento']['tipo'] . ' ' . $v['Descuento']['descripcion'] . '. Fecha: ' . $this->format($v['Descuento']['alta'], 'date') . ' (Cuota: ' . $cuotaActual . '/' . $v['Descuento']['cuotas'] . ')';
				} else {
					$concepto[$codigoConcepto]['nombre'] = $v['Descuento']['tipo'] . ' ' . $v['Descuento']['descripcion'] . '. Fecha: ' . $this->format($v['Descuento']['alta'], 'date');
				}
				
				/**
				* Creo un registro el la tabla auxiliar que debera ejecutarse en caso de que se confirme la pre-liquidacion.
				*/
				$auxiliar = null;
				$auxiliar['descuento_id'] = $v['Descuento']['id'];
				$auxiliar['fecha'] = '##MACRO:fecha_liquidacion##';
				$auxiliar['liquidacion_id'] = '##MACRO:liquidacion_id##';
				$auxiliar['monto'] = $valorCuota;
				$auxiliar['observacion'] = '(Cuota: ' . $cuotaActual . '/' . $v['Descuento']['cuotas'] . ')';
				$auxiliares[] = array('save'=>serialize($auxiliar), 'model' => 'DescuentosDetalle');

				/**
				* Si se termino de pagar el credito, debo actualizar el estado a Finalizado.
				*/
				if (($totalDescontado + $valorCuota) >=  $v['Descuento']['monto']) {
					$auxiliar = null;
					$auxiliar['estado'] = 'Finalizado';
					$auxiliar['id'] = $v['Descuento']['id'];
					$auxiliares[] = array('save'=>serialize($auxiliar), 'model' => 'Descuento');
				}

				/**
				* Si solo uno a la vez, no puedo ponerle otro descuento, por lo tanto, salgo del foreach.
				* De la query vienen ordenados por fecha de alta.
				*/
				if ($v['Descuento']['concurrencia'] === 'Solo uno a la vez') {
					break;
				}
			}
		}
		//d(array('concepto' => $concepto, 'auxiliar' => $auxiliares));
		return array('conceptos' => $concepto, 'auxiliar' => $auxiliares);
	}


/**
 * descontar field is bitwise, must sum values then.
 */
	function beforeSave($options = array()) {
		$this->data['Descuento']['descontar'] = array_sum($this->data['Descuento']['descontar']);
		if ($this->data['Descuento']['tipo'] === 'Vale') {
			$this->data['Descuento']['cuotas'] = 1;
		}
		return parent::beforeSave($options);
	}

	
}
?>