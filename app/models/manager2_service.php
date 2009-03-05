<?php
/**
 * Este archivo contiene toda la logica de acceso a datos necesaria para la comunicacion con manager2.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pragmatia de RPB S.A.
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
 * La clase encapsula la logica de acceso a datos asociada a las necesidades de comunicacion via WebServices
 * entre Manager2 y Pragtico.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Manager2Service extends AppModel {

	var $useTable = false;
	
/**
 * Facturacion.
 *
 * @param integer $id El ultimo id que Manager2 ha recibido.
 * @return string El xml con el formato establecido.
 */
	function facturacion($id) {

		if (is_numeric($id)) {
			$Pago = ClassRegistry::init('Pago');
			$Factura = ClassRegistry::init('Factura');
			$condiciones['Liquidacion.estado'] = array('Confirmada');
			$condiciones['Liquidacion.id >'] = $id;
			$liquidaciones = $Pago->Liquidacion->find('first', array('checkSecurity'=>false, 'conditions'=>$condiciones, 'fields' => 'max(Liquidacion.id) as ultimo'));
			$registros = $Factura->calcularCoeficientes($condiciones);
			$niveles[0] = array('model' => 'Empleador', 'field' => 'cuit');
			$niveles[1] = array('model' => 'Coeficiente', 'field' => 'id');
			$registros = $Factura->mapToKey($registros, array('keyLevels'=>$niveles, 'valor'=>array('models'=>array(array('name' => '0'), array('name' => 'Coeficiente', 'fields' => 'nombre')))));
			$doc = new DomDocument('1.0');
			$root = $doc->createElement('datos');
			$root->setAttribute ('firstId', $id);
			$root->setAttribute ('lastId', $liquidaciones[0]['ultimo']);
			$root = $doc->appendChild($root);
			
			$empleadores = $doc->createElement('empleadores');
			$empleadores = $root->appendChild($empleadores);
			
			foreach ($registros as $cuit=>$registro) {
				$child = $doc->createElement('empleador');
				$child->setAttribute('cuit', str_replace('-', '', $cuit));
				$empleador = $empleadores->appendChild($child);
				
				$child = $doc->createElement('coeficientes');
				$coeficientes = $empleador->appendChild($child);
				foreach ($registro as $coeficienteId=>$v) {
					$child = $doc->createElement('coeficiente');
					$child->setAttribute('codigo', $coeficienteId);
					$child->setAttribute('nombre', $v['nombre']);
					$child->setAttribute('cantidad', '');
					$child->setAttribute('textoAdicional', '');
					$child->setAttribute('pagado', $v['pagado']);
					$child->setAttribute('importe', $v['total']);
					$child = $coeficientes->appendChild($child);
				}
			}
			return $doc->saveXML();
		} else {
  			return '';
		}
	}

/**
 * Empleadores.
 *
 * @param integer $id El ultimo id que Manager2 ha recibido.
 * @return string El xml con el formato establecido.
 */
	function empleadores($id) {
	
		if (is_numeric($id)) {
			$Empleador = ClassRegistry::init('Empleador');
			$Empleador->Behaviors->detach('Permisos');
			$registros = $Empleador->find('all',
				array(	'conditions' 	=> array('Empleador.id >' => $id),
	  					'contain'		=> array('Area'),
	  					'limit'			=> 2,
						'fields'		=>	array(	'Empleador.cuit',
													'Empleador.nombre',
													'Empleador.direccion',
													'Empleador.barrio',
													'Empleador.ciudad',
													'Empleador.pais',
													'Empleador.telefono',
													'Empleador.fax',
													'Empleador.pagina_web',
												 	'Empleador.group_id'),
					 	'order'			=> array('Empleador.group_id')));

			$tmp = $registros;
			$ultimo = array_pop($tmp);
			$doc = new DomDocument('1.0');
			
			$root = $doc->createElement('datos');
			$root->setAttribute('firstId', $id);
			$root->setAttribute('lastId', $ultimo['Empleador']['id']);
			$root = $doc->appendChild($root);
			$empleadores = $root->appendChild($doc->createElement('empleadores'));
			
			$prevGroup = null;
			foreach ($registros as $registro) {
				if ($registro['Empleador'] !== $prevGroup) {
					$grupo = $doc->createElement('grupo');
					$grupo->setAttribute('codigo', $registro['Empleador']['group_id']);
					$grupo = $empleadores->appendChild($grupo);
				}
				
				foreach ($registro['Area'] as $area) {
					$child = $doc->createElement('empleador');
					$child->setAttribute('codigo', $area['id']);
					foreach ($registro['Empleador'] as $k => $v) {
						if ($k === 'cuit') {
							$v = str_replace('-', '', $v);
						} elseif ($k === 'pagina_web') {
							$k = 'paginaWeb';
						} elseif ($k === 'group_id') {
							continue;
						}
						$child->setAttribute($k, $v);
					}
					$grupo->appendChild($child);
				}
			}
			return $doc->saveXML();
		} else {
			return '';
		}
	}



/**
 * Pagos.
 *
 * @param integer $id El ultimo id que Manager2 ha recibido.
 * @return string El xml con el formato establecido.
 */
	function pagos($id) {

		if (is_numeric($id)) {
			$Pago = ClassRegistry::init('Pago');
			$registros = $Pago->find('all', array(	'contain'	=>array('Relacion.Trabajador',
																		'PagosForma.Cuenta'),
													'conditions'=>array('Pago.id >'		=> $id,
																		'Pago.monto >'	=> 0),
													'checkSecurity'=>false,
													'order'		=>'Pago.id'));
			$tmp = $registros;
			$ultimo = array_pop($tmp);
			
			$doc = new DomDocument('1.0');
			$root = $doc->createElement('datos');
			$root->setAttribute ('firstId', $id);
			$root->setAttribute ('lastId', $ultimo['Pago']['id']);
			$root = $doc->appendChild($root);
			
			$pagos = $doc->createElement('pagos');
			$pagos = $root->appendChild($pagos);
			
			foreach ($registros as $registro) {
				$child = $doc->createElement('pago');
				$child->setAttribute('cuil', str_replace('-', '', $registro['Relacion']['Trabajador']['cuil']));
				$child->setAttribute('nombre', $registro['Relacion']['Trabajador']['apellido'] . ' ' . $registro['Relacion']['Trabajador']['nombre']);
				$child->setAttribute('cuenta', $registro['Relacion']['Trabajador']['cuenta']);
				$pago = $pagos->appendChild($child);
				foreach ($registro['PagosForma'] as $forma) {
					$child = $doc->createElement('medio');
					$child->setAttribute('comprobante', $forma['cheque_numero']);
					$child->setAttribute('tipo', $forma['forma']);
					if (!empty($forma['Cuenta']['cbu'])) {
						$child->setAttribute('cbuOrigen', $forma['Cuenta']['cbu']);
					} else {
						$child->setAttribute('cbuOrigen', '');
					}
					$child->setAttribute('monto', $forma['monto']);
					$child->setAttribute('fechaEmision', $forma['fecha']);
					$child->setAttribute('fechaPago', $forma['fecha_pago']);
					$child = $pago->appendChild($child);
				}
			}
			return $doc->saveXML();
		} else {
			return '';
		}
	}


/**
 * Anulaciones de Pagos.
 *
 * @param integer $id El ultimo id que Manager2 ha recibido.
 * @return string El xml con el formato establecido.
 */
	function anulaciones_pagos($id) {

		if (is_numeric($id)) {
			$Pago = ClassRegistry::init('Pago');
			$registros = $Pago->find('all', array(	'contain'		=>
														array('PagosForma'=>array(
															'conditions'=>array(	'PagosForma.monto <'=>0,
																					'PagosForma.id >'=>$id, ))),
													'checkSecurity'=>false));
			$tmp = $registros;
			$doc = new DomDocument('1.0');
			$root = $doc->createElement('datos');
			$root->setAttribute ('firstId', $id);
			$root->setAttribute ('lastId', $ultimo['PagosForma']['id']);
			$root = $doc->appendChild($root);
			
			$pagos = $doc->createElement('pagos');
			$pagos = $root->appendChild($pagos);
			
			foreach ($registros as $registro) {
				if (!empty($registro['PagosForma'])) {
					$child = $doc->createElement('pago');
					$child->setAttribute('cuil', str_replace('-', '', $registro['Relacion']['Trabajador']['cuil']));
					$child->setAttribute('nombre', $registro['Relacion']['Trabajador']['apellido'] . ' ' . $registro['Relacion']['Trabajador']['nombre']);
					$child->setAttribute('cuenta', $registro['Relacion']['Trabajador']['cuenta']);
					$pago = $pagos->appendChild($child);
					foreach ($registro['PagosForma'] as $forma) {
						$child = $doc->createElement('medio');
						$child->setAttribute('comprobante', $forma['cheque_numero']);
						$child->setAttribute('tipo', $forma['forma']);
						if (!empty($forma['Cuenta']['cbu'])) {
							$child->setAttribute('cbuOrigen', $forma['Cuenta']['cbu']);
						} else {
							$child->setAttribute('cbuOrigen', '');
						}
						$child->setAttribute('monto', $forma['monto'] * -1);
						$child->setAttribute('fechaEmision', $forma['fecha']);
						$child->setAttribute('fechaPago', $forma['fecha_pago']);
						$child = $pago->appendChild($child);
					}
				}
			}
			return $doc->saveXML();
		} else {
			return '';
		}
	}
	
}
?>