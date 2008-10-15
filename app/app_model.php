<?php
/* SVN FILE: $Id: app_model.php 5118 2007-05-18 17:19:53Z phpnut $ */

/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 5118 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-05-18 12:19:53 -0500 (Fri, 18 May 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package		cake
 * @subpackage	cake.app
 */
class AppModel extends Model {

	//var $recursive = -1;
	
	var $actsAs   = array('Util', 'Permisos', 'Containable', 'Validaciones');
	//var $actsAs   = array('Util');
	//var $actsAs   = array('Transaction', 'Permisos', 'Containable', 'Util');
	//var $actsAs   = array('Transaction', 'Containable', 'Util');
	//var $actsAs   = array('Transaction', 'Permisos', 'Bindable' => array('notices' => true));
	//var $actsAs = array('Bindable'); 
	//var $actsAs   = array('transaction');
	
/**
 * Variable que guarda la informacion de errores que se generen al intentar ejecutar consultas SQL.
 *
 */
	var $dbError;


/**
 * Variable que guarda la informacion de warnings que se generen al intentar ejecutar consultas SQL.
 *
 */
	var $dbWarning;




/**
* Cuando se genera un error, lo busco y lo dejo disponible.
*/
	function onError() {
		$this->__buscarError();
	}

	
/**
 * Permite eliminar registros de un Model en base a condiciones.
 *
 * @param mixed $conditions Condiciones que se deben cumplir.
 * @param boolean $cascade Si es tru, borra los registros que dependen de estos.
 * @param boolean $callbacks Ejecuta callbacks (No se usa de momento).
 * @return boolean True si todo salio bien, False en otro caso.
 * @access public
 */
	function deleteAll($conditions, $cascade = true, $callbacks = false) {
		/*
		$seguridad = $this->generarCondicionSeguridad("delete");
		if(!empty($seguridad)) {
			$conditions = array("AND"=>array($seguridad, $conditions));
		}
		return parent::deleteAll($conditions, $cascade, $callbacks);
		*/
		$ids = Set::extract(
			$this->find('all', array_merge(array('fields' => "{$this->alias}.{$this->primaryKey}", 'recursive' => -1), compact('conditions'))),
			"{n}.{$this->alias}.{$this->primaryKey}"
		);
		
		if(!empty($ids)) {
			$commit = true;
			$c = 0;
			$this->begin();
			foreach($ids as $id) {
				if($this->del($id, $cascade, false)) {
					$c++;
				}
				else {
					$commit = false;
					break;
				}
			}
			if(($c == count($ids)) && $commit === true) {
				$this->commit();
				return true;
			}
			else {
				$this->rollback();
				return false;
			}
		}
		return false;
	}
	
/**
 * Sobreescribo la function del(),
 * 
 * Maneja transacciones SOLO cuando de hay una relacion de tipo hasMany (master/detail).
 * Chequeo por errores al borrar y llamo a la funcion afterSaveFailed.
 */
	function del($id = null, $cascade = true, $manejarTransaccion = true) {
		$returnVal = false;
		/**
		* Solo borro master/detail cuando es una relacion hasMany.
		* La politica para el manejo de la seguridad es que para poder borrar el master,
		* debo necesariamente tener permiso para borrar TODOS los details.
		*/
        if(!empty($this->hasMany)) {
			$this->{$this->primaryKey} = $id;
			/**
			* Inicio la transaccion.
			*/
			if($manejarTransaccion === true) {
  				$this->begin();
  			}
			$returnVal = true;
			foreach($this->hasMany as $k=>$v) {
				if($returnVal === true) {
					$condiciones = null;
					$condiciones[$v['className'] . "." . $v['foreignKey']] = $id;
					$condiciones['checkSecurity'] = "delete";
					$detalles = $this->{$v['className']}->find("all", array("conditions"=>$condiciones, "fields"=>$this->{$v['className']}->primaryKey, "recursive"=>-1));

					if (!empty($detalles)) {
						foreach($detalles as $k1=>$v1) {
							/**
							* Obtengo el id de cada detalle relacionado al master.
							*/
							$id_detalle = $v1[$v['className']][$this->{$v['className']}->primaryKey];

							/**
							* No necesito ninguna asociacion porque entro siempre por clave primaria.
							*/
							$this->{$v['className']}->recursive = -1;
							if (!$this->{$v['className']}->del($id_detalle, $cascade, $manejarTransaccion)) {
								$returnVal = false;
								break;
							}
						}
					}
				}
			}
			
			/**
			* Si ya pude borra sin error todos los details, ahora borro el master.
			*/
			if($returnVal === true) {
				$condiciones = null;
				$condiciones[$this->name . "." . $this->primaryKey] = $id;
				$condiciones['checkSecurity'] = "delete";
				$puedeBorrar = $this->find("count", array("conditions"=>$condiciones, "recursive"=>-1));
				if ($puedeBorrar == 1) {
					if(!parent::del($id)) {
						$returnVal = false;
					}
				}
				else {
					$returnVal = false;
				}
			}
			
			/**
			* Confirmo la transaccion si no ocurrieron errores.
			*/
			if($returnVal === true) {
				if($manejarTransaccion === true) {
					$this->commit();
				}
			}
			else {
				if($manejarTransaccion === true) {
					$this->rollback();
				}
			}
		}
		else {
			$puedeBorrar = $this->find("count", array("conditions"=>array("checkSecurity"=>"delete", $this->name . "." . $this->primaryKey=>$id), "recursive"=>-1));
			if ($puedeBorrar == 1) {
				$returnVal = parent::del($id, $cascade);
			}
		}
		
        if($returnVal === false) {
        	$this->__buscarError();
            $this->afterDeleteFailed($id);
        }
        return $returnVal;
	}    
    
/**
 * Sobreescribo la function save().
 * Maneja transacciones SOLO cuando de hay una relacion de tipo hasMany (master/detail).
 * Por una cuestion de implementacion en el model detail, debo definir un array (unique) que contendra el nombre
 * de los campos que componen la Unique del model. Esto usara para buscar y borrar y re-insertar ante un update.
 * Chequeo por errores al guardar y llamo a la funcion afterSaveFailed.
 */
    function save($data = null, $validate = true, $fieldList = array(), $manejarTransaccion = true) {
		/**
		* Solo guardo master/detail cuando es una relacion hasMany y en el vector vienen componentes
		* de esta relacion y no otra
		*/
		if(!empty($data)) {
			$r =  array_intersect_key($data, $this->hasMany);
			if(!empty($this->hasMany) && !empty($r)) {
			
				$keys = array_keys($data);
				/**
				* El primer elemento del vector de keys debe ser el master en el master/datail.
				*/
				if ($keys[0] !== $this->name) {
					$returnVal = false;
				}
				else {
					$returnVal = true;
				}

				/**
				* Quito el primer elemento y me muevo por las keys (foraneas).
				*/
				array_shift($keys);
				
				/**
				* Inicio la transaccion.
				*/
				if($manejarTransaccion === true) {
					$this->begin();
				}
				if($returnValParent = parent::save($data[$this->name], $validate, $fieldList)) {
					if(!empty($data[$this->name][$this->primaryKey])) {
						/**
						* Es un update, tengo el id del anterior.
						*/
						$id = $this->id;
						$accion = "update";
						$datoExistente = $this->find("first", array("conditions"=>array($this->name . "." . $this->primaryKey=>$id), "contain"=>$keys));
					}
					else {
						/**
						* Busco el ultimo id que inserte.
						*/
						$id = $this->getLastInsertId();
						$accion = "insert";
					}
					
					foreach($keys as $key) {
						/**
						* Debo identificar si se ha quitado alguno/s, el/los cual/es debo eliminar.
						* Solo en caso de ser un update. Cuando es un insert, estoy seguro que no existira.
						*/
						if($accion === "update") {
							$idsDetailAntesModificar = Set::extract("/" . $key . "/" . $this->$key->primaryKey, $datoExistente);
							$idsDetailDespuesModificar = Set::extract("/" . $this->$key->primaryKey, array_values($data[$key]));
							$idsDetailEliminados = array_diff($idsDetailAntesModificar, $idsDetailDespuesModificar);
							if(!empty($idsDetailEliminados)) {
								$this->$key->deleteAll(array($key . "." . $this->$key->primaryKey=>$idsDetailEliminados));
							}
						}
						
						foreach($data[$key] as $k=>$v) {
							if($returnVal === true) {
								/**
								* Asigno el valor del id del master, al arreglo de la foranea (detail).
								*/
								$v[$this->hasMany[$key]['foreignKey']] = $id;

								/**
								* Debo decidir si es un update o un insert.
								* Para ello necesito que este seteada la variable $unique en el model.
								*/
								$find = array();
								foreach($this->$key->unique as $unique) {
									if(isset($v[$unique])) {
										$find[$key . "." . $unique] = $this->setDBFieldValue($this->$key, $unique, $v[$unique], true);
									}
								}

								$lineaDetalle = $this->$key->find($find, array($this->$key->primaryKey));
								if(!empty($lineaDetalle)) {
									/**
									* El registro ya existe, debo actualizarlo.
									*/
									$v[$this->$key->primaryKey] = $lineaDetalle[$key][$this->$key->primaryKey];
								}
								else {
									/**
									* El registro no existe, debo insertarlo.
									*/
									$this->$key->create($v);
								}

								if($this->$key->validates()) {
									if(!$this->$key->save($v, $validate, $fieldList, $manejarTransaccion)) {
										/**
										* Vuelvo atras si algo salio mal.
										*/
										if($manejarTransaccion === true) {
											$this->rollback();
										}
										$returnVal = false;
									}
								}
								else {
									$this->validationErrors[$this->name][$key][$k] = $this->$key->validationErrors;
									unset($this->$key->validationErrors);
									//if($manejarTransaccion === true) {
									//	$this->rollback();
									//}
									$returnVal = false;
								}
							}
						}
					}
					/**
					* Confirmo la transaccion si no ocurrieron errores.
					*/
					if($returnVal === true && $manejarTransaccion === true) {
						$this->commit();
					}
				}
				else {
					if($manejarTransaccion === true) {
						$this->rollback();
					}
					$returnVal = false;
				}
			}
			else {
				if($returnValParent = parent::save($data, $validate, $fieldList)) {
					$returnVal = true;
				}
				else {
					$returnVal = false;				
				}
			}
		}
		else {
			return false;
		}

        if($returnVal === false) {
			$this->__buscarError();
            $this->afterSaveFailed();
            return false;
        }
        else {
        	$this->__buscarWarning();
        }
		$returnValParent[$this->name][$this->primaryKey] = $this->id;
        return $returnValParent;
    }
    
/**
 * Llama a la funcion invalidate().
 * Lo hago de esta forma para que este metodo pueda ser sobreescrito desde el model.
 *
*/
    function afterSaveFailed() {
        //$this->invalidate('DbError');
        return false;
    }
    
/**
 * Lo hago de esta forma para que este metodo pueda ser sobreescrito desde el model.
*/
    function afterDeleteFailed($id) {
        return true;
    }
    
    
/**
 * Retorna la variable $this->dbError con los errores que puedan haber surgido de alguna query.
 *
*/
    function getError() {
    	return $this->dbError;
    }


/**
 * Retorna la variable $this->dbError con los warnings que puedan haber surgido de alguna query.
 *
*/
    function getWarning() {
    	return $this->dbWarning;
    }

    
/**
 * Carga la variable (array) $this->dbWarning de la clase con los warnings.
 * Si un mensaje especifico para el motor no ha sido definido, retorna el mensaje de warning que genero la DB.
 *
 * @access private
 * @return void.
*/
    function __buscarWarning() {
		$warnings = $this->query("SHOW WARNINGS");
		if(!empty($warnings)) {
			$c = 0;
			$quitar = array();
			foreach($warnings as $v) {
				$w[$c]['warningRdbms'] = $v[0];
				$w[$c]['warningRdbmsNumero'] = $v[0]['Code'];
				$w[$c]['warningRdbmsDescripcion'] = $v[0]['Message'];
				switch($w[$c]['warningRdbmsNumero']) {
					case "1265":
						$tmp = str_replace("Data truncated for column '", "", $w[$c]['warningRdbmsDescripcion']);
						$tmp = preg_replace("/' at row [0-9]+$/", "", $tmp);
						$tableInfo = $this->schema();
						/**
						* Evita que me diga que trunco una fecha cuando esto no tiene importancia.
						*/
						if(!empty($tableInfo[$tmp]['type']) && $tableInfo[$tmp]['type'] == "date") {
							$quitar[] = $c;
						}
						else {
							$w[$c]['warningDescripcion'] = "El campo " . inflector::humanize($tmp) . " quedo sin un valor asiganado.";
						}
						break;
					default:
						$w[$c]['warningDescripcion'] = $w[$c]['warningRdbmsDescripcion'];
						break;
				}
				$c++;
			}
			
			foreach($quitar as $v) {
				unset($w[$v]);
			}
			if(!empty($w)) {
				$this->dbWarning[] = $w;
			}
		}
 	}


/**
 * Carga la variable (array) $this->dbError de la clase con los errores.
 * Si un mensaje especifico para el motor no ha sido definido, retorna el mensaje de error que genero la DB.
 *
 * @access private
 * @return void.
*/
    function __buscarError() {
    	$error = $this->query("SHOW ERRORS");
    	if(!empty($error)) {
			$this->dbError['errorRdbms'] = array_pop(array_pop($error));
    	}
    
		if(!empty($this->dbError['errorRdbms'])) {
			/**
			 * Mensajes faciles de entender para el usuario.
			 * La clave es el numero de error de MySQL.
			 */
			$dbError = array ( 	"1062" => "El registro ya existe.",
								"1064" => "Error de sintaxis en la instruccion SQL.",
								"1054" => "Columna desconocida.",
								"1048" => "La columna no puede contener un valor nulo.",
								"1452" => "No es posible agregar/modificar el registro porque posee un registro relacionado.",
								"1451" => "No es posible borrar/modificar el registro porque posee un registro relacionado.",
								"1217" => "El registro esta siendo usado en otra tabla.");
				
			$this->dbError['model'] = $this->name;
			$this->dbError['errorRdbmsNumero'] = $this->dbError['errorRdbms']['Code'];
			$this->dbError['errorRdbmsDescripcion'] = $this->dbError['errorRdbms']['Message'];
			
			if(isset($dbError[$this->dbError['errorRdbmsNumero']])) {
				$this->dbError['errorDescripcion'] = $dbError[$this->dbError['errorRdbmsNumero']];
			}
			else {
				$this->dbError['errorDescripcion'] = $this->dbError['errorRdbmsDescripcion'];
			}
			/**
			* Intento buscar una descripcion adicional para mensaje de error, siempre y cuando el rdbms me de la opcion.
			*/
			switch($this->dbError['errorRdbmsNumero']) {
				case 1064:
					$this->dbError['errorRdbms']['Message'] = str_replace("  ", " ", str_replace("  ", " ", str_replace("\t", " ", str_replace("\n", "", $this->dbError['errorRdbms']['Message']))));
					preg_match("/.+near (.+) at line.+/", $this->dbError['errorRdbms']['Message'], $matches);
					$this->dbError['errorDescripcion'] = "El error puede provenir de " . $matches[1];
					break;
				case 1048:
					$this->dbError['errorDescripcion'] = "Ha intentado grabar un valor nulo en el campo " . up(preg_replace("/.+'([a-z]+)'.+/", "$1", $this->dbError['errorRdbmsDescripcion']));
					break;
				case 1452:
					preg_match("/REFERENCES `(.+)` \(/", $this->dbError['errorRdbms']['Message'], $matches);
					$this->dbError['errorDescripcion'] = "Ha intentado agregar/modificar un registro que necesariamente necesita un registro relacionado de la tabla " . $matches['1'] . ".";
					break;
				case 1451:
					preg_match("/[a-z]+\/([a-z,_]+)`\,/", $this->dbError['errorRdbms']['Message'], $matches);
					$this->dbError['errorDescripcion'] = "Ha intentado borrar/modificar un registro que esta relacionado con otro de la tabla " . $matches['1'] . ".";
					break;
				case 1062:
					$key = array_pop(explode(" for key ", $this->dbError['errorRdbmsDescripcion'])) - 1;
					if(is_numeric($key)) {
						/**
						* Para el caso de mySql, las keys (constrains), el numero que indica cuando hay un error un una key,
						* corresponde al numero de key devuelta por la query, asi, si indica, por ejemplo, el error
						* SQL Error: 1062: Duplicate entry '45' for key 2, el key "2" corresponde al segundo registro devuelto
						* por la query "SHOW KEYS FROM ..."
						*/
						$keysTmp = $this->query("SHOW KEYS FROM " . $this->useTable);
						foreach($keysTmp as $v) {
							$keys[$v['STATISTICS']['Key_name']][] = up($v['STATISTICS']['Column_name']);
						}
						$i=0;
						foreach($keys as $v) {
							if($i == $key) {
								$campos = implode(", ", $v);
								if(count($v) > 1) {
									$this->dbError['errorDescripcion'] = "Ha intentado grabar un valor que ya existe para la combinacion de campos " . $campos . " de la tabla " . up($this->useTable) . ".";
								}
								else {
									$this->dbError['errorDescripcion'] = "Ha intentado grabar un valor que ya existe para el campo " . $campos . " de la tabla " . up($this->useTable) . ".";
								}
								break;
							}
							$i++;
						}
						/*
						$tableInfo = $this->_schema;
						$c=0;
						foreach($tableInfo as $k=>$v) {
							if(!empty($v['key'])) {
								$vCampos[$c] = $v;
								$vCampos[$c]['nombre'] = $k;
								$c++;
							}
						}
						$this->dbError['errorDescripcion'] = "Ha intentado grabar un valor que ya existe en el campo " . up($vCampos[$key]['nombre']) . " de la tabla " . up($this->useTable) . ".";
						*/
					}
				break;
			}
		}
    }
}
?>