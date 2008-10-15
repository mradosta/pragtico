<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los conceptos.
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
 * La clase encapsula la logica de acceso a datos asociada a los conceptos.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Concepto extends AppModel {

	var $order = array('Concepto.nombre'=>'asc');

		/*
        'formula' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe ingresar la formula de calculo del concepto.')
        ),
        */
	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre del concepto.')
        ),
        'codigo' => array(
			array(
				'rule'	=> '/^[a-z,0-9,_]+$/',
				'message'	=>'El codigo del concepto solo puede contener letras minusculas y numeros.'),
			array(
				'rule'	=> array('minLength', 4),
				'message'	=>'El codigo del concepto debe tener al menos 4 caracteres.'),
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el codigo del concepto.')
        ),
        'tipo' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar el tipo de concepto.')
        ),
        'coeficiente_id' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar un coeficiente.')
        ),
        'desde' => array(
			array(
				'rule'	=> VALID_DATE_NULO,
				'message'	=>'La fecha no es valida.'),
			array(
				'rule'	=> '__validarRango',
				'message'	=>'La vigencia desde debe ser inferior o igual a la vigencia hasta.')
        ),
        'hasta' => array(
			array(
				'rule'	=> VALID_DATE_NULO,
				'message'	=>'La fecha no es valida.'),
			array(
				'rule'	=> '__validarRango',
				'message'	=>'La vigencia hasta debe ser superior o igual a la vigencia desde.')
        )        
	);

	var $belongsTo = array(	'Coeficiente' =>
                        array('className'    => 'Coeficiente',
                              'foreignKey'   => 'coeficiente_id'));
                              
	var $hasAndBelongsToMany = array(	'Convenio' =>
								array('with' => 'ConveniosConcepto'),
										'Empleador' =>
								array('with' => 'EmpleadoresConcepto'),
										'Relacion' =>
								array('with' => 'RelacionesConcepto'));

/**
 * Valida que el extremo superior del rango sea mayor al inferior
 * en caso de que ambos esten seteados.
 */
    function __validarRango($value, $params = array()) {
		if(!empty($this->data[$this->name]['desde']) && !empty($this->data[$this->name]['hasta'])) {
			if($this->data[$this->name]['desde'] > $this->data[$this->name]['hasta']) {
				return false;
			}
		}
        return true;
    }


/**
 * Encuentra los conceptos asociados a una relacion.
 * Retorna un array con todos los conceptos, teniendo en cuenta la estructura de jerarquias
 * que existen entre ellos, es decir, si un concepto es asociado a una relacion y su formula
 * es escrita en este nivel, esta prevalecera por sobre la formula del concepto asociada al
 * empleador, o al convenio colectivo, o al mismo concepto.
 * La jerarquia es: 	Relacion,
						Empleador,
						Convenio Colectivo,
						Concepto.
 */

	//function findConceptos($tipo = "Relacion", $relacion = null, $codigoConcepto = null, $opciones = array()) {
	function findConceptos($tipo = "Relacion", $opciones = array()) {

		//$opciones['relacion'];

		/**
		* TODO: Implementar forma de generar queryes de cakephp
		*/
		$default['hasta'] = "2000-01-01";
		$default['desde'] = "2050-12-31";
		$default['condicionAdicional'] = ""; // de la forma string....
		$opciones = am($default, $opciones);


		
		
		if(!empty($opciones['condicionAdicional'])) {
			$condicionAdicional = " and " . $opciones['condicionAdicional'] . " ";
		}
		else {
			$condicionAdicional = "";
		}
		
		$fieldsRelaciones =				array(	"RelacionesConcepto.id",
												"RelacionesConcepto.relacion_id",
												"RelacionesConcepto.concepto_id",
												"RelacionesConcepto.desde",
												"RelacionesConcepto.hasta",
												"RelacionesConcepto.formula");
		$fieldsEmpleadoresConcepto =	array(	"EmpleadoresConcepto.id",
												"EmpleadoresConcepto.empleador_id",
												"EmpleadoresConcepto.concepto_id",
												"EmpleadoresConcepto.desde",
												"EmpleadoresConcepto.hasta",
												"EmpleadoresConcepto.formula");
		$fieldsConveniosConcepto = 		array(	"ConveniosConcepto.id",
												"ConveniosConcepto.convenio_id",
												"ConveniosConcepto.concepto_id",
												"ConveniosConcepto.desde",
												"ConveniosConcepto.hasta",
												"ConveniosConcepto.formula");
		$fieldsConceptos = 				array(	"Concepto.id",
												"Concepto.codigo",
												"Concepto.nombre",
												"Concepto.tipo",
												"Concepto.periodo",
												"Concepto.sac",
												"Concepto.imprimir",
												"Concepto.antiguedad",
												"Concepto.cantidad",
												"Concepto.formula",
												"Concepto.desde",
												"Concepto.hasta",
												"Concepto.pago",
												"Concepto.orden");
		$fieldCoeficientes = 			array(	"Coeficiente.id",
												"Coeficiente.nombre",
												"Coeficiente.tipo",
												"Coeficiente.valor");
		$fieldEmpleadoresCoeficiente = 	array(	"EmpleadoresCoeficiente.valor");
		$order 		= "ORDER BY
								CASE Concepto.tipo WHEN 'Remunerativo' THEN 0
									WHEN 'No Remunerativo' THEN 1
									WHEN 'Deduccion' THEN 2
								END";

		if($tipo == "Relacion") {
			$fields = am($fieldsRelaciones, $fieldsEmpleadoresConcepto, $fieldsConveniosConcepto, $fieldsConceptos, $fieldCoeficientes, $fieldEmpleadoresCoeficiente);
			$table 	= 	"relaciones_conceptos";
			$joins	=	array(
							array(
								"alias" => "EmpleadoresConcepto",
								"table" => "empleadores_conceptos",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"RelacionesConcepto.concepto_id = EmpleadoresConcepto.concepto_id",
											"EmpleadoresConcepto.empleador_id"=> $opciones['relacion']['Relacion']['empleador_id'] ))
							),
							array(
								"alias" => "ConveniosConcepto",
								"table" => "convenios_conceptos",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"RelacionesConcepto.concepto_id = ConveniosConcepto.concepto_id",
											"ConveniosConcepto.convenio_id" => $opciones['relacion']['ConveniosCategoria']['convenio_id']))
							),
							array(
								"alias" => "Concepto",
								"table" => "conceptos",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"RelacionesConcepto.concepto_id = Concepto.id"))
							),
							array(
								"alias" => "Coeficiente",
								"table" => "coeficientes",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"Concepto.coeficiente_id = Coeficiente.id"))
							),
							array(
								"alias" => "EmpleadoresCoeficiente",
								"table" => "empleadores_coeficientes",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"Coeficiente.id = EmpleadoresCoeficiente.coeficiente_id",
											"EmpleadoresCoeficiente.empleador_id"	=> $opciones['relacion']['Relacion']['empleador_id']))
							)							
						);
			$conditions = array(
							"RelacionesConcepto.relacion_id" => $opciones['relacion']['Relacion']['id'],
							array("OR"	=> array(	"RelacionesConcepto.desde" => "0000-00-00",
												"RelacionesConcepto.desde <=" => $opciones['desde'])),
							array("OR"	=> array(	"RelacionesConcepto.hasta" => "0000-00-00",
												"RelacionesConcepto.hasta >=" => $opciones['hasta']))
						);
		}
		elseif($tipo == "Empleador") {
			$fields = am($fieldsEmpleadoresConcepto, $fieldsConveniosConcepto, $fieldsConceptos, $fieldCoeficientes, $fieldEmpleadoresCoeficiente);
			$table 	= 	"empleadores_conceptos";
			$joins 	=	array(
							array(
								"alias" => "ConveniosConcepto",
								"table" => "convenios_conceptos",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"EmpleadoresConcepto.concepto_id = ConveniosConcepto.concepto_id",
											"ConveniosConcepto.convenio_id" => $opciones['relacion']['ConveniosCategoria']['convenio_id'],
											array("OR"	=> array(	"ConveniosConcepto.desde" => "0000-00-00",
																	"ConveniosConcepto.desde <=" => $opciones['desde'])),
											array("OR"	=> array(	"ConveniosConcepto.hasta" => "0000-00-00",
																	"ConveniosConcepto.hasta >=" => $opciones['hasta'])))
								)
							),
							array(
								"alias" => "Concepto",
								"table" => "conceptos",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"EmpleadoresConcepto.concepto_id = Concepto.id"))
							),
							array(
								"alias" => "Coeficiente",
								"table" => "coeficientes",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"Concepto.coeficiente_id = Coeficiente.id"))
							),
							array(
								"alias" => "EmpleadoresCoeficiente",
								"table" => "empleadores_coeficientes",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"Coeficiente.id = EmpleadoresCoeficiente.coeficiente_id",
											"EmpleadoresCoeficiente.empleador_id"	=> $opciones['relacion']['Relacion']['empleador_id']))
							)							
						);
			$conditions = array(
							"EmpleadoresConcepto.empleador_id" => $opciones['relacion']['Relacion']['empleador_id'],
							array("OR"	=> array(	"EmpleadoresConcepto.desde" => "0000-00-00",
													"EmpleadoresConcepto.desde <=" => $opciones['desde'])),
							array("OR"	=> array(	"EmpleadoresConcepto.hasta" => "0000-00-00",
													"EmpleadoresConcepto.hasta >=" => $opciones['hasta']))
						);
		}
		elseif($tipo == "ConvenioColectivo") {
			$fields = am($fieldsConveniosConcepto, $fieldsConceptos, $fieldCoeficientes, $fieldEmpleadoresCoeficiente);
			$table 	= 	"convenios_conceptos";
			$joins 	=	array(
							array(
								"alias" => "Concepto",
								"table" => "conceptos",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"ConveniosConcepto.concepto_id = Concepto.id"))
							),
							array(
								"alias" => "Coeficiente",
								"table" => "coeficientes",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"Concepto.coeficiente_id = Coeficiente.id"))
							),
							array(
								"alias" => "EmpleadoresCoeficiente",
								"table" => "empleadores_coeficientes",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"Coeficiente.id = EmpleadoresCoeficiente.coeficiente_id",
											"EmpleadoresCoeficiente.empleador_id"	=> $opciones['relacion']['Relacion']['empleador_id']))
							)							
						);
			$conditions = array(
							"ConveniosConcepto.convenio_id" => $opciones['relacion']['ConveniosCategoria']['convenio_id'],
							array("OR"	=> array(	"ConveniosConcepto.desde" => "0000-00-00",
													"ConveniosConcepto.desde <=" => $opciones['desde'])),
							array("OR"	=> array(	"ConveniosConcepto.hasta" => "0000-00-00",
													"ConveniosConcepto.hasta >=" => $opciones['hasta']))
						);
		}
		elseif($tipo == "ConceptoPuntual") {
			$fields = am($fieldsEmpleadoresConcepto, $fieldsConveniosConcepto, $fieldsConceptos, $fieldCoeficientes, $fieldEmpleadoresCoeficiente);
			$table 	= 	"conceptos";
			$joins 	=	array(
							array(
								"alias" => "ConveniosConcepto",
								"table" => "convenios_conceptos",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"Concepto.id = ConveniosConcepto.concepto_id"),
											"ConveniosConcepto.convenio_id" => $opciones['relacion']['ConveniosCategoria']['convenio_id'])
							),
							array(
								"alias" => "EmpleadoresConcepto",
								"table" => "empleadores_conceptos",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"Concepto.id = EmpleadoresConcepto.concepto_id",
											"EmpleadoresConcepto.empleador_id"=> $opciones['relacion']['Relacion']['empleador_id'] ))
							),
							array(
								"alias" => "Coeficiente",
								"table" => "coeficientes",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"Concepto.coeficiente_id = Coeficiente.id"))
							),
							array(
								"alias" => "EmpleadoresCoeficiente",
								"table" => "empleadores_coeficientes",
								"type" 	=> "LEFT",
								"conditions" => array(
									array(	"Coeficiente.id = EmpleadoresCoeficiente.coeficiente_id",
											"EmpleadoresCoeficiente.empleador_id"	=> $opciones['relacion']['Relacion']['empleador_id']))
							)							
						);
			$conditions = array(
							"Concepto.codigo" => $opciones['codigoConcepto'],
							array("OR"	=> array(	"Concepto.desde" => "0000-00-00",
													"Concepto.desde <=" => $opciones['desde'])),
							array("OR"	=> array(	"Concepto.hasta" => "0000-00-00",
													"Concepto.hasta >=" => $opciones['hasta']))
						);
		}
		elseif($tipo == "Todos") {

			$fields = $fieldsConceptos;
			$table	= "conceptos";
			$conditions = array(
							array("OR"	=> array(	"Concepto.desde" => "0000-00-00",
													"Concepto.desde <=" => $opciones['desde'])),
							array("OR"	=> array(	"Concepto.hasta" => "0000-00-00",
													"Concepto.hasta >=" => $opciones['hasta']))
						);
			$order	= "ORDER BY Concepto.nombre, Concepto.codigo";
		}
		
		$sql = $this->generarSql(array("fields"=>$fields, "table"=>$table, "conditions"=>$conditions, "joins"=>$joins, "order"=>$order));
		$r = $this->query($sql);
		$conceptos = array();
		foreach($r as $v) {
			/**
			* En principio tomo el concepto como verdad, luego puede estar sobreescrito.
			* La jerarquia es: 	Relacion,
								Empleador,
								Convenio Colectivo,
								Concepto.
			*/

			/**
			* Descarto por las fechas de vigencias.
			*/
			
			/**
			* De la relacion.
			*/
			if(!empty($v['RelacionesConcepto']['desde']) && $v['RelacionesConcepto']['desde'] != "0000-00-00" && $v['RelacionesConcepto']['desde'] > $opciones['desde']) {
				continue;
			}
			if(!empty($v['RelacionesConcepto']['hasta']) && $v['RelacionesConcepto']['hasta'] != "0000-00-00" && $v['RelacionesConcepto']['hasta'] < $opciones['hasta']) {
				continue;
			}
			
			/**
			* Del empleador.
			*/
			if(!empty($v['EmpleadoresConcepto']['desde']) && $v['EmpleadoresConcepto']['desde'] != "0000-00-00" && $v['EmpleadoresConcepto']['desde'] > $opciones['desde']) {
				continue;
			}
			if(!empty($v['EmpleadoresConcepto']['hasta']) && $v['EmpleadoresConcepto']['hasta'] != "0000-00-00" && $v['EmpleadoresConcepto']['hasta'] < $opciones['hasta']) {
				continue;
			}

			/**
			* Del convenio.
			*/
			if(!empty($v['ConveniosConcepto']['desde']) && $v['ConveniosConcepto']['desde'] != "0000-00-00" && $v['ConveniosConcepto']['desde'] > $opciones['desde']) {
				continue;
			}
			if(!empty($v['ConveniosConcepto']['hasta']) && $v['ConveniosConcepto']['hasta'] != "0000-00-00" && $v['ConveniosConcepto']['hasta'] < $opciones['hasta']) {
				continue;
			}
			
			/**
			* Del concepto.
			*/
			if(!empty($v['Concepto']['desde']) && $v['Concepto']['desde'] != "0000-00-00" && $v['Concepto']['desde'] > $opciones['desde']) {
				continue;
			}
			if(!empty($v['Concepto']['hasta']) && $v['Concepto']['hasta'] != "0000-00-00" && $v['Concepto']['hasta'] < $opciones['hasta']) {
				continue;
			}


			/**
			* Asigo como valido el concepto y su coeficiente,
			* luego, en base a la jerarquia, sobreescribo la formula si coresponde.
			if(isset($v['Coeficiente'])) {
				//$conceptos[$v['Concepto']['codigo']] = am($v['Concepto'], $v['Coeficiente']);
			}
			else {
				//$conceptos[$v['Concepto']['codigo']] = $v['Concepto'];
			}
			*/
			$conceptos[$v['Concepto']['codigo']] = $v['Concepto'];
			$conceptos[$v['Concepto']['codigo']]['concepto_id'] = $v['Concepto']['id'];

			/**
			* Verifico que el valor del coeficiente no haya sido sobreescrito por el empleador.
			*/
			$conceptos[$v['Concepto']['codigo']]['coeficiente_id'] = $v['Coeficiente']['id'];
			$conceptos[$v['Concepto']['codigo']]['coeficiente_nombre'] = $v['Coeficiente']['nombre'];
			$conceptos[$v['Concepto']['codigo']]['coeficiente_tipo'] = $v['Coeficiente']['tipo'];
			$conceptos[$v['Concepto']['codigo']]['coeficiente_valor'] = $v['Coeficiente']['valor'];
			if(!empty($v['EmpleadoresCoeficiente']['coeficiente_valor'])) {
				$conceptos[$v['Concepto']['codigo']]['coeficiente_valor'] = $v['EmpleadoresCoeficiente']['coeficiente_valor'];
			}

			/**
			* Sobreescribo Formulas.
			*/
			if(!empty($v['RelacionesConcepto']['formula'])) {
				$conceptos[$v['Concepto']['codigo']]['formula'] = $v['RelacionesConcepto']['formula'];
			}
			elseif(!empty($v['EmpleadoresConcepto']['formula'])) {
				$conceptos[$v['Concepto']['codigo']]['formula'] = $v['EmpleadoresConcepto']['formula'];
			}
			elseif(!empty($v['ConveniosConcepto']['formula'])) {
				$conceptos[$v['Concepto']['codigo']]['formula'] = $v['ConveniosConcepto']['formula'];
			}
			
			/**
			* Sobreescribo ids.
			*/
			if(!empty($v['RelacionesConcepto']['id'])) {
				$conceptos[$v['Concepto']['codigo']]['id'] = $v['RelacionesConcepto']['id'];
			}
			elseif(!empty($v['EmpleadoresConcepto']['formula'])) {
				$conceptos[$v['Concepto']['codigo']]['id'] = $v['EmpleadoresConcepto']['id'];
			}
			elseif(!empty($v['ConveniosConcepto']['formula'])) {
				$conceptos[$v['Concepto']['codigo']]['id'] = $v['ConveniosConcepto']['id'];
			}
		}
		return $conceptos;
	}



/**
 * Encuentra los conceptos asociados a una relacion.
 * Retorna un array con todos los conceptos, teniendo en cuenta la estructura de jerarquias
 * que existen entre ellos, es decir, si un concepto es asociado a una relacion y su formula
 * es escrita en este nivel, esta prevalecera por sobre la formula del concepto asociada al
 * empleador, o al convenio colectivo, o al mismo concepto.
 * La jerarquia es: 	Relacion,
						Empleador,
						Convenio Colectivo,
						Concepto.
 */

	function findConceptos_deprecated($tipo = "Relacion", $relacion = null, $codigoConcepto = null, $opciones = array()) {

		/**
		* TODO: Implementar forma de generar queryes de cakephp
		*/
		$default['hasta'] = "2000-01-01";
		$default['desde'] = "2050-12-31";
		$default['condicionAdicional'] = ""; // de la forma string....
		$opciones = am($default, $opciones);


		
		
		if(!empty($opciones['condicionAdicional'])) {
			$condicionAdicional = " and " . $opciones['condicionAdicional'] . " ";
		}
		else {
			$condicionAdicional = "";
		}
		
		if($tipo == "Relacion") {
			$sql = "
					select
										rc.id,
										rc.relacion_id,
										rc.concepto_id,
										rc.desde,
										rc.hasta,
										rc.formula,
										ec.id,
										ec.empleador_id,
										ec.concepto_id,
										ec.desde,
										ec.hasta,
										ec.formula,
										cc.id,
										cc.convenio_id,
										cc.concepto_id,
										cc.desde,
										cc.hasta,
										cc.formula,
										c.id,
										c.codigo,
										c.nombre,
										c.nombre_formula,
										c.tipo,
										c.periodo,
										c.sac,
										c.imprimir,
										c.antiguedad,
										c.remuneracion,
										c.cantidad,
										c.formula,
										c.desde,
										c.hasta,
										c.pago,
										c.orden,
										co.id as coeficiente_id,
										co.nombre as coeficiente_nombre,
										co.tipo as coeficiente_tipo,
										co.valor as coeficiente_valor,
										eco.valor as coeficiente_valor
					from 				relaciones_conceptos rc
										left join empleadores_conceptos ec
											on (rc.concepto_id = ec.concepto_id
												and ec.empleador_id = '" . $relacion['Relacion']['empleador_id'] . "')
										left join convenios_conceptos cc on (rc.concepto_id = cc.concepto_id
											and cc.convenio_id = '" . $relacion['ConveniosCategoria']['convenio_id'] . "')
										left join conceptos c on (rc.concepto_id = c.id)
										left join coeficientes co on (c.coeficiente_id = co.id)
										left join empleadores_coeficientes eco on (eco.coeficiente_id = co.id
											and eco.empleador_id = '" . $relacion['Relacion']['empleador_id'] . "')
					where				1=1
					and 				(rc.desde = '0000-00-00' or rc.desde <= '" . $opciones['desde'] . "')
					and 				(rc.hasta = '0000-00-00' or rc.hasta >= '" . $opciones['hasta'] . "')
					and					rc.relacion_id = '" . $relacion['Relacion']['id'] . "'
					" . $condicionAdicional . "
					order by			case c.tipo
											when 'Remunerativo' then 0
											when 'No Remunerativo' then 1
											when 'Deduccion' then 2
										end
				";
		}
		elseif($tipo == "Empleador") {
			$sql = "
					select				ec.id,
										ec.empleador_id,
										ec.concepto_id,
										ec.desde,
										ec.hasta,
										ec.formula,
										cc.id,
										cc.convenio_id,
										cc.concepto_id,
										cc.desde,
										cc.hasta,
										cc.formula,
										c.id,
										c.codigo,
										c.nombre,
										c.nombre_formula,
										c.tipo,
										c.periodo,
										c.sac,
										c.imprimir,
										c.antiguedad,
										c.remuneracion,
										c.cantidad,
										c.formula,
										c.desde,
										c.hasta,
										c.pago,
										c.orden,
										co.id as coeficiente_id,
										co.nombre as coeficiente_nombre,
										co.tipo as coeficiente_tipo,
										co.valor as coeficiente_valor,
										eco.valor as coeficiente_valor
					from				empleadores_conceptos ec
										left join convenios_conceptos cc on (ec.concepto_id = cc.concepto_id
											and cc.convenio_id = '" . $relacion['ConveniosCategoria']['convenio_id'] . "'
											and (cc.desde = '0000-00-00' or cc.desde <= '" . $opciones['desde'] . "')
											and (cc.hasta = '0000-00-00' or cc.hasta >= '" . $opciones['hasta'] . "'))
										left join conceptos c on (ec.concepto_id = c.id)
										left join coeficientes co on (c.coeficiente_id = co.id)
										left join empleadores_coeficientes eco on (eco.coeficiente_id = co.id
											and eco.empleador_id = '" . $relacion['Relacion']['empleador_id'] . "')
					where				1=1
					and					ec.empleador_id = '" . $relacion['Relacion']['empleador_id'] . "'
					and 				(ec.desde = '0000-00-00' or ec.desde <= '" . $opciones['desde'] . "')
					and 				(ec.hasta = '0000-00-00' or ec.hasta >= '" . $opciones['hasta'] . "')
					" . $condicionAdicional . "
					order by			case c.tipo
											when 'Remunerativo' then 0
											when 'No Remunerativo' then 1
											when 'Deduccion' then 2
										end
				";
		}	
		elseif($tipo == "ConvenioColectivo") {
			$sql = "
					select				cc.id,
										cc.convenio_id,
										cc.concepto_id,
										cc.desde,
										cc.hasta,
										cc.formula,
										c.id,
										c.codigo,
										c.nombre,
										c.nombre_formula,
										c.tipo,
										c.periodo,
										c.sac,
										c.imprimir,
										c.antiguedad,
										c.remuneracion,
										c.cantidad,
										c.formula,
										c.desde,
										c.hasta,
										c.pago,
										c.orden,
										co.id as coeficiente_id,
										co.nombre as coeficiente_nombre,
										co.tipo as coeficiente_tipo,
										co.valor as coeficiente_valor,
										eco.valor as coeficiente_valor
					from				convenios_conceptos cc
										left join conceptos c on (cc.concepto_id = c.id)
										left join coeficientes co on (c.coeficiente_id = co.id)
										left join empleadores_coeficientes eco on (eco.coeficiente_id = co.id
											and eco.empleador_id = '" . $relacion['Relacion']['empleador_id'] . "')
					where				1=1
					and					cc.convenio_id = '" . $relacion['ConveniosCategoria']['convenio_id'] . "'
					and 				(cc.desde = '0000-00-00' or cc.desde <= '" . $opciones['desde'] . "')
					and 				(cc.hasta = '0000-00-00' or cc.hasta >= '" . $opciones['hasta'] . "')
					" . $condicionAdicional . "
					order by			case c.tipo
											when 'Remunerativo' then 0
											when 'No Remunerativo' then 1
											when 'Deduccion' then 2
										end
				";
		}	
		elseif($tipo == "ConceptoPuntual") {
			$sql = "
					select				ec.id,
										ec.empleador_id,
										ec.concepto_id,
										ec.desde,
										ec.hasta,
										ec.formula,
										cc.id,
										cc.convenio_id,
										cc.concepto_id,
										cc.desde,
										cc.hasta,
										cc.formula,
										c.id,
										c.codigo,
										c.nombre,
										c.nombre_formula,
										c.tipo,
										c.periodo,
										c.sac,
										c.imprimir,
										c.antiguedad,
										c.remuneracion,
										c.cantidad,
										c.formula,
										c.desde,
										c.hasta,
										c.pago,
										c.orden,
										co.id as coeficiente_id,
										co.nombre as coeficiente_nombre,
										co.tipo as coeficiente_tipo,
										co.valor as coeficiente_valor,
										eco.valor as coeficiente_valor
					from				conceptos c
										left join convenios_conceptos cc on (c.id = cc.concepto_id
											and cc.convenio_id = '" . $relacion['ConveniosCategoria']['convenio_id'] . "')
										left join empleadores_conceptos ec on (c.id = ec.concepto_id
											and ec.empleador_id = '" . $relacion['Relacion']['empleador_id'] . "')
										left join coeficientes co on (c.coeficiente_id = co.id)
										left join empleadores_coeficientes eco on (eco.coeficiente_id = co.id
											and eco.empleador_id = '" . $relacion['Relacion']['empleador_id'] . "')
					where				1=1
					and 				(c.desde = '0000-00-00' or c.desde <= '" . $opciones['desde'] . "')
					and 				(c.hasta = '0000-00-00' or c.hasta >= '" . $opciones['hasta'] . "')
					and					c.codigo = '" . $codigoConcepto . "'
				";
		}
		elseif($tipo == "Todos") {
			$sql = "
					select				c.id,
										c.codigo,
										c.nombre,
										c.nombre_formula,
										c.tipo,
										c.periodo,
										c.sac,
										c.imprimir,
										c.antiguedad,
										c.remuneracion,
										c.cantidad,
										c.formula,
										c.desde,
										c.hasta,
										c.pago,
										c.orden
					from				conceptos c
					where				1=1
					and 				(c.desde = '0000-00-00' or c.desde <= '" . $opciones['desde'] . "')
					and 				(c.hasta = '0000-00-00' or c.hasta >= '" . $opciones['hasta'] . "')
					" . $condicionAdicional . "
					order by			c.nombre, c.codigo
				";
		}
		//debug($sql);
		$r = $this->query($sql);
		$conceptos = array();
		foreach($r as $v) {
			/**
			* En principio tomo el concepto como verdad, luego puede estar sobreescrito.
			* La jerarquia es: 	Relacion,
								Empleador,
								Convenio Colectivo,
								Concepto.
			*/

			/**
			* Descarto por las fechas de vigencias.
			*/
			
			/**
			* De la relacion.
			*/
			if(!empty($v['rc']['desde']) && $v['rc']['desde'] != "0000-00-00" && $v['rc']['desde'] > $opciones['desde']) {
				continue;
			}
			if(!empty($v['rc']['hasta']) && $v['rc']['hasta'] != "0000-00-00" && $v['rc']['hasta'] < $opciones['hasta']) {
				continue;
			}
			
			/**
			* Del empleador.
			*/
			if(!empty($v['ec']['desde']) && $v['ec']['desde'] != "0000-00-00" && $v['ec']['desde'] > $opciones['desde']) {
				continue;
			}
			if(!empty($v['ec']['hasta']) && $v['ec']['hasta'] != "0000-00-00" && $v['ec']['hasta'] < $opciones['hasta']) {
				continue;
			}

			/**
			* Del convenio.
			*/
			if(!empty($v['cc']['desde']) && $v['cc']['desde'] != "0000-00-00" && $v['cc']['desde'] > $opciones['desde']) {
				continue;
			}
			if(!empty($v['cc']['hasta']) && $v['cc']['hasta'] != "0000-00-00" && $v['cc']['hasta'] < $opciones['hasta']) {
				continue;
			}
			
			/**
			* Del concepto.
			*/
			if(!empty($v['c']['desde']) && $v['c']['desde'] != "0000-00-00" && $v['c']['desde'] > $opciones['desde']) {
				continue;
			}
			if(!empty($v['c']['hasta']) && $v['c']['hasta'] != "0000-00-00" && $v['c']['hasta'] < $opciones['hasta']) {
				continue;
			}


			/**
			* Asigo como valido el concepto y su coeficiente,
			* luego, en base a la jerarquia, sobreescribo la formula si coresponde.
			*/
			if(isset($v['co'])) {
				$conceptos[$v['c']['codigo']] = am($v['c'], $v['co']);
			}
			else {
				$conceptos[$v['c']['codigo']] = $v['c'];
			}
			$conceptos[$v['c']['codigo']]['concepto_id'] = $v['c']['id'];

			/**
			* Verifico que el valor del coeficiente no haya sido sobreescrito por el empleador.
			*/
			if(!empty($v['eco']['coeficiente_valor'])) {
				$conceptos[$v['c']['codigo']]['coeficiente_valor'] = $v['eco']['coeficiente_valor'];
			}

			/**
			* Sobreescribo Formulas.
			*/
			if(!empty($v['rc']['formula'])) {
				$conceptos[$v['c']['codigo']]['formula'] = $v['rc']['formula'];
			}
			elseif(!empty($v['ec']['formula'])) {
				$conceptos[$v['c']['codigo']]['formula'] = $v['ec']['formula'];
			}
			elseif(!empty($v['cc']['formula'])) {
				$conceptos[$v['c']['codigo']]['formula'] = $v['cc']['formula'];
			}
			
			/**
			* Sobreescribo ids.
			*/
			if(!empty($v['rc']['id'])) {
				$conceptos[$v['c']['codigo']]['id'] = $v['rc']['id'];
			}
			elseif(!empty($v['ec']['formula'])) {
				$conceptos[$v['c']['codigo']]['id'] = $v['ec']['id'];
			}
			elseif(!empty($v['cc']['formula'])) {
				$conceptos[$v['c']['codigo']]['id'] = $v['cc']['id'];
			}
		}
		
		return $conceptos;
	}
	


/**
 * Agrega o Quita un concepto o varios conceptos a una o varias relaciones.
 */
	function agregarQuitarConcepto($relaciones = array(), $conceptos = array(), $opciones) {
		$c = 0;
		$relacionOk = array();
		if(isset($opciones['accion']) && ($opciones['accion'] == "quitar" || $opciones['accion'] == "agregar")) {
			$accion = $opciones['accion'];
			$error = false;
			$this->begin();
			foreach($relaciones as $relacion_id) {
				foreach($conceptos as $concepto_id) {
					$save['relacion_id'] = $relacion_id;
					$save['concepto_id'] = $concepto_id;
					$this->RelacionesConcepto->recursive = -1;
					$existe = $this->RelacionesConcepto->find($save);
					if(empty($existe) && $accion == "agregar") {
						$this->RelacionesConcepto->create();
						if($this->RelacionesConcepto->save($save)) {
							$relacionOk[$relacion_id] = 1;
						}
						else {
							$error = true;
						}
					}
					elseif(!empty($existe) && $accion == "quitar") {
						if($this->RelacionesConcepto->del($existe['RelacionesConcepto']['id'])) {
							$relacionOk[$relacion_id] = 1;
						}
						else {
							$error = true;
						}
					}
				}
			}
			if($error) {
				$this->rollback();
				$c = 0;
			}
			else {
				$this->commit();
				$c = array_sum($relacionOk);
			}
		}
		return $c;
	}
}
?>