<?php
/**
 * Paginador Component.
 * Se encarga de la paginacion en las grillas.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.controllers.components
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de la paginacion.
 * Se encarga de la paginacion, de armar las condiciones de busqueda y mantenerlas en la session.
 *
 * @package     pragtico
 * @subpackage  app.controllers.components
 */
class PaginadorComponent extends Object {

/**
 * Los componentes que necesitare.
 *
 * @var array
 * @access public
 */
	var $components = array('Util');

/**
 * El Controller que instancio el component.
 *
 * @var array
 * @access public
 */
    var $controller;


/**
 * Inicializa el Component para usar en el controller.
 *
 * @param object $controller Una referencia al controller que esta instanciando el component.
 * @return void
 * @access public
 */
    function startup(&$controller) {
        $this->controller = &$controller;
    }


/**
 * Genera las condiciones para el paginador a partir de los datos que vengan cargados en
 * $this->data['Condicion']. Si este esta vacio, intenta leerlos desde la sesion si esta existe
 * en caso de que se haya paginado.
 *
 * @return array Un array con las condiciones de la forma que exije el framework para el metodo find.
 * @access public
 */
    function generarCondicion() {
		if (isset($this->controller->data['Formulario']['accion']) && $this->controller->data['Formulario']['accion'] == 'limpiar') {
			$this->controller->Session->del('filtros.' . $this->controller->name . '.' . $this->controller->action);
			unset($this->controller->data['Condicion']);
			return array();
		}

		$filter = $this->controller->Session->read('filtros.' . $this->controller->name . '.' . $this->controller->action);
		if (!empty($filter)) {
			$condiciones = $filter['condiciones'];
			$valoresLov = $filter['valoresLov'];
		} else {
    		$condiciones = $valoresLov = array();
		}
		
		if (!empty($this->controller->data['Condicion']) && is_array($this->controller->data['Condicion'])) {
			foreach ($this->controller->data['Condicion'] as $k => $v) {
				if (empty($v)) {
					unset($this->controller->data['Condicion'][$k]);
				} elseif (is_array($v)) {
					$v = implode('**||**', $v);
				}

				if (strpos($k, '-') && is_string($v) && strlen($v) > 0) {
					$t = explode('-', $k);
					if (count($t) == 2) {
						if (substr($t[1], -2) !== '__') {
							/**
							* La seleccion multiple desde una lov o desde un checkMultiple viene separada
							* por **||**. En este caso, debo armar un IN
							*/
							if (strpos($v, '**||**') > 0) {
								$condiciones[$t[0] . '.' . $t[1]] = explode('**||**', $v);
							} else {
								$condiciones[$t[0] . '.' . $t[1]] = $v;
							}
						} else {
						/**
						* Si termina con __ significa que son los valores de una lov que 'se ven'.
						* Los guardo por separado, porque debo restaurarlos para mostrar la busqueda, aunque
						* no seran parte del where de busqueda en la query.
						*/
							$valoresLov[$t[0] . '-' . $t[1]] = $v;
						}
					}
				}
			}
			/**
			* Vuelvo a recorrer el array para ver que no existan los desde y hasta como campos separados,
			* de manera de unificarlos si los hay en un unico array del tipo 'and'=>array(...
			*/
			foreach ($condiciones as $campo => $valor) {
				if (substr($campo, strlen($campo) - 7) === '__desde') {
					$nuevoCampo = str_replace('__desde', '', $campo);
					if (!isset($condiciones[$nuevoCampo])) {
						$r = $this->__reemplazos($campo, $valor);
						unset($condiciones[$campo]);
						$condiciones[$r['key']] = $r['value'];
					}
				} elseif (substr($campo, strlen($campo) - 7) === '__hasta') {
					$nuevoCampo = str_replace('__hasta', '', $campo);
					if (!isset($condiciones[$nuevoCampo])) {
						$r = $this->__reemplazos($campo, $valor);
						unset($condiciones[$campo]);
						$condiciones[$r['key']] = $r['value'];
					}
				} else {
					$r = $this->__reemplazos($campo, $valor);
					unset($condiciones[$campo]);
					$condiciones[$r['key']] = $r['value'];
				}
			}
			
			/**
			* Grabo en la session las condiciones mas los valores de la lov, que si bien no se usaran en las busquedas,
			* me sirven para recargar el control con el valor seleccionado.
			*/
			if (!empty($condiciones)) {
				$this->controller->Session->write('filtros.' . $this->controller->name . '.' . $this->controller->action, array('condiciones' => $condiciones, 'valoresLov' => $valoresLov));
			}
		}

		return $condiciones;
    }


/**
 * Genera el array para $this->data a partir de las condiciones para que el helper pinte nuevamente
 * los valores en la vista.
 *
 * @access public
 * @return void
 */
    function generarData() {
		$condiciones = $this->controller->Session->read('filtros.' . $this->controller->name . '.' . $this->controller->action);
		if (!empty($condiciones)) {
        	/**
        	* Restauro los valores 'que se ven de una lov, para no perderlos.
        	* Estos no estan con las condiciones porque no se usaron en los filtros, aunque si deben mostrarse
        	* en el control lov.
        	*/
        	if (!empty($condiciones['valoresLov']) && is_array($condiciones['valoresLov'])) {
				foreach ($condiciones['valoresLov'] as $k => $v) {
					$this->controller->data['Condicion'][$k] = $v;
				}
			}
			/**
			* A partir del array de condiciones, vuelvo a generar el array data para que el helper lo entienda,
			* y restaure los valores.
			*/
			foreach ($condiciones['condiciones'] as $k => $v) {
				$condicionMultiple = null;
				$sufix = substr(trim($k), -2);
				$k = str_replace('.', '-', $this->__removerReemplazos($k));
				if ($sufix == '>=') {
					$this->controller->data['Condicion'][$k . '__desde'] = $this->Util->format($this->__removerReemplazos($v), array('type' => 'datetime'));
				} elseif ($sufix == '<=') {
					$this->controller->data['Condicion'][$k . '__hasta'] = $this->Util->format($this->__removerReemplazos($v), array('type' => 'datetime'));
				} else {
					$this->controller->data['Condicion'][$k] = $this->__removerReemplazos($v);
				}
			}
        }
	}


/**
 * Establece las condiciones, realiza las consultas a la base y deja el array $this->data['Condicion']
 * de manera que el helper pueda cargar los valores de las busquedas.
 *
 * @param array $condicion Condiciones que se sumaran a las que hay en la sesion.
 * @param array $whiteList Campos que no deben ser inlcuidos en los filtros pero si guardados en la session.
 *
 * @return array Resultados de la paginacion.
 * @access public
 */
	function paginar($condicion = array(), $whiteList = array()) {
		$condiciones = array_merge($this->generarCondicion(), $condicion);
		if (!empty($this->controller->paginate['conditions'])) {
			$condiciones = array_merge($this->controller->paginate['conditions'], $condiciones);
		}
		$this->controller->paginate['conditions'] = array_diff_key($condiciones, array_flip($whiteList));

		$model = Inflector::classify($this->controller->name);

		$resultado = array();
		if (!empty($this->controller->{$model}->totalizar)) {
			/**
			* Si he seteado contain, lo guardo y lo quito, ya que al utilizar una funcion de grupo se deberia quitar.
			*/
			if (isset($this->controller->{$model}->Behaviors->Containable->runtime[$model])) {
				$contain = $this->controller->{$model}->Behaviors->Containable->runtime[$model];
			}

			foreach ($this->controller->{$model}->totalizar as $operacion => $campos) {
				foreach ($campos as $campo) {
					$r = $this->controller->{$model}->find('first', array(
												'conditions'	=> $condiciones,
												'fields'		=> strtoupper($operacion) . '(' . $model . '.' . $campo . ') as total'));
					$resultado[$campo] = $r[$model]['total'];
				}
			}

			/**
			* Restauro contain si lo tenia seteado.
			*/
			if (!empty($contain)) {
				$this->controller->{$model}->Behaviors->Containable->runtime[$model] = $contain;
			}
		}

		/**
		* Si he seteado dinamicamente contain, me aseguro de aplicarlo tambien en el paginador.
		*/
		if (isset($this->controller->{$model}->Behaviors->Containable->runtime[$model])) {
			if (!empty($this->controller->{$model}->Behaviors->Containable->runtime[$model]['contain'])) {
				$this->controller->paginate = array($model=>array_merge($this->controller->paginate, array('contain'=>$this->controller->{$model}->Behaviors->Containable->runtime[$model]['contain'])));
			} else {
				$this->controller->paginate = array($model=>array_merge($this->controller->paginate, array('contain'=>false)));
			}
		}

		$this->generarData();
		$registros = $this->controller->paginate();
		return array('registros' => $registros, 'totales' => $resultado);
	}


/**
 * Realiza los reemplazos necesarios en funcion del tipo de campo para ser entendidos por un query SQL.
 *
 * @param string $modelCampo El nombre del model y del campo en la forma Model.Campo.
 * @param array	 $v El valor que tiene el campo.
 * @return string Valor del campo ya reemplazado en funcion de su tipo.
 * @access private
 */
	function __reemplazos($modelCampo, $v) {
 		$valor = $v;
 		if (strpos($modelCampo, '.')) {
			$t = explode('.', $modelCampo);
			$model = $t[0];
			$campo = $t[1];

			if (substr($campo, strlen($campo) - 7) == '__desde') {
				$campo = str_replace('__desde', '', $campo);
				$extra = 'desde';
			} elseif (substr($campo, strlen($campo) - 7) == '__hasta') {
				$campo = str_replace('__hasta', '', $campo);
				$extra = 'hasta';
			}

			if (isset($this->controller->$model) && is_object($this->controller->$model)) {
				$tipoDato = $this->controller->$model->getColumnType($campo);
			}
			/**
			* Para el caso de una busqueda por un model asociado, veo si lo encuentro.
			*/
			elseif (isset($this->controller->{$this->controller->modelClass}->$model) && is_object($this->controller->{$this->controller->modelClass}->$model)) {
				$tipoDato = $this->controller->{$this->controller->modelClass}->$model->getColumnType($campo);
			}

			$key = $model . '.' . $campo;
			if (!empty($tipoDato)) {
				switch($tipoDato) {
					case 'text':
					case 'string':
 						$valor = '%' . $v . '%';
 						$key .= ' like';
						break;
					case 'date':
					case 'datetime':
						if ($tipoDato === 'datetime') {
							$v = $this->Util->format($v, array('type' => 'datetime'));
						} else {
							$v = $this->Util->format($v, array('type' => 'date'));
						}

						if (isset($extra)) {
							if ($extra == 'desde') {
								$valor = $v;
								$key .= ' >=';
							} elseif ($extra == 'hasta') {
								$valor = $v;
								$key .= ' <=';
							}
						} else {
							$valor = $v;
						}
						break;
					default:
						$valor = $v;
				}
			}
		}
		return array('key'=>$key, 'value'=>$valor);
	}


/**
 * Quita los reemplazos realizados por el metodo '__reemplazos' de manera de volver el valor del campo a su estado
 * original.
 *
 * @param string $valor Un valor con caracteres agregados por el metodo reemplazos.
 * @return string El Valor del campo sin los reemplazos.
 * @access private
 */
	function __removerReemplazos($valor) {
		if (is_string($valor)) {
			return trim(str_replace(array('like', '%', '>=', '<='), '', $valor));
		} else {
			return $valor;
		}
	}
}
?>