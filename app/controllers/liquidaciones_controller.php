<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a la liquidacion de sueldos.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.controllers
 * @since			Pragtico v 1.0.0
 * @version			1.0.0
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */

/**
 * La clase encapsula la logica de negocio asociada a la liquidacion de sueldos.
 *
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class LiquidacionesController extends AppController {

	var $components = array('Formulador');
	//var $helpers = array("ExcelWriter", "Pdf", "Excel");
	var $uses = array("Liquidacion", "Relacion", "Ausencia" ,"Variable", "Siap", "Banco");
	var $__relacion;
	var $__variables;
	var $__variablesYaResueltas;
	var $__conceptos;


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
*/
	function generar_archivo_siap() {
		
		if(!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] == "generar" && !empty($this->data['Condicion']['Siap-version'])) {
			if(empty($this->data['Condicion']['Siap-periodo'])) {
				$this->Session->setFlash("Debe especificar un periodo.", "error");
			}
			elseif($this->data['Condicion']['Siap-modo'] == "Por Grupo" && empty($this->data['Condicion']['Siap-grupo_id'])) {
				$this->Session->setFlash("Debe seleccionar un Grupo.", "error");
			}
			elseif($this->data['Condicion']['Siap-modo'] == "Por Empleador" && empty($this->data['Condicion']['Siap-empleador_id'])) {
				$this->Session->setFlash("Debe seleccionar un Empleador.", "error");
			}
			elseif(!preg_match("/^(20\d\d)(0[1-9]|1[012])$/", $this->data['Condicion']['Siap-periodo'], $periodo)) {
				$this->Session->setFlash("Debe especificar un periodo valido (AAAAMM).", "error");
			}
			else {
			
				$siap = $this->Siap->findById($this->data['Condicion']['Siap-version']);
				foreach($siap['SiapsDetalle'] as $k=>$v) {
					$detalles[$v['elemento']] = $v;
				}

				$conditions = array("Liquidacion.ano"=>$periodo[1], "Liquidacion.mes"=>$periodo[2]);
				if($this->data['Condicion']['Siap-modo'] == "Por Grupo") {
					$conditions = am($conditions, array("Liquidacion.group_id & " . $this->data['Condicion']['Siap-grupo_id'] => ">0"));
				}
				elseif($this->data['Condicion']['Siap-modo'] == "Por Empleador") {
					$conditions = am($conditions, array("Liquidacion.empleador_id" => $this->data['Condicion']['Siap-empleador_id']));
				}
				$this->Liquidacion->recursive = 2;
				$liquidaciones = $this->Liquidacion->find("all", array("conditions"=>$conditions));
				d($conditions);
				
				$lineas = null;
				foreach($liquidaciones as $liquidacion) {
					$campos = $detalles;
					$campos['c1']['valor'] = str_replace("-", "", $liquidacion['Trabajador']['cuil']);
					$campos['c2']['valor'] = $liquidacion['Trabajador']['apellido'] . " " . $liquidacion['Trabajador']['nombre'];
					if(!empty($liquidacion['Relacion']['situacion_id'])) {
						$campos['c5']['valor'] = $liquidacion['Relacion']['Situacion']['codigo'];
					}
					if(!empty($liquidacion['Trabajador']['condicion_id'])) {
						$campos['c6']['valor'] = $liquidacion['Trabajador']['Condicion']['codigo'];
					}
					if(!empty($liquidacion['Trabajador']['actividad_id'])) {
						$campos['c7']['valor'] = $liquidacion['Trabajador']['Actividad']['codigo'];
					}
					$campos['c8']['valor'] = $liquidacion['Trabajador']['Localidad']['codigo_zona'];
					if(!empty($liquidacion['Trabajador']['modalidad_id'])) {
						$campos['c10']['valor'] = $liquidacion['Trabajador']['Modalidad']['codigo'];
					}
					if(!empty($liquidacion['Trabajador']['obra_social_id'])) {
						$campos['c11']['valor'] = $liquidacion['Trabajador']['ObrasSocial']['codigo'];
					}
					$campos['c12']['valor'] = $liquidacion['Trabajador']['adherentes_os'];
					$campos['c13']['valor'] = $liquidacion['Liquidacion']['total'];
					$campos['c14']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
					$campos['c20']['valor'] = $liquidacion['Trabajador']['Localidad']['nombre'];
					$campos['c21']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
					$campos['c22']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
					/**
					* TODO: Rem4 esta mal
					*/
					$campos['c23']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
					/**
					* TODO: Siniestrado
					$campos['c24']['valor'] = "";
					*/
					if($liquidacion['Empleador']['corresponde_reduccion'] == "Si") {
						$campos['c25']['valor'] = "S";
					}
					else {
						$campos['c25']['valor'] = " ";
					}
					if($liquidacion['Trabajador']['jubilacion'] == "Reparto") {
						$campos['c29']['valor'] = "1";
					}
					else {
						$campos['c29']['valor'] = "0";
					}
					$campos['c36']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
					$campos['c42']['valor'] = $liquidacion['Liquidacion']['remunerativo'];
					if($liquidacion['Relacion']['ConveniosCategoria']['nombre'] == "Fuera de convenio") {
						$campos['c43']['valor'] = "0";
					}
					else {
						$campos['c43']['valor'] = "1";
					}
					$lineas[] = $this->__generarRegistro($campos);
				}
				$this->set("archivo", array("contenido"=>implode("\n\r", $lineas), "nombre"=>$periodo[1] . "-" . $periodo[2] . ".txt"));
				$this->render("archivo_siap", "txt");
			}
		}
		
		$usuario = $this->Session->read('__Usuario');
		$grupos = array();
		foreach($usuario['Grupo'] as $grupo) {
			if($grupo['tipo'] == "De Grupos") {
				$grupos[$grupo['id']] = $grupo['nombre'];
			}
		}
		$this->set("modos", array("Por Empleador"=>"Por Empleador", "Por Grupo"=>"Por Grupo"));
		$this->set("grupos", $grupos);
		$this->set("siaps", $this->Siap->find("list", array("fields"=>array("Siap.version"))));
	}
        
	function __generarRegistro($campos) {
		$v = null;
		if(!empty($campos)) {
			foreach($campos as $campo) {
				if($campo['direccion_relleno'] == "Derecha") {
					$v[] = str_pad($campo['valor'], $campo['longitud'], $campo['caracter_relleno'], STR_PAD_RIGHT);
				}
				elseif($campo['direccion_relleno'] == "Izquierda") {
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
		if(!empty($this->data['Formulario']['accion'])) {
			if($this->data['Formulario']['accion'] == "asignar") {
				if(!empty($this->data['Condicion']['Suss-fecha']) && !empty($this->data['Condicion']['Suss-banco_id']) && !empty($this->data['Condicion']['Suss-periodo'])) {
					if(preg_match("/^(20\d\d)(0[1-9]|1[012])$/", $this->data['Condicion']['Suss-periodo'], $periodo)) {
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
		$this->paginate = am($this->paginate, array('conditions' => array("Liquidacion.estado"=>"Confirmada")));
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
		$this->set("meses", $this->Util->getMeses());
	}


	function test_variables() {
		/**
		* Obtengo el listado completo de variables.
		*/
		$variables = $this->Util->combine($this->Variable->find("all"), "{n}.Variable.nombre", "{n}.Variable.formula");
		

		$datosRelacion['id'] = "1";
		$datosRelacion['ingreso'] = "2008-01-05";
		$datosRelacion['egreso'] = "2008-01-28";
		$datosRelacion['horas'] = "8";
		$datosRelacion['basico'] = "1500";
		$datosConveniosCategoria['jornada'] = "Mensual";
		$datosConveniosCategoria['costo'] = "1000";
		$this->__relacion['Relacion'] = $datosRelacion;
		$this->__relacion['ConveniosCategoria'] = $datosConveniosCategoria;
		$variables['#mes_liquidacion'] = "01";
		$variables['#ano_liquidacion'] = "2008";
		$variables['#periodo_liquidacion'] = "mensual"; //mensual, primeraQuincena, segundaQuincena
		$variables['#fecha_desde_liquidacion'] = "2008-01-01";
		$variables['#fecha_hasta_liquidacion'] = "2008-01-31";
		
		$this->__variables = $variables;

		$this->__getVariables(array_keys($this->__variables));
		ksort($this->__variables);
		d($this->__variables);
	}



/**
 * PreLiquidar.
 * Me permite hacer una preliquidacion.
 */
	function preliquidar() {

		$this->__filasPorPagina();
		$this->paginate = am($this->paginate, array('conditions' => array("Liquidacion.estado"=>"Sin Confirmar")));

		$this->set("tipos", array("normal"=>"Normal", "sac"=>"Sac", "vacaciones"=>"Vacaciones", "liquidacion_final"=>"Liquidacion Final", "especial"=>"Especial"));
		$liquidacionesYaConfirmadas = array();

		if($this->data['Formulario']['accion'] == "buscar") {
			/**
			* Realizo las validaciones basicas para poder preliquidar.
			*/
			if(empty($this->data['Extras']['Liquidacion-periodo'])) {
				$this->Session->setFlash("Debe especificar un periodo.", "error");
			}
			else {
				/**
				* Obtengo el periodo separado por ano, mes y periodo propiamente dicho.
				*/
				$periodo = $this->Util->traerPeriodo($this->data['Extras']['Liquidacion-periodo']);
				if($periodo === false) {
					$this->Session->setFlash("Debe especificar un periodo valido de la forma AAAAMM[1Q|2Q|M].", "error");
				}
				else if(empty($this->data['Condicion']['Relacion-empleador_id'])
						&& empty($this->data['Condicion']['Relacion-trabajador_id'])
							&& empty($this->data['Condicion']['Relacion-id'])) {
							$this->Session->setFlash("Debe seleccionar un empleador, un trabajador o una relacion laboral.", "error");
				}
				else {
					
					/**
					* A partir del periodo creo las condiciones desde y hasta para usar en filtros de fechas.
					*/
					$opciones = array(	"mes"	=> $periodo['mes'],
										"ano"	=> $periodo['ano']);
										
					if ($periodo['periodo'] == "1Q") {
						$opciones = am($opciones, array("dia"=>"01"));
						$fechaDesde = $this->Util->traerFecha($opciones);
						$opciones = am($opciones, array("dia"=>"15"));
						$fechaHasta = $this->Util->traerFecha($opciones);
					}
					elseif ($periodo['periodo'] == "2Q") {
						$opciones = am($opciones, array("dia"=>"16"));
						$fechaDesde = $this->Util->traerFecha($opciones);
						$opciones = am($opciones, array("dia"=>$this->Util->traerUltimoDiaDelMes($opciones)));
						$fechaHasta = $this->Util->traerFecha($opciones);
					}
					elseif ($periodo['periodo'] == "M") {
						$opciones = am($opciones, array("dia"=>"01"));
						$fechaDesde = $this->Util->traerFecha($opciones);
						$opciones = am($opciones, array("dia"=>$this->Util->traerUltimoDiaDelMes($opciones)));
						$fechaHasta = $this->Util->traerFecha($opciones);
					}
					
					/**
					* Busco las relaciones que debo liquidar de acuerdo a los criterios ingresados.
					*/
					$condiciones = $this->Paginador->generarCondicion($this->data);
					$condiciones['Relacion.ingreso <='] = $fechaHasta;
					$condiciones['Relacion.estado'] = "Activa";
					
					//$this->Relacion->contain(array("ConveniosCategoria.ConveniosCategoriasHistorico", "ConveniosCategoria.Convenio", "Trabajador.ObrasSocial", "Empleador"));
					$this->Relacion->contain(array("ConveniosCategoria.ConveniosCategoriasHistorico", "Trabajador.ObrasSocial", "Empleador"));
					$relaciones = $this->Relacion->find("all", array("conditions"=>$condiciones));

					/**
					* Borro TODAS las liquidaciones no confirmadas del usuario.
					*/
					$usuario = $this->Session->read("__Usuario");
					$delete = array("user_id"=>$usuario['Usuario']['id'], "estado"=>'Sin Confirmar');
					$this->Liquidacion->contain();
					$this->Liquidacion->deleteAll($delete);

					/**
					* Obtengo el listado completo de variables y las inicializo sin valor.
					*/
					$variablesTmp = $this->Variable->find("all");
					foreach($variablesTmp as $v) {
						$variables[$v['Variable']['nombre']] = $v['Variable'];
						$variables[$v['Variable']['nombre']]['valor'] = "#N/A";
					}
					
					/**
					* Resuelvo las variables que vienen por parametros.
					*/
					$variables['#mes_liquidacion']['valor'] = $periodo['mes'];
					$variables['#ano_liquidacion']['valor'] = $periodo['ano'];
					$variables['#periodo_liquidacion']['valor'] = $periodo['periodo'];
					$variables['#periodo_liquidacion_completo']['valor'] = $periodo['periodoCompleto'];
					$variables['#fecha_desde_liquidacion']['valor'] = $fechaDesde;
					$variables['#fecha_hasta_liquidacion']['valor'] = $fechaHasta;
					$variables['#tipo_liquidacion']['valor'] = $this->data['Extras']['Liquidacion-tipo'];
					

					/**
					* De las liquidaciones que he seleccionado para pre-liquidar, verifico que no tengan
					* liquidaciones confirmadas para el mismo periodo.
					* TODO: Verificar el tipo de liquidacion.
					*/
					$condicionesLiquidacion['Liquidacion.mes'] = $periodo['mes'];
					$condicionesLiquidacion['Liquidacion.ano'] = $periodo['ano'];
					$condicionesLiquidacion['Liquidacion.periodo'] = $periodo['periodo'];
					
					$liquidaciones = $this->Relacion->Liquidacion->find("all", array("recursive"=>-1, "fields"=>"relacion_id, count(1) as existentes","group"=>"Liquidacion.relacion_id, Liquidacion.ano, Liquidacion.mes, Liquidacion.periodo", "conditions"=>$condicionesLiquidacion));
					foreach($liquidaciones as $v) {
						if($v[0]['existentes'] > 0) {
							$liquidacionesYaConfirmadas[] = $v['Liquidacion']['relacion_id'];
						}
					}

					/**
					* Recorro cada relacion de las seleccionadas y trato de liquidarle si aun no se le ha liquidado.
					*/
					$ids = array();
					$opciones['variables'] = $variables;
					foreach($relaciones as $k=>$relacion) {
						$ids[] = $this->__getLiquidacion($relacion, $opciones);
					}
					if(!empty($ids)) {
						$this->Liquidacion->contain(array("Relacion.Trabajador", "Relacion.Empleador"));
						$resultados = $this->Paginador->paginar(array("Liquidacion.id"=>$ids));
					}
				}
			}
		}

		if(empty($resultados)) {
			$this->Liquidacion->contain(array("Relacion.Trabajador", "Relacion.Empleador"));
			$resultados = $this->Paginador->paginar();
		}
		$this->set("registros", $resultados['registros']);
		$this->set("liquidacionesYaConfirmadas", $liquidacionesYaConfirmadas);
	}


	function __getVariableValor($variable) {
		if($this->__variables[$variable]['valor'] == "#N/A") {
			$this->__getVariables($variable);
		}
		return $this->__variables[$variable]['valor'];
	}


	function __getLiquidacion($relacion, $opciones) {
		$this->__variablesYaResueltas = $conceptosExrasSinCalcular = $conceptosExrasCalculados = $auxiliar = $errores = array();
		$this->__conceptos = null;
		$this->__relacion = $relacion;

		/**
		* Con cada relacion, recalculo las variables.
		*/
		$this->__variables = $opciones['variables'];

		/**
		* Verifico si debo hacerle algun descuento.
		*/
		$condicionesDescuentos = null;
		$condicionesDescuentos['desde'] = $this->__getVariableValor("#fecha_desde_liquidacion");
		$condicionesDescuentos['hasta'] = $this->__getVariableValor("#fecha_hasta_liquidacion");
		$condicionesDescuentos['periodo'] = $this->__getVariableValor("#periodo_liquidacion");
		$condicionesDescuentos['tipo'] = $this->__getVariableValor("#tipo_liquidacion");
		$condicionesDescuentos['smvm'] = $this->__getVariableValor("#smvm");
		$descuentos = $this->Relacion->Descuento->buscarDescuento($condicionesDescuentos, $relacion);
		foreach($descuentos['concepto'] as $v) {
			$conceptosExrasSinCalcular = am($conceptosExrasSinCalcular, $v);
		}
		$auxiliar = am($auxiliar, $descuentos['auxiliar']);
		

		/**
		* Verifico si tiene licencias para el periodo.
		$opciones = array();
		$opciones['desde'] = $this->__variables['#fecha_desde_liquidacion']['valor'];
		$opciones['hasta'] = $this->__variables['#fecha_hasta_liquidacion']['valor'];
		$licencias = $this->Relacion->Licencia->buscarDiasLicencia($opciones, $this->__relacion);
		foreach($licencias as $licencia) {
			foreach($licencia['variables'] as $licenciaTipo=>$licenciaValor) {
				if(isset($this->__variables[$licenciaTipo])) {
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
			if(!empty($licencia['errores'])) {
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
		//$vacaciones = $this->Relacion->Vacacion->buscarDiasVacaciones($opciones, $this->__relacion);
		//d($vacaciones);
		//$conceptosExrasCalculados = am($conceptosExrasCalculados, $descuentos['concepto']);
		//$auxiliar = am($auxiliar, $descuentos['auxiliar']);


		/**
		* Busco las ausencias del periodo.
		$condicionesAusencias = null;
		$condicionesAusencias['desde'] = $this->__variables['#fecha_desde_liquidacion']['valor'];
		$condicionesAusencias['hasta'] = $this->__variables['#fecha_hasta_liquidacion']['valor'];
		$ausencias = $this->Relacion->Ausencia->buscarAusencia($condicionesAusencias, $this->__relacion);
		foreach($horas['variables'] as $horaTipo=>$horaValor) {
			$this->__variables[$horaTipo]['valor'] = $horaValor;
		}
		$auxiliar = am($auxiliar, $horas['auxiliar']);
		$conceptosExrasSinCalcular = am($conceptosExrasSinCalcular, $horas['conceptos']);
		*/


		/**
		* Busco las horas trabajadas en el periodo y las cargo al array variables.
		*/
		$condicionesHoras = null;
		$condicionesHoras['periodo'] = $this->__getVariableValor("#periodo_liquidacion_completo");
		$horas = $this->Relacion->Hora->buscarHora($condicionesHoras, $relacion);
		foreach($horas['variables'] as $horaTipo=>$horaValor) {
			$this->__variables[$horaTipo]['valor'] = $horaValor;
		}
		$auxiliar = am($auxiliar, $horas['auxiliar']);
		$conceptosExrasSinCalcular = am($conceptosExrasSinCalcular, $horas['conceptos']);

		/**
		* Busco los conceptos para esta relacion.
		*/
		$opcionesFindConcepto = array();
		$opcionesFindConcepto['desde'] = $this->__getVariableValor("#fecha_desde_liquidacion");
		$opcionesFindConcepto['hasta'] = $this->__getVariableValor("#fecha_hasta_liquidacion");
		$this->__conceptos = $this->Relacion->RelacionesConcepto->Concepto->findConceptos("Relacion", am(array("relacion"=>$relacion), $opcionesFindConcepto));

		/**
		* Verifico que este el concepto sueldo_basico.
		*/
		if(!in_array("sueldo_basico", array_keys($this->__conceptos))) {
			$conceptosExrasSinCalcular = am($conceptosExrasSinCalcular, $this->Relacion->RelacionesConcepto->Concepto->findConceptos("ConceptoPuntual", am(array("relacion"=>$this->__relacion, "codigoConcepto"=>"sueldo_basico"), $opcionesFindConcepto)));
		}

		/**
		* Agrego los conceptos que aunque no este asociados a la relacion, deben necesariamente estar.
		* No los tengo calculados aun, el liquidador los resuelve como un concepto mas.
		* Los vengo cargando en el array $conceptosExrasSinCalcular.
		*/
		$this->__conceptos = am($this->__conceptos, $conceptosExrasSinCalcular);

		/**
		* Resuelvo.
		*/
		foreach($this->__conceptos as $cCod=>$concepto) {
			$resolucion = $this->__getConceptoValor($concepto, $opcionesFindConcepto);
			$this->__conceptos[$cCod] = am($this->__conceptos[$cCod], $resolucion);
		}


		/**
		* Verifico si se generaron errores. No lo hago en el mismo ciclo anterior, porque de la
		* resolucion pueden haberse necesitado conceptos y los errores pueden venir de estos.
		*/
		foreach($this->__conceptos as $cCod=>$concepto) {
			if(!empty($this->__conceptos[$cCod]['errores'])) {
				foreach($this->__conceptos[$cCod]['errores'] as $error) {
					$errores[] = $this->__agregarError($error);
				}
			}
		}

		/**
		* Agrego los conceptos que aunque no este asociados a la relacion, deben necesariamente estar.
		* Ya los tengo calculados.
		* Los vengo cargando en el array $conceptosExrasCalculados.
		foreach($conceptosExrasCalculados as $conceptoExraCalculado) {
			$this->__conceptos = am($conceptoExraCalculado, $this->__conceptos);
		}
		*/

		/**
		* Preparo el array para guardar la pre-liquidacion.
		* Lo guardo como una liquidacion con esta "Sin Confirmar". Si se confirma, cambio este estado,
		* sino, a la siguiente pasada del preliquidador, la elimino.
		*/
		$liquidacion = null;
		$liquidacion['fecha'] = date("Y-m-d");
		$liquidacion['ano'] = $this->__variables['#ano_liquidacion']['valor'];
		$liquidacion['mes'] = $this->__variables['#mes_liquidacion']['valor'];
		$liquidacion['periodo'] = $this->__variables['#periodo_liquidacion']['valor'];
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

		foreach($this->__conceptos as $detalleLiquidacion) {
			$v = $this->__agregarDetalle($detalleLiquidacion);
			if(!empty($v)) {
				$detalle[] = $this->__agregarDetalle($detalleLiquidacion);
			}


			if($detalleLiquidacion['imprimir'] == "Si" || $detalleLiquidacion['imprimir'] == "Solo con valor") {

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
		$totales['total'] = $totales['remunerativo'] + $totales['no_remunerativo'] - $totales['deduccion'];

		/**
		* Si a este empleador hay que aplicarle redondeo, lo hago y lo dejo expresado
		* con el concepto redondeo en el detalle de la liquidacion.
		*/
		if($this->__relacion['Empleador']['redondear'] == "Si") {
			$redondeo = round($totales['total']) - $totales['total'];
			if($redondeo != 0) {
				$conceptoRedondeo = $this->Relacion->RelacionesConcepto->Concepto->findConceptos("ConceptoPuntual", $this->__relacion, "redondeo", $opcionesFindConcepto);
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
				if($redondeo > 0) {
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


		$save = array("Liquidacion"=>am($liquidacion, $totales), "LiquidacionesDetalle"=>$detalle, "LiquidacionesAuxiliar"=>$auxiliar, "LiquidacionesError"=>$errores);
		$this->Liquidacion->create();
		if($this->Liquidacion->save($save)) {
			return $this->Liquidacion->id;
		}
		else {
			return false;
		}
	}
	
/**
* Permite confirmar una liquidacion.
* Las liquidaciones estan en la tabla liquidaciones pero con estado "Sin Confirmar". Mientras esten en
* este estado se las puede modificar, borrar, etc. Una vez confirmado, se congela.
*/
	function confirmar() {
		$ids = $this->Util->extraerIds($this->data['seleccionMultiple']);
		
		if(!empty($ids)) {
			/**
			* En la tabla auxiliares tengo un array de los datos listos para guardar.
			* Puede haber campos que deben ser guardados y no tienen valor, estos debo ponerle valor actual,
			* por ejemplo, la fecha del dia que se confirma, y no la del dia que se pre-liquido.
			*/
			$auxiliares = $this->Liquidacion->LiquidacionesAuxiliar->find("all", array("conditions"=>array("LiquidacionesAuxiliar.liquidacion_id"=>$ids)));
			$c = 0;
			$this->Liquidacion->begin();
			$idsAuxiliares = null;
			foreach($auxiliares as $v) {
				$model = $v['LiquidacionesAuxiliar']['model'];
				$idsAuxiliares[] = $v['LiquidacionesAuxiliar']['id'];
				$save = unserialize($v['LiquidacionesAuxiliar']['save']);
				foreach($save as $campo=>$valor) {
					preg_match("/^##MACRO:([a-z,_]+)##$/",$valor, $matches);
					if(!empty($matches[1])) {
						switch($matches[1]) {
							case "fecha_liquidacion": //Indica la fecha de la liquidacion.
								$save[$campo] = date("Y-m-d");
								break;
							case "liquidacion_id": //Indica el id de la liquidacion que se grabara.
								$save[$campo] = $v['LiquidacionesAuxiliar']['liquidacion_id'];
								break;
						}
					}
				}
				$modelSave = new $model();
				$modelSave->create();
				if($modelSave->save($save)) {
					$c++;
				}
			}
			
			/**
			* Cada liquidacion confirmada, es en teoria un pago pendiente, entonces lo inserto.
			*/
			$creacionPagos = false;
			$creacionPagos = $this->Liquidacion->generarPagosPendientes($ids);
			

			/**
			* Si lo anterior salio todo ok, continuo.
			*/
			if($c == count($auxiliares) && $creacionPagos === true) {
				$this->Liquidacion->contain();
				if($this->Liquidacion->updateAll(array("estado"=>"'Confirmada'"), array("Liquidacion.id"=>$ids))) {
					/**
					* Borro de la tabla auxiliar.
					*/
					if(!empty($idsAuxiliares)) {
						$this->Liquidacion->LiquidacionesAuxiliar->contain();
						$this->Liquidacion->LiquidacionesAuxiliar->deleteAll(array("LiquidacionesAuxiliar.id"=>$idsAuxiliares));
					}
					$this->Liquidacion->commit();
					$this->Session->setFlash("Se confirmaron correctamente " . count($ids) . " liquidacion/es.", "ok");
				}
				else {
					$this->Liquidacion->rollback();
					$this->Liquidacion->__buscarError();
					$this->Session->setFlash("Ocurrio un error al intentar confirmar las liquidaciones.", "error");
				}
			}
			else {
				$this->Liquidacion->rollback();
				$this->Session->setFlash("Ocurrio un error al intentar confirmar las liquidaciones. No se puedieron actualizar los registros relacionados.", "error");
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
		if(!empty($id)) {
			$this->data = $this->Liquidacion->read(null, $id);
		}
		elseif(!empty($this->params['form']['valor']) && !empty($this->params['form']['conceptoCodigo']) && !empty($this->params['form']['liquidacionId'])) {
			//$valor = $this->params['form']['valor'];
			//$conceptoCodigo = $this->params['form']['conceptoCodigo'];
			//$liquidacionId = $this->params['form']['liquidacionId'];
			$id = $this->params['form']['liquidacionId'];
			$this->Liquidacion->addEditDetalle($this->params['form']);

			$liquidacion = $this->Liquidacion->findById($id);
			$this->Relacion->contain(array("ConveniosCategoria.ConveniosCategoriasHistorico", "Trabajador.ObrasSocial", "Empleador"));			
			$relacion = $this->Relacion->findById($liquidacion['Liquidacion']['relacion_id']);
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

			if(!empty($liquidacionesDetalleId['LiquidacionesDetalle']['id'])) {
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


/**
* Dado un concepto, resuelve la formula.
*/
	function __getConceptoValor($concepto, $opciones) {
		$valor = null;
		$errores = array();
		$formula = $concepto['formula'];
		//debug($concepto);
		//debug($concepto);
		
		/**
		* Si en la formula hay variables, busco primero estos valores.
		*/
		preg_match_all("/(#[a-z0-9_]+)/", $formula, $variables_tmp);
		if(!empty($variables_tmp[1])) {
			$this->__getVariables($variables_tmp[1]);
			foreach($variables_tmp[1] as $k=>$v) {
				/**
				* Debe buscar la variable para reemplazarla dentro de la formula.
				* Usa la RegEx y no str_replace, porque por ejemplo, si debo reemplzar #horas, y en cuentra
				* #horas lo hara ok, pero si encuentra #horas_enfermedad, dejara REEMPLAZO_enfermedad.
				*/
				if(isset($this->__variables[$v])) {
					if($this->__variables[$v]['valor'] !== "#N/A") {
						$formula = preg_replace("/".$v."(\W)|".$v."$/", $this->__variables[$v]['valor'] . "$1", $formula);
					}
					else {
						$errores[] = array(	"tipo"					=>"Variable No Resuelta",
											"gravedad"    			=>"Alta",
											"concepto"				=>$concepto['codigo'],
											"variable"				=>$v,
											"formula"				=>$concepto['formula'],
											"descripcion"			=>"La formula intenta usar una variable que no ha podido ser resuelta.",
											"recomendacion"			=>"Verifique que los datos hayan sido correctamente ingresados.",
											"descripcion_adicional"	=>$this->__variables[$v]['formula']);
					}
				}
				else {
					$errores[] = array(	"tipo"					=>"Variable Inexistente",
										"gravedad"    			=>"Alta",
										"concepto"				=>$concepto['codigo'],
										"variable"				=>$v,
										"formula"				=>$concepto['formula'],
										"descripcion"			=>"La formula intenta usar una variable inexistente.",
										"recomendacion"			=>"Verifique que la formula este correctamente definida y que las variables que esta formula utiliza existan en el sistema.",
										"descripcion_adicional"	=>"");
				}
			}
		}
		
		
		/**
		* Si en la cantidad hay una variable, la reemplazo.
		*/
		$conceptoCantidad = 0;
		if(!empty($concepto['cantidad'])) {
			if(isset($this->__variables[$concepto['cantidad']])) {
				if($this->__variables[$concepto['cantidad']] !== "#N/A") {
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


		/**
		* Verifico si el nombre que se muestra del concepto es una formula.
		* Esta formula esta limitada solo al manejo BASICO de strings, que para el caso, es suficiente.
		*/
		if(!empty($concepto['nombre_formula'])) {
			$nombreConcepto = $concepto['nombre_formula'];
			
			if(substr($nombreConcepto, 0, 4) == "=if(") {
				preg_match_all("/(\'#[a-z,A-Z,0-9,\s]+\'[\s]*=[\s]*\'[a-z,A-Z,0-9,\s]+\')/", $nombreConcepto, $strings);
				if(!empty($strings[1][0])) {
					$tmp = explode(",", str_replace(" ", "", str_replace("=if(", "", str_replace("'", "", str_replace(")", "", $nombreConcepto)))));
					if(count($tmp) >= 2 && substr($tmp[0], 0, 1) == "#" && strpos($tmp[0], "=") > 1) {
						$condicion = explode("=", $tmp[0]);
						$this->__getVariables(array($condicion[0]));
						if(isset($this->__variables[$condicion[0]])) {
							if($this->__variables[$condicion[0]]['valor'] !== "#N/A") {
								if($this->__variables[$condicion[0]]['valor'] == $condicion[1]) {
									$nombreConcepto = $tmp[1];
								}
								else {
									$nombreConcepto = $tmp[2];
								}
							}
							else {
								$errores[] = array(	"tipo"					=>"Variable No Resuelta",
													"gravedad"				=>"Media",
													"concepto"				=>$concepto['codigo'],
													"variable"				=>$condicion[0],
													"formula"				=>$concepto['formula'],
													"descripcion"			=>"La formula del nombre intenta usar una variable que no ha podido ser resuelta.",
													"recomendacion"			=>"Verifique que los datos hayan sido correctamente ingresados.",
													"descripcion_adicional"	=>$this->__variables[$condicion[0]]['formula']);
							}
						}
						else {
							$errores[] = array(	"tipo"					=>"Variable Inexistente",
												"gravedad"				=>"Media",
												"concepto"				=>$concepto['codigo'],
												"variable"				=>$condicion[0],
												"formula"				=>$concepto['formula'],
												"descripcion"			=>"El nombre del concepto intenta usar una variable inexistente.",
												"recomendacion"			=>"Verifique que la formula del nombre este correctamente definida y que las variables utilizadas existan en el sistema.",
												"descripcion_adicional"	=>$concepto['nombre']);
						}
					}
				}
			}
		}
		else {
			$nombreConcepto = $concepto['nombre'];
		}
		
		/**
		* Si en el nombre hay variables, busco primero estos valores.
		*/
		preg_match_all("/(#[a-z0-9_]+)/", $nombreConcepto, $variables_tmp);
		if(!empty($variables_tmp[0])) {
			$this->__getVariables($variables_tmp[0]);
			foreach($variables_tmp[0] as $k=>$v) {
				/**
				* Debe buscar la variable para reemplazarla dentro de la formula.
				* Usa la RegEx y no str_replace, porque por ejemplo, si debo reemplazar #horas, y en cuentra
				* #horas lo hara ok, pero si encuentra #horas_enfermedad, dejara REEMPLAZO_enfermedad.
				*/
				if(isset($this->__variables[$v])) {
					if($this->__variables[$v]['valor'] !== "#N/A") {
						$nombreConcepto = preg_replace("/".$v."(\W)|".$v."$/", $this->__variables[$v]['valor'] . "$1", $nombreConcepto);
					}
					else {
						$errores[] = array(	"tipo"					=>"Variable No Resuelta",
           									"gravedad"				=>"Media",
											"concepto"				=>$concepto['codigo'],
											"variable"				=>$v,
											"formula"				=>"",
											"descripcion"			=>"El nombre del concepto intenta usar una variable que no ha podido ser resuelta.",
											"recomendacion"			=>"Verifique que los datos hayan sido correctamente ingresados.",
											"descripcion_adicional"	=>$nombreConcepto);
					}
				}
				else {
					$errores[] = array(	"tipo"					=>"Variable Inexistente",
										"gravedad"				=>"Media",
										"concepto"				=>$concepto['codigo'],
										"variable"				=>$v,
										"formula"				=>"",
										"descripcion"			=>"El nombre del concepto intenta usar una variable inexistente.",
										"recomendacion"			=>"Verifique que el nombre este correctamente definido y que las variables que este utiliza existan en el sistema.",
										"descripcion_adicional"	=>$nombreConcepto);
				}
			}
		}
		

		/**
		* El formulador si hay una comparacion de strings se equivoca, entonces, lo reemplazo por su
		* equivalente en ascci y comparo numeros que se que anda bien.
		*/
		preg_match_all("/(\'[a-z,A-Z,0-9,[:space:]]+\'[[:space:]]*=[[:space:]]*\'[a-z,A-Z,0-9,[:space:]]+\')/", $formula, $strings);
		foreach($strings[1] as $string) {
			$partes = explode("=", str_replace(" ", "", str_replace("'", "", $string)));
			$parteIzquierda = $parteDerecha = "";
			foreach($partes as $k=>$parte){
				$parte = strtolower($parte);
				$tmp = str_split($parte);
				foreach($tmp as $letra) {
					if($k == 0) {
						$parteIzquierda .= ord($letra);
					}
					else {
						$parteDerecha .= ord($letra);
					}
				}
			}
			$formula = str_replace($string, $parteIzquierda . " = " . $parteDerecha, $formula);
		}
		
		/**
		* Busco valores que espero vengan como datos dentro de la relacion.
		if(preg_match_all("/\[([a-z,A-Z]*)\]\[([a-z,A-Z]*)\]/", $formula, $matches)) {
			foreach($matches[0] as $k=>$match) {
				$tmpValor = $this->__relacion[$matches[1][$k]][$matches[2][$k]];
				$formula = str_replace($match, $tmpValor, $formula);
			}
		}
		*/

		
		/**
		* Veo si es un consulta SQL.
		if(preg_match("/^select/i", $formula)) {
			$sql = $formula . " and r.id = '" . $rId . "'";
			$valor = $this->Liquidacion->ejecutarConsulta($sql);
		}
		*/

		/**
		* Veo si es un valor directo.
		if(preg_match(VALID_NUMBER, $concepto['formula'])) {
			$valor = $concepto['formula'];
		}
		*/
		
		/**
		* Veo si es una formula, que me indica la suma del remunerativo, de las deducciones o del no remunerativo.
		*/
		if(preg_match("/^=sum[\s]*\([\s]*(Remunerativo|Deduccion|No\sRemunerativo)[\s]*\)$/", $formula, $matches)) {
			foreach($this->__conceptos as $conceptoTmp) {
				if($conceptoTmp['tipo'] == $matches[1] && ($conceptoTmp['imprimir'] == "Si" || $conceptoTmp['imprimir'] == "Solo con valor")) {
					if(empty($conceptoTmp['valor'])) {
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
		elseif(substr($formula, 0, 1) == "=") {
			preg_match_all("/(@[\w]+)/", $formula, $matches);

			/**
			* Verifico que tenga calculado todos los conceptos que esta formula me pide.
			* Si aun no lo tengo, lo calculo.
			*/
			if(!empty($matches[1])) {
				foreach($matches[1] as $match) {
					$match = substr($match, 1);
					
					/**
					* Si no esta, lo busco.
					*/
					if(!isset($this->__conceptos[$match])) {
						/**
						* Busco los conceptos que puedan estar faltandome.
						* Los agrego al array de conceptos identificandolos y poniendoles el estado a no imprimir.
						*/
						$conceptoParaCalculoTmp = $this->Relacion->RelacionesConcepto->Concepto->findConceptos("ConceptoPuntual", am(array("relacion"=>$this->__relacion, "codigoConcepto"=>$match), $opciones));
						if(empty($conceptoParaCalculoTmp)) {
							$errores[] = array(	"tipo"					=>"Concepto Inexistente",
												"gravedad"				=>"Alta",
												"concepto"				=>$match,
												"variable"				=>"",
												"formula"				=>$formula,
												"descripcion"			=>"La formula requiere de un concepto inexistente.",
												"recomendacion"			=>"Verifique la formula y que todos los conceptos que esta utiliza existan.",
												"descripcion_adicional"	=>"verifique: " . $concepto['codigo']);
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
					if(!isset($this->__conceptos[$match]['valor'])) {
						if(isset($this->__conceptos[$match])) {
							$resolucionCalculo = $this->__getConceptoValor($this->__conceptos[$match], $opciones);
							$this->__conceptos[$match] = am($resolucionCalculo, $this->__conceptos[$match]);
						}
						else {
							$errores[] = array(	"tipo"					=>"Concepto Inexistente",
												"gravedad"				=>"Alta",
												"concepto"				=>$match,
												"variable"				=>"",
												"formula"				=>$formula,
												"descripcion"			=>"La formula requiere de un concepto inexistente.",
												"recomendacion"			=>"Verifique la formula y que todos los conceptos que esta utiliza existan.",
												"descripcion_adicional"	=>"verifique: " . $concepto['codigo']);
						}
					}
						
					/**
					* Reemplazo en la formula el concepto por su valor.
					*/
					if(isset($this->__conceptos[$match])) {
						$resolucionCalculo['valor'] = $this->__conceptos[$match]['valor'];
						$formula = preg_replace("/(@" . $match . ")([\)|\s|\*|\+\/\-|\=|\,]*[^_])/", $resolucionCalculo['valor'] . "$2", $formula);
						$resolucionCalculo['debug'] = $formula;
						}
					else {
						$errores[] = array(	"tipo"					=>"Concepto Inexistente",
											"gravedad"				=>"Alta",
											"concepto"				=>$match,
											"variable"				=>"",
											"formula"				=>$formula,
											"descripcion"			=>"La formula requiere de un concepto inexistente.",
											"recomendacion"			=>"Verifique la formula y que todos los conceptos que esta utiliza existan.",
											"descripcion_adicional"	=>"verifique: " . $concepto['codigo']);
					}
				}
			}

			/**
			* Resuelvo la formula.
			*/
			$valor = $this->Formulador->resolver($formula);
		}
		elseif($formula == "") {
			$errores[] = array(	"tipo"					=>"Formula de Concepto Inexistente",
								"gravedad"				=>"Media",
								"concepto"				=>$concepto['codigo'],
								"variable"				=>"",
								"formula"				=>"",
								"descripcion"			=>"El concepto no tiene definida una formula.",
								"recomendacion"			=>"Ingrese la formula correspondiente al concepto en caso de que sea necesario. Para evitar este error ingrese como formula: =0",
								"descripcion_adicional"	=>"Se asume como 0 (cero) el valor del concepto.");
			$valor = 0;
		}
		else {
			$valor = "#N/A";
		}

		return array("valor"=>$valor, "debug"=>$formula, "valor_cantidad"=>$conceptoCantidad, "nombre"=>$nombreConcepto, "errores"=>$errores);
	}


/**
 * Obtiene el valor las variables.
 * Actualiza el array $this->__variables con el valor de la variable.
 */
function __getVariables($variables) {

	if(!is_array($variables) && is_string($variables)) {
		$variables = array($variables);
	}
	$variables = array_unique($variables);

	$valor = null;
	foreach($variables as $variable) {
		if(in_array($variable, $this->__variablesYaResueltas)) {
			continue;
		}
		
		/**
		* Intento resolver las variables que vienen dadas por relacion.
		*/
		if(!isset($this->__variables[$variable])) {
			$this->__variables[$variable]['valor'] = "#N/A";
			$this->__variables[$variable]['error'] = "Inexistente";
			$this->__variablesYaResueltas[] = $variable;
			continue;
		}


		/**
		* Busco si es una variable que viene dada por la relacion.
		* Depende de recursive, puede venir $data[model1][model2][campo] 0 $data[model1][campo]
		*/
		if(preg_match("/^\[([a-zA-Z]*)\]\[([a-zA-Z]*)\]\[([a-zA-Z_]*)\]$/", $this->__variables[$variable]['formula'], $matchesA) || preg_match("/^\[([a-zA-Z]*)\]\[([a-zA-Z_]*)\]$/", $this->__variables[$variable]['formula'], $matchesB)) {
			if(isset($matchesA[1]) && isset($matchesA[2]) && isset($matchesA[3]) && isset($this->__relacion[$matchesA[1]][$matchesA[2]][$matchesA[3]])) {
				$valor = $this->__relacion[$matchesA[1]][$matchesA[2]][$matchesA[3]];
			}
			elseif(isset($matchesB[1]) && isset($matchesB[2]) && isset($this->__relacion[$matchesB[1]][$matchesB[2]])) {
				$valor = $this->__relacion[$matchesB[1]][$matchesB[2]];
			}
			else {
				$valor = "#N/A";
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
		}

		/**
		* Si es una formula, la resuelvo.
		*/
		if(substr($this->__variables[$variable]['formula'], 0, 1) == "=") {
			$formula = $this->__variables[$variable]['formula'] . " ";
			/**
			* Si en la formula hay variables, busco primero estos valores.
			*/
			preg_match_all("/(#[a-z0-9_]+)/", $formula, $variables_tmp);
			if(!empty($variables_tmp[0])) {
				$this->__getVariables($variables_tmp[0]);
				foreach($variables_tmp[0] as $v) {
					$formula = preg_replace("/(" . $v . ")([\)|\s|\*|\+\/\-|\=|\,]*[^_])/", $this->__variables[$v]['valor'] . "$2", $formula);
				}
			}
			$this->__variables[$variable]['valor'] = $this->Formulador->resolver($formula);
			$this->__variablesYaResueltas[] = $variable;
			continue;
		}
		
		switch($variable) {
			case "#fecha_actual":
				$this->__variables[$variable]['valor'] = date("Y-m-d");
				break;
			case "#dia_ingreso":
				$this->__getVariables(array("#fecha_ingreso"));
				$this->__variables[$variable]['valor'] = $this->Util->traerDia(array("fecha" => $this->__variables['#fecha_ingreso']['valor']));
				break;
			case "#dia_egreso":
				$this->__getVariables(array("#fecha_egreso"));
				$this->__variables[$variable]['valor'] = $this->Util->traerDia(array("fecha" => $this->__variables['#fecha_egreso']['valor']));
				break;
			case "#mes_ingreso":
				$this->__getVariables(array("#fecha_ingreso"));
				$this->__variables[$variable]['valor'] = $this->Util->traerMes(array("fecha" => $this->__variables['#fecha_ingreso']['valor']));
				break;
			case "#mes_egreso":
				$this->__getVariables(array("#fecha_egreso"));
				$this->__variables[$variable]['valor'] = $this->Util->traerMes(array("fecha" => $this->__variables['#fecha_egreso']['valor']));
				break;
			case "#ano_ingreso":
				$this->__getVariables(array("#fecha_ingreso"));
				$this->__variables[$variable]['valor'] = $this->Util->traerAno(array("fecha" => $this->__variables['#fecha_ingreso']['valor']));
				break;
			case "#ano_egreso":
				$this->__getVariables(array("#fecha_egreso"));
				$this->__variables[$variable]['valor'] = $this->Util->traerAno(array("fecha" => $this->__variables['#fecha_egreso']['valor']));
				break;
			case "#dia_desde_liquidacion":
				$this->__getVariables(array("#fecha_desde_liquidacion"));
				$this->__variables[$variable]['valor'] = $this->Util->traerDia(array("fecha" => $this->__variables['#fecha_desde_liquidacion']['valor']));
				break;
			case "#dia_hasta_liquidacion":
				$this->__getVariables(array("#fecha_hasta_liquidacion"));
				$this->__variables[$variable]['valor'] = $this->Util->traerDia(array("fecha" => $this->__variables['#fecha_hasta_liquidacion']['valor']));
				break;
			case "#dias_antiguedad":
			case "#meses_antiguedad":
			case "#anos_antiguedad":
				$this->__getVariables(array("#fecha_ingreso", "#fecha_egreso"));
				if($this->__variables['#fecha_egreso']['valor'] != "0000-00-00" && $this->__variables['#fecha_egreso']['valor'] < $this->__variables['#fecha_hasta_liquidacion']['valor']) {
					$antiguedad = $this->Util->diferenciaEntreFechas(array("hasta"=>$this->__variables['#fecha_egreso']['valor'], "desde"=>$this->__variables['#fecha_ingreso']['valor']));
					$agregarDias = 0;
				}
				else {
					$antiguedad = $this->Util->diferenciaEntreFechas(array("hasta"=>$this->__variables['#fecha_hasta_liquidacion']['valor'], "desde"=>$this->__variables['#fecha_ingreso']['valor']));
					$agregarDias = 1;
				}
				if($variable == "#dias_antiguedad") {
					$this->__variables[$variable]['valor'] = ($antiguedad['dias'] + $agregarDias);
				}
				elseif($variable == "#meses_antiguedad") {
					$this->__variables[$variable]['valor'] = floor(($antiguedad['dias'] + $agregarDias) / 30);
				}
				elseif($variable == "#anos_antiguedad") {
					$this->__variables[$variable]['valor'] = floor(($antiguedad['dias'] + $agregarDias) / 365);
				}
				break;
			case "#dias_corridos_periodo":
				$r = array("#fecha_ingreso", "#dia_ingreso", "#mes_ingreso", "#ano_ingreso", "#dia_egreso", "#mes_egreso", "#ano_egreso", "#mes_liquidacion", "#ano_liquidacion", "#dia_desde_liquidacion", "#dia_hasta_liquidacion", "#fecha_desde_liquidacion", "#fecha_hasta_liquidacion");
				$this->__getVariables($r);
				if($this->__variables['#dia_ingreso']['valor'] > 1
					&& $this->__variables['#mes_ingreso']['valor'] == $this->__variables['#mes_liquidacion']['valor']
						&& $this->__variables['#ano_ingreso']['valor'] == $this->__variables['#ano_liquidacion']['valor']) {
					$desde = $this->__variables['#fecha_ingreso']['valor'];
				}
				else {
					$desde = $this->__variables['#fecha_desde_liquidacion']['valor'];
				}

				if($this->__variables['#dia_egreso']['valor'] < $this->__variables['#dia_hasta_liquidacion']['valor']
					&& $this->__variables['#mes_egreso']['valor'] == $this->__variables['#mes_liquidacion']['valor']
						&& $this->__variables['#ano_egreso']['valor'] == $this->__variables['#ano_liquidacion']['valor']) {
					$hasta = $this->__variables['#fecha_egreso']['valor'];
					$agregarDias = 0;
				}
				else {
					$hasta = $this->__variables['#fecha_hasta_liquidacion']['valor'];
					$agregarDias = 1;
				}
				$antiguedad = $this->Util->diferenciaEntreFechas(array("desde"=>$desde, "hasta"=>$hasta));
				$this->__variables[$variable]['valor'] = $antiguedad['dias'] + $agregarDias;
				break;
			case "#dias_antiguedad_al_31_12":
			case "#meses_antiguedad_al_31_12":
			case "#anos_antiguedad_al_31_12":
				$this->__getVariables(array("#fecha_ingreso"));
				$anoAnterior = $this->Util->traerAno(array("fecha" => $this->__variables['#fecha_ingreso']['valor'])) - 1;
				$fechaHasta = $this->Util->traerFecha(array("ano"=>$anoAnterior, "mes"=>12, "dia"=>31));
				if($fechaHasta > $this->__variables['#fecha_ingreso']['valor']) {
					$antiguedad = $this->Util->diferenciaEntreFechas(array("hasta"=>$fechaHasta, "desde"=>$this->__variables['#fecha_ingreso']['valor']));
				}
				else {
					$antiguedad['dias'] = 0;
				}
				if($variable == "#dias_antiguedad_al_31_12") {
					$this->__variables[$variable]['valor'] = $antiguedad['dias'];
				}
				elseif($variable == "#meses_antiguedad_al_31_12") {
					$this->__variables[$variable]['valor'] = floor($antiguedad['dias'] / 30);
				}
				elseif($variable == "#anos_antiguedad_al_31_12") {
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
			case "#ausencias_justificadas":
			case "#ausencias_injustificadas":
				/**
				* Debo buscar las ausencias cuya fecha hasta se encuentre en el periodo que voy a liquidar
				* o sea posterior.
				*/
				$condicionesAusencias = null;
				$condicionesAusencias['Ausencia.hasta'] = ">=" . $this->__variables['#fecha_desde_liquidacion'];
				$condicionesAusencias['Ausencia.relacion_id'] = $this->__relacion['Relacion']['id'];
				$this->Relacion->Ausencia->contain(array('AusenciasMotivo'));
				$ausencias = $this->__obtenerAusencias($this->Relacion->Ausencia->findAll($condicionesAusencias));
				$this->__variables["#ausencias_justificadas"]['valor'] = $ausencias['Justificada'];
				$this->__variables["#ausencias_injustificadas"]['valor'] = $ausencias['Injustificada'];
				break;
		}
		$this->__variablesYaResueltas[] = $variable;
	}
}



/**
* Calcula los dias de ausencias segun el tipo (justificadas o injustificadas) que hubo durante el periodo.
*
* return array 
*/
function __obtenerAusencias_deprecated($ausencias = null) {
	$totalDiasAusencia['Justificada'] = 0;
	$totalDiasAusencia['Injustificada'] = 0;
	if(!empty($ausencias)) {
		foreach($ausencias as $ausencia) {

			$diaDesde = $this->Util->traerDia(array("fecha" => $ausencia['Ausencia']['desde']));
			$mesDesde = $this->Util->traerMes(array("fecha" => $ausencia['Ausencia']['desde']));
			$anoDesde = $this->Util->traerAno(array("fecha" => $ausencia['Ausencia']['desde']));
			$diaHasta = $this->Util->traerDia(array("fecha" => $ausencia['Ausencia']['hasta']));
			$mesHasta = $this->Util->traerMes(array("fecha" => $ausencia['Ausencia']['hasta']));
			$anoHasta = $this->Util->traerAno(array("fecha" => $ausencia['Ausencia']['hasta']));

			switch($this->__variables['#periodo_liquidacion']) {
				case "primeraQuincena":
					$diaInicioPeriodo = 1;
					$diaFinPeriodo = 15;
					break;
				case "segundaQuincena":
					$diaInicioPeriodo = 16;
					$diaFinPeriodo = 31;
					break;
				case "mensual":
					$diaInicioPeriodo = 1;
					$diaFinPeriodo = 31;
					break;
			}

			if($anoDesde == $anoHasta && $mesDesde == $mesHasta) {
				if($diaDesde > $diaInicioPeriodo) {
					$diaInicio = $diaDesde;
				}
				else {
					$diaInicio = $diaInicioPeriodo;
				}
				if($diaHasta < $diaFinPeriodo) {
					$diaFin = $diaHasta;
				}
				else {
					$diaFin = $diaFinPeriodo;
				}

				$fechaDesde = $this->Util->traerFecha(array("ano"=>$anoDesde, "mes"=>$mesDesde, "dia"=>$diaInicio));
				$fechaHasta = $this->Util->traerFecha(array("ano"=>$anoDesde, "mes"=>$mesDesde, "dia"=>$diaFin));
				$diferencia = $this->Util->diferenciaEntreFechas(array("hasta"=>$fechaHasta, "desde"=>$fechaDesde));
				$dias = $diferencia['dias']+1;
			}
			elseif($anoDesde == $anoHasta) {

				if($mesDesde < $this->__variables['#mes_liquidacion']) {
					$diaInicio = $diaInicioPeriodo;
				}
				
				if($mesHasta > $this->__variables['#mes_liquidacion'] || $diaHasta > $diaFinPeriodo) {
					$diaFin = $diaFinPeriodo;
				}
				else {
					$diaFin = $diaHasta;
				}
				
				$fechaDesde = $this->Util->traerFecha(array("ano"=>$anoDesde, "mes"=>$this->__variables['#mes_liquidacion'], "dia"=>$diaInicio));
				$fechaHasta = $this->Util->traerFecha(array("ano"=>$anoDesde, "mes"=>$this->__variables['#mes_liquidacion'], "dia"=>$diaFin));
				$diferencia = $this->Util->diferenciaEntreFechas(array("hasta"=>$fechaHasta, "desde"=>$fechaDesde));
				$dias = $diferencia['dias']+1;
			}
			else {

				if($anoDesde < $this->__variables['#ano_liquidacion']) {
					$diaInicio = $diaInicioPeriodo;
				}
				else {
					$diaInicio = $diaDesde;
				}

				if($anoHasta > $this->__variables['#ano_liquidacion'] || $mesHasta > $this->__variables['#mes_liquidacion'] || $diaHasta > $diaFinPeriodo) {
					$diaFin = $diaFinPeriodo;
				}
				else {
					$diaFin = $diaHasta;
				}
				
				$fechaDesde = $this->Util->traerFecha(array("ano"=>$this->__variables['#ano_liquidacion'], "mes"=>$this->__variables['#mes_liquidacion'], "dia"=>$diaInicio));
				$fechaHasta = $this->Util->traerFecha(array("ano"=>$this->__variables['#ano_liquidacion'], "mes"=>$this->__variables['#mes_liquidacion'], "dia"=>$diaFin));
				$diferencia = $this->Util->diferenciaEntreFechas(array("hasta"=>$fechaHasta, "desde"=>$fechaDesde));
				$dias = $diferencia['dias']+1;
			}
			if($ausencia['Ausencia']['parcial'] == "Medio Dia") {
				$dias = $dias / 2;
			}
			$totalDiasAusencia[$ausencia['AusenciasMotivo']['tipo']]+=$dias;
		}
	}
	return $totalDiasAusencia;
}


/**
* Esta funcion realiza el mapeo entre lo que tengo en el array de conceptos,
* y los datos que necesito para guardarlo en el detalle de la liquidacion.
*/
	function __agregarDetalle($detalleLiquidacion) {
		//debug($detalleLiquidacion);
		$detalle = null;
		if(!empty($detalleLiquidacion['concepto_id'])) {
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
* y los datos que necesito para guardarlo en los errores de la liquidacion.
*/
	function __agregarError($errorLiquidacion) {
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

}
?>