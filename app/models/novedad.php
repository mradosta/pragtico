<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las novedades.
 * Una novedad es un ingreso de datos al sistema no confirmado aun.
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
 * La clase encapsula la logica de acceso a datos asociada a las novedades.
 * Una novedad es un ingreso de datos al sistema no confirmado aun.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Novedad extends AppModel {

/**
 * El orden por defecto.
 *
 * @var array
 * @access public
*/
	var $order = array("periodo", "tipo");


/**
 * Los modificaciones al comportamiento estandar de app_controller.php
 *
 * @var array
 * @access public
*/
	var $modificadores = array(	"index"=>array(	"contain"=>array("Relacion.Empleador",
																"Relacion.Trabajador")),
								"edit"=>array(	"contain"=>array("Relacion.Empleador",
																"Relacion.Trabajador")));

/**
 * Los permisos por defecto con los que se guardaran los datos de este model.
 *
 * @var integer
 * @access protected
 */
	//protected $__permissions = 122;
	
/**
 * Las opciones validadas de formatos de planillas que se podran generar e importar.
 *
 * @var array
 * @access public
 */
	var $opciones = array("formato"=>array("Excel5"=>"Excel", "Excel2007"=>"Excel 2007"));
	
	var $belongsTo = array(	'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'));


	//debo pintar el estado
	function afterFind($results, $primary = false) {
		if($primary) {
			foreach($results as $k => $v) {
				if(isset($v['Novedad']['tipo']) && $v['Novedad']['tipo'] === 'Concepto') {
					$conditions = array('RelacionesConcepto.concepto_id' =>	$v['Novedad']['concepto_id'],
										'RelacionesConcepto.relacion_id' =>	$v['Novedad']['relacion_id']);
							
					$concepto = $this->Relacion->RelacionesConcepto->find('first', array(
												'recursive'		=> -1,
												'conditions' 	=> $conditions));
					
					if(empty($concepto['Concepto']['observacion']) && $concepto['Concepto']['observacion'] === 'Ingresado desde planilla') {
						$results[$k]['Novedad']['existe'] = false;
					}
					else {
						$results[$k]['Novedad']['existe'] = true;
					}
					d($r['Novedad']['relacion_id']);
				}
			}
		}
	}
	
/**
 * Graba las novedades provenientes desde la planilla.
 * Maneja transacciones.
 *
 * @param array $datos Los datos a grabar.
 * @param array $periodo El periodo al cual se asignaran los datos de las novedades.
 * @return boolean True si fue posible guardar las novedades ingresadas, false en otro caso
 * @access public 
 */
 	function grabar($datos, $periodo) {
		if(!preg_match(VALID_PERIODO, $periodo) || empty($datos) || !is_array($datos)) {
			return false;
		}
		
		$predefinidos = $this->getIngresosPosibles("predefinidos");
		
		foreach($datos as $relacion_id => $data) {
			foreach($data as $tipo => $registros) {
				foreach($registros as $registro) {
				
					$save = null;
					$save['Novedad']['id'] = null;
					$save['Novedad']['periodo'] = $periodo;
					$save['Novedad']['relacion_id'] = $relacion_id;
					
					if(!in_array($tipo, $predefinidos)) {
						/**
						* Busco el id del concepto correspondiente al nombre que importe desde la planilla.
						*/
						$this->Relacion->RelacionesConcepto->Concepto->recursive = -1;
						$concepto = $this->Relacion->RelacionesConcepto->Concepto->findByNombre($tipo);
						if(empty($concepto['Concepto']['id'])) {
							continue;
						}
						$save['Novedad']['concepto_id'] = $concepto['Concepto']['id'];
						$save['Novedad']['data'] = "#valor_planilla:" . $registro;
						$save['Novedad']['tipo'] = 'Concepto';
					}
					else {
						$save['Novedad']['concepto_id'] = null;
						$save['Novedad']['data'] = $registro;
						$save['Novedad']['tipo'] = $tipo;
					}
					
					$saveAll[] = $save;
				}
			}
		}
		return $this->saveAll($saveAll);
	}


/**
 * Distribuye las novedades en las diferecntes tablas (horas, ausencias, descuentos) o crea los conceptos necesarios.
 *
 * @param array $ids Los ids de las novedades a distribuir en cada tabla.
 * @return mixed Cantidad de novedades distribuidas. False en caso de error o que no hayn podido confirmarse todos los ids.
 * @access public
 */
	function distribuir($ids) {
		$novedades = $this->find("all", array("conditions"=>array("Novedad.id"=>$ids), "recursive"=>-1));
		$c = $i = $ii = 0;
		
		foreach($novedades as $novedad) {
			$data = unserialize($novedad['Novedad']['data']);
			$periodo = $this->format($novedad['Novedad']['periodo'], "periodo");
			switch($novedad['Novedad']['tipo']) {
				case "Horas":
					foreach($data as $tipo=>$cantidad) {
						$saves[$i]['Hora']['id'] = null;
						$saves[$i]['Hora']['tipo'] = $tipo;
						$saves[$i]['Hora']['cantidad'] = $cantidad;
						$saves[$i]['Hora']['estado'] = "Confirmada";
						$saves[$i]['Hora']['relacion_id'] = $novedad['Novedad']['relacion_id'];
						$saves[$i]['Hora']['periodo'] = $periodo['periodoCompleto'];
						$saves[$i]['Hora']['observacion'] = "Ingresado desde planilla";
					}
				break;
				case "Ausencias":
					$motivo = $this->Relacion->Ausencia->AusenciasMotivo->findByMotivo($data['Motivo']);
					/**
					* Si no cargo el motivo, o este no existe, lo pongo como justificado.
					*/
					if(empty($motivo['Motivo']['id'])) {
						$motivo['Motivo']['id'] = "1";
					}
					$saves[$i]['Ausencia']['id'] = null;
					$saves[$i]['Ausencia']['relacion_id'] = $novedad['Novedad']['relacion_id'];
					$saves[$i]['Ausencia']['ausencia_motivo_id'] = $motivo['Motivo']['id'];
					$saves[$i]['AusenciasSeguimiento'][$ii]['dias'] = $data['Dias'];
					$saves[$i]['AusenciasSeguimiento'][$ii]['desde'] = $periodo['desde'];
					$saves[$i]['AusenciasSeguimiento'][$ii]['observacion'] = "Ingresado desde planilla";
					$saves[$i]['AusenciasSeguimiento'][$ii]['estado'] = "Confirmado";
					$ii++;
				break;
				case "Vales":
					$saves[$i]['Descuento']['id'] = null;
					$saves[$i]['Descuento']['alta'] = $periodo['desde'];
					$saves[$i]['Descuento']['desde'] = $periodo['desde'];
					$saves[$i]['Descuento']['relacion_id'] = $novedad['Novedad']['relacion_id'];
					$saves[$i]['Descuento']['monto'] = $data['Importe'];
					$saves[$i]['Descuento']['tipo'] = "Vale";
					$saves[$i]['Descuento']['descontar'] = array("1");
					$saves[$i]['Descuento']['concurrencia'] = "Permite superponer";
					$saves[$i]['Descuento']['estado'] = "Activo";
					$saves[$i]['Descuento']['observacion'] = "Ingresado desde planilla";
				break;
				case "Concepto":
					$this->Relacion->RelacionesConcepto->Concepto->recursive = -1;
					$concepto = $this->Relacion->RelacionesConcepto->Concepto->findByNombre($data['concepto']);
					if(empty($concepto['Concepto']['id'])) {
						continue;
					}
					
					/**
					* Debo verificar que la relacion no tenga asociado el concepto ya.
					* En caso de tenerlo, solo le modifico la formula.
					* Si no lo tiene lo agrego con vigencia solo para el periodo.
					*/
					$f = $this->Relacion->RelacionesConcepto->find('first', array(
																	'recursive'		=> -1,
																	'conditions' 	=> array(
													'RelacionesConcepto.relacion_id' => $novedad['Novedad']['relacion_id'], 
													'RelacionesConcepto.concepto_id' => $concepto['Concepto']['id'])));
					
					if(!empty($f['RelacionesConcepto']['formula'])) {
						$formula = str_replace('#valor_planilla', '#valor_planilla:', $f['RelacionesConcepto']['formula']);
						$formula = str_replace('::', ':', $formula);
						$saves[$i]['RelacionesConcepto']['id'] = $f['RelacionesConcepto']['id'];
					}
					else {
						$formula = "=" . $data['valor'];
						$saves[$i]['RelacionesConcepto']['id'] = null;
						$saves[$i]['RelacionesConcepto']['desde'] = $periodo['desde'];
						$saves[$i]['RelacionesConcepto']['hasta'] = $periodo['hasta'];
						$saves[$i]['RelacionesConcepto']['relacion_id'] = $novedad['Novedad']['relacion_id'];
						$saves[$i]['RelacionesConcepto']['concepto_id'] = $concepto['Concepto']['id'];
						$saves[$i]['RelacionesConcepto']['observacion'] = "Ingresado desde planilla";
					}
					$saves[$i]['RelacionesConcepto']['formula'] = $formula;
					
				break;
			}
			$i++;
		}
		
		$this->begin();
		foreach($saves as $save) {
			$keys = array_keys($save);
			if($this->Relacion->{$keys[0]}->save($save, true, array(), false)) {
				$c++;
			}
		}
		
		if($i === $c) {
			$this->deleteAll(array("Novedad.id"=>$ids), false, false, false);
			$this->commit();
			return $i;
		}
		else {
			$this->rollback();
			return false;
		}
	}


/**
 * Obtiene un listado de los posibles campos que puedo ingresar por novedades.
 *
 * @return Array con los posibles campos que debo ingresar.
 * @access public.
 */
	function getIngresosPosibles($tipo = "todos") {
		$predefinidos[] = "Horas";
		$predefinidos[] = "Ausencias";
		$predefinidos[] = "Vales";
		if($tipo === "todos") {
			$Concepto = new Concepto();
			$conceptos = $Concepto->find("all", array("conditions"=>array("Concepto.novedad"=>"Si"), "recursive"=>-1));
			return array_merge($predefinidos, Set::extract("/Concepto/nombre", $conceptos));
		}
		elseif($tipo === "predefinidos") {
			return $predefinidos;	
		}
	}

}
?>