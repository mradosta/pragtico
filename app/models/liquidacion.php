<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las liquidaciones.
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
 * La clase encapsula la logica de acceso a datos asociada a las liquidaciones.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Liquidacion extends AppModel {

	/**
	* Seteo los tipos posibles de liquidaciones que podre realizar.
	*/
	var $opciones = array('tipo' => array(
						  		'normal'			=> 'Normal',
			   					'descuentos'		=> 'Descuentos',
			   					'sac'				=> 'Sac',
		   						'vacaciones'		=> 'Vacaciones',
		   						'liquidacion_final'	=> 'Liquidacion Final',
		   						'especial'			=> 'Especial'));
	
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
                              

/**
 * I must overwrite default cakePHP deleteAll method because it's not performant when there're many 
 * relations and many records.
 * I also add transaccional behavior and a better error check.
 * TODO:
 * 		when the relation has a dependant relation, this method will not delete that relation.
 */	
	function deleteAll($conditions, $cascade = true, $callbacks = false) {
		$ids = Set::extract(
			$this->find('all', array_merge(array(
							'fields' 	=> $this->alias . '.' . $this->primaryKey,
							'recursive' => 0), compact('conditions'))),
			'{n}.' . $this->alias . '.' . $this->primaryKey
		);
		
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$c = 0;
		$db->begin($this);
		foreach ($this->hasMany as $assoc => $data) {
			$table = $db->name(Inflector::tableize($assoc));
			$conditions = array($data['foreignKey'] => $ids);
			$sql = sprintf('DELETE FROM %s %s', $table, $db->conditions($conditions));
			$this->query($sql);

			if (empty($this->dbError)) {
				$c++;
			}
		}
		
		if (count($this->hasMany) === $c) {
			$sql = sprintf('DELETE FROM %s %s', $db->name($this->useTable), $db->conditions(array($this->primaryKey => $ids)));
			$this->query($sql);
			//$this->__buscarError();
			if (empty($this->dbError)) {
				$db->commit($this);
				return true;
			}
			else {
				$db->rollback($this);
				return false;
			}
		}
		else {
			$db->rollback($this);
			return false;
		}
	}
	

/**
 * Generates a receipt.
 *
 * @param string $type. The type of recipt you want to generate.
 *      - normal
 *      - sac
 *      - bla
 *      - bla
 * @param array $options.
 *      - period: 1=first_half, 2=second_half.
 *      - year: The year where to calcula SAC.
 *      - january to december: Sum of remuneratives total by month.
 * @return array. A receipt ready to be saved.
 * @access public
 */
    function getReceipt($relationships = null, $type = 'normal', $options = array()) {

        
        $relationshipsIds = Set::extract('/Relacion/id', $relationships);
		if ($type === 'normal') {
			
		}
        elseif ($type === 'sac') {

            $defaults['january'] = 0;
            $defaults['february'] = 0;
            $defaults['march'] = 0;
            $defaults['april'] = 0;
            $defaults['may'] = 0;
            $defaults['june'] = 0;
            $defaults['july'] = 0;
            $defaults['august'] = 0;
            $defaults['september'] = 0;
            $defaults['october'] = 0;
            $defaults['november'] = 0;
            $defaults['december'] = 0;
            $options = array_merge($defaults, $options);
            
            $condtions['Liquidacion.relacion_id'] = $relationshipsIds;
            $condtions['Liquidacion.ano'] = $options['year'];
            if ($options['period'] == '1') {
                $condtions['mes'] = array('AND' => array(
                        'Liquidacion.mes >=' => 1,
                        'Liquidacion.mes <=' => 6));
            } elseif ($options['period'] == '2') {
                $condtions['mes'] = array('AND' => array(
                        'Liquidacion.mes >=' => 6,
                        'Liquidacion.mes <=' => 12));
            } else {
                return array('error' => sprintf('Wrong period (%s). Only "1" for the first_half or "2" for the second_half allowed for type %s.', $options['period'], $type));
            }

            foreach ($relationships as $relationship) {
                $options['relation'] = array_pop(Set::combine(array($relationship), '{n}.Relacion.id', array('{2}, {1} ({0})', '{n}.Empleador.nombre', '{n}.Trabajador.nombre', '{n}.Trabajador.apellido')));
                $options['start'] = strtotime($relationship['Relacion']['ingreso'] . ' 00:00:00 UTC');
                $options['end'] = strtotime($relationship['Relacion']['egreso'] . ' 00:00:00 UTC');
            }

            /** Use PHPExcel to get complex calculations done */
            set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes');
            App::import('Vendor', 'IOFactory', true, array(APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel'), 'IOFactory.php');


            /**
             * Use this code to serialize an excel2007 file.
             */
            //$objPHPExcelReader = PHPExcel_IOFactory::createReader('Excel2007');
            //$objPHPExcel = $objPHPExcelReader->load(WWW_ROOT . 'files' . DS . 'base' . DS . 'sac.xlsx');
            //$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Serialized');
            //$objPHPExcelWriter->save('/tmp/sac-serialized-v1.6.5');

            //$objPHPExcelReader = PHPExcel_IOFactory::createReader('Serialized');
            //$objPHPExcel = $objPHPExcelReader->load(WWW_ROOT . 'files' . DS . 'base' . DS . 'sac-serialized-v1.6.5');
            $objPHPExcelReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objPHPExcelReader->load(WWW_ROOT . 'files' . DS . 'base' . DS . 'sac.xlsx');
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcelSheet = $objPHPExcel->getActiveSheet();

            foreach ($options as $cellName => $data) {
                $objPHPExcelSheet->setCellValue(ucfirst($cellName), $data);
            }

            //$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            //$objPHPExcelWriter->save('/tmp/sac-generated.xlsx');
            return sprintf('%01.2f', $objPHPExcelSheet->getCell('TOTAL_PRAGTICO')->getCalculatedValue());
            
            //$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            //$objPHPExcelWriter->save('/tmp/sac-generated.xls');
            $objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objPHPExcelWriter->save('/tmp/sac-generated2.xlsx');
            
            $fields = array('Liquidacion.relacion_id', 'SUM(remunerativo) AS total_remunerativo');
            $groupBy = array('Liquidacion.relacion_id', 'Liquidacion.mes');

            $r = $this->find('all', array(
                    'recursive' => -1,
                    'fields'    => $fields,
                    'condtions' => $condtions,
                    'group'     => $groupBy));
            //d($r);


            
        }
    }
    

function afterFind($results, $primary = false) {
    //array_walk($results, create_function(’&$v’, ‘$v['Photo']['rownum'] = $v[0]['rownum']; unset($v[0]);’));
    if ($primary == true) {
        if (Set::check($results, '0.0')) {
            $fieldName = key($results[0][0]);
            foreach ($results as $key=>$value) {
                $results[$key][$this->alias][$fieldName] = $value[0][$fieldName];
                unset($results[$key][0]);
            }
        }
    }
    return $results;
}

	function addEditDetalle($opciones) {
		/**
		* Se refiere a los conceptos que deben tratarse de forma especial, ya que modifican data en table, u otra cosa.
		*/
		$this->recursive = -1;
		$liquidacion = $this->findById($opciones['liquidacionId']);
		$conceptosHora = array('horas_extra_50', 'horas_extra_100');
		if (in_array($opciones['conceptoCodigo'], $conceptosHora)) {
			$this->LiquidacionesDetalle->Concepto->recursive = -1;
			$concepto = $this->LiquidacionesDetalle->Concepto->findByCodigo($opciones['conceptoCodigo']);

			$save['relacion_id'] = $liquidacion['Liquidacion']['relacion_id'];
			$save['liquidacion_id'] = $liquidacion['Liquidacion']['id'];
			$save['tipo'] = str_replace('Horas ', '', preg_replace('/0$/', '0 %', Inflector::humanize($opciones['conceptoCodigo'])));
			$save['periodo'] = $liquidacion['Liquidacion']['ano'] . str_pad('0', 2, $liquidacion['Liquidacion']['mes'], STR_PAD_RIGHT) . $liquidacion['Liquidacion']['periodo'];
			$save['estado'] = 'Pendiente';
			$save['cantidad'] = $opciones['valor'];
			$save['observacion'] = 'Ingresado desde la modificacion de una Liquidacion';
			$horaModel = new Hora();
			//$horaModel->begin();
			$horaModel->save(array('Hora'=>$save));
			
			//$horaModel->rollBack();
		}
	}



/**
* Dado un concepto, resuelve la formula.
*/
	function __getConceptValue($concepto, $opciones = array()) {
		$valor = null;
		$errores = array();
		$formula = $concepto['formula'];
		
		
		/**
		* Si en la formula hay variables, busco primero estos valores.
		*/
		if (preg_match_all('/(#[a-z0-9_]+)/', $formula, $variablesTmp)) {

			foreach ($variablesTmp[1] as $k=>$v) {
				/**
				* Debe buscar la variable para reemplazarla dentro de la formula.
				* Usa la RegEx y no str_replace, porque por ejemplo, si debo reemplzar #horas, y en cuentra
				* #horas lo hara ok, pero si encuentra #horas_enfermedad, dejara REEMPLAZO_enfermedad.
				*/
				$formula = preg_replace("/".$v."(\W)|".$v."$/", $this->getVarValue($v) . "$1", $formula);
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
					$nombreConcepto = preg_replace("/".$v."(\W)|".$v."$/", $this->getVarValue($v) . "$1", $nombreConcepto);
				}
			}
			
			if (substr($nombreConcepto, 0, 3) === '=if') {
				$nombreConcepto = $this->resolver($nombreConcepto);
			} else {
				$nombreConcepto = substr($nombreConcepto, 1);
			}
		} else {
			$nombreConcepto = $concepto['nombre'];
		}

		/**
		* Veo si es una formula, hay un not, obtengo los conceptos y rearmo los formula eliminando la perte del not.
		*/
		if (preg_match('/not\((.*)\)/', $formula, $matches)) {
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
						$resolucionCalculo = $this->__getConceptValue($conceptoTmp, $opciones);
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
						$conceptoParaCalculoTmp = $this->Relacion->RelacionesConcepto->Concepto->findConceptos('ConceptoPuntual', array_merge(array('relacion' => $this->getRelationship(), 'codigoConcepto' => $match), $opciones));
						if (empty($conceptoParaCalculoTmp)) {
							$this->__setError(array(	"tipo"					=> "Concepto Inexistente",
														"gravedad"				=> "Alta",
														"concepto"				=> $match,
														"variable"				=> "",
														"formula"				=> $formula,
														"descripcion"			=> "La formula requiere de un concepto inexistente.",
														"recomendacion"			=> "Verifique la formula y que todos los conceptos que esta utiliza existan.",
														"descripcion_adicional"	=> "verifique: " . $concepto['codigo']));
						} else {
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
							$resolucionCalculo = $this->__getConceptValue($this->__conceptos[$match], $opciones);
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
					} else {
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
			$valor = $this->resolver($formula);
		} elseif (empty($formula)) {
			$this->__setError(array(	"tipo"					=> "Formula de Concepto Inexistente",
										"gravedad"				=> "Media",
										"concepto"				=> $concepto['codigo'],
										"variable"				=> "",
										"formula"				=> "",
										"descripcion"			=> "El concepto no tiene definida una formula.",
										"recomendacion"			=> "Ingrese la formula correspondiente al concepto en caso de que sea necesario. Para evitar este error ingrese como formula: =0",
										"descripcion_adicional"	=> "Se asume como 0 (cero) el valor del concepto."));
			$valor = 0;
		} else {
			$valor = "#N/A";
		}
		
		return array("valor"=>$valor, "debug"=>$formula, "valor_cantidad"=>$conceptoCantidad, "nombre"=>$nombreConcepto, "errores"=>$errores);
	}
	
	
/**
 * Busca el valor de una variable.
 *
 * @param string $variable El nombre de la variable que busco.
 * @return mixed El valor de la variable.
 * @access private.
 */
    function getVarValue($variable) {
        
        if (!isset($this->__variables[$variable])) {
            $this->__setError(array(    'tipo'                  => 'Variable Inexistente',
                                        'gravedad'              => 'Media',
                                        'concepto'              => '',
                                        'variable'              => $variable,
                                        'formula'               => '',
                                        'descripcion'           => 'La formula intenta usar una variable inexistente. Se resolvera a 0 (cero) su valor.',
                                        'recomendacion'         => 'Verifique que la formula este correctamente definida y que las variables que esta formula utiliza existan en el sistema.',
                                        'descripcion_adicional' => ''));

            $this->setVar($variable, 0);
            return 0;
        }
        /**
        * Ya he resuelto esta variable anteriormente.
        */
        elseif (isset($this->__variables[$variable]['valor'])) {
            return $this->__variables[$variable]['valor'];
        }
        /**
        * Intento resolverla.
        */
        else {
            /**
            * Si es una formula, la resuelvo.
            */
            if (substr($this->__variables[$variable]['formula'], 0, 1) === '=') {
                $formula = $this->__variables[$variable]['formula'];
                /**
                * Si en la formula hay variables, busco primero estos valores.
                */
                if (preg_match_all('/(#[a-z0-9_]+)/', $formula, $variables_tmp)) {
                    foreach ($variables_tmp[1] as $v) {
                        $formula = preg_replace('/(' . $v . ')([\)|\s|\*|\+\/\-|\=|\,]*[^_])/', $this->getVarValue($v) . '$2', $formula);
                    }
                }

                $valor = $this->resolver($formula);
                
                if ($valor === '#N/A') {
                    $valor = 0;
                    $this->__setError(array(    'tipo'                  => 'Variable No Resuelta',
                                                'gravedad'              => 'Alta',
                                                'concepto'              => '',
                                                'variable'              => $variable,
                                                'formula'               => $this->__variables[$variable]['formula'],
                                                'descripcion'           => 'La formula intenta usar una variable que no es posible resolverla con los datos de la relacion.',
                                                'recomendacion'         => 'Verifique que la relacion tenga cargados todos los datos necesarios.',
                                                'descripcion_adicional' => ''));
                }
                $this->setVar($variable, $valor);
                return $valor;
            }
            
            
            /**
            * Busco si es una variable que viene dada por la relacion.
            * Depende de recursive, puede venir $data[model1][model2][campo] 0 $data[model1][campo]
            */
            if (preg_match('/^\[([a-zA-Z]*)\]\[([a-zA-Z]*)\]\[([a-zA-Z_]*)\]$/', $this->__variables[$variable]['formula'], $matchesA) || preg_match('/^\[([a-zA-Z]*)\]\[([a-zA-Z_]*)\]$/', $this->__variables[$variable]['formula'], $matchesB)) {
                $relationship = $this->getRelationship();
                if (isset($matchesA[1]) && isset($matchesA[2]) && isset($matchesA[3]) && isset($relationship[$matchesA[1]][$matchesA[2]][$matchesA[3]])) {
                    $valor = $relationship[$matchesA[1]][$matchesA[2]][$matchesA[3]];
                } elseif (isset($matchesB[1]) && isset($matchesB[2]) && isset($relationship[$matchesB[1]][$matchesB[2]])) {
                    $valor = $relationship[$matchesB[1]][$matchesB[2]];
                } else {
                    $this->__setError(array(    'tipo'                  => 'Variable No Resuelta',
                                                'gravedad'              => 'Alta',
                                                'concepto'              => '',
                                                'variable'              => $variable,
                                                'formula'               => $this->__variables[$variable]['formula'],
                                                'descripcion'           => 'La formula intenta usar una variable que no es posible resolverla con los datos de la relacion.',
                                                'recomendacion'         => 'Verifique que la relacion tenga cargados todos los datos necesarios.',
                                                'descripcion_adicional' => ''));
                    
                    $valor = 0;
                }
                
                switch($this->__variables[$variable]['formato']) {
                    case 'Minuscula':
                        $valor = strtolower($valor);
                    break;
                    case 'Mayuscula':
                        $valor = strtoupper($valor);
                    break;
                }
                $this->setVar($variable, $valor);
                return $valor;
            }
            
            
            /**
             * System vars. HardCoded
             */
            switch ($variable) {
                case '#mes_liquidacion':
                    $this->setVar($variable, $this->getPeriod('mes'));
                break;
                case '#ano_liquidacion':
                    $this->setVar($variable, $this->getPeriod('ano'));
                break;
                case '#periodo_liquidacion':
                    $this->setVar($variable, $this->getPeriod('periodo'));
                break;
                case '#periodo_liquidacion_completo':
                    $this->setVar($variable, $this->getPeriod('periodoCompleto'));
                break;
                case '#fecha_desde_liquidacion':
                    $this->setVar($variable, $this->getPeriod('desde'));
                break;
                case '#fecha_hasta_liquidacion':
                    $this->setVar($variable, $this->getPeriod('hasta'));
                break;
                case '#fecha_actual':
                    $this->setVar($variable, date('Y-m-d'));
                break;
                case '#dia_ingreso':
                    $this->setVar($variable, $this->format($this->getVarValue('#fecha_ingreso'), 'dia'));
                break;
                case '#dia_egreso':
                    $this->setVar($variable, $this->format($this->getVarValue('#fecha_egreso'), 'dia'));
                break;
                case '#mes_ingreso':
                    $this->setVar($variable, $this->format($this->getVarValue('#fecha_ingreso'), 'mes'));
                break;
                case '#mes_egreso':
                    $this->setVar($variable, $this->format($this->getVarValue('#fecha_egreso'), 'mes'));
                break;
                case '#ano_ingreso':
                    $this->setVar($variable, $this->format($this->getVarValue('#fecha_ingreso'), 'ano'));
                break;
                case '#ano_egreso':
                    $this->setVar($variable, $this->format($this->getVarValue('#fecha_egreso'), 'ano'));
                break;
                case '#dia_desde_liquidacion':
                    $this->setVar($variable, $this->getPeriod('desde'));
                break;
                case '#dia_hasta_liquidacion':
                    $this->setVar($variable, $this->getPeriod('hasta'));
                break;
                case '#dias_antiguedad':
                case '#meses_antiguedad':
                case '#anos_antiguedad':
                    $fechaEgreso = $this->getVarValue('#fecha_egreso');
                    if ($fechaEgreso !== '0000-00-00' && $fechaEgreso < $this->getVarValue('#fecha_hasta_liquidacion')) {
                        $antiguedad = $this->dateDiff($this->getVarValue('#fecha_ingreso'), $fechaEgreso);
                    } else {
                        $antiguedad = $this->dateDiff($this->getVarValue('#fecha_ingreso'), $this->getVarValue('#fecha_hasta_liquidacion'));
                    }
                    if ($variable === '#dias_antiguedad') {
                        $this->setVar($variable, $antiguedad['dias']);
                    } elseif ($variable === '#meses_antiguedad') {
                        $this->setVar($variable, floor($antiguedad['dias'] / 30));
                    } elseif ($variable === '#anos_antiguedad') {
                        $this->setVar($variable, floor($antiguedad['dias'] / 365));
                    }
                break;
                case '#dias_corridos_periodo':
                    if ($this->getVarValue('#dia_ingreso') > 1
                        && $this->getVarValue('#mes_ingreso') === $this->getVarValue('#mes_liquidacion')
                        && $this->getVarValue('#ano_ingreso') === $this->getVarValue('#ano_liquidacion')) {
                        $desde = $this->getVarValue('#fecha_ingreso');
                    } else {
                        $desde = $this->getVarValue('#fecha_desde_liquidacion');
                    }
    
                    if ($this->getVarValue('#dia_egreso') < $this->getVarValue('#dia_hasta_liquidacion')
                        && $this->getVarValue('#mes_egreso') === $this->getVarValue('#mes_liquidacion')
                        && $this->getVarValue('#ano_egreso') === $this->getVarValue('#ano_liquidacion')) {
                        $hasta = $this->getVarValue('#fecha_egreso');
                    } else {
                        $hasta = $this->getVarValue('#fecha_hasta_liquidacion');
                    }
                    $antiguedad = $this->dateDiff($desde, $hasta);
                    $this->setVar($variable, $antiguedad['dias']);
                break;
                case '#dias_vacaciones':
					/**
					 * $formula = "=IF(AND(MONTH(date('2008-07-07'))>6,YEAR(date('2008-07-07'))=YEAR(date('2008-12-31');DAY(A2)>1)),INT(NETWORKDAYS(date('2008-07-07'),date('2008-12-31'))/20),IF(AND(MONTH(date('2008-07-07'))<6,YEAR(date('2008-07-07'))=YEAR(date('2008-12-31'))),14,IF((YEAR(date('2008-12-31'))-YEAR(date('2008-07-07')))<=5,14,IF((YEAR(date('2008-12-31'))-YEAR(date('2008-07-07')))<=10,21,IF((YEAR(date('2008-12-31'))-YEAR(date('2008-07-07')))<=15,28,35)))))";
					 * Assumptions:
					 * 6 month => 182 days (average between first and second half).
					 * 5 years => 1826 days (add 1 day because of lead year).
					 */
					$ingreso = $this->getVarValue('#fecha_ingreso');
					$endYear = (int)$this->getPeriod('ano');
					$startYear = (int)$this->format($ingreso, 'ano');
                    $december31 = $this->format(array('ano' => $endYear, 'mes' => 12, 'dia' => 31), array('format' => 'Y-m-d', 'type' => 'date'));
					$ingreso = $this->getVarValue('#fecha_ingreso');
                    $antiguedad = $this->dateDiff($ingreso, $december31);
					//debug($endYear);d($startYear);
                    if ($antiguedad['dias'] <= 182 || (12 - (int)$this->format($ingreso, 'mes') < 6 && $endYear === $startYear)) {
						$this->setVar($variable, floor($antiguedad['dias'] / 30));
					//} elseif ($antiguedad['dias'] > 182 && $antiguedad['dias'] <= 1826) {
					} elseif ($endYear - $startYear <= 5) {
						$this->setVar($variable, 14);
					//} elseif ($antiguedad['dias'] > 1826 && $antiguedad['dias'] <= (1826 * 2)) {
					} elseif ($endYear - $startYear <= 10) {
						$this->setVar($variable, 21);
					//} elseif ($antiguedad['dias'] > (1826 * 2) && $antiguedad['dias'] <= (1826 * 4)) {
					} elseif ($endYear - $startYear <= 20) {
						$this->setVar($variable, 28);
					} else {
						$this->setVar($variable, 35);
					}
                    break;
                case '#horas':
                case '#horas_ajuste':
                case '#horas_ajuste_extra_100':
                case '#horas_ajuste_extra_50':
                case '#horas_ajuste_extra_nocturna_100':
                case '#horas_ajuste_extra_nocturna_50':
                case '#horas_ajuste_nocturna':
                case '#horas_extra_100':
                case '#horas_extra_50':
                case '#horas_extra_nocturna_100':
                case '#horas_extra_nocturna_50':
                case '#horas_nocturna':
                    /**
                    * Busco las horas trabajadas en el periodo y las cargo al array variables.
                    */
                    $horas = $this->Relacion->Hora->getHoras($this->getRelationship(), $this->getPeriod());
                    foreach ($horas['variables'] as $horaTipo=>$horaValor) {
                        $this->setVar($horaTipo, $horaValor);
                    }
                    $this->__setAuxiliar($horas['auxiliar']);
                    $this->__setConcepto($horas['conceptos'], 'SinCalcular');
                break;
                case '#ausencias_justificadas':
                case '#ausencias_injustificadas':
                    $ausencias = $this->Relacion->Ausencia->getAusencias($this->getRelationship(), $this->getPeriod());
                    $this->setVar('#ausencias_justificadas', $ausencias['Justificada']);
                    $this->setVar('#ausencias_injustificadas', $ausencias['Injustificada']);
                break;
            }
            /*
                    $this->__setError(array(    'tipo'                  => 'Variable No Resuelta',
                                                'gravedad'              => 'Alta',
                                                'concepto'              => '',
                                                'variable'              => $variable,
                                                'formula'               => $this->__variables[$variable]['formula'],
                                                'descripcion'           => 'La formula intenta usar una variable que no es posible resolverla con los datos de la relacion.',
                                                'recomendacion'         => 'Verifique que la relacion tenga cargados todos los datos necesarios.',
                                                'descripcion_adicional' => ''));
            */      
            return $this->getVarValue($variable);
        }
    }
    

    function __setError($error) {
        $this->__saveError[] = $error;
    }
    
    function __getError() {
        return $this->__saveError;
    }

    function setPeriod($period) {
        /** Guess is setting just the year */
        if (is_numeric($period) && strlen($period) === 4) {
            $this->__period['ano'] = $period;
        } else {
            $this->__period = $this->format($period, 'periodo');
        }
    }
    
    function getPeriod($option = '') {
        if (isset($this->__period[$option])) {
            return $this->__period[$option];
        } else {
            return $this->__period;
        }
    }

    function setRelationship($relationship) {
        $this->__relationship = $relationship;
    }

    function getRelationship($model = null) {
		if (!empty($model)) {
        	return $this->__relationship[$model];
		} else {
			return $this->__relationship;
		}
    }
    
    
    function setVar($var, $value = null) {
        if (is_string($var) && !is_null($value)) {
            $this->__variables[$var]['valor'] = $value;
        } else {
            foreach ($var as $varName => $varDefinition) {
                if (!is_array($varDefinition)) {
                    $tmp = $varDefinition;
                    $varDefinition = null;
                    $varDefinition['valor'] = $tmp;
                }
                $this->__variables[$varName] = $varDefinition;
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
 * Agrega conceptos al array de conceptos de su tipo.
 *
 * @param array $concepto Conceptos.
 * @param string $concepto El tipo de conceptos que se desea agregar.
 * @return void.
 * @access private.
 */
    function __setConcepto($conceptos, $tipo = 'SinCalcular') {
        if ($tipo === 'SinCalcular') {
            if (empty($this->__conceptos)) {
                $this->__conceptos = $conceptos;
            }
            else {
                $this->__conceptos = array_merge($this->__conceptos, $conceptos);
            }
        }
    }
    
}
?>