<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las facturas.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.models
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a las facturas.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Factura extends AppModel {


	var $hasMany = array(	'Liquidacion' =>
                        array('className'   => 'Liquidacion',
                              'foreignKey' 	=> 'factura_id'),
							'FacturasDetalle' =>
                        array('className'   => 'FacturasDetalle',
                              'foreignKey' 	=> 'factura_id'));

	var $belongsTo = array(	'Empleador' =>
                        array('className'   => 'Empleador',
                              'foreignKey' 	=> 'empleador_id'));

	function resumen($condiciones = null, $tipo = "resumido") {
		
		if($tipo == "resumido") {
			$sql = "
				select
							Empleador.id,
							Empleador.cuit,
							Empleador.nombre,
							LiquidacionesDetalle.concepto_codigo,
							LiquidacionesDetalle.concepto_nombre,
							LiquidacionesDetalle.concepto_orden,
							LiquidacionesDetalle.coeficiente_valor,
							sum(LiquidacionesDetalle.valor) as total,
							sum(LiquidacionesDetalle.valor_cantidad) as cantidad,
							count(1) as cuenta
				from 		empleadores Empleador,
							liquidaciones Liquidacion,
							liquidaciones_detalles LiquidacionesDetalle 
				where		1=1
				and			Liquidacion.id = LiquidacionesDetalle.liquidacion_id
				and			Empleador.id = Liquidacion.empleador_id
				and			(LiquidacionesDetalle.concepto_imprimir = 'Si'
							or LiquidacionesDetalle.concepto_imprimir = 'Solo con valor')
				and			";
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			$sql .= $db->conditions($condiciones, true, false);
			$sql .= " group by
							Empleador.id,
							Empleador.cuit,
							Empleador.nombre,
							LiquidacionesDetalle.concepto_codigo,
							LiquidacionesDetalle.concepto_nombre,
							LiquidacionesDetalle.concepto_orden,
							LiquidacionesDetalle.coeficiente_valor
				order by	Empleador.nombre,
							LiquidacionesDetalle.concepto_orden
			";

			$r = $this->query($sql);
			foreach($r as $v) {
				$data = null;
				$data['nombre'] = $v['LiquidacionesDetalle']['concepto_nombre'];
				$data['coeficiente'] = $v['LiquidacionesDetalle']['coeficiente_valor'];
				$data['total'] = $v['0']['total'];
				$data['cantidad'] = $v['0']['cantidad'];

				if(!isset($return[$v['Empleador']['id']])) {
					$return[$v['Empleador']['id']]['cuit'] = $v['Empleador']['cuit'];
					$return[$v['Empleador']['id']]['nombre'] = $v['Empleador']['nombre'];
				}
				$return[$v['Empleador']['id']]['Concepto'][] = $data;
			}
			return array_values($return);
		}
		elseif($tipo == "detallado") {
			$sql = "
				select
							Empleador.id,
							Empleador.cuit,
							Empleador.nombre,
							Trabajador.id,
							Trabajador.legajo,
							Trabajador.cuil,
							Trabajador.nombre,
							Trabajador.apellido,
							Area.nombre,
							LiquidacionesDetalle.concepto_codigo,
							LiquidacionesDetalle.concepto_nombre,
							LiquidacionesDetalle.concepto_orden,
							LiquidacionesDetalle.coeficiente_valor,
							LiquidacionesDetalle.valor as total,
							LiquidacionesDetalle.valor_cantidad as cantidad
				from 		empleadores Empleador,
							trabajadores Trabajador,
							liquidaciones Liquidacion,
							liquidaciones_detalles LiquidacionesDetalle,
							relaciones Relacion left join areas Area on (Area.id = Relacion.area_id)
				where		1=1
				and			Liquidacion.id = LiquidacionesDetalle.liquidacion_id
				and			Empleador.id = Liquidacion.empleador_id
				and			Trabajador.id = Liquidacion.trabajador_id
				and			Relacion.id = Liquidacion.relacion_id
				and			(LiquidacionesDetalle.concepto_imprimir = 'Si'
							or LiquidacionesDetalle.concepto_imprimir = 'Solo con valor')
				and			";
			$db =& ConnectionManager::getDataSource($this->useDbConfig);
			$sql .= $db->conditions($condiciones, true, false);
			$sql .= "
				order by	Empleador.nombre,
							Trabajador.apellido,
							Trabajador.nombre,
							LiquidacionesDetalle.concepto_orden
			";

			$r = $this->query($sql);
			foreach($r as $v) {
				$data = null;
				$data['nombre'] = $v['LiquidacionesDetalle']['concepto_nombre'];
				$data['coeficiente'] = $v['LiquidacionesDetalle']['coeficiente_valor'];
				$data['total'] = $v['LiquidacionesDetalle']['total'];
				$data['cantidad'] = $v['LiquidacionesDetalle']['cantidad'];

				if(!isset($return[$v['Empleador']['id']])) {
					$return[$v['Empleador']['id']]['cuit'] = $v['Empleador']['cuit'];
					$return[$v['Empleador']['id']]['nombre'] = $v['Empleador']['nombre'];
				}
				if(!isset($return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']])) {
					$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['legajo'] = $v['Trabajador']['legajo'];
					$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['cuil'] = $v['Trabajador']['cuil'];
					$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['apellido'] = $v['Trabajador']['apellido'];
					$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['nombre'] = $v['Trabajador']['nombre'];
					$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['area'] = $v['Area']['nombre'];
				}
				$return[$v['Empleador']['id']]['Trabajador'][$v['Trabajador']['id']]['Concepto'][] = $data;
			}
			return array_values($return);
		}
	}

	function prefacturar($condiciones = null) {

		/**
		* Adecuo las condiciones.
		*/
		if($periodo = $this->traerPeriodo($condiciones['Condicion']['Liquidacion-periodo'])) {
			$conditions['Liquidacion.mes'] = $periodo['mes'];
			$conditions['Liquidacion.ano'] = $periodo['ano'];
			unset($condiciones['Condicion']['Liquidacion-periodo']);
		}
		if($condiciones['Condicion']['Liquidacion-estado'] == "indistinto") {
			$conditions['Liquidacion.estado'] = array("Confirmada", "Sin Confirmar");
			unset($condiciones['Condicion']['Liquidacion-estado']);
		}
		
		$query['fields']	= array("Liquidacion.id",
									"Empleador.id",
									"Empleador.nombre",
									"Coeficiente.id",
									"Coeficiente.nombre",
									"SUM(LiquidacionesDetalle.valor) AS subtotal",
									"IF(ISNULL(EmpleadoresCoeficiente.valor), Coeficiente.valor, Coeficiente.valor + EmpleadoresCoeficiente.valor) AS valor",
									"SUM(LiquidacionesDetalle.valor) * (IF(ISNULL(EmpleadoresCoeficiente.valor), Coeficiente.valor, Coeficiente.valor + EmpleadoresCoeficiente.valor)) AS total",);
		$query['group']		= array("Empleador.id", "Empleador.nombre", "Coeficiente.id", "Coeficiente.nombre");
		$query['order']		= array("Empleador.id");
		$query['joins']		= array(array(
									"table" => "liquidaciones_detalles",
									"type" 	=> "INNER"
								),array(
									"table" => "empleadores",
									"type" 	=> "INNER",
									"conditions"=> array("Empleador.id"=>DboSource::identifier("Liquidacion.empleador_id"))
								),array(
									"table" 	=> "coeficientes",
									"type" 		=> "LEFT",
									"conditions"=> array("Coeficiente.id"=>DboSource::identifier("LiquidacionesDetalle.coeficiente_id"))
								),array(
									"table" 	=> "empleadores_coeficientes",
									"type" 		=> "LEFT",
									"conditions"=> array("EmpleadoresCoeficiente.empleador_id"=>DboSource::identifier("Liquidacion.empleador_id"),
														 "EmpleadoresCoeficiente.coeficiente_id"=>DboSource::identifier("LiquidacionesDetalle.coeficiente_id"))
								));
		$query['conditions']= am(	$conditions,
									$this->getConditions($condiciones),
									array("OR"=>array(	"LiquidacionesDetalle.concepto_imprimir" => array("Si", "Solo con valor"))));

		$r = $this->query($this->generarSql($query, $this->Liquidacion));
		if(!empty($r)) {
			$niveles[0] = array("model"=>"Empleador", "field"=>"id");
			$niveles[1] = array("model"=>"Coeficiente", "field"=>"id");
			$r = $this->mapToKey($r, array("keyLevels"=>$niveles, "valor"=>array("model"=>"0")));
			$ids = array();
			$this->begin();
			foreach($r as $empleadorId => $v) {
				$saveEncabezado = null;
				$saveDetalle = null;
				$c = $total = 0;
				$saveEncabezado['fecha'] = date("d/m/Y");
				$saveEncabezado['empleador_id'] = $empleadorId;
				$saveEncabezado['estado'] = "Sin Confirmar";
				foreach($v as $coeficienteId => $valores) {
					$saveDetalle[$c]['coeficiente_id'] = $coeficienteId;
					$saveDetalle[$c]['subtotal'] = $valores['subtotal'];
					$saveDetalle[$c]['valor'] = $valores['valor'];
					$saveDetalle[$c]['total'] = $valores['total'];
					$total+= $valores['total'];
					$c++;
				}
				$saveEncabezado['total'] = $total;
				if($saveEstado = $this->save(array("Factura"=>$saveEncabezado, "FacturasDetalle"=>$saveDetalle), true, array(), false)) {
					$ids[] = $saveEstado['Factura']['id'];
				}
			}
			if(count($ids) === count($r)) {
				$this->commit();
				return $ids;
			}
			else {
				$this->rollback();
			}
		}
		return false;
	}

}
?>