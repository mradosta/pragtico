<?php
/**
 * Model de la aplicacion.
 *
 * Todos los model heredan desde esta clase, por lo que defino metodos que usare en todos los models aca.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos de todo la aplicacion.
 *
 * @package     pragtico
 * @subpackage  app
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
	var $actsAs = array("Containable", "Util", "Permisos", "Validaciones");
	//var $actsAs = array("Util", "Permisos", "Validaciones");


/**
 * Los permisos con los que se guardaran los datos por defecto.
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

	
	function del($id = null, $cascade = true) {
		
		$this->order = null;
		$this->setSecurityAccess('delete');
		
		/**
		 * Asuming dependent related models, need to be deleted as a transaction.
		 */
		$this->begin();
		
		if (parent::del($id, $cascade)) {
			$this->commit();
			return true;
		} else {
			$this->rollback();
			return false;
		}
	}
	
	
	function deleteAll($conditions, $cascade = true, $callbacks = false) {
		
		$this->begin();
		if (parent::deleteAll($conditions, $cascade, $callbacks)) {
			$this->commit();
			return true;
		} else {
			$this->rollback();
			return false;
		}
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
		if (!empty($warnings)) {
			$c = 0;
			$quitar = array();
			foreach ($warnings as $v) {
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
						if (!empty($tableInfo[$tmp]['type']) && $tableInfo[$tmp]['type'] == "date") {
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
			
			foreach ($quitar as $v) {
				unset($w[$v]);
			}
			if (!empty($w)) {
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
    	if (is_numeric($permissions) && $permissions >= 0 && $permissions <= 511) {
    		$this->__permissions = $permissions;
    		return true;
    	}
    	return false;
	}


/**
 * Returns permissions used to save records in this model.
 *
 * @return integer Permissions numeric value used to save records for this model.
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
    	if (!empty($error)) {
			$this->dbError['errorRdbms'] = array_pop(array_pop($error));
    	}
    
		if (!empty($this->dbError['errorRdbms'])) {
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
			
			if (isset($dbError[$this->dbError['errorRdbmsNumero']])) {
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
					if (is_numeric($key)) {
						/**
						* Para el caso de mySql, las keys (constrains), el numero que indica cuando hay un error un una key,
						* corresponde al numero de key devuelta por la query, asi, si indica, por ejemplo, el error
						* SQL Error: 1062: Duplicate entry '45' for key 2, el key "2" corresponde al segundo registro devuelto
						* por la query "SHOW KEYS FROM ..."
						*/
						$keysTmp = $this->query("SHOW KEYS FROM " . $this->useTable);
						foreach ($keysTmp as $v) {
							$keys[$v['STATISTICS']['Key_name']][] = up($v['STATISTICS']['Column_name']);
						}
						$i=0;
						foreach ($keys as $v) {
							if ($i == $key) {
								$campos = implode(", ", $v);
								if (count($v) > 1) {
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