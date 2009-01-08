<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a la liquidacion de sueldos.
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
 * La clase encapsula la logica de negocio asociada a la liquidacion de sueldos.
 *
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class LiquidacionesController extends AppController {

	//var $components = array('Formulador', 'DebugKit.Toolbar');
	var $components = array('Formulador');
	//var $helpers = array("ExcelWriter", "Pdf", "Excel");
	private $__relacion;
	private $__variables;
	private $__periodo;
	private $__conceptos;




/**
 * PreLiquidar.
 * Me permite hacer una preliquidacion.
 */
	function preliquidar() {

		$this->__filasPorPagina();
		$this->paginate = array_merge($this->paginate, array('conditions' => array("Liquidacion.estado" => "Sin Confirmar")));

		if ($this->data['Formulario']['accion'] === "generar") {
			/**
			* Realizo las validaciones basicas para poder preliquidar.
			*/
			if (empty($this->data['Condicion']['Liquidacion-periodo'])) {
				$this->Session->setFlash("Debe especificar un periodo.", "error");
			}
			else {
				/**
				* Obtengo el periodo separado por ano, mes y periodo propiamente dicho.
				*/
				$this->__periodo = $this->Util->format($this->data['Condicion']['Liquidacion-periodo'], "periodo");
				if ($this->__periodo === false) {
					$this->Session->setFlash("Debe especificar un periodo valido de la forma AAAAMM[1Q|2Q|M].", "error");
					//redirect
				}
				else if (empty($this->data['Condicion']['Relacion-empleador_id'])
						&& empty($this->data['Condicion']['Relacion-trabajador_id'])
							&& empty($this->data['Condicion']['Relacion-id'])) {
							$this->Session->setFlash("Debe seleccionar un empleador, un trabajador o una relacion laboral.", "error");
				}
				else {
					
					/**
					* Busco las relaciones que debo liquidar de acuerdo a los criterios ingresados.
					*/
					$tipoLiquidacion = $this->data['Condicion']['Liquidacion-tipo'];
					unset($this->data['Condicion']['Liquidacion-tipo']);
					unset($this->data['Condicion']['Liquidacion-periodo']);
					$condiciones = $this->Paginador->generarCondicion($this->data);
					$condiciones['Relacion.ingreso <='] = $this->__periodo['hasta'];
					$condiciones['Relacion.estado'] = "Activa";
					$this->Liquidacion->Relacion->recursive = -1;
					$relaciones = $this->Liquidacion->Relacion->find("all", array(	"contain"		=> array(	"ConveniosCategoria.ConveniosCategoriasHistorico",
																									"Trabajador.ObrasSocial",
																									"Empleador"),
																		"conditions"	=> $condiciones));
					/**
					* Busco las informaciones de los conveniso que pueden necesitarse en las formulas.
					* Lo hago de esta forma, ya que busco todo junto y no uno por uno en cada relacion por una cuestion de performance,
					* ya que seguramente las relaciones liquidadas tengas los mismos convenios.
					*/
					$informaciones = $this->Liquidacion->Relacion->ConveniosCategoria->Convenio->getInformacion(Set::extract("/ConveniosCategoria/convenio_id", $relaciones));
					
					/**
					* Borro TODAS las liquidaciones no confirmadas del usuario.
					*/
					$usuario = $this->Session->read("__Usuario");
					$delete = array("Liquidacion.user_id"=>$usuario['Usuario']['id'], "Liquidacion.estado"=>'Sin Confirmar');
					if (!$this->Liquidacion->deleteAll($delete)) {
						d("ERROR al borrare");
					}
					
					/**
					* Obtengo el listado completo de variables y las inicializo sin valor.
					*/
					$Variable = ClassRegistry::init("Variable");
					$variablesTmp = $Variable->find("all", array("order"=>false));
					foreach ($variablesTmp as $v) {
						$variables[$v['Variable']['nombre']] = $v['Variable'];
					}
					$variables['#tipo_liquidacion']['valor'] = $tipoLiquidacion;
					//$variables['#tipo_liquidacion']['valor'] = $this->data['Condicion']['Liquidacion-tipo'];
					//$variables['#tipo_liquidacion']['valor'] = "normal";
					//d($this->__getVariableValor("#tipo_liquidacion"));
					/**
					* Resuelvo las variables que vienen por parametros.
					$variables['#mes_liquidacion']['valor'] = $periodo['mes'];
					$variables['#ano_liquidacion']['valor'] = $periodo['ano'];
					$variables['#periodo_liquidacion']['valor'] = $periodo['periodo'];
					$variables['#periodo_liquidacion_completo']['valor'] = $periodo['periodoCompleto'];
					$variables['#fecha_desde_liquidacion']['valor'] = $periodo['desde'];
					$variables['#fecha_hasta_liquidacion']['valor'] = $periodo['hasta'];
					$variables['#tipo_liquidacion']['valor'] = $this->data['Extras']['Liquidacion-tipo'];
					*/
					

					/**
					* De las liquidaciones que he seleccionado para pre-liquidar, verifico que no sean
					* liquidaciones ya confirmadas para el mismo periodo del mismo tipo.
					*/
					$condicionesLiquidacion['Liquidacion.mes'] = $this->__periodo['mes'];
					$condicionesLiquidacion['Liquidacion.ano'] = $this->__periodo['ano'];
					$condicionesLiquidacion['Liquidacion.periodo'] = $this->__periodo['periodo'];
					$condicionesLiquidacion['Liquidacion.tipo'] = $variables['#tipo_liquidacion']['valor'];
					$condicionesLiquidacion['Liquidacion.estado'] = "Confirmada";
					$condicionesLiquidacion['Liquidacion.relacion_id'] = Set::extract("/Relacion/id", $relaciones);
					$liquidaciones = $this->Liquidacion->Relacion->Liquidacion->find("all", array(	
																						"recursive"	=> -1,
																						"fields"	=> "relacion_id",
																						"conditions"=> $condicionesLiquidacion));
					
					$confirmadas = Set::extract("/Liquidacion/relacion_id", $liquidaciones);

					/**
					* Recorro cada relacion de las seleccionadas y trato de liquidarle si aun no la he liquidado.
					*/
					$ids = null;
					$opciones['variables'] = $variables;
					$opciones['informaciones'] = $informaciones;
					foreach ($relaciones as $k=>$relacion) {
						if (!in_array($relacion['Relacion']['id'], $confirmadas)) {
							$ids[] = $this->__getLiquidacion($relacion, $opciones);
						}
					}
					
					$condicionesLiquidacion['Liquidacion.estado'] = array("Sin Confirmar", "Confirmada");
					$this->Liquidacion->contain(array("Relacion.Trabajador", "Relacion.Empleador", "LiquidacionesError"));
					$resultados = $this->Paginador->paginar($condicionesLiquidacion);
				}
			}
		}

		if (empty($resultados)) {
			$this->Liquidacion->contain(array("Relacion.Trabajador", "Relacion.Empleador", "LiquidacionesError"));
			$resultados = $this->Paginador->paginar();
		}
		$this->set("registros", $resultados['registros']);
	}


/**
 * Genera una liquidacion para una relacion.
 * La guarda con estado "Sin Confirmar"
 *
 * @param array $relacion Una relacion laboral.
 * @param array $opciones Las opciones que puedo necesitar.
 *		$opciones['variables'] Son las variables que vienen dadas para todas las liquidaciones.
 * @return integer El id de la liquidacion generada.
 * @access private.
 */
	function __getLiquidacion($relacion, $opciones) {
		$this->__saveError = array();
		$this->__saveAuxiliar = array();
		$this->__conceptosSinCalcular = array();
		
		$this->__conceptos = null;
		$this->__relacion = $relacion;

		
		/**
		* Con cada relacion debo recalcular las variables.
		*/
		$this->__setVariable($opciones['variables']);
		
		
		/**
		* Las informaciones que vienen dadas por convenio, son variables ya resuletas.
		*/
		if (!empty($opciones['informaciones'][$relacion['ConveniosCategoria']['convenio_id']])) {
			$this->__setVariable($opciones['informaciones'][$relacion['ConveniosCategoria']['convenio_id']]);
		}
		
		
		/**
		* Verifico si debo hacerle algun descuento.
		*/
		$opcionesDescuentos = null;
		$opcionesDescuentos['desde'] = $this->__getVariableValor("#fecha_desde_liquidacion");
		$opcionesDescuentos['hasta'] = $this->__getVariableValor("#fecha_hasta_liquidacion");
		$opcionesDescuentos['tipo'] = $this->__getVariableValor("#tipo_liquidacion");
		$opcionesDescuentos['periodo'] = $this->__getVariableValor("#periodo_liquidacion");
		//$condicionesDescuentos['smvm'] = $this->__getVariableValor("#smvm");
		$descuentos = $this->Liquidacion->Relacion->Descuento->getDescuentos($relacion, $opcionesDescuentos);
		
		//foreach ($descuentos['concepto'] as $v) {
		$this->__setConcepto($descuentos['concepto'], "SinCalcular");
		//}
		$this->__setAuxiliar($descuentos['auxiliar']);
		

		/**
		* Verifico si tiene licencias para el periodo.
		$opciones = array();
		$opciones['desde'] = $this->__variables['#fecha_desde_liquidacion']['valor'];
		$opciones['hasta'] = $this->__variables['#fecha_hasta_liquidacion']['valor'];
		$licencias = $this->Liquidacion->Relacion->Licencia->buscarDiasLicencia($opciones, $this->__relacion);
		foreach ($licencias as $licencia) {
			foreach ($licencia['variables'] as $licenciaTipo=>$licenciaValor) {
				if (isset($this->__variables[$licenciaTipo])) {
					$this->__variables[$licenciaTipo]['valor'] = $licenciaValor;
				}
				else {
					$error = array(	"tipo"					=>"Variable Inexistente",
									"gravedad"				=>"Alta",
									"concepto"				=>"",
									"variable"				=>$licenciaTipo,
									"formula"				=>"",
									"descripcion"			=>"Hay datos cargados en el sistema y no esta definida la variable para poder usarlos.",
									"recomendacion"			=>"Verifique que la variable este correctamente definida en el sistema.",
									"descripcion_adicional"	=>"");
					$errores[] = $this->__agregarError(array($error));
				}
			}
			$conceptosExrasSinCalcular = am($conceptosExrasSinCalcular, array($licencia['conceptos']));
			if (!empty($licencia['errores'])) {
				$errores[] = $licencia['errores'];
			}
		}
		*/


		/**
		* Verifico si tiene vacaciones para el periodo.
		*/
		//$opciones = array();
		//$opciones['desde'] = $this->__variables['#fecha_desde_liquidacion'];
		//$opciones['hasta'] = $this->__variables['#fecha_hasta_liquidacion'];
		//$vacaciones = $this->Liquidacion->Relacion->Vacacion->buscarDiasVacaciones($opciones, $this->__relacion);
		//d($vacaciones);
		//$conceptosExrasCalculados = am($conceptosExrasCalculados, $descuentos['concepto']);
		//$auxiliar = am($auxiliar, $descuentos['auxiliar']);


		/**
		* Busco las ausencias del periodo.
		$condicionesAusencias = null;
		$condicionesAusencias['desde'] = $this->__variables['#fecha_desde_liquidacion']['valor'];
		$condicionesAusencias['hasta'] = $this->__variables['#fecha_hasta_liquidacion']['valor'];
		$ausencias = $this->Liquidacion->Relacion->Ausencia->buscarAusencia($condicionesAusencias, $this->__relacion);
		foreach ($horas['variables'] as $horaTipo=>$horaValor) {
			$this->__variables[$horaTipo]['valor'] = $horaValor;
		}
		$auxiliar = am($auxiliar, $horas['auxiliar']);
		$conceptosExrasSinCalcular = am($conceptosExrasSinCalcular, $horas['conceptos']);
		*/


		/**
		* Busco las horas trabajadas en el periodo y las cargo al array variables.
		$condicionesHoras = null;
		$condicionesHoras['periodo'] = $this->__getVariableValor("#periodo_liquidacion_completo");
		$horas = $this->Liquidacion->Relacion->Hora->buscarHora($condicionesHoras, $relacion);
		foreach ($horas['variables'] as $horaTipo=>$horaValor) {
			$this->__variables[$horaTipo]['valor'] = $horaValor;
		}
		$auxiliar = am($auxiliar, $horas['auxiliar']);
		$conceptosExrasSinCalcular = am($conceptosExrasSinCalcular, $horas['conceptos']);
		*/

		/**
		* Busco los conceptos para esta relacion.
		*/
		if ($this->__getVariableValor("#tipo_liquidacion") === 'normal') {
			$opcionesFindConcepto = null;
			$opcionesFindConcepto['desde'] = $this->__getVariableValor("#fecha_desde_liquidacion");
			$opcionesFindConcepto['hasta'] = $this->__getVariableValor("#fecha_hasta_liquidacion");
			//$this->__conceptos = $this->Liquidacion->Relacion->RelacionesConcepto->Concepto->findConceptos("Relacion", array_merge(array("relacion"=>$this->__relacion), $opcionesFindConcepto));
			$this->__setConcepto($this->Liquidacion->Relacion->RelacionesConcepto->Concepto->findConceptos("Relacion", array_merge(array("relacion"=>$this->__relacion), $opcionesFindConcepto)), "SinCalcular");
		}
		
		/**
		* Verifico que este el concepto sueldo_basico.
		if (!in_array("sueldo_basico", array_keys($this->__conceptos))) {
			$conceptosExrasSinCalcular = am($conceptosExrasSinCalcular, $this->Liquidacion->Relacion->RelacionesConcepto->Concepto->findConceptos("ConceptoPuntual", am(array("relacion"=>$this->__relacion, "codigoConcepto" => "sueldo_basico"), $opcionesFindConcepto)));
		}
		*/

		/**
		* Agrego los conceptos que aunque no este asociados a la relacion, deben necesariamente estar.
		* No los tengo calculados aun, el liquidador los resuelve como un concepto mas.
		* Los vengo cargando en el array $conceptosExrasSinCalcular.
		*/
		//$this->__conceptos = am($this->__conceptos, $conceptosExrasSinCalcular);

		/**
		* Resuelvo.
		*/
		foreach ($this->__conceptos as $cCod=>$concepto) {
			$resolucion = $this->__getConceptoValor($concepto, $opcionesFindConcepto);
			$this->__conceptos[$cCod] = array_merge($this->__conceptos[$cCod], $resolucion);
		}


		/**
		* Verifico si se generaron errores. No lo hago en el mismo ciclo anterior, porque de la
		* resolucion pueden haberse necesitado conceptos y los errores pueden venir de estos.
		foreach ($this->__conceptos as $cCod=>$concepto) {
			if (!empty($this->__conceptos[$cCod]['errores'])) {
				foreach ($this->__conceptos[$cCod]['errores'] as $error) {
					$errores[] = $this->__agregarError($error);
				}
			}
		}
		*/

		/**
		* Agrego los conceptos que aunque no este asociados a la relacion, deben necesariamente estar.
		* Ya los tengo calculados.
		* Los vengo cargando en el array $conceptosExrasCalculados.
		foreach ($conceptosExrasCalculados as $conceptoExraCalculado) {
			$this->__conceptos = am($conceptoExraCalculado, $this->__conceptos);
		}
		*/

		/**
		* Preparo el array para guardar la pre-liquidacion.
		* Lo guardo como una liquidacion con estado "Sin Confirmar".
		* Cuando se confirma, solo cambio el estado, sino, a la siguiente pasada del preliquidador, la elimino.
		*/
		$liquidacion = null;
		$liquidacion['fecha'] = date("Y-m-d");
		$liquidacion['ano'] = $this->__getVariableValor('#ano_liquidacion');
		$liquidacion['mes'] = $this->__getVariableValor('#mes_liquidacion');
		$liquidacion['periodo'] = $this->__getVariableValor('#periodo_liquidacion');
		$liquidacion['tipo'] = $this->__getVariableValor('#tipo_liquidacion');
		$liquidacion['estado'] = "Sin Confirmar";
		$liquidacion['relacion_id'] = $this->__relacion['Relacion']['id'];
		$liquidacion['relacion_ingreso'] = $this->__relacion['Relacion']['ingreso'];
		$liquidacion['relacion_horas'] = $this->__relacion['Relacion']['horas'];
		$liquidacion['relacion_basico'] = $this->__relacion['Relacion']['basico'];
		$liquidacion['relacion_area_id'] = $this->__relacion['Relacion']['area_id'];
		$liquidacion['trabajador_id'] = $this->__relacion['Trabajador']['id'];
		$liquidacion['trabajador_cuil'] = $this->__relacion['Trabajador']['cuil'];
		$liquidacion['trabajador_nombre'] = $this->__relacion['Trabajador']['nombre'];
		$liquidacion['trabajador_apellido'] = $this->__relacion['Trabajador']['apellido'];
		$liquidacion['empleador_id'] = $this->__relacion['Empleador']['id'];
		$liquidacion['empleador_cuit'] = $this->__relacion['Empleador']['cuit'];
		$liquidacion['empleador_nombre'] = $this->__relacion['Empleador']['nombre'];
		$liquidacion['empleador_direccion'] = $this->__relacion['Empleador']['direccion'];
		$liquidacion['convenio_categoria_convenio_id'] = $this->__relacion['ConveniosCategoria']['convenio_id'];
		$liquidacion['convenio_categoria_nombre'] = $this->__relacion['ConveniosCategoria']['nombre'];
		$liquidacion['convenio_categoria_costo'] = $this->__relacion['ConveniosCategoria']['costo'];
		$liquidacion['convenio_categoria_jornada'] = $this->__relacion['ConveniosCategoria']['jornada'];

		$totales['remunerativo'] = 0;
		$totales['no_remunerativo'] = 0;
		$totales['deduccion'] = 0;
		$totales['total_beneficios'] = 0;
		$totales['total_pesos'] = 0;
		$detalle = null;
		foreach ($this->__conceptos as $detalleLiquidacion) {
			$v = $this->__agregarDetalle($detalleLiquidacion);
			if (!empty($v)) {
				$detalle[] = $this->__agregarDetalle($detalleLiquidacion);
			}


			if ($detalleLiquidacion['imprimir'] === "Si" || $detalleLiquidacion['imprimir'] === "Solo con valor") {

				$pago = "total_" . strtolower($detalleLiquidacion['pago']);
				switch ($detalleLiquidacion['tipo']) {
					case "Remunerativo":
						$totales[$pago] += $detalleLiquidacion['valor'];
						$totales['remunerativo'] += $detalleLiquidacion['valor'];
						break;
					case "No Remunerativo":
						$totales[$pago] += $detalleLiquidacion['valor'];
						$totales['no_remunerativo'] += $detalleLiquidacion['valor'];
						break;
					case "Deduccion":
						$totales[$pago] -= $detalleLiquidacion['valor'];
						$totales['deduccion'] += $detalleLiquidacion['valor'];
						break;
				}
			}
		}
		//d($totales);
		$totales['no_remunerativo'] -= $totales['total_beneficios'] ;
		$totales['total'] = $totales['remunerativo'] + $totales['no_remunerativo'] - $totales['deduccion'];

		/**
		* Si a este empleador hay que aplicarle redondeo, lo hago y lo dejo expresado
		* con el concepto redondeo en el detalle de la liquidacion.
		*/
		if ($this->__relacion['Empleador']['redondear'] === "Si") {
			$redondeo = round($totales['total']) - $totales['total'];
			if ($redondeo !== 0) {
				$opcionesFindConcepto['codigoConcepto'] = "redondeo";
				$conceptoRedondeo = $this->Liquidacion->Relacion->RelacionesConcepto->Concepto->findConceptos("ConceptoPuntual", am(array("relacion"=>$this->__relacion), $opcionesFindConcepto));
				$conceptoRedondeo['redondeo']['debug'] = "=" . round($totales['total']) . " - " . $totales['total'];
				$conceptoRedondeo['redondeo']['valor_cantidad'] = "0";

				/**
				* Modifico el total.
				*/
				$totales['total'] += $redondeo;
				$totales['total_pesos'] += $redondeo;

				/**
				* Dependiendo del signo, lo meto como un concepto Remunerativo o una Deduccion.
				*/
				if ($redondeo > 0) {
					$totales['remunerativo'] += $redondeo;
					$conceptoRedondeo['redondeo']['tipo'] = "No Remunerativo";
					$conceptoRedondeo['redondeo']['valor'] = $redondeo;
				}
				else {
					$totales['deduccion'] += $redondeo;
					$conceptoRedondeo['redondeo']['tipo'] = "Deduccion";
					$conceptoRedondeo['redondeo']['valor'] = ($redondeo * -1);
				}
				$detalle[] = $this->__agregarDetalle($conceptoRedondeo['redondeo']);
			}
		}

		foreach (array('remunerativo', 'no_remunerativo', 'deduccion', 'total_pesos', 'total_beneficios', 'total') as $total) {
			$totales[$total] = number_format($totales[$total], 3, '.', '');
		}
		
		/**
		* Genero los pagos pendientes.
		* Diferencio en los diferentes tipos (beneficios o pesos).
		*/
		$auxiliar = null;
		$auxiliar['estado'] = "Pendiente";
		$auxiliar['fecha'] = "##MACRO:fecha_liquidacion##";
		$auxiliar['liquidacion_id'] = "##MACRO:liquidacion_id##";
		$auxiliar['relacion_id'] = $liquidacion['relacion_id'];
		
		$auxiliar['monto'] = $totales['total_pesos'];
		$auxiliar['moneda'] = "Pesos";
		$this->__setAuxiliar(array("save"=>serialize($auxiliar), "model" => "Pago"));
		
		$auxiliar['monto'] = $totales['total_beneficios'];
		$auxiliar['moneda'] = "Beneficios";
		$this->__setAuxiliar(array("save"=>serialize($auxiliar), "model" => "Pago"));
		
		$save['Liquidacion']			= array_merge($liquidacion, $totales);
		$save['LiquidacionesDetalle']	= $detalle;
		
		$auxiliar = null;
		$auxiliar = $this->__getAuxiliar();
		if (!empty($auxiliar)) {
			$save['LiquidacionesAuxiliar'] = $auxiliar;
		}
		
		$error = null;
		$error = $this->__getError();
		if (!empty($error)) {
			$save['LiquidacionesError'] = $error;
		}
		
		$save['Liquidacion']			= array_merge($liquidacion, $totales);
		$save['LiquidacionesDetalle']	= $detalle;
		$this->Liquidacion->create();
		if ($this->Liquidacion->saveAll($save)) {
			return $this->Liquidacion->id;
		}
		else {
			return false;
		}
	}





/**
* Dado un concepto, resuelve la formula.
*/
	function __getConceptoValor($concepto, $opciones) {
		$valor = null;
		$errores = array();
		$formula = $concepto['formula'];
		
		/**
		* Si en la formula hay variables, busco primero estos valores.
		*/
		if (preg_match_all("/(#[a-z0-9_]+)/", $formula, $variablesTmp)) {

			foreach ($variablesTmp[1] as $k=>$v) {
				/**
				* Debe buscar la variable para reemplazarla dentro de la formula.
				* Usa la RegEx y no str_replace, porque por ejemplo, si debo reemplzar #horas, y en cuentra
				* #horas lo hara ok, pero si encuentra #horas_enfermedad, dejara REEMPLAZO_enfermedad.
				*/
				$formula = preg_replace("/".$v."(\W)|".$v."$/", $this->__getVariableValor($v) . "$1", $formula);
			}
		}
		
		/**
		* TODO. ver esta variable
		*/
		$conceptoCantidad = 0;
		/**
		* Si en la cantidad hay una variable, la reemplazo.
		$conceptoCantidad = 0;
		if (!empty($concepto['cantidad'])) {
			if (isset($this->__variables[$concepto['cantidad']])) {
				if ($this->__variables[$concepto['cantidad']] !== "#N/A") {
					$conceptoCantidad = $this->__variables[$concepto['cantidad']]['valor'];
				}
				else {
					$errores[] = array(	"tipo"					=>"Variable No Resuelta",
          								"gravedad"				=>"Media",
										"concepto"				=>$concepto['codigo'],
										"variable"				=>$concepto['cantidad'],
										"formula"				=>$concepto['formula'],
										"descripcion"			=>"La cantidad intenta usar una variable que no ha podido ser resuelta.",
										"recomendacion"			=>"Verifique que los datos hayan sido correctamente ingresados.",
										"descripcion_adicional"	=>$this->__variables[$v]['formula']);
				}
			}
			else {
				$errores[] = array(	"tipo"					=>"Variable Inexistente",
         							"gravedad"				=>"Media",
									"concepto"				=>$concepto['codigo'],
									"variable"				=>$concepto['cantidad'],
									"formula"				=>$concepto['formula'],
									"descripcion"			=>"La cantidad intenta usar una variable inexistente.",
									"recomendacion"			=>"Verifique que la cantidad este correctamente definida y que la variable que la cantidad utiliza exista en el sistema.",
									"descripcion_adicional"	=>"");
			}
		}
		*/


		/**
		* Verifico si el nombre que se muestra del concepto es una formula, la resuelvo.
		*/
		if (!empty($concepto['nombre_formula'])) {
			$nombreConcepto = $concepto['nombre_formula'];
			
			/**
			* Si en el nombre hay variables, busco primero estos valores.
			*/
			if (preg_match_all("/(#[a-z0-9_]+)/", $nombreConcepto, $variablesTmp)) {
				foreach ($variablesTmp[1] as $k=>$v) {
					/**
					* Debe buscar la variable para reemplazarla dentro de la formula.
					* Usa la RegEx y no str_replace, porque por ejemplo, si debo reemplzar #horas, y en cuentra
					* #horas lo hara ok, pero si encuentra #horas_enfermedad, dejara REEMPLAZO_enfermedad.
					*/
					$nombreConcepto = preg_replace("/".$v."(\W)|".$v."$/", $this->__getVariableValor($v) . "$1", $nombreConcepto);
				}
			}
			if (substr($nombreConcepto, 0, 4) === "=if (") {
				$nombreConcepto = $this->Formulador->resolver($nombreConcepto);
			}
			else {
				$nombreConcepto = str_replace("=", "", $nombreConcepto);
			}
		}
		else {
			$nombreConcepto = $concepto['nombre'];
		}

		/**
		* Veo si es una formula, hay un not, obtengo los conceptos y rearmo los formula eliminando la perte del not.
		*/
		if (preg_match("/not\((.*)\)/", $formula, $matches)) {
			$pos = strpos($matches[1], ")");
			if ($pos) {
				$formula = str_replace(", ", ",", $formula);
				$formula = str_replace(" ,", ",", $formula);
				$reemplazoNot = str_replace(", ", ",", $matches[1]);
				$reemplazoNot = str_replace(" ,", ",", $reemplazoNot);
				$reemplazoNot = substr($reemplazoNot, 0, $pos);
				$conceptosNot = explode(",", str_replace("@", "", $reemplazoNot));
				$reemplazoNot = "not(" . $reemplazoNot . ",";
			}
			$formula = str_replace($reemplazoNot, "", $formula);
		}
		
		
		/**
		* Veo si es una formula, que me indica la suma del remunerativo, de las deducciones o del no remunerativo.
		*/
		if (preg_match("/^=sum[\s]*\([\s]*(Remunerativo|Deduccion|No\sRemunerativo)[\s]*\)$/i", $formula, $matches)) {
			if (!isset($conceptosNot)) {
				$conceptosNot = array();
			}
			foreach ($this->__conceptos as $conceptoTmp) {
				if (!in_array($conceptoTmp['codigo'], $conceptosNot) && $conceptoTmp['tipo'] == $matches[1] && ($conceptoTmp['imprimir'] === "Si" || $conceptoTmp['imprimir'] === "Solo con valor")) {
					if (empty($conceptoTmp['valor'])) {
						$resolucionCalculo = $this->__getConceptoValor($conceptoTmp, $opciones);
						$this->__conceptos[$conceptoTmp['codigo']] = am($resolucionCalculo, $this->__conceptos[$conceptoTmp['codigo']]);
						$conceptoTmp['valor'] = $resolucionCalculo['valor'];
					}
					$valor += $conceptoTmp['valor'];
				}
			}
		}

		/**
		* Veo si es una formula, que tiene otros conceptos dentro.
		* Lo se porque los codigos de los conceptos empiezan siempre con @.
		*/
		elseif (substr($formula, 0, 1) === "=") {
			
			/**
			* Verifico que tenga calculado todos los conceptos que esta formula me pide.
			* Si aun no lo tengo, lo calculo.
			*/
			if (preg_match_all("/(@[\w]+)/", $formula, $matches)) {
				foreach ($matches[1] as $match) {
					$match = substr($match, 1);
					
					/**
					* Si no esta, lo busco.
					*/
					if (!isset($this->__conceptos[$match])) {
						/**
						* Busco los conceptos que puedan estar faltandome.
						* Los agrego al array de conceptos identificandolos y poniendoles el estado a no imprimir.
						*/
						$conceptoParaCalculoTmp = $this->Liquidacion->Relacion->RelacionesConcepto->Concepto->findConceptos("ConceptoPuntual", array_merge(array("relacion"=>$this->__relacion, "codigoConcepto"=>$match), $opciones));
						if (empty($conceptoParaCalculoTmp)) {
							$this->__setError(array(	"tipo"					=> "Concepto Inexistente",
														"gravedad"				=> "Alta",
														"concepto"				=> $match,
														"variable"				=> "",
														"formula"				=> $formula,
														"descripcion"			=> "La formula requiere de un concepto inexistente.",
														"recomendacion"			=> "Verifique la formula y que todos los conceptos que esta utiliza existan.",
														"descripcion_adicional"	=> "verifique: " . $concepto['codigo']));
						}
						else {
							$conceptoParaCalculo = array_pop($conceptoParaCalculoTmp);
							$conceptoParaCalculo['imprimir'] = "No";
							$this->__conceptos[$match] = $conceptoParaCalculo;
						}
					}
					
					/**
					* Si no tiene valor, lo calculo.
					*/
					if (!isset($this->__conceptos[$match]['valor'])) {
						if (isset($this->__conceptos[$match])) {
							$resolucionCalculo = $this->__getConceptoValor($this->__conceptos[$match], $opciones);
							$this->__conceptos[$match] = array_merge($resolucionCalculo, $this->__conceptos[$match]);
						}
						else {
							$this->__setError(array(	"tipo"					=> "Concepto Inexistente",
														"gravedad"				=> "Alta",
														"concepto"				=> $match,
														"variable"				=> "",
														"formula"				=> $formula,
														"descripcion"			=> "La formula requiere de un concepto inexistente.",
														"recomendacion"			=> "Verifique la formula y que todos los conceptos que esta utiliza existan.",
														"descripcion_adicional"	=> "verifique: " . $concepto['codigo']));
						}
					}
						
					/**
					* Reemplazo en la formula el concepto por su valor.
					*/
					if (isset($this->__conceptos[$match])) {
						$resolucionCalculo['valor'] = $this->__conceptos[$match]['valor'];
						$formula = preg_replace("/(@" . $match . ")([\)|\s|\*|\+\/\-|\=|\,]*[^_])/", $resolucionCalculo['valor'] . "$2", $formula);
						$resolucionCalculo['debug'] = $formula;
					}
					else {
						$this->__setError(array(	"tipo"					=> "Concepto Inexistente",
													"gravedad"				=> "Alta",
													"concepto"				=> $match,
													"variable"				=> "",
													"formula"				=> $formula,
													"descripcion"			=> "La formula requiere de un concepto inexistente.",
													"recomendacion"			=> "Verifique la formula y que todos los conceptos que esta utiliza existan.",
													"descripcion_adicional"	=> "verifique: " . $concepto['codigo']));
					}
				}
			}

			/**
			* Resuelvo la formula.
			*/
			$valor = $this->Formulador->resolver($formula);
		}
		elseif (empty($formula)) {
			$this->__setError(array(	"tipo"					=> "Formula de Concepto Inexistente",
										"gravedad"				=> "Media",
										"concepto"				=> $concepto['codigo'],
										"variable"				=> "",
										"formula"				=> "",
										"descripcion"			=> "El concepto no tiene definida una formula.",
										"recomendacion"			=> "Ingrese la formula correspondiente al concepto en caso de que sea necesario. Para evitar este error ingrese como formula: =0",
										"descripcion_adicional"	=> "Se asume como 0 (cero) el valor del concepto."));
			$valor = 0;
		}
		else {
			$valor = "#N/A";
		}
		
		return array("valor"=>$valor, "debug"=>$formula, "valor_cantidad"=>$conceptoCantidad, "nombre"=>$nombreConcepto, "errores"=>$errores);
	}


	
	
	function __setError($error) {
		$this->__saveError[] = $error;
	}
	
	function __getError() {
		return $this->__saveError;
	}
	
	
/**
 * Busca el valor de una variable.
 *
 * @param string $variable El nombre de la variable que busco.
 * @return mixed El valor de la variable.
 * @access private.
 */
	function __getVariableValor($variable) {
		
		if (!isset($this->__variables[$variable])) {
			$this->__setError(array(	"tipo"					=> "Variable Inexistente",
										"gravedad"    			=> "Media",
										"concepto"				=> "",
										"variable"				=> $variable,
										"formula"				=> "",
										"descripcion"			=> "La formula intenta usar una variable inexistente. Se resolvera a 0 (cero) su valor.",
										"recomendacion"			=> "Verifique que la formula este correctamente definida y que las variables que esta formula utiliza existan en el sistema.",
										"descripcion_adicional"	=> ""));
			
			$this->__variables[$variable]['valor'] = "0";
			return $this->__variables[$variable]['valor'];
		}
		/**
		* Ya he resuelto esta variable anteriormente.
		*/
		else if (isset($this->__variables[$variable]['valor'])) {
			return $this->__variables[$variable]['valor'];
		}
		/**
		* Intento resolverla.
		*/
		else {
			/**
			* Si es una formula, la resuelvo.
			*/
			if (substr($this->__variables[$variable]['formula'], 0, 1) === "=") {
				//d("X");
				//d($this->__variables[$variable]['formula']);
				$formula = $this->__variables[$variable]['formula'];
				/**
				* Si en la formula hay variables, busco primero estos valores.
				*/
				if (preg_match_all("/(#[a-z0-9_]+)/", $formula, $variables_tmp)) {
					foreach ($variables_tmp[1] as $v) {
						$formula = preg_replace("/(" . $v . ")([\)|\s|\*|\+\/\-|\=|\,]*[^_])/", $this->__getVariableValor($v) . "$2", $formula);
					}
				}
				
				$valor = $this->Formulador->resolver($formula);
				
				if ($valor === "#N/A") {
					$valor = 0;
					$this->__setError(array(	"tipo"					=> "Variable No Resuelta",
												"gravedad"    			=> "Alta",
												"concepto"				=> "",
												"variable"				=> $variable,
												"formula"				=> $this->__variables[$variable]['formula'],
												"descripcion"			=> "La formula intenta usar una variable que no es posible resolverla con los datos de la relacion.",
												"recomendacion"			=> "Verifique que la relacion tenga cargados todos los datos necesarios.",
												"descripcion_adicional"	=> ""));
				}
				$this->__variables[$variable]['valor'] = $valor;
				return $this->__variables[$variable]['valor'];
			}
			
			
			/**
			* Busco si es una variable que viene dada por la relacion.
			* Depende de recursive, puede venir $data[model1][model2][campo] 0 $data[model1][campo]
			*/
			if (preg_match("/^\[([a-zA-Z]*)\]\[([a-zA-Z]*)\]\[([a-zA-Z_]*)\]$/", $this->__variables[$variable]['formula'], $matchesA) || preg_match("/^\[([a-zA-Z]*)\]\[([a-zA-Z_]*)\]$/", $this->__variables[$variable]['formula'], $matchesB)) {
				if (isset($matchesA[1]) && isset($matchesA[2]) && isset($matchesA[3]) && isset($this->__relacion[$matchesA[1]][$matchesA[2]][$matchesA[3]])) {
					$valor = $this->__relacion[$matchesA[1]][$matchesA[2]][$matchesA[3]];
				}
				elseif (isset($matchesB[1]) && isset($matchesB[2]) && isset($this->__relacion[$matchesB[1]][$matchesB[2]])) {
					$valor = $this->__relacion[$matchesB[1]][$matchesB[2]];
				}
				else {
					$this->__setError(array(	"tipo"					=> "Variable No Resuelta",
												"gravedad"    			=> "Alta",
												"concepto"				=> "",
												"variable"				=> $variable,
												"formula"				=> $this->__variables[$variable]['formula'],
												"descripcion"			=> "La formula intenta usar una variable que no es posible resolverla con los datos de la relacion.",
												"recomendacion"			=> "Verifique que la relacion tenga cargados todos los datos necesarios.",
												"descripcion_adicional"	=> ""));
					
					$this->__variables[$variable]['valor'] = "0";
					return $this->__variables[$variable]['valor'];
				}
				
				switch($this->__variables[$variable]['formato']) {
					case "Minuscula":
						$valor = strtolower($valor);
						break;
					case "Mayuscula":
						$valor = strtoupper($valor);
						break;
				}
				$this->__variables[$variable]['valor'] = $valor;
				return $this->__variables[$variable]['valor'];
			}
			
			
			/**
			* Son variables "sistemicas". Las resuelvo en "duro".
			*/
			switch($variable) {
				case "#mes_liquidacion":
					$this->__variables[$variable]['valor'] = $this->__periodo['mes'];
				break;
				case "#ano_liquidacion":
					$this->__variables[$variable]['valor'] = $this->__periodo['ano'];
				break;
				case "#periodo_liquidacion":
					$this->__variables[$variable]['valor'] = $this->__periodo['periodo'];
				break;
				case "#periodo_liquidacion_completo":
					$this->__variables[$variable]['valor'] = $this->__periodo['periodoCompleto'];
				break;
				case "#fecha_desde_liquidacion":
					$this->__variables[$variable]['valor'] = $this->__periodo['desde'];
				break;
				case "#fecha_hasta_liquidacion":
					$this->__variables[$variable]['valor'] = $this->__periodo['hasta'];
				break;
				case "#fecha_actual":
					$this->__variables[$variable]['valor'] = date("Y-m-d");
				break;
				case "#dia_ingreso":
					$this->__variables[$variable]['valor'] = $this->Util->format($this->__getVariableValor('#fecha_ingreso'), "dia");
				break;
				case "#dia_egreso":
					$this->__variables[$variable]['valor'] = $this->Util->format($this->__getVariableValor('#fecha_egreso'), "dia");
				break;
				case "#mes_ingreso":
					$this->__variables[$variable]['valor'] = $this->Util->format($this->__getVariableValor('#fecha_ingreso'), "mes");
				break;
				case "#mes_egreso":
					$this->__variables[$variable]['valor'] = $this->Util->format($this->__getVariableValor('#fecha_egreso'), "mes");
				break;
				case "#ano_ingreso":
					$this->__variables[$variable]['valor'] = $this->Util->format($this->__getVariableValor('#fecha_ingreso'), "ano");
				break;
				case "#ano_egreso":
					$this->__variables[$variable]['valor'] = $this->Util->format($this->__getVariableValor('#fecha_egreso'), "ano");
				break;
				case "#dia_desde_liquidacion":
					$this->__variables[$variable]['valor'] = $this->__periodo['desde'];
				break;
				case "#dia_hasta_liquidacion":
					$this->__variables[$variable]['valor'] = $this->__periodo['hasta'];
				break;
				case "#dias_antiguedad":
				case "#meses_antiguedad":
				case "#anos_antiguedad":
					$fechaEgreso = $this->__getVariableValor('#fecha_egreso');
					if ($fechaEgreso !== "0000-00-00" && $fechaEgreso < $this->__getVariableValor('#fecha_hasta_liquidacion')) {
						$antiguedad = $this->Util->dateDiff($this->__getVariableValor('#fecha_ingreso'), $fechaEgreso);
						$agregarDias = 0;
					}
					else {
						$antiguedad = $this->Util->dateDiff($this->__getVariableValor('#fecha_ingreso'), $this->__getVariableValor('#fecha_hasta_liquidacion'));
						$agregarDias = 1;
					}
					if ($variable === "#dias_antiguedad") {
						$this->__variables[$variable]['valor'] = ($antiguedad['dias'] + $agregarDias);
					}
					elseif ($variable === "#meses_antiguedad") {
						$this->__variables[$variable]['valor'] = floor(($antiguedad['dias'] + $agregarDias) / 30);
					}
					elseif ($variable === "#anos_antiguedad") {
						$this->__variables[$variable]['valor'] = floor(($antiguedad['dias'] + $agregarDias) / 365);
					}
				break;
				case "#dias_corridos_periodo":
					if ($this->__getVariableValor('#dia_ingreso') > 1
						&& $this->__getVariableValor('#mes_ingreso') === $this->__getVariableValor('#mes_liquidacion')
						&& $this->__getVariableValor('#ano_ingreso') === $this->__getVariableValor('#ano_liquidacion')) {
						$desde = $this->__getVariableValor('#fecha_ingreso');
					}
					else {
						$desde = $this->__getVariableValor('#fecha_desde_liquidacion');
					}
	
					if ($this->__getVariableValor('#dia_egreso') < $this->__getVariableValor('#dia_hasta_liquidacion')
						&& $this->__getVariableValor('#mes_egreso') === $this->__getVariableValor('#mes_liquidacion')
						&& $this->__getVariableValor('#ano_egreso') === $this->__getVariableValor('#ano_liquidacion')) {
						$hasta = $this->__getVariableValor('#fecha_egreso');
						$agregarDias = 0;
					}
					else {
						$hasta = $this->__getVariableValor('#fecha_hasta_liquidacion');
						$agregarDias = 1;
					}
					$antiguedad = $this->Util->dateDiff($desde, $hasta);
					$this->__variables[$variable]['valor'] = $antiguedad['dias'] + $agregarDias;
				break;
				case "#dias_antiguedad_al_31_12":
				case "#meses_antiguedad_al_31_12":
				case "#anos_antiguedad_al_31_12":
					$this->__getVariables(array("#fecha_ingreso"));
					$anoAnterior = $this->Util->traerAno(array("fecha" => $this->__variables['#fecha_ingreso']['valor'])) - 1;
					$fechaHasta = $this->Util->traerFecha(array("ano"=>$anoAnterior, "mes"=>12, "dia"=>31));
					if ($fechaHasta > $this->__variables['#fecha_ingreso']['valor']) {
						$antiguedad = $this->Util->diferenciaEntreFechas(array("hasta"=>$fechaHasta, "desde"=>$this->__variables['#fecha_ingreso']['valor']));
					}
					else {
						$antiguedad['dias'] = 0;
					}
					if ($variable == "#dias_antiguedad_al_31_12") {
						$this->__variables[$variable]['valor'] = $antiguedad['dias'];
					}
					elseif ($variable == "#meses_antiguedad_al_31_12") {
						$this->__variables[$variable]['valor'] = floor($antiguedad['dias'] / 30);
					}
					elseif ($variable == "#anos_antiguedad_al_31_12") {
						$this->__variables[$variable]['valor'] = floor($antiguedad['dias'] / 365);
					}
					break;
				/*
				case "#dias_vacaciones":
					$this->__variables[$variable]['valor'] = 0;
					break;
				case "#dias_licencia":
					$this->__variables[$variable]['valor'] = 0;
					break;
				*/
				case "#horas":
				case "#horas_ajuste":
				case "#horas_ajuste_extra_100":
				case "#horas_ajuste_extra_50":
				case "#horas_ajuste_extra_nocturna_100":
				case "#horas_ajuste_extra_nocturna_50":
				case "#horas_ajuste_nocturna":
				case "#horas_extra_100":
				case "#horas_extra_50":
				case "#horas_extra_nocturna_100":
				case "#horas_extra_nocturna_50":
				case "#horas_nocturna":
					/**
					* Busco las horas trabajadas en el periodo y las cargo al array variables.
					*/
					$horas = $this->Liquidacion->Relacion->Hora->getHoras($this->__relacion, $this->__periodo);
					foreach ($horas['variables'] as $horaTipo=>$horaValor) {
						$this->__variables[$horaTipo]['valor'] = $horaValor;
					}
					$this->__setAuxiliar($horas['auxiliar']);
					$this->__setConcepto($horas['conceptos'], "SinCalcular");
				break;
				case "#ausencias_justificadas":
				case "#ausencias_injustificadas":
					$ausencias = $this->Liquidacion->Relacion->Ausencia->getAusencias($this->__relacion, $this->__periodo);
					$this->__variables["#ausencias_justificadas"]['valor'] = $ausencias['Justificada'];
					$this->__variables["#ausencias_injustificadas"]['valor'] = $ausencias['Injustificada'];
				break;
			}
			/*
					$this->__setError(array(	"tipo"					=> "Variable No Resuelta",
												"gravedad"    			=> "Alta",
												"concepto"				=> "",
												"variable"				=> $variable,
												"formula"				=> $this->__variables[$variable]['formula'],
												"descripcion"			=> "La formula intenta usar una variable que no es posible resolverla con los datos de la relacion.",
												"recomendacion"			=> "Verifique que la relacion tenga cargados todos los datos necesarios.",
												"descripcion_adicional"	=> ""));
			*/		
			return $this->__variables[$variable]['valor'];
		}
	}

	
	

/**
 * Agrega conceptos al array de conceptos de su tipo.
 *
 * @param array $variable Puede ser una variable (que viene expresada como array, o un array de variables.
 * @return void.
 * @access private.
 */
	function __setVariable($variable) {
		foreach ($variable as $variableNombre => $variableDefinicion) {
			if (!is_array($variableDefinicion)) {
				$tmp = $variableDefinicion;
				$variableDefinicion = null;
				$variableDefinicion['valor'] = $tmp;
			}
			$this->__variables[$variableNombre] = $variableDefinicion;
		}
	}


/**
 * Agrega conceptos al array de conceptos de su tipo.
 *
 * @param array $concepto Conceptos.
 * @param string $concepto El tipo de conceptos que se desea agregar.
 * @return void.
 * @access private.
 */
	function __setConcepto($conceptos, $tipo = "SinCalcular") {
		if ($tipo === "SinCalcular") {
			if (empty($this->__conceptos)) {
				//$this->__conceptosSinCalcular = $conceptos;
				$this->__conceptos = $conceptos;
			}
			else {
				//$this->__conceptosSinCalcular = array_merge($this->__conceptosSinCalcular, $conceptos);
				$this->__conceptos = array_merge($this->__conceptos, $conceptos);
			}
		}
	}


/**
 * Agrega datos que seran guardados en la tabla liquidaciones_auxiliares.
 *
 * @param array $auxiliar Los datos a guardar.
 * @return void.
 * @access private.
 */
	function __setAuxiliar($auxiliar) {
		if (!empty($auxiliar)) {
			if (!isset($auxiliar[0])) {
				$auxiliar = array($auxiliar);
			}
			if (empty($this->__saveAuxiliar)) {
				$this->__saveAuxiliar = $auxiliar;
			}
			else {
				$this->__saveAuxiliar = array_merge($this->__saveAuxiliar, $auxiliar);
			}
		}
	}

	
	function __getAuxiliar() {
		return $this->__saveAuxiliar;
	}
	
	
/**
 * Agrega una variable al conjunto de variables ya resueltas.
 *
 * @param string $variable El nombre de la variable que ya ha sido resuelta.
 * @return void.
 * @access private.
 */
	//function __setVariableResuelta($variable) {
	//	$this->__variablesResueltas[$variable] = $variable;
	//}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
/**
* Esta funcion realiza el mapeo entre lo que tengo en el array de conceptos,
* y los datos que necesito para guardarlo en el detalle de la liquidacion.
*/
	function __agregarDetalle($detalleLiquidacion) {
		//debug($detalleLiquidacion);
		$detalle = null;
		if (!empty($detalleLiquidacion['concepto_id'])) {
			$detalle['concepto_id'] = $detalleLiquidacion['concepto_id'];
			$detalle['concepto_codigo'] = $detalleLiquidacion['codigo'];
			$detalle['concepto_nombre'] = $detalleLiquidacion['nombre'];
			$detalle['concepto_tipo'] = $detalleLiquidacion['tipo'];
			$detalle['concepto_periodo'] = $detalleLiquidacion['periodo'];
			$detalle['concepto_sac'] = $detalleLiquidacion['sac'];
			$detalle['concepto_imprimir'] = $detalleLiquidacion['imprimir'];
			$detalle['concepto_antiguedad'] = $detalleLiquidacion['antiguedad'];
			//$detalle['concepto_remuneracion'] = $detalleLiquidacion['remuneracion'];
			$detalle['concepto_formula'] = $detalleLiquidacion['formula'];
			$detalle['concepto_cantidad'] = $detalleLiquidacion['cantidad'];
			$detalle['concepto_orden'] = $detalleLiquidacion['orden'];
			$detalle['coeficiente_id'] = $detalleLiquidacion['coeficiente_id'];
			$detalle['coeficiente_nombre'] = $detalleLiquidacion['coeficiente_nombre'];
			$detalle['coeficiente_tipo'] = $detalleLiquidacion['coeficiente_tipo'];
			$detalle['coeficiente_valor'] = $detalleLiquidacion['coeficiente_valor'];
			$detalle['debug'] = $detalleLiquidacion['debug'];
			$detalle['valor'] = $detalleLiquidacion['valor'];
			$detalle['valor_cantidad'] = $detalleLiquidacion['valor_cantidad'];
		}
		return $detalle;
	}


/**
 * Esta funcion realiza el mapeo entre lo que tengo en el array de errores,
 * y los datos que necesito para guardarlo en la tabla liquidaciones_errores.
 */
	function __agregarError_deprecated($errorLiquidacion) {
		$error = null;
		$error['tipo'] = $errorLiquidacion['tipo'];
		$error['concepto'] = $errorLiquidacion['concepto'];
		$error['gravedad'] = $errorLiquidacion['gravedad'];
		$error['variable'] = $errorLiquidacion['variable'];
		$error['recomendacion'] = $errorLiquidacion['recomendacion'];
		$error['formula'] = $errorLiquidacion['formula'];
		$error['descripcion'] = $errorLiquidacion['descripcion'];
		$error['descripcion_adicional'] = $errorLiquidacion['descripcion_adicional'];
		return $error;
	}

	
	
/**
 * recibo_pdf.
 * Genera un archivo pdf con el recibo.
 */
	function recibo_pdf($id) {
		$this->Liquidacion->contain(array("LiquidacionesDetalle"));
		$this->data = $this->Liquidacion->read(null, $id);
		$this->layout = "pdf";
		//d($this->data);
	}


	function edit_en_linea_deprecated() {
		
		$this->set("data", $conceptoCodigo . " " . $liquidacionId);
		$this->render("../elements/autocomplete");
	}

/**
 * recibo_html.
 * Muestra via desglose el recibo (detalle) de la preliquidacion.
 */
	function recibo_html($id = null) {
		$this->Liquidacion->contain(array("LiquidacionesDetalle"));

		//test - borrar
		//$id = null;
		//$this->params['form'] = unserialize('a:4:{s:5:"valor";s:5:"6.001";s:2:"id";s:0:"";s:13:"liquidacionId";s:2:"36";s:14:"conceptoCodigo";s:14:"horas_extra_50";}');
		if (!empty($id)) {
			$this->data = $this->Liquidacion->read(null, $id);
		}
		elseif (!empty($this->params['form']['valor']) && !empty($this->params['form']['conceptoCodigo']) && !empty($this->params['form']['liquidacionId'])) {
			//$valor = $this->params['form']['valor'];
			//$conceptoCodigo = $this->params['form']['conceptoCodigo'];
			//$liquidacionId = $this->params['form']['liquidacionId'];
			$id = $this->params['form']['liquidacionId'];
			$this->Liquidacion->addEditDetalle($this->params['form']);

			$liquidacion = $this->Liquidacion->findById($id);
			$this->Liquidacion->Relacion->contain(array("ConveniosCategoria.ConveniosCategoriasHistorico", "Trabajador.ObrasSocial", "Empleador"));			
			$relacion = $this->Liquidacion->Relacion->findById($liquidacion['Liquidacion']['relacion_id']);
			$periodo = $this->Util->traerPeriodo($liquidacion['Liquidacion']['ano'] . str_pad("0", 2, $liquidacion['Liquidacion']['mes'], STR_PAD_RIGHT) . $liquidacion['Liquidacion']['periodo']);
			$variables = null;
			$variables['#mes_liquidacion']['valor'] = $periodo['mes'];
			$variables['#ano_liquidacion']['valor'] = $periodo['ano'];
			$variables['#periodo_liquidacion']['valor'] = $periodo['periodo'];
			$variables['#periodo_liquidacion_completo']['valor'] = $periodo['periodoCompleto'];
			$variables['#fecha_desde_liquidacion']['valor'] = $periodo['desde'];
			$variables['#fecha_hasta_liquidacion']['valor'] = $periodo['hasta'];
			$variables['#tipo_liquidacion']['valor'] = "normal";
			$variables['#smvm']['valor'] = "1000";

			$opciones['variables'] = $variables;
			$this->__getLiquidacion($relacion, $opciones);
			
			//$variables['#tipo_liquidacion']['valor'] = $this->data['Extras']['Liquidacion-tipo'];
			
			//d($periodo);

			/*
			$liquidacionesDetalleId = $this->Liquidacion->LiquidacionesDetalle->find("first", array(
				"recursive"=>-1,
				"conditions"=>
					array(	"LiquidacionesDetalle.liquidacion_id"=>$liquidacionId,
							"LiquidacionesDetalle.concepto_codigo"=>$conceptoCodigo)));

			if (!empty($liquidacionesDetalleId['LiquidacionesDetalle']['id'])) {
				$this->Liquidacion->LiquidacionesDetalle->save(array("LiquidacionesDetalle"=>
					array(	"id"=>$liquidacionesDetalleId['LiquidacionesDetalle']['id'],
							"valor_cantidad"=>$valor	)));
			}
			*/
		}
		$this->data = $this->Liquidacion->read(null, $id);
	}


/**
 * recibo_html_debug.
 * Muestra via desglose el recibo (detalle) de la preliquidacion con informacion de debug.
 */
	function recibo_html_debug($id) {
		$this->Liquidacion->contain(array("LiquidacionesDetalle"));
		$this->data = $this->Liquidacion->read(null, $id);
	}


/**
 * errores.
 * Muestra via desglose los errores de la preliquidacion.
 */
	function errores($id) {
		$this->Liquidacion->contain(array("LiquidacionesError"));
		$this->data = $this->Liquidacion->read(null, $id);
	}
	
	
	
	
	function agregar_observacion($id) {
		/**
		* Agrego una url a la History para que vuelva bien a donde debe, ya que no uso un edit  comun.
		*/
		$this->History->addFakeUrl();
		$this->Liquidacion->recursive = -1;
		$this->data = $this->Liquidacion->read(null, $id);
	}


	function riesgo_indemnizatorio() {
	}



/**
 * Genera el archivo para importar en SIAP y generar el 931.
 * TODO: Deberia mover esto al controller siap.
 *
 * @return void.
 * @access public.
*/
	function generar_archivo_siap() {
		
		if (!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] === "generar" && !empty($this->data['Condicion']['Siap-version'])) {
			if (!empty($this->data['Condicion']['Siap-empleador_id'])
				&& !empty($this->data['Condicion']['Siap-grupo_id'])) {
				$this->Session->setFlash("Debe seleccionar el Empleador o el Grupo, pero no ambos.", "error");
			}
			elseif (empty($this->data['Condicion']['Siap-empleador_id'])
				&& empty($this->data['Condicion']['Siap-grupo_id'])) {
				$this->Session->setFlash("Debe seleccionar un Empleador o un Grupo por lo menos.", "error");
			}
			elseif (empty($this->data['Condicion']['Siap-periodo']) || !preg_match("/^(20\d\d)(0[1-9]|1[012])$/", $this->data['Condicion']['Siap-periodo'], $periodo)) {
				$this->Session->setFlash("Debe especificar un periodo valido de la forma AAAAMM.", "error");
			}
			else {
				$periodo = $this->Util->format($this->data['Condicion']['Siap-periodo'], 'periodo');
				
				/**
				* Busco los empleadores para los cuales debo generar el archivo.
				*/
				if (!empty($this->data['Condicion']['Siap-empleador_id'])) {
					$empleadores = $this->data['Condicion']['Siap-empleador_id'];
				}
				else {
					$empleadores = Set::extract("/Empleador/id", $this->Liquidacion->Relacion->Empleador->find("all", 
							array("recursive" 	=> -1,
								"conditions" 	=> array(
										"(Empleador.group_id & " . $this->data['Condicion']['Siap-grupo_id'] . ") >" => 0)
								)));
				}
				
				
				$ausenciasMotivo = $this->Liquidacion->Relacion->Ausencia->AusenciasMotivo->find('all', array('conditions' => array('NOT' => array('AusenciasMotivo.situacion_id' => null))));
				$ausenciasMotivo = Set::combine($ausenciasMotivo, '{n}.AusenciasMotivo.id', '{n}.Situacion');
				
				
				$Siap = ClassRegistry::init("Siap");
				$data = $Siap->findById($this->data['Condicion']['Siap-version']);
				foreach ($data['SiapsDetalle'] as $k => $v) {
					$detalles[$v['elemento']] = $v;
				}
				
				$conditions = array("Liquidacion.empleador_id" 	=> $empleadores,
									"Liquidacion.estado"		=> "Confirmada",
		 							"Liquidacion.ano"			=> $periodo['ano'],
		 							"Liquidacion.mes"			=> $periodo['mes']);
				
				$liquidaciones = $this->Liquidacion->find("all", 
						array(	"checkSecurity"	=> false,
								"contain"		=> array(	"Empleador",
										"Relacion" 		=> array("Situacion", "ConveniosCategoria", "Ausencia" => 
												array("conditions" => array("Ausencia.desde >=" => $periodo['desde'], "Ausencia.desde <=" => $periodo['hasta']))),
										"Trabajador" 	=> array("ObrasSocial", "Condicion", "Siniestrado", "Localidad")),
								"conditions"	=> $conditions));
				
				if (!empty($liquidaciones)) {
					
					/**
					* Must sumarize. Can't do in by query because of contain.
					*/
					$all = Set::extract('/Liquidacion/relacion_id', $liquidaciones);
					$unique = array_unique($all);
					$duplicates = Set::diff($unique, $all);
					
					$liquidacionesOriginal = $liquidaciones;
					$liquidaciones = Set::combine($liquidaciones, '{n}.Relacion.id', '{n}');
					
					$totales = array('remunerativo', 'no_remunerativo', 'deduccion', 'total_pesos', 'total_beneficios', 'total');
					foreach ($duplicates as $duplicateRelacionId) {
						foreach ($totales as $total) {
							$liquidaciones[$duplicateRelacionId]['Liquidacion'][$total] = 0;
						}
					}
					
					foreach ($liquidacionesOriginal as $liquidacion) {
						if (!in_array($liquidacion['Liquidacion']['relacion_id'], $duplicates)) {
							continue;
						}
						
						foreach ($totales as $total) {
							$liquidaciones[$liquidacion['Liquidacion']['relacion_id']]['Liquidacion'][$total] += $liquidacion['Liquidacion'][$total];
						}
					}
					
					$lineas = null;
					foreach ($liquidaciones as $liquidacion) {
						$campos = $detalles;
						$campos['c1']['valor'] = str_replace("-", "", $liquidacion['Trabajador']['cuil']);
						$campos['c2']['valor'] = $liquidacion['Trabajador']['apellido'] . " " . $liquidacion['Trabajador']['nombre'];
						if (!empty($liquidacion['Relacion']['situacion_id'])) {
							$campos['c5']['valor'] = $liquidacion['Relacion']['Situacion']['codigo'];
						}
						if (!empty($liquidacion['Trabajador']['condicion_id'])) {
							$campos['c6']['valor'] = $liquidacion['Trabajador']['Condicion']['codigo'];
						}
						if (!empty($liquidacion['Trabajador']['actividad_id'])) {
							$campos['c7']['valor'] = $liquidacion['Trabajador']['Actividad']['codigo'];
						}
						$campos['c8']['valor'] = $liquidacion['Trabajador']['Localidad']['codigo_zona'];
						if (!empty($liquidacion['Trabajador']['modalidad_id'])) {
							$campos['c10']['valor'] = $liquidacion['Trabajador']['Modalidad']['codigo'];
						}
						if (!empty($liquidacion['Trabajador']['obra_social_id'])) {
							$campos['c11']['valor'] = $liquidacion['Trabajador']['ObrasSocial']['codigo'];
						}
						$campos['c12']['valor'] = $liquidacion['Trabajador']['adherentes_os'];
						$campos['c13']['valor'] = $liquidacion['Liquidacion']['remunerativo'] + $liquidacion['Liquidacion']['no_remunerativo'];
						$campos['c14']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
						$campos['c20']['valor'] = $liquidacion['Trabajador']['Localidad']['nombre'];
						$campos['c21']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
						$campos['c22']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
						
						/**
						 * Viene expresado como una formula.
						 */
						$campos['c23']['valor'] = $this->Formulador->resolver(str_replace("c23", $liquidacion['Liquidacion']['remunerativo'], $campos['c23']['valor']));
						
						if (!empty($liquidacion['Trabajador']['siniestrado_id'])) {
							$campos['c24']['valor'] = $liquidacion['Trabajador']['Siniestrado']['codigo'];
						}
						if ($liquidacion['Empleador']['corresponde_reduccion'] === "Si") {
							$campos['c25']['valor'] = "S";
						}
						else {
							$campos['c25']['valor'] = " ";
						}
						if ($liquidacion['Trabajador']['jubilacion'] === "Reparto") {
							$campos['c29']['valor'] = "1";
						}
						else {
							$campos['c29']['valor'] = "0";
						}
						
						
						/**
						* Trabajo con la situacion de revista
						*/
						$cantidadAusencias = 0;
						$camposTmp = null;
						$camposTmp[0] = array();
						foreach ($liquidacion['Relacion']['Ausencia'] as $k => $ausencia) {
							if (isset($ausenciasMotivo[$ausencia['ausencia_motivo_id']])) {
								$cantidadAusencias++;
								$camposTmp[$k]['situacion'] = $ausenciasMotivo[$ausencia['ausencia_motivo_id']]['codigo'];
								$camposTmp[$k]['dia'] = array_pop(explode('-', $ausencia['desde']));
							}
						}	
							
						$campoNumero = 30;
						foreach ($camposTmp as $k => $tmp) {
							$campoNumero += ($k * 2);
							if ($cantidadAusencias < 3 && $k === 0) {
								$campos['c' . $campoNumero]['valor'] = "1";
								if ($liquidacion['Relacion']['ingreso'] <= $periodo['desde']) {
									$campos['c' . ($campoNumero + 1)]['valor'] = array_pop(explode('-', $periodo['desde']));
								} else {
									$campos['c' . ($campoNumero + 1)]['valor'] = array_pop(explode('-', $liquidacion['Relacion']['ingreso']));
								}
								$campoNumero = $campoNumero + 2;
							} 
							elseif ($cantidadAusencias > 0) {
								$campos['c' . $campoNumero]['valor'] = $tmp['situacion'];
								$campos['c' . ($campoNumero + 1)]['valor'] = $tmp['dia'];
							}
						}
						
						
						$campos['c36']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
						$campos['c42']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
						if ($liquidacion['Relacion']['ConveniosCategoria']['nombre'] === "Fuera de convenio") {
							$campos['c43']['valor'] = "0";
						}
						else {
							$campos['c43']['valor'] = "1";
						}
						$lineas[] = $this->__generarRegistro($campos);
					}
					$this->set("archivo", array("contenido"=>implode("\n\r", $lineas), "nombre" => "SIAP-" . $periodo['ano'] . "-" . $periodo['mes'] . ".txt"));
					$this->render(".." . DS . "elements" . DS . "txt", "txt");
				}
				else {
					$this->Session->setFlash("No se han encontrado liquidaciones confirmadas para el periodo seleccioando segun los criterios especificados.", "error");
				}
			}
		}
		
		$this->set("grupos", $this->Util->getUserGroups());
		/*
		$usuario = $this->Session->read('__Usuario');
		if (!empty($usuario['Grupo'])) {
			foreach ($usuario['Grupo'] as $grupo) {
				$grupos[$grupo['id']] = $grupo['nombre'];
			}
			$this->set("grupos", $grupos);
		}
		*/
	}
	
	
/**
 * Genera el una linea del archivo para importar en SIAP.
 *
 * @param array La descripcion del campo, donde me indica como debe comportarse.
 * @return string Una linea formateada para ser importada.
 * @access private.
*/
	function __generarRegistro($campos) {
		$v = array();
		if (!empty($campos)) {
			foreach ($campos as $campo) {
				if ($campo['direccion_relleno'] === "Derecha") {
					$v[] = str_pad($campo['valor'], $campo['longitud'], $campo['caracter_relleno'], STR_PAD_RIGHT);
				}
				elseif ($campo['direccion_relleno'] === "Izquierda") {
					$v[] = str_pad($campo['valor'], $campo['longitud'], $campo['caracter_relleno'], STR_PAD_LEFT);
				}
				else {
					$v[] = $campo['valor'];
				}
			}
		}
		return implode("", $v);
	}


	function asignar_suss() {
		if (!empty($this->data['Formulario']['accion'])) {
			if ($this->data['Formulario']['accion'] == "asignar") {
				if (!empty($this->data['Condicion']['Suss-fecha']) && !empty($this->data['Condicion']['Suss-banco_id']) && !empty($this->data['Condicion']['Suss-periodo'])) {
					if (preg_match("/^(20\d\d)(0[1-9]|1[012])$/", $this->data['Condicion']['Suss-periodo'], $periodo)) {
						d($periodo);
					}
					else {
						$this->Session->setFlash("El periodo ingresado no es valido. Debe tener la forma AAAAMM", "error");
					}
				}
				else {
					$this->Session->setFlash("Debe ingresar todos los datos requeridos.", "error");
				}
			}
			else {
				$this->History->goBack();
			}
		}
		$this->set("bancos", $this->Banco->find("list", array("fields"=>array("Banco.nombre"))));
	}
	
	
	
	function index() {
		if (!empty($this->data['Condicion']['Liquidacion-periodo'])) {
			if ($tmp = $this->Util->format($this->data['Condicion']['Liquidacion-periodo'], "periodo")) {
				$condiciones['Liquidacion.ano'] = $tmp['ano'];
				$condiciones['Liquidacion.mes'] = $tmp['mes'];
				$condiciones['Liquidacion.periodo'] = $tmp['periodo'];
			}
			unset($this->data['Condicion']['Liquidacion-periodo']);
		}
		$condiciones['Liquidacion.estado'] = "Confirmada";
		$this->paginate = array_merge($this->paginate, array('conditions' => $condiciones));
		parent::index();
	}

/**
 * pagos.
 * Muestra via desglose los Pagos generados por esta liquidacion.
 */
	function pagos($id) {
		$this->Liquidacion->contain(array("Pago"));
		$this->data = $this->Liquidacion->read(null, $id);
	}


/**
 * 
 * 
 */
	function __seteos() {
		$periodos["M"] = "Mensual";
		$periodos["1Q"] = "Primera Quincena";
		$periodos["2Q"] = "Segunda Quincena";
		$this->set("periodos", $periodos);
		$this->set("meses", $this->Util->format("all", array("type" => "mesEnLetras", "case" => "ucfirst")));
	}



	
	
/**
 * Permite confirmar liquidaciones.
 * Las liquidaciones estan en la tabla liquidaciones pero con estado "Sin Confirmar".
 *
 * Esto implica que:
 *		- Las horas liquidadas cambian a estado "Liquidada".
 *		- Las ausencias_seguimientos liquidadas cambian a estado "Liquidado".
 *		- Las liquidaciones cambian a estado "Liquidada".
 *		- Se generan los pagos pendientes.
 *		- Se agregan detalles de descuentos.
 *
 * @return void.
 * @access public.
 */
	function confirmar() {
		$ids = $this->Util->extraerIds($this->data['seleccionMultiple']);
		
		if (!empty($ids)) {
			/**
			* En la tabla auxiliares tengo un array de los datos listos para guardar.
			* Puede haber campos que deben ser guardados y no tienen valor, estos debo ponerle valor actual,
			* por ejemplo, la fecha del dia que se confirma, y no la del dia que se pre-liquido.
			*/
			$auxiliares = $this->Liquidacion->LiquidacionesAuxiliar->find("all", array("conditions"=>array("LiquidacionesAuxiliar.liquidacion_id"=>$ids)));
			$c = 0;
			$this->Liquidacion->begin();
			$idsAuxiliares = null;
			foreach ($auxiliares as $v) {
				$model = $v['LiquidacionesAuxiliar']['model'];
				$idsAuxiliares[] = $v['LiquidacionesAuxiliar']['id'];
				$save = unserialize($v['LiquidacionesAuxiliar']['save']);

				foreach ($save as $campo=>$valor) {
					preg_match('/^##MACRO:([a-z,_]+)##$/',$valor, $matches);
					if (!empty($matches[1])) {
						switch($matches[1]) {
							case 'fecha_liquidacion':
								$save[$campo] = date("d/m/Y");
								break;
							case 'liquidacion_id':
								$save[$campo] = $v['LiquidacionesAuxiliar']['liquidacion_id'];
								break;
						}
					}
				}
				
				$modelSave = ClassRegistry::init($model);
				$save = array($model => $save);
				$modelSave->create($save);
				if ($modelSave->save($save)) {
					$c++;
				}
			}
			

			/**
			 * Si lo anterior salio todo ok, continuo.
			 */
			if ($c === count($auxiliares)) {
				$this->Liquidacion->recursive = -1;
				if ($this->Liquidacion->updateAll(array('estado' => "'Confirmada'"), array('Liquidacion.id' => $ids))) {
					/**
					 * Borro de la tabla auxiliar.
					 */
					if (!empty($idsAuxiliares)) {
						$this->Liquidacion->LiquidacionesAuxiliar->recursive = -1;;
						$this->Liquidacion->LiquidacionesAuxiliar->deleteAll(array('LiquidacionesAuxiliar.id' => $idsAuxiliares));
					}
					$this->Liquidacion->commit();
					$this->Session->setFlash('Se confirmaron correctamente ' . count($ids) . ' liquidacion/es.', 'ok');
				} else {
					$this->Liquidacion->rollback();
					$this->Liquidacion->__buscarError();
					$this->Session->setFlash('Ocurrio un error al intentar confirmar las liquidaciones.', 'error');
				}
			} else {
				$this->Liquidacion->rollback();
				$this->Session->setFlash('Ocurrio un error al intentar confirmar las liquidaciones. No se puedieron actualizar los registros relacionados.', 'error');
			}
		}
		$this->History->goBack(2);
	}
	

/**
 * recibo_excel.
 * Genera un archivo excel con el recibo.
 */
	function recibo_excel($id) {
		$this->Liquidacion->contain(array("LiquidacionesDetalle"));
		$this->data = $this->Liquidacion->read(null, $id);
		$this->layout = "excel";
		//d($this->data);
	}
	
}
?>