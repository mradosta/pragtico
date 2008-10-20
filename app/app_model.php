<?php
/**
 * Model de la aplicacion.
 *
 * Todos los model heredan desde esta clase, por lo que defino metodos que usare en todos los models aca.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos de todo la aplicacion.
 *
 * @package		pragtico
 * @subpackage	app
 */
class AppModel extends Model {

	/**
	* TODO:
	* Deberia asegurarme que todo sea recursive = -1, y cuando lo necesite algun level mas de recursive,
	* logralo con el behavior contain().
	* var $recursive = -1;
	*/

/**
 * Los behaviors que uso en todos los models.
 *
 * @var array
 * @access public
 */
	var $actsAs = array("Util", "Permisos", "Containable", "Validaciones");


/**
 * Los permisos con los que se guardaran los datos.
 *
 * @var integer
 * @access protected
 */
	protected $__permissions = "496";
	
	
/**
 * Mantiene informacion de errores que se generen al intentar ejecutar consultas SQL.
 *
 * @var array
 * @access public
 */
	var $dbError;


/**
 * Mantiene informacion de warnings que se generen al intentar ejecutar consultas SQL.
 *
 * @var array
 * @access public
 */
	var $dbWarning;


/**
 * Cuando se genera un error, lo busco y lo dejo disponible.
 *
 * @return void.
 * @access public
*/
	function onError() {
		$this->__buscarError();
	}

	
/**
 * Permite eliminar multiples registros de un Model en base a condiciones.
 *
 * @param mixed $conditions Condiciones que se deben cumplir para eliminar los registros.
 * @param boolean $cascade Si es true, elimina los registros que dependen de estos.
 * @param boolean $callbacks Ejecuta callbacks (No se usa de momento).
 * @param boolean $manejarTransaccion Indica si deben manejarse transacciones o no.
 * @return boolean True si se pudieron eliminar todos los registros, False en otro caso.
 * @access public
 */
	function deleteAll($conditions, $cascade = true, $callbacks = false, $manejarTransaccion = true) {

		/**
		* Evito que por error borre toda la tabla.
		*/
		if(empty($conditions)) {
			return false;
		}
	
		/**
		* Quito el orden y las relacines belongsTo, ya que no las necesito y solo volverias mas lenta la query.
		*/
		$this->order = null;
		$this->belongsTo = null;
		$ids = Set::extract("/" . $this->alias . "/" . $this->primaryKey,
			$this->find("all", array(	'fields'	=> $this->alias . "." . $this->primaryKey,
										'recursive' => -1,
										'conditions'=> $conditions)));
		
		if(!empty($ids)) {
			$commit = true;
			$c = 0;
			if($manejarTransaccion === true) {
				$this->begin();
			}
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
				if($manejarTransaccion === true) {
					$this->commit();
				}
				return true;
			}
			else {
				if($manejarTransaccion === true) {
					$this->rollback();
				}
				return false;
			}
		}
		return false;
	}
	
	
/**
 * Sobreescribo la function del(),
 * Maneja transacciones por defecto cuando de hay una relacion de tipo hasMany (master/detail).
 * 
 * @param mixed $id El identificador unica de registro (clave primaria).
 * @param boolean $cascade Si es true, elimina los registros que dependen de estos.
 * @param boolean $manejarTransaccion Indica si deben manejarse transacciones o no.
 * @return boolean True si se pudo eliminar el registro, False en otro caso.
 * @access public
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
        }
        return $returnVal;
	}    
    
    
/**
 * Sobreescribo la function save().
 * Maneja transacciones por defecto cuando de hay una relacion de tipo hasMany (master/detail).
 * 
 * Por una cuestion de implementacion en el model detail, debo definir un array (unique) que contendra el nombre
 * de los campos que componen la Unique del model. Esto usara para buscar y borrar y re-insertar ante un update.
 * 
 * @param array $data El array con los datos a guardar.
 * @param boolean $validate Indica si debo verificar las validaciones antes de guardar o no.
 * @param array $fieldList Lista de campos que estan permitido que sean escritos.
 * @return boolean True si se pudo guardar el registro, False en otro caso.
 * @access public
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
            return false;
        }
        else {
        	$this->__buscarWarning();
        }
		$returnValParent[$this->name][$this->primaryKey] = $this->id;
        return $returnValParent;
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
 * Setea los permisos con los que se guardaran los registros.
 *
 * @param integer $permisos con los que se guardaran los datos.
 * @return boolean True si puedieron setearse los permisos, false en cualquier otro caso.
 * @access public
 */
    function setPermissions($permissions) {
    	if(is_numeric($permissions) && $permissions >= 0 && $permissions <= 511) {
    		$this->__permissions = $permissions;
    		return true;
    	}
    	return false;
	}


/**
 * Retorna los permisos con los que se guardaran los registros.
 *
 * @return integer $permisos con los que se guardaran los datos.
 * @access public
 */
	function getPermissions() {
		return $this->__permissions;
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
					}
				break;
			}
		}
    }
}
?>