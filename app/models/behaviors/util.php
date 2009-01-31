<?php
/**
 * Behavior que contiene utilidades varias para ser usadas en los models.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.models.behaviors
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 *Intento concentrar en esta clase todo los comportamientos reusables a nivel de models.
 *
 *
 * @package     pragtico
 * @subpackage  app.models.behaviors
 */
//class UtilBehavior extends ModelBehavior {
App::import('Behavior', 'Containable');
class UtilBehavior extends ContainableBehavior {	
	
	function xafterFind(&$model, $results, $primary = false) {
		
		if (!isset($results[0][0]) && !empty($this->backUpRelations)) {
			foreach ($results as $k => $result) {
				//d($this->backUpRelations);
				foreach ($this->backUpRelations as $firstLevelKey => $firstLevel) {
					foreach ($firstLevel as $secondLevel) {
						$results[$k][$firstLevelKey][$secondLevel] = $result[$secondLevel];
						unset($results[$k][$secondLevel]);
					}
				}
			}
			return $results;
		} else {
			return parent::afterFind($model, $results, $primary);
		}
	}

	
    function beforeFind_deprecated(&$model, $query) {
		
        if (!empty($query['order'][0])) {
        	$schema = $model->schema();
        	if (!is_array($query['order'][0])) {
        		$query['order'][0] = array($query['order'][0]);
        	}
        	elseif (isset($query['order'][0][0]) && strpos($query['order'][0][0], ",")) {
        		foreach (explode(",", $query['order'][0][0]) as $v) {
        			if (stripos($v, "asc")) {
        				$query['order'][0][trim(str_replace("asc", "", $v))] = "asc";
        			}
        			elseif (stripos($v, "desc")) {
        				$query['order'][0][trim(str_replace("desc", "", $v))] = "desc";
        			}
        			else {
        				$query['order'][0][trim($v)] = "asc";
        			}
        		}
        		unset($query['order'][0][0]);
        	}
        	
        	foreach ($query['order'][0] as $field=>$direccion) {
        		if (is_numeric($field)) {
        			$field = $direccion;
        			$modelName = $model->name;
        			$direccion = "asc";
        		}
        		if (strpos($field, '.')) {
        			$tmp = explode(".", $field);
        			$field = $tmp[1];
        			$modelName = $tmp[0];
        			if (preg_match("/asc|desc$/", $field, $matches)) {
        				$direccion = $matches[0];
        				$field = trim(str_replace($direccion, "", $field));
        			}
        		}
        		if ($schema[$field]['type'] === "string" || $schema[$field]['type'] === "text" || substr($schema[$field]['type'], 0, 5) === "enum(") {
        			$direccion = "COLLATE utf8_spanish2_ci " . $direccion;
        		}
        		$orden[$modelName . "." . $field] = $direccion;
			}
			$query['order'] = $orden;
        }
		return $query;
	}
    function xbeforeFind(&$model, $query) {
		
        if (!empty($query['order'][0])) {
        	$schema = $model->schema();
        	if (!is_array($query['order'][0])) {
        		$query['order'][0] = array($query['order'][0]);
        	}
        	elseif (isset($query['order'][0][0]) && strpos($query['order'][0][0], ",")) {
        		foreach (explode(",", $query['order'][0][0]) as $v) {
        			if (stripos($v, "asc")) {
        				$query['order'][0][trim(str_replace("asc", "", $v))] = "asc";
        			}
        			elseif (stripos($v, "desc")) {
        				$query['order'][0][trim(str_replace("desc", "", $v))] = "desc";
        			}
        			else {
        				$query['order'][0][trim($v)] = "asc";
        			}
        		}
        		unset($query['order'][0][0]);
        	}
        	
        	foreach ($query['order'][0] as $field=>$direccion) {
        		if (is_numeric($field)) {
        			$field = $direccion;
        			$modelName = $model->name;
        			$direccion = "asc";
        		}
        		if (strpos($field, '.')) {
        			$tmp = explode(".", $field);
        			$field = $tmp[1];
        			$modelName = $tmp[0];
        			if (preg_match("/asc|desc$/", $field, $matches)) {
        				$direccion = $matches[0];
        				$field = trim(str_replace($direccion, "", $field));
        			}
        		}
        		if ($schema[$field]['type'] === "string" || $schema[$field]['type'] === "text" || substr($schema[$field]['type'], 0, 5) === "enum(") {
        			$direccion = "COLLATE utf8_spanish2_ci " . $direccion;
        		}
        		$orden[$modelName . "." . $field] = $direccion;
			}
			$query['order'] = $orden;
        }
		
		
		if (is_string($query['fields']) && stristr($query['fields'], 'count')) {
			return parent::beforeFind($model, $query);
		}
				
		//http://www.mark-story.com/posts/view/using-bindmodel-to-get-to-deep-relations
		$contain = array();
		if (isset($this->myRuntime)) {
			$contain = $this->myRuntime[$model->alias]['contain'];
		}
		
		if (isset($query['contain'])) {
			$contain = array_merge($contain, (array)$query['contain']);
		}
		
		$containments = parent::containments($model, $contain);
		$map = parent::containmentsMap($containments);
		if (isset($map[$model->name])) {
			$firstLevelBelongsTos = Set::extract('/belongsTo/.', $map[$model->name]);
			
			foreach ($firstLevelBelongsTos as $relation) {
				if (isset($map[$relation])) { 
					$this->backUpRelations[$relation] = Set::extract('/belongsTo', $map[$relation]);
				} else {
					$this->backUpRelations[$relation] = array();
				}
				unset($map[$relation]['belongsTo']);
				if (empty($map[$relation])) {
					unset($map[$relation]);
				}
			}
			unset($map[$model->name]['belongsTo']);
		}
		if (empty($map[$model->name])) {
			unset($map[$model->name]);
		}
		
		if (!empty($firstLevelBelongsTos) && empty($map)) {
			$hasOne = array();
			foreach ($this->backUpRelations as $firstLevelRelation => $secondLevelBelongsTos) {
				$firstInstance = ClassRegistry::init($firstLevelRelation);
				
				$hasOneTmp = null;
				$hasOneTmp[$firstLevelRelation]['foreignKey'] = false;
				$hasOneTmp[$firstLevelRelation]['conditions'] = 
						array($model->name . '.' . $model->belongsTo[$firstLevelRelation]['foreignKey'] . ' = ' . $firstLevelRelation . '.' . $firstInstance->primaryKey);
				$hasOne = array_merge($hasOne, $hasOneTmp);
				
				if (!empty($secondLevelBelongsTos)) {
					foreach ($secondLevelBelongsTos as $secondLevelRelation) {
						$secondInstance = ClassRegistry::init($secondLevelRelation);
						$hasOneTmp = null;
						$hasOneTmp[$secondLevelRelation]['foreignKey'] = false;
						$hasOneTmp[$secondLevelRelation]['conditions'] = 
								array($firstLevelRelation . '.' . $firstInstance->belongsTo[$secondLevelRelation]['foreignKey'] . ' = ' . $secondLevelRelation . '.' . $secondInstance->primaryKey);
						$hasOne = array_merge($hasOne, $hasOneTmp);
					}
				}
			}
			
			foreach ($this->types as $type) {
				$unbind = array_keys($model->{$type});
				if (!empty($unbind)) {
					$unbinds[$type] = $unbind;
				}
			}
			$model->unbindModel($unbinds);
			$model->bindModel(array('hasOne' => $hasOne));
			unset($query['contain']);
			$query['recursive'] = 1;
			return $query;
		} else {
			$this->backUpRelations = null;
			$query['contain'] = $contain;
        	return parent::beforeFind($model, $query);
		}
    }


	
/**
 * A los campos string, text o enum, les da sorporte para collation espanol de mysql cuando debo hacer un order by.
 *
 * @param object $model Model que usa este behavior.
 * @param array $query Los datos que tengo para armar la query.
 * @return array $query Los datos para realizar la query con laa parte del order modificada para soporte de collation.
 * @access public
 */
	
	function xcontain(&$Model) {
		$args = func_get_args();
		$contain = call_user_func_array('am', array_slice($args, 1));
		$this->myRuntime[$Model->alias]['contain'] = $contain;
		$this->runtime[$Model->alias]['contain'] = $contain;
		//parent::contain($Model, $contain);
	}
		
/**
 * De un archivo subido, lo parseo y lo deja disponible para cargarlo en la base de datos.
 *
 * @param object $model Model que usa este behavior.
 * @param array $opciones. Ej:
 *			- array("validTypes" => array("text/rtf", "application/msword"))
 * @return true si fue posible parsear el archivo subido, false, en cualquier otro caso.
 * @access public
 */
	function getFile(&$model, $opciones = array()) {
		if (!empty($model->data[$model->name]['archivo']['name'])) {
			if (isset($model->data[$model->name]['archivo']['error']) && $model->data[$model->name]['archivo']['error'] === 0) {
				if (!empty($opciones['validTypes'])) {
					if (!in_array($model->data[$model->name]['archivo']['type'], $opciones['validTypes'])) {
						$model->dbError['errorDescripcion'] = "El archivo No corresponde al tipo esperado (" . implode(", ", $opciones['validTypes']) . ")";
						return false;
					}
				}
				$contenido = fread(fopen($model->data[$model->name]['archivo']['tmp_name'], "r"), $model->data[$model->name]['archivo']['size']);
				$model->data[$model->name]['file_size'] = $model->data[$model->name]['archivo']['size'];
				$model->data[$model->name]['file_type'] = $model->data[$model->name]['archivo']['type'];
				$model->data[$model->name]['file_data'] = $contenido;
				unset($model->data[$model->name]['archivo']);
				return true;
			}
			else {
				$model->dbError['errorDescripcion'] = "El archivo no se subio correctamente. Intentelo nuevamente.";
				return false;
			}
		}
		return true;
	}

	
/**
 * A partir de un periodo expresado en formato string, retorna un array de ano, mes y periodo.
 *
 * @param object $model Model que usa este behavior.
 * @param string $periodo El periodo que sea convertir en array.
 * @return mixed Array con los datos ya separados de la forma:
 *			$return['ano'] = "2007";
 *			$return['mes'] = "12";
 *			$return['periodo'] = "M";
 *			$return['primerDia'] = "1";
 *			$return['ultimoDia'] = "31";
 *			$return['fechaInicio'] = "2007-12-01";
 *			$return['fechaFin'] = "2007-12-31";
 * false, en cualquier otro caso.		
 * @access public
 */
	function getPeriodo(&$model, $periodo) {
		return $this->traerPeriodo(&$model, $periodo);
	}

/**
 * TODO:
 * deprecar este metodo.
 */	 
	function traerPeriodo(&$model, $periodo) {
		$periodo = strtoupper($periodo);
		if (!empty($periodo) && preg_match(VALID_PERIODO, $periodo, $matches)) {

			$return['periodoCompleto'] = $matches[0];
			if ($matches[3] == "M") {
				$return['tipo'] = "Mensual";
			}
			else {
				$return['tipo'] = "Quincenal";
			}
			$return['ano'] = $matches[1];
			$return['mes'] = $matches[2];
			$return['periodo'] = $matches[3];
			if ($matches[3] === "M" || $matches[3] === "1Q") {
				$return['primerDia'] = "1";
				$return['fechaInicio'] = $return['ano'] . "-" . $return['mes'] . "-01";
 			}
 			else {
				$return['primerDia'] = "16";
				$return['fechaInicio'] = $return['ano'] . "-" . $return['mes'] . "-16";
 			}
			if ($matches[3] === "M" || $matches[3] === "2Q") {
				$return['ultimoDia'] = $this->format($return['fechaInicio'], array("type" => "ultimoDiaDelMes"));
				$return['fechaFin'] = $return['ano'] . "-" . $return['mes'] . "-" . $return['ultimoDia'];
 			}
 			else {
				$return['ultimoDia'] = "15";
				$return['fechaFin'] = $return['ano'] . "-" . $return['mes'] . "-15";
 			}
			return $return;
		}
		return false;
	}


/**
 * Formatea un valor de acuerdo a un formato.
 *
 * @param object $model Model que usa este behavior.
 * @param string $valor Un valor a formatear.
 * @param array $options Array que contiene el tipo de formato y/o sus opciones.
 * @return string Un string con el valor formateado de acuerdo a lo especificado.
 * @access public
 */
	function format(&$model, $valor, $options = array()) {
		App::import("Helper", array("Number", "Time", "Formato"));
		$formato = new FormatoHelper();
		$formato->Time = new TimeHelper();
		$formato->Number = new NumberHelper();
		return $formato->format($valor, $options);
	}

/**
 * A partir de un array de Condiciones propio (con campos lov de selecciona multiple, por ejemplo), genero un array
 * de condiciones de acuerdo a lo que cakePHP necesita.
 *
 * @param object $model Model que usa este behavior.
 * @param array $condiciones Las condiciones para formar el where de la query.
 * @return array Las condiciones para ser utilizados por el metodo find de cakePHP.
 * @access public
 */
	function getConditions(&$model, $condiciones) {
		return $this->__getConditions($condiciones);
	}
	
	
/**
 * A partir de un array de Condiciones propio (con campos lov de selecciona multiple, por ejemplo), genero un array
 * de condiciones de acuerdo a lo que cakePHP necesita.
 *
 * @param array $condiciones Las condiciones para formar el where de la query.
 * @return array Las condiciones para ser utilizados por el metodo find de cakePHP.
 * @access private
 */
	function __getConditions($condiciones) {
		$return = array();
		if (!empty($condiciones['Condicion']) && is_array($condiciones['Condicion'])) {
			foreach ($condiciones['Condicion'] as $k => $v) {
				if (!empty($v) && strpos($k, "-") && is_string($v) && strlen($v) > 0) {
				
					$t = explode("-", $k);
					if (count($t) == 2) {
						if (substr($t[1], -2) != "__") {
							/**
							* La seleccion multiple desde una lov viene separada por **||**. En este caso
							* debo armar un IN
							*/
							if (strpos($v, "**||**") > 0) {
								$return[$t[0] . "." . $t[1]] = explode("**||**", $v);
							}
							else {
								$return[$t[0] . "." . $t[1]] = $v;
							}
						}
					}
				}
			}
		}
		return $return;
	}


/**
 * Genera una query.
 * En debug level > 0, tambien la formatea para una mejor visualizacion.
 *
 * @param object $model Model que usa este behavior.
 * @param array $data Los datos para generar una query.
 * @param object $modelPreferido Model que usa deberia utilizar en lugar del model que usa el behavior para armar la query.
 * @return string Una query lista para ser ejecuta en la DB.
 * @access public
 */
	function generarSql(&$model, $data, &$modelPreferido = null) {

		if (empty($modelPreferido)) {
			$modelPreferido = $model;
		}
		
		$db =& ConnectionManager::getDataSource($modelPreferido->useDbConfig);
		$default = array(
			"table"		=> Inflector::tableize($modelPreferido->name),
			"limit"		=> null,
			"offset" 	=> null,
			"fields" 	=> array(),
			"conditions"=> null,
			"order"		=> null,
			"joins"		=> array(),
			"group" 	=> null);

		$queryData = am($default, $data);
		$queryData['alias'] = $db->name(Inflector::classify($queryData['table']));
		
		if (empty($queryData['fields'])) {
			$queryData['fields'] = $db->fields($modelPreferido);
		}
		else {
			$queryData['fields'] = $db->fields($modelPreferido, null, $queryData['fields']);
		}
		foreach ($queryData['joins'] as $k=>$v) {
			$queryData['joins'][$k]['table'] = $db->name($v['table']);
			$queryData['joins'][$k]['alias'] = $db->name(Inflector::classify($v['table']));
			if (empty($v['conditions'])) {
				$queryData['joins'][$k]['conditions'][] = $queryData['alias'] . ".id = " . $queryData['joins'][$k]['alias'] . "." . strtolower($queryData['alias']) . "_id";
			}
		}
		
		$sql = $db->buildStatement($queryData, $modelPreferido);

		/**
		* Parseo la query sql muy simple y rapidamente, de modo de poder ver la query mas facilmente cuando debugeo.
		* Hay mucho para mejorar en este parseo....
		*/
		if (Configure::read("debug") > 0) {
			$sql = preg_replace("/(^SELECT)/", "\n$1\t\t", $sql);
			$sql = str_replace(",", ",\n\t\t", $sql);
			$sql = str_replace("FROM", "\nFROM\t\t", $sql);
			$sql = str_replace("LEFT JOIN", "\n\t\tLEFT JOIN", $sql);
			$sql = str_replace("INNER JOIN", "\n\t\tINNER JOIN", $sql);
			$sql = preg_replace("/(LEFT JOIN.*)(ON.*)/", "$1\n\t\t\t$2", $sql);
			$sql = preg_replace("/(INNER JOIN.*)(ON.*)/", "$1\n\t\t\t$2", $sql);
			$sql = preg_replace("/(WHERE)(.*)/", "\n$1\t\t$2", $sql);
			$tmp = explode("WHERE", $sql);
			if (isset($tmp[0]) && isset($tmp[1])) {
				$tmp[0] = preg_replace("/(ON.*)(AND.*)/", "$1\n\t\t\t\t$2", $tmp[0]);
				$tmp[1] = implode("\n\t\t\tIN", explode("IN", $tmp[1]));
				$tmp[1] = str_replace(",\n\t\t", ",", $tmp[1]);
				$sql = $tmp[0] . "WHERE" . implode("\nAND\t\t", explode("AND", $tmp[1]));
			}
			$sql = str_replace("ORDER BY", "\nORDER BY\t", $sql);
			$sql = str_replace("GROUP BY", "\nGROUP BY\t", $sql);
			$tmp = explode("ORDER", $sql);
			if (isset($tmp[0]) && isset($tmp[1])) {
				$tmp[1] = str_replace(",", ",\n\t\t", $tmp[1]);
				$sql = $tmp[0] . "ORDER" . $tmp[1];
			}
			$tmp = explode("GROUP", $sql);
			if (isset($tmp[0]) && isset($tmp[1])) {
				$tmp[1] = str_replace(",", ",\n\t\t", $tmp[1]);
				$sql = $tmp[0] . "GROUP" . $tmp[1];
			}
			$sql = str_replace("\t ", "\t", $sql);
		}
		return $sql;
	}


/**
 * Coloca elementos del array como keys jeraquicas segun se hayan especificado.
 *
 * $datos el array de datos.
 * $opciones array
 *
 * De esta forma, tomara la primary key del model, como keylevel 0 y de dato todo el contenido del registro.
 * ej: 	$model->mapToKey($data);
 *
 *
 * ej: 	$niveles[0] = array("model" => "A", "field" => "aa");
 *		$niveles[1] = array("model" => "A", "field" => "bb");
 *		$niveles[2] = array("model" => "B", "field" => "aa");
 *		$model->mapToKey($data, array("keyLevels"=>$niveles, "valor"=>array("model" => "A"))); //todos los campos del model A
 *		$model->mapToKey($data, array("keyLevels"=>$niveles, "valor"=>array("models"=>array(array("name" => "A", "fields"=>array("cc")), array("name" => "B", "fields" => "aa")))));
 *		$model->mapToKey($data, array("keyLevels"=>$niveles, "valor"=>array("models"=>array(array("name" => "A", "fields"=>array("cc")), array("name" => "B", "fields"=>array("aa", "bb"))))));
 *
 */

	function mapToKey(&$model, $datos, $opciones = array()) {

		$return = false;
		if (!empty($datos) && is_array($datos)) {

			$opcionesDefault = array();
			if (!isset($opciones['keyLevels'][0]['model']) && isset($opciones['keyLevels']['model']) && isset($opciones['keyLevels']['field'])) {
				$opciones['keyLevels'] = array($opciones['keyLevels']);
			}
			$opcionesDefault['keyLevels'][0]['model'] = $model->name;
			$opcionesDefault['keyLevels'][0]['field'] = $model->primaryKey;
			$opcionesDefault['valor']['model'] = $model->name;
			$opcionesDefault['valor']['fields'] = array();
			$opciones = am($opcionesDefault, $opciones);


			foreach ($datos as $data) {
			
				foreach ($opciones['keyLevels'] as $k=>$level) {
					if (isset($data[$level['model']][$level['field']])) {
						$l[$k] = $data[$level['model']][$level['field']];
					}
				}

				if (!empty($opciones['valor']['models'])) {
					if (!is_array($opciones['valor']['models'])) {
						$opciones['valor']['models'] = array($opciones['valor']['models']);
					}
					foreach ($opciones['valor']['models'] as $modelo) {
						/**
						* Si no tengo los fields, asumo que son todos.
						*/
						if (empty($modelo['fields'])) {
							if (isset($data[$modelo['name']])) {
								foreach ($data[$modelo['name']] as $field=>$v) {
									$valor[$field] = $v;
								}
							}
						}
						else {
							if (!is_array($modelo['fields'])) {
								$modelo['fields'] = array($modelo['fields']);
							}
							foreach ($modelo['fields'] as $field) {
								if (isset($data[$modelo['name']][$field])) {
									$valor[$field] = $data[$modelo['name']][$field];
								}
							}
						}
					}
				}
				elseif (!empty($opciones['valor']['fields'])) {
					if (is_array($opciones['valor']['fields'])) {
						foreach ($opciones['valor']['fields'] as $field) {
							$valor = null;
							if (isset($data[$opciones['valor']['model']][$field])) {
								$valor[$field] = $data[$opciones['valor']['model']][$field];
							}
						}
					}
					else {
						$valor = $data[$opciones['valor']['model']][$opciones['valor']['fields']];
					}
				}
				else {
					$valor = $data[$opciones['valor']['model']];
				}
				
				switch(count($opciones['keyLevels'])) {
					case 1:
						$return[$l[0]] = $valor;
						break;
					case 2:
						$return[$l[0]][$l[1]] = $valor;
						break;
					case 3:
						$return[$l[0]][$l[1]][$l[2]] = $valor;
						break;
					case 4:
						$return[$l[0]][$l[1]][$l[2]][$l[3]] = $valor;
						break;
					case 5:
						$return[$l[0]][$l[1]][$l[2]][$l[3]][$l[4]] = $valor;
						break;
				}
			}
		}
		return $return;
	}



/**
 * Calcula la diferencia entre dos fechas.
 *
 * Las fechas deben estar en formato mysql
 * 	Formatos Admitidos de entrada:
 *			yyyy-mm-dd hh:mm:ss
 *			yyyy-mm-dd hh:mm
 *			yyyy-mm-dd
 *
 * @param object $model Model que usa este behavior.
 * @param string $fechaDesde La fecha desde la cual se tomara la diferencia.
 * @param string $fechaHasta La fecha hasta la cual se tomara la diferencia. Si no se pasa la fecha hasta,
 * se tomara la fecha actual como segunda fecha.
 *
 * @return mixed 	array con dias, horas, minutos y segundos en caso de que las fechas sean validas.
 * 					False en caso de que las fechas sean invalidas.
 * @access public
 */
	function dateDiff(&$model, $fechaDesde, $fechaHasta = null) {
		App::import("Vendor", "dates", "pragmatia");
		$Dates = new Dates();
		return $Dates->dateDiff($fechaDesde, $fechaHasta);
	}


/**
 * Suma una cantidad de "intervalo" a una fecha.
 *
 * @param string $fecha La fecha a la cual se le debe sumar el intervalo.
 * @param string $intervalo El intervalo de tiempo.
 * El intervalo puede ser:
 *		y Year
 *		q Quarter
 *		m Month
 * 		w Week
 * 		d Day
 * 		h Hour
 * 		n minute
 * 		s second
 * @param integer $cantidad La cantidad de intervalo a sumar a la fecha.
 * @return mixed La fecha en formato yyyy-mm-dd hh:mm:ss con el intervalo agregado, false si no fue posible realizar la operacion.
 * @access public
 */
	function dateAdd(&$model, $fecha, $cantidad = 1, $intervalo = 'd') {
		App::import('Vendor', 'dates', 'pragmatia');
		$Dates = new Dates();
		return $Dates->dateAdd($fecha, $cantidad, $intervalo);
	}


/**
 * De un array del tipo key => value, genera un array cuyas keys son potencias de dos, 
 * para usar en operacion de matematica binaria.
 *
 * @param object $model Model que usa este behavior.
 * @param array $valores El array con los valores.
 * @return array con los valores ingresados cuyas keys ahora son ponenecia de dos.
 * @access public
 */
	function getValoresEnPotenciaDeDos(&$model, $valores) {
		$return = array();
		if (!empty($valores) && is_array($valores)) {
			$key = 1;
			foreach ($valores as $k=>$v) {
				$return[$key] = $v;
				$key = $key * 2;
			}
		}
		return $return;
	}
	
}
?>