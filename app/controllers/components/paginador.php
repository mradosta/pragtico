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
 * Controller associate to the component.
 *
 * @var array
 * @access public
 */
    var $controller;


/**
 * $whiteListFields are fields that should be saved in session but should not be used at filters.
 *
 * @var array
 * @access private
 */
    var $__whiteListFields = array();


/**
 * $conditions that should be applied to current filter and saved to session.
 *
 * @var array
 * @access private
 */
    var $__conditions = array();


/**
 * $conditions that should be remove from filter and session.
 *
 * @var array
 * @access private
 */
    var $__conditionsToRemove = array();


/**
 * Inicializa el Component para usar en el controller.
 *
 * @param object $controller Una referencia al controller que esta instanciando el component.
 * @return void
 * @access public
 */
    function startup($controller) {
        $this->controller = $controller;
    }


/**
 * Genera las condiciones para el paginador a partir de los datos que vengan cargados en
 * $this->data['Condicion']. Si este esta vacio, intenta leerlos desde la sesion si esta existe
 * en caso de que se haya paginado.
 *
 * @param boolean $useSession. If true, session data for the controller will be merged with controller->data
 *								to create conditions.
 *								When false, just controller->data will be use to create conditions.
 *
 * @return array Un array con las condiciones de la forma que exije el framework para el metodo find.
 * @access public
 */
    function generarCondicion($useSession = true, $whiteListFields = array()) {

        /** Delete filters */
        if (isset($this->controller->data['Formulario']['accion']) && $this->controller->data['Formulario']['accion'] == 'limpiar') {
            $this->controller->Session->del('filtros.' . $this->controller->name . '.' . $this->controller->action);
            unset($this->controller->data['Condicion']);
            return array();
        }


        /** Get session data */
        $conditions = $this->__conditions;
        $valoresLov = array();
        if ($useSession === true) {
            $filter = $this->controller->Session->read('filtros.' . $this->controller->name . '.' . $this->controller->action);
            if (!empty($filter)) {
                $conditions = array_merge($filter['condiciones'], $conditions);
                $valoresLov = $filter['valoresLov'];
            }
        }


        if (!empty($this->controller->data['Condicion'])) {
            foreach ($this->controller->data['Condicion'] as $k => $v) {

                list($model, $field) = explode('-', $k);
                $modelField = $model . '.' . $field;

                /** Ignore empty values and removed then from sessions */
                if (empty($v)) {
                    unset($conditions[$modelField]);
                    continue;
                }
                
                /** Ignore on lov descriptive data */
                if (substr($field, -2) === '__' || in_array($k, $whiteListFields)) {
                    $valoresLov[$k] = $v;
                    continue;
                }


                /** Replace range conditions
                $modelField = str_replace('__desde', ' >=', $modelField);
                $modelField = str_replace('__hasta', ' <=', $modelField);
                 */


                $conditions = array_merge($this->__reemplazos($modelField, $v), $conditions);
            }
        }

        if (!empty($this->__conditionsToRemove)) {
            foreach ($this->__conditionsToRemove as $k) {
                unset($conditions[$k]);
            }
        }

        if (!empty($conditions) || !empty($valoresLov)) {
            $this->controller->Session->write('filtros.' . $this->controller->name . '.' . $this->controller->action, array('condiciones' => $conditions, 'valoresLov' => $valoresLov));
        }
        return $conditions;
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
				if ($sufix === '>=') {
                    $k .= '__desde';
				} elseif ($sufix === '<=') {
                    $k .= '__hasta';
				}
                $this->controller->data['Condicion'][$k] = $this->__removerReemplazos($v);
			}
        }
	}


/**
 * Sets $__whiteListFields.
 *
 * @param array|string $whiteListFields.
 */
    function setWhiteList($whiteListFields) {
        $this->__whiteListFields = array_merge($this->__whiteListFields, (array)$whiteListFields);
    }


/**
 * Sets conditions.
 *
 * @param array|string $conditions.
 */
    function setCondition($conditions) {
        $this->__conditions = array_merge($this->__conditions, (array)$conditions);
    }


/**
 * Remove conditions.
 *
 * @param array|string $conditions.
 */
    function removeCondition($conditions) {
        $this->__conditionsToRemove = array_merge($this->__conditionsToRemove, (array)$conditions);
    }


/**
 * Establece las condiciones, realiza las consultas a la base y deja el array $this->data['Condicion']
 * de manera que el helper pueda cargar los valores de las busquedas.
 *
 * @param array $condicion Condiciones que se sumaran a las que hay en la sesion.
 * @param array $whiteList Campos que no deben ser inlcuidos en los filtros pero si guardados en la session.
 * @param boolean $useSession. If true, session data for the controller will be merged with controller->data
 *								to create conditions.
 *								When false, just controller->data will be use to create conditions.
 *
 * @return array Resultados de la paginacion.
 * @access public
 */
    function paginar($options = array()) {

        $defaults = array(  'whiteListFields'   => $this->__whiteListFields,
                            'extraConditions'   => array(),
                            'mergeConditions'   => false,
                            'useSession'        => true);

        $options = array_merge($defaults, $options);
        
        if ($defaults['useSession'] === true) {
            $conditions = array_merge($this->generarCondicion($options['useSession'], $options['whiteListFields']), $options['extraConditions']);
        } else {
            $conditions = $condicion;
        }

        if (!empty($this->controller->{$this->controller->modelClass}->modificadores[$this->controller->action]['contain'])) {
            $this->controller->paginate['contain'] = $this->controller->{$this->controller->modelClass}->modificadores[$this->controller->action]['contain'];
        }
        
        if (!empty($this->controller->paginate['conditions'])) {
            $this->controller->paginate['conditions'] = array_merge($this->controller->paginate['conditions'], $conditions);
        } else {
            $this->controller->paginate['conditions'] = $conditions;
        }

        $this->generarData();
        return $this->controller->paginate();
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

			if (isset($this->controller->{$model}) && is_object($this->controller->{$model})) {
				$tipoDato = $this->controller->{$model}->getColumnType($campo);
			}
			/**
			* Para el caso de una busqueda por un model asociado, veo si lo encuentro.
			*/
			elseif (isset($this->controller->{$this->controller->modelClass}->{$model}) && is_object($this->controller->{$this->controller->modelClass}->$model)) {
				$tipoDato = $this->controller->{$this->controller->modelClass}->{$model}->getColumnType($campo);
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
		return array($key => $valor);
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
			return trim(str_replace(array('!=', 'like', '%', '>=', '<='), '', $valor));
		} else {
			return $valor;
		}
	}
}
?>