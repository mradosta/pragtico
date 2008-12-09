<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los descuentos.
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
 * La clase encapsula la logica de acceso a datos asociada a los descuentos.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Descuento extends AppModel {

	var $order = array('Descuento.alta'=>'desc');
	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array(	"index"=>array(	"contain"=>array("Relacion.Empleador",
																"Relacion.Trabajador")),
								"edit"=>array(	"contain"=>array("Relacion.Empleador",
																"Relacion.Trabajador")),
								"add" =>array(								
										"valoresDefault"=>array("alta"=>"date('d/m/Y')",
																"desde"=>"date('d/m/Y')")));

	var $opciones = array("descontar"=> array(	"1"=>	"Con Cada Liquidacion",
												"2"=>	"Primera Quincena",
												"4"=>	"Segunda Quincena",
												"8"=>	"Sac",
												"16"=>	"Vacaciones",
												"32"=>	"Liquidacion Final"));
							
	var $validate = array(
        'alta' => array(
			array(
				'rule'	=> VALID_DATE, 
				'message'	=>'Debe ingresar una fecha valida.'),
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe ingresar una fecha.'),
        ),
        'desde' => array(
			array(
				'rule'	=> VALID_DATE, 
				'message'	=>'Debe ingresar una fecha valida.'),
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe ingresar una fecha.'),
        ),
        'monto' => array(
			array(
				'rule'	=> VALID_NUMBER,
				'message'	=>'Debe ingresar el monto a descontar.')
        ),
        'tipo' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe seleccionar el tipo de descuento.')
        ),
        'descripcion' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe ingresar la descripcion del descuento.')
        ),
        'relacion_id__' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe especificar la relacion laboral a la cual realizar el descuento.')
        )
	);


	var $belongsTo = array(	'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'));
	
	var $hasMany = array(	'DescuentosDetalle' =>
					array('className'    => 'DescuentosDetalle',
						  'foreignKey'   => 'descuento_id'));



/**
 * buscarDescuento
 * Dada un ralacion y un periodo verifica si hay un descuento pendiente y su monto.
 * @return array vacio si no hay nada que descontar.
 */
	function getDescuentos($relacion, $condiciones) {

		switch($opciones['tipo']) {
			case "normal":
				if($opciones['periodo'] === "1Q") {
					$descontar = 3;
				}
				elseif($opciones['periodo'] === "2Q" || $opciones['periodo'] === "M") {
					$descontar = 5;
				}
				break;
			case "sac":
				$descontar = 9;
			break;
			case "vacaciones":
				$descontar = 17;
			break;
			case "liquidacion_final":
				$descontar = 33;
			break;
			case "especial":
				$descontar = 1;
			break;
		}
		
		$sql = "
			select 		d.id,
						d.tipo,
						d.relacion_id,
						d.descripcion,
						d.monto,
						d.maximo,
						d.concurrencia,
						d.cuotas as cuotas,
						count(dd.id) as cuotas_descontadas,
						sum(dd.monto) as total_pagado
			from 		descuentos d
						left join descuentos_detalles dd on (dd.descuento_id = d.id)
			where		1=1
			and			d.desde >= '" . $opciones['desde'] . "'
			and			d.relacion_id = '" . $relacion['Relacion']['id'] . "'
			and			(d.descontar & " . $descontar . ") > 0
			and			d.estado = 'Activo'
			group by	d.id,
						d.tipo,
						d.relacion_id,
						d.descripcion,
						d.monto,
						d.maximo,
						d.concurrencia,
						d.cuotas
			order by	d.alta
		";

		$r = $this->query($sql);
		$conceptos = $auxiliares = array();
		if(!empty($r)) {
			foreach ($r as $k=>$v) {
				$cuotaDescontadas = $v['0']['cuotas_descontadas'];
				$cuotaActual = $cuotaDescontadas + 1;
				switch($v['d']['tipo']) {
					case "Prestamo":
					case "Vale":
						$valorCuota = $v['d']['monto'] / $v['d']['cuotas'];
						$formula = "=" . $valorCuota;
						break;
					case "Embargo":
						$valorCuota = $v['d']['monto'] / $v['d']['cuotas'];
						break;
					case "Cuota Alimentaria":
						break;
				}

				//if($v['d']['maximo'] > 0) {
				//	$valorCuota = $v['d']['maximo'];
				//}

				
				/**
				* Busco el codigo del concepto.
				*/
				$modelConcepto = new Concepto();
				$codigoConcepto = strtolower($v['d']['tipo']);
				$concepto = $modelConcepto->findConceptos("ConceptoPuntual", $relacion, $codigoConcepto, $opciones);
				if(!empty($formula)) {
					$concepto[$codigoConcepto]['formula'] = $formula;
				}
				$concepto[$codigoConcepto]['debug'] = "Tipo:" . $codigoConcepto . ", Monto Total:$" . $v['d']['monto'] . ", Cuotas:" . $v['d']['cuotas'] . ", Cuotas Descontadas:" . $cuotaDescontadas . ", Saldo:$" . ($v['d']['monto'] - $v['0']['total_pagado']) . ", Cuota a Descontar en esta Liquidacion:" . $cuotaActual . ", Valor esta Cuota:$" . $valorCuota;
				$concepto[$codigoConcepto]['valor_cantidad'] = "0";
				$concepto[$codigoConcepto]['nombre'] = $v['d']['tipo'] . " " . $v['d']['descripcion'] . " (Cuota: " . $cuotaActual . "/" . $v['d']['cuotas'] . ")";
				$conceptos[] = $concepto;

				/**
				* Creo un registro el la tabla auxiliar que debera ejecutarse en caso de que se confirme la pre-liquidacion.
				*/
				$auxiliar = null;
				$auxiliar['descuento_id'] = $v['d']['id'];
				$auxiliar['fecha'] = "##MACRO:fecha_liquidacion##";
				$auxiliar['liquidacion_id'] = "##MACRO:liquidacion_id##";
				$auxiliar['monto'] = $valorCuota;
				$auxiliar['observacion'] = "(Cuota: " . $cuotaActual . "/" . $v['d']['cuotas'] . ")";
				$auxiliares[] = array("save"=>serialize($auxiliar), "model"=>"DescuentosDetalle");

				/**
				* Si se termino de pagar el credito, debo actualizar el estado a Finalizado.
				*/
				if(($v['0']['total_pagado'] + $valorCuota) >=  $v['d']['monto']) {
					$auxiliar = null;
					$auxiliar['estado'] = "Finalizado";
					$auxiliar['id'] = $v['d']['id'];
					$auxiliares[] = array("save"=>serialize($auxiliar), "model"=>"Descuento");
				}

				/**
				* Si solo uno a la vez, no puedo ponerle otro descuento, por lo tanto, salgo del foreach.
				* De la query vienen ordenados por fecha de alta.
				*/
				if($v['d']['concurrencia'] == "Solo uno a la vez") {
					break;
				}
			}
		}
		return array("concepto"=>$conceptos, "auxiliar"=>$auxiliares);
	}


/**
 * Como el campo descontar es un bitwise, debo gardar la suma de todos los valores y no un valor puntual.
 */
	function beforeSave() {
		$this->data['Descuento']['descontar'] = array_sum($this->data['Descuento']['descontar']);
		return parent::beforeSave();
	}

	
}
?>