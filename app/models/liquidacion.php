<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las liquidaciones.
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
 * La clase encapsula la logica de acceso a datos asociada a las liquidaciones.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Liquidacion extends AppModel {

	var $hasMany = array(	'LiquidacionesDetalle' =>
                        array('className'   => 'LiquidacionesDetalle',
                              'foreignKey' 	=> 'liquidacion_id',
                              'order'		=> 'LiquidacionesDetalle.concepto_orden',
                              'dependent'	=> true),
                            'LiquidacionesError' =>
                        array('className'   => 'LiquidacionesError',
                              'foreignKey' 	=> 'liquidacion_id',
                              'dependent'	=> true),
                            'LiquidacionesAuxiliar' =>
                        array('className'   => 'LiquidacionesAuxiliar',
                              'foreignKey' 	=> 'liquidacion_id',
                              'dependent'	=> true),
							'Pago' =>
                        array('className'   => 'Pago',
                              'foreignKey' 	=> 'liquidacion_id',
                              'dependent'	=> true));

	var $belongsTo = array(	'Trabajador' =>
                        array('className'    => 'Trabajador',
                              'foreignKey'   => 'trabajador_id'),
							'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'),
							'Empleador' =>
                        array('className'    => 'Empleador',
                              'foreignKey'   => 'empleador_id'),
							'Factura' =>
                        array('className'    => 'Factura',
                              'foreignKey'   => 'factura_id')                              );
                              


	function addEditDetalle($opciones) {
		/**
		* Se refiere a los conceptos que deben tratarse de forma especial, ya que modifican data en table, u otra cosa.
		*/
		$this->recursive = -1;
		$liquidacion = $this->findById($opciones['liquidacionId']);
		$conceptosHora = array("horas_extra_50", "horas_extra_100");
		if(in_array($opciones['conceptoCodigo'], $conceptosHora)) {
			$this->LiquidacionesDetalle->Concepto->recursive = -1;
			$concepto = $this->LiquidacionesDetalle->Concepto->findByCodigo($opciones['conceptoCodigo']);

			$save['relacion_id'] = $liquidacion['Liquidacion']['relacion_id'];
			$save['liquidacion_id'] = $liquidacion['Liquidacion']['id'];
			$save['tipo'] = str_replace("Horas ", "", preg_replace("/0$/", "0 %", Inflector::humanize($opciones['conceptoCodigo'])));
			$save['periodo'] = $liquidacion['Liquidacion']['ano'] . str_pad("0", 2, $liquidacion['Liquidacion']['mes'], STR_PAD_RIGHT) . $liquidacion['Liquidacion']['periodo'];
			$save['estado'] = "Pendiente";
			$save['cantidad'] = $opciones['valor'];
			$save['observacion'] = "Ingresado desde la modificacion de una Liquidacion";
			$horaModel = new Hora();
			//$horaModel->begin();
			$horaModel->save(array("Hora"=>$save));
			
			//$horaModel->rollBack();
		}
	}


/**
 * A partir de las liquidaciones que son confirmadas, genera los pagos que deben realizarse.
 * Los deja en estado Pendiente.
 *
 * $liquidacionesIds	array con los ids de las liquidaciones que seran confirmadas y que deben generar los pagos.
 * return true si la operacion se pudo realizar correctamente.
 */
	function generarPagosPendientes($liquidacionesIds) {
		/**
		* Busco el usurio actual guardado en la sesion.
		*/
		$session = &new SessionComponent();
		$usuario = $session->read('__Usuario');
		
		$sql = "
			insert into pagos (
						liquidacion_id,
						relacion_id,
						fecha,
						monto,
						moneda,
						estado,
						user_id,
						group_id,
						permissions,
						created
					)
			select	Liquidacion.id,
					Liquidacion.relacion_id,
					Liquidacion.fecha,
					Liquidacion.total_pesos as total,
					'Pesos' as moneda,
					'Pendiente',
					" . $usuario['Usuario']['id'] . ",
					" . $usuario['Usuario']['grupo_id'] . ",
					500,
					concat(curdate(), ' ', curtime())
			from	liquidaciones Liquidacion
			where	1=1
			and		Liquidacion.id in ('" . implode("', '", $liquidacionesIds) . "')
			and		Liquidacion.total_pesos > 0
			union
			select	Liquidacion.id,
					Liquidacion.relacion_id,
					Liquidacion.fecha,
					Liquidacion.total_beneficios as total,
					'Beneficios' as moneda,
					'Pendiente',
					" . $usuario['Usuario']['id'] . ",
					" . $usuario['Usuario']['grupo_id'] . ",
					500,
					concat(curdate(), ' ', curtime())
			from	liquidaciones Liquidacion
			where	1=1
			and		Liquidacion.id in ('" . implode("', '", $liquidacionesIds) . "')
			and 	Liquidacion.total_beneficios > 0
		";
		/**
		* Ejecuto la consulta con @ para que no muestre errores (sin los hubiere).
		* Como es una consulta de insersion, no me interesa lo que devuelva, sino que no haya habido errores.
		*/
		@$this->query($sql);
		$this->__buscarError();
		if(empty($this->dbError['errorRdbms'])) {
			return true;
		}
		else {
			return false;
		}
	}

	
}
?>