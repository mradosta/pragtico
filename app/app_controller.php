<?php
/* SVN FILE: $Id: app_controller.php 5118 2007-05-18 17:19:53Z phpnut $ */
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Short description for class.
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		cake
 * @subpackage	cake.app
 */
class AppController extends Controller {

	//var $helpers = array("Html", "Formato", "Javascript", "Ajax", "Formulario", "Paginador", "Pdf", "ExcelWriter");
	var $helpers = array("Form", "Formato", "Formulario", "Paginador", "Asset");
	//var $helpers = array("Formato", "Formulario", "Paginador");
	//var $components = array('Paginador', 'RequestHandler', 'History', 'Util', 'Auth');
	var $components = array('Paginador', 'RequestHandler', 'History', 'Util');
	var $paginate = array('limit' => 15);
	var $uses = array("Menu", "Usuario");


	//var $actions

/*
<?php $band_list_options = array(
    'conditions' => $band_conditions,
    'fields' => array('Band.id', 'Band.pretty_field')
);
$this->set('bands', $this->Band->find('list', $band_list_options));?>

<?php Set::combine($posts, '{n}.Post.id', array('%s %s', '{n}.Post.title', '{n}.Post.user_id'));?>
*/



/**
* listable(),
*
*/
	function listable() {
		if($this->RequestHandler->isAjax()) {
			$this->RequestHandler->renderAs($this, 'ajax');
		}
			
		/**
		* Puedo tener cualquier condicion de las que soporta el metodo find.
		*/
		$opcionesValidas = array("displayField", "groupField", "conditions", "fields", "order", "limit", "recursive", "group", "contain", "model");
		$opcionesValidasArray = array("displayField", "groupField", "conditions", "fields", "order", "contain");
		foreach($opcionesValidas as $opcionValida) {
			if(!empty($this->params['named'][$opcionValida])) {
				if(in_array($opcionValida, $opcionesValidasArray)) {
					$condiciones[$opcionValida] = unserialize($this->params['named'][$opcionValida]);
				}
				else {
					$condiciones[$opcionValida] = $this->params['named'][$opcionValida];
				}
			}
		}
		if(!empty($condiciones['model'])) {
			if (!ClassRegistry::isKeySet($condiciones['model'])) {
				App::import("model", $condiciones['model']);
			}
			$model = new $condiciones['model']();
			unset($condiciones['model']);
		}
		else {
			$model = $this->{$this->modelClass};
		}
		
		if(empty($condiciones['displayField'])) {
			$displayFields = array($model->displayField);
		}
		else {
			$displayFields = $condiciones['displayField'];
			unset($condiciones['displayField']);
		}

		if(!empty($condiciones['groupField'][0])) {
			$group = $condiciones['groupField'][0];
			unset($condiciones['groupField']);
		}

		foreach($displayFields as $displayField) {
			$display[] = "{n}." . $displayField;
			$exp[] = "%s";
		}
		array_unshift($display, implode(" - ", $exp));
		$data = $model->find("all", $condiciones);
		if(isset($group)) {
			$data = $this->Util->combine($data, "{n}." . $model->name . "." . $model->primaryKey, $display, "{n}." . $group);
		}
		else {
			$data = $this->Util->combine($data, "{n}." . $model->name . "." . $model->primaryKey, $display);
		}
		return $data;
	}

/**
* Index.
*/    
	function index() {
		$this->__filasPorPagina();

		/**
		 * Me aseguro de no perder ningun parametro que venga como un post dentro edl Formulario.
		 * Lo en vio nuevamente como un parametro.
		 */
		if(!empty($this->data['Formulario'])) {
			foreach($this->data['Formulario'] as $k=>$v) {
				$this->params['named'][$k] = $v;
			}
		}
		$this->layout = "default";
		if(!empty($this->params['isAjax'])
			|| (isset($this->params['named']['layout'])
				&& $this->params['named']['layout'] == "lov")) {
			$this->layout = "lov";
		}

		/**
		* Seccion propia del index
		*/
		
		/**
		* Puede haber un modificador al comportamiento estandar setaeado en el model.
		*/
		if(isset($this->{$this->modelClass}->modificadores[$this->action]['contain'])) {
			$this->{$this->modelClass}->contain($this->{$this->modelClass}->modificadores[$this->action]['contain']);
		}
		
		$resultados = $this->Paginador->paginar();
        $this->set('registros', $resultados['registros']);
        $this->set('totales', $resultados['totales']);
	}


/**
 * Add.
 */
	function add() {
		if(!empty($this->data['Form']['accion'])) {
			if (in_array($this->data['Form']['accion'], array("grabar", "duplicar"))) {
				$data = $this->data;

				/**
				* En caso de que se trate de un duplicar, debo sacar el valor de la calve primaria de la tabla,
				* sino me hara un update en lugar de un isert.
				*/
				$goBack = 1;
				if($this->data['Form']['accion'] == "duplicar") {
					unset($data[$this->modelClass][$this->{$this->modelClass}->primaryKey]);
					$goBack = 2;
				}
				unset($data['Form']);
				unset($data['Bar']);
				if($this->{$this->modelClass}->create($data) && $this->{$this->modelClass}->validates()) {
					if($this->{$this->modelClass}->save($data)) {
						$this->Session->setFlash("El nuevo registro se guardo correctamente.", "ok", array("warnings"=>$this->{$this->modelClass}->getWarning()));
						if((isset($this->data['Form']['volverAInsertar']) && $this->data['Form']['volverAInsertar'] == "1")) {
							$this->render("add");
						}
						else{
							if(isset($this->data['Form']['params'])) {
								$this->__setearParams(unserialize($this->data['Form']['params']));
							}
							$this->History->goBack($goBack);
						}
					}
					else {
						$tmp = $this->{$this->modelClass}->validationErrors;
						unset($this->{$this->modelClass}->validationErrors[$this->modelClass]);
						if(!empty($tmp[$this->modelClass])) {
							$this->{$this->modelClass}->validationErrors[0] = $tmp[$this->modelClass];
						}
						$dbError = $this->{$this->modelClass}->getError();
						$this->Session->setFlash("El nuevo registro no pudo guardarse.", "error", array("errores"=>$dbError));
					}
				}
				else {
					$this->set('dbError', $this->{$this->modelClass}->getError());
				}
			}
			elseif($this->data['Form']['accion'] == "cancelar") {
    			$this->History->goBack(1);
			}
		}
		else {
			/**
			* Puede haber un modificador al comportamiento estandar setaeado en el model.
			* En este caso se refiere a establecer los valores por defecto.
			* En caso de ser funciones, por seguridad, deben validarse con la expresion regular ya que se ejecutan
			* mediante eval.
			*/
			if(isset($this->{$this->modelClass}->modificadores[$this->action]['valoresDefault'])) {
				foreach($this->{$this->modelClass}->modificadores[$this->action]['valoresDefault'] as $campo=>$valoresDefault) {
					if(is_string($valoresDefault) && eregi("date(.*)", $valoresDefault)) {
						$this->data[$this->modelClass][$campo] = eval("return " . $valoresDefault . ";");
					}
					else {
						$this->data[$this->modelClass][$campo] = $valoresDefault;
					}
				}
			}
		}
		/**
		* Si hay parametros, me esta indicando que debo cargar un campo lov desde un desglose.
		*/
		if(!empty($this->passedArgs)) {
			$this->__setearParams($this->passedArgs);
		}

		/**
		* Identifico que viene de un reques ajax (un detalle de una tabla fromTo, por ejemplo)
		if(!empty($this->params['isAjax']) && $this->params['isAjax'] == "1") {
			$this->set('variablesForm', array("isAjax"=>"1"));
			if($this->Session->check($this->name . "." . $this->action)) {
				$sesion = $this->Session->read($this->name . "." . $this->action);
			}
		}
		*/
		
	}


/**
* __setearParams
*
* Carga datos para poder pintar los valores de la lov.
*/
function __setearParams($params) {
	foreach($params as $k=>$v) {
		list($model, $field) = explode(".", $k);
		$this->data[$model][$field] = $v;
		$modelAsociado = str_replace(" ", "", inflector::humanize(str_replace("_id", "", $field)));
		$resultado = $this->{$model}->{$modelAsociado}->find(array($modelAsociado . "." . $this->{$model}->{$modelAsociado}->primaryKey => $v));
		if(!empty($resultado)) {
			$this->data[$modelAsociado] = $resultado;
		}
	}
}


/**
* Me setea la cantidad de filas por pagina que debo pintar.
*/
	function __filasPorPagina() {
		/**
		* Verifico cuantas filas por pagina debo pintar.
		*/
		if(!empty($this->params['named']['filas_por_pagina']) && is_numeric($this->params['named']['filas_por_pagina'])) {
			/**
			* Dejo predeterminado para esta sesion el cambio.
			*/
			$usuario = $this->Session->read("__Usuario");
			$usuario['Usuario']['preferencias']['filas_por_pagina'] = $this->params['named']['filas_por_pagina'];
			$this->Session->write("__Usuario", $usuario);
			$opciones = array('limit' => $this->params['named']['filas_por_pagina']);
		}
		else {
			$opciones = array('limit' => $this->Util->traerPreferencia("filas_por_pagina"));
		}
		$this->paginate = am($this->paginate, $opciones);
	}



/**
 * edit.
 * Si viene el parametro id, se refiere a un unico registro,
 * si viene seteado seleccion multiple, recupera multiple registros.
 */
function edit($id=null) {
	if(!empty($id)) {
		$ids[] = $id;
	}
	else {
		$ids = $this->Util->extraerIds($this->data['seleccionMultiple']);
	}
	if(!empty($ids)) {
		
		/**
		* Puede haber un modificador al comportamiento estandar setaeado en el model.
		*/
		if(isset($this->{$this->modelClass}->modificadores[$this->action]['contain'])) {
			$this->{$this->modelClass}->contain($this->{$this->modelClass}->modificadores[$this->action]['contain']);
		}

		//$this->{$this->modelClass}->contain("Trabajador", "Empleador", "Area", "ConveniosCategoria.Convenio");
		
		$this->data = $this->{$this->modelClass}->find("all", array("acceso"=>"write", "conditions"=>array($this->modelClass . ".id"=>$ids)));
		//$this->data = $this->{$this->modelClass}->find("all", array("acceso"=>"write", "contain"=>array("Trabajador", "Empleador", "Area", "ConveniosCategoria.Convenio"), "conditions"=>array($this->modelClass . ".id"=>$ids)));
		
		//d($this->data);
		/*
		if(empty($this->data)) {
			$this->Session->setFlash(null, "permisos");
			$this->History->goBack();
		}
		else {
			$this->render("add");
		}
		*/
		$this->render("add");
	}
}


/**
 * saveMultiple.
 * Se encarga ed guardar datos editados.
 * Maneja arrays de data, es decir, puede guardar data de edicion multiple como simple.
 * Valida y guarda master/detail.
 */
function saveMultiple() {
	$this->action = "edit";

	if(!empty($this->data['Form']['accion'])) {
		if ($this->data['Form']['accion'] == "grabar") {
			$invalidFields = array();
			$c = 0;

			/**
			* Saco lo que no tengo que grabar.
			* En form, tenfo informacion que mande desde la vista.
			* En Bar es informacion temporal que neesita el control relacionado.
			*/
			unset($this->data['Form']);
			unset($this->data['Bar']);
			/**
			* Me aseguro de trabajar siempre con un array de data.
			*/
			if(empty($this->data[0])) {
				$this->data = array($this->data);
			}
			foreach($this->data as $k=>$v) {
				foreach($v as $model=>$datos) {
					$detailErrors = false;
					if($model == $this->modelClass && isset($datos['id'])) {
						$ids[] = $datos['id'];
					}
					else {
						foreach($datos as $kDetail=>$datosDetail) {
							$this->{$this->modelClass}->{$model}->create($datosDetail);
							if(!$this->{$this->modelClass}->{$model}->validates()) {
								$invalidFields[$k][$model][$kDetail] = $this->{$this->modelClass}->{$model}->validationErrors;
								$detailErrors = true;
							}
						}
					}
				}

				/**
				* En el caso de un master/detail, solo grabo cuando valide todos los detail y el master.
				*/
				if($this->{$this->modelClass}->create($v) && $this->{$this->modelClass}->validates($v)) {
					if($detailErrors === false) {
						if($this->{$this->modelClass}->save($v)) {
							$c++;
						}
					}
				}
				else {
					$invalidFields[$k][$this->modelClass] = $this->{$this->modelClass}->validationErrors;
				}
			}
			
			$dbError = $this->{$this->modelClass}->getError();
			
			/**
			* En base al/los errores que pueden haber determino que mensaje mostrar.
			*/
			if (empty($invalidFields) && empty($dbError)) {
				if($c == 1) {
					$mensaje = "El registro se guardo correctamente.";
				}
				else {
					$mensaje = "Se guardaron correctamente ". $c . " de " . count($this->data) . " registros";
				}
				$this->Session->setFlash($mensaje, "ok", array("warnings"=>$this->{$this->modelClass}->getWarning()));
    			$this->History->goBack(2);
			}
			else {
				/**
				* Puede haber un modificador al comportamiento estandar setaeado en el model.
				*/
				if(isset($this->{$this->modelClass}->modificadores[$this->action]['contain'])) {
					$this->{$this->modelClass}->contain($this->{$this->modelClass}->modificadores[$this->action]['contain']);
				}

				/**
				* Debo recuperar nuevamente los datos porque los necesito en los controler relacionados (Lov, relacionado).
				* Los que ya tengo, los dejo como estaban, porque se debe a que no validaron.
				*/
				$data = $this->data;
				$this->data = $this->{$this->modelClass}->find("all", array("acceso"=>"write", "conditions"=>array($this->modelClass . ".id"=>$ids)));
				foreach($data as $k=>$v) {
					foreach($v as $model=>$datos) {
						$this->data[$k][$model] = $datos;
					}
				}
			
				/**
				* Pongo nuevamente los errores de validacion en el model de manera que
				* puedan ser pintados en la vista.
				*/
				$this->{$this->modelClass}->validationErrors = $invalidFields;
				//$this->set('dbError', $dbError);
				$this->Session->setFlash("El nuevo registro no pudo guardarse.", "error", array("errores"=>$dbError));				
				//d($dbError);
			}
			$this->render("add");
		}
		elseif($this->data['Form']['accion'] == "cancelar") {
			//d($this->History->show());
   			$this->History->goBack(1);
		}
	}
}


/**
 * Delete.
 */
   	function delete($id = false) {
        if ($id && is_numeric($id)) {
			if ($this->{$this->modelClass}->delete($id)) {
				$this->Session->setFlash("El registro se elimino correctamente.", "ok", array("warnings"=>$this->{$this->modelClass}->getWarning()));
			}
			else {
				/**
				* Si no se pudo borrar y no hay errores (no fue a causa de un error), significa que no se pudo borrar
				* por una cuestion de permisos.
				*/
				$errores = $this->{$this->modelClass}->getError();
				if(empty($errores)) {
					$this->Session->setFlash(null, 'permisos');
				}
				else {
					$this->Session->setFlash("No fue posible eliminar el registro.", "error", array("errores"=>$errores));
				}			
			}
        }
		$this->History->goBack(1);
	}

/**
 * DeleteMultiple.
 */
   function deleteMultiple() {
   
		$ids = $this->Util->extraerIds($this->data['seleccionMultiple']);
		if(!empty($ids)) {
			if ($this->{$this->modelClass}->deleteAll(array($this->modelClass . "." . $this->{$this->modelClass}->primaryKey => $ids))) {
				$cantidad = count($ids);
				if($cantidad == 1) {
					$mensaje = "Se elimino " . $cantidad . " registro correctamente.";
				}
				else {
					$mensaje = "Se eliminaron " . $cantidad . " registros correctamente.";
				}
				$this->Session->setFlash($mensaje, "ok");
			}
			else {
				/**
				* Si no se pudo borrar y no hay errores (no fue a causa de un error), significa que no se pudo borrar
				* por una cuestion de permisos.
				*/
				$errores = $this->{$this->modelClass}->getError();
				if(empty($errores)) {
					$this->Session->setFlash(null, 'permisos');
				}
				else {
					$this->Session->setFlash('No fue posible eliminar los registro solicitados.', 'error', array("errores"=>$errores));
				}
			}
		}
		$this->History->goBack(1);
	}


/**
 * Setea el grupo por defecto y lo guarda en la sesion.
 */
	function setear_grupo_default($id) {
		$usuario = $this->Session->read("__Usuario");
		$usuario['Usuario']['preferencias']['grupo_default_id'] = $id;
		$this->Session->write("__Usuario", $usuario);
		$this->History->goBack();
	}
	
/**
 * Permite agregar o quitar un grupo a los grupos preseleccionados del usuario.
 */
	function cambiar_grupo_activo() {
		if(!empty($this->params['named']['accion']) && !empty($this->params['named']['grupo_id']) && is_numeric($this->params['named']['grupo_id'])) {
			$usuario = $this->Session->read("__Usuario");
			if($this->params['named']['accion'] == "agregar") {
				$usuario['Usuario']['preferencias']['grupos_seleccionados'] = $usuario['Usuario']['preferencias']['grupos_seleccionados'] + $this->params['named']['grupo_id'];
			}
			elseif($this->params['named']['accion'] == "quitar") {
				$usuario['Usuario']['preferencias']['grupos_seleccionados'] = $usuario['Usuario']['preferencias']['grupos_seleccionados'] - $this->params['named']['grupo_id'];
			}
			$this->Session->write("__Usuario", $usuario);
		}
		$this->History->goBack();
	}

/**
 * permisos.
 * Muestra via desglose los permisos de un registro y permite via ajax la modificacion de los mismos.
 */
	function permisos($id) {
		$this->{$this->modelClass}->recursive = -1;
		$registro = $this->{$this->modelClass}->findById($id);
		
		if(!empty($this->params['named']['quitarGrupo'])) {
			$save[$this->modelClass]['group_id'] = (int)$registro[$this->modelClass]['group_id'] - (int)$this->params['named']['quitarGrupo'];
		}
		elseif(!empty($this->params['named']['agregarGrupo'])) {
			$save[$this->modelClass]['group_id'] = (int)$registro[$this->modelClass]['group_id'] + (int)$this->params['named']['agregarGrupo'];
		}
		elseif(!empty($this->params['named']['quitarRol'])) {
			$save[$this->modelClass]['role_id'] = (int)$registro[$this->modelClass]['role_id'] - (int)$this->params['named']['quitarRol'];
		}
		elseif(!empty($this->params['named']['agregarRol'])) {
			$save[$this->modelClass]['role_id'] = (int)$registro[$this->modelClass]['role_id'] + (int)$this->params['named']['agregarRol'];
		}
		elseif(!empty($this->params['named']['accion'])) {
			switch($this->params['named']['accion']) {
				case "pt": //permitir todo.
					$save[$this->modelClass]['permissions'] = 511;
					break;
				case "dt": //denegar todo.
					$save[$this->modelClass]['permissions'] = 0;
					break;
				case "plt": //permitir leer todo.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 292;
					break;
				case "dlt": //denegar leer todo.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~292;
					break;
				case "pet": //permitir escribir todo.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 146;
					break;
				case "det": //denegar escribir todo.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~146;
					break;
				case "pdt": //permitir eliminar todo.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 73;
					break;
				case "ddt": //denegar eliminar todo.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~73;
					break;
				case "ptd": //permitir todo dueno.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 448;
					break;
				case "dtd": //denegar todo dueno.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~448;
					break;
				case "ptg": //permitir todo grupo.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 56;
					break;
				case "dtg": //denegar todo grupo.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~56;
					break;
				case "pto": //permitir todo otros.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 7;
					break;
				case "dto": //denegar todo otros.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~7;
					break;
				case "pdl": //permitir dueno lectura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 256;
					break;
				case "ddl": //denegar dueno lectura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~256;
					break;
				case "pde": //permitir dueno escritura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 128;
					break;
				case "dde": //denegar dueno escritura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~128;
					break;
				case "pdd": //permitir dueno eliminar.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 64;
					break;
				case "ddd": //denegar dueno eliminar.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~64;
					break;
				case "pgl": //permitir grupo lectura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 32;
					break;
				case "dgl": //denegar grupo lectura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~32;
					break;
				case "pge": //permitir grupo escritura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 16;
					break;
				case "dge": //denegar grupo escritura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~16;
					break;
				case "pgd": //permitir grupo eliminar.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 8;
					break;
				case "dgd": //denegar grupo eliminar.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~8;
					break;
				case "pol": //permitir otros lectura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 4;
					break;
				case "dol": //denegar otros lectura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~4;
					break;
				case "poe": //permitir otros escritura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 2;
					break;
				case "doe": //denegar otros escritura.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~2;
					break;
				case "pod": //permitir otros eliminar.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] | 1;
					break;
				case "dod": //denegar otros eliminar.
					$save[$this->modelClass]['permissions'] = $registro[$this->modelClass]['permissions'] & ~1;
					break;
			}
		}
		
		if(!empty($save)) {
			$save[$this->modelClass][$this->{$this->modelClass}->primaryKey] = $id;
			/**
			* Si pudo grabar con exito, los permisos de este registro cambiaron, entonces lo cambio.
			*/
			if($this->{$this->modelClass}->save($save, false)) {
				if(isset($save[$this->modelClass]['group_id'])) {
					$registro[$this->modelClass]['group_id'] = $save[$this->modelClass]['group_id'];
				}
				if(isset($save[$this->modelClass]['role_id'])) {
					$registro[$this->modelClass]['role_id'] = $save[$this->modelClass]['role_id'];
				}
				if(isset($save[$this->modelClass]['permissions'])) {
					$registro[$this->modelClass]['permissions'] = $save[$this->modelClass]['permissions'];
				}
			}
		}

		
		/**
		* Busco el usuario, grupo/s y rol/es a los que pertenece el registro.
		*/
		$modelUsuario = new Usuario();
		$modelUsuario->recursive = -1;
		$usuario = $modelUsuario->find("first", array("checkSecurity"=>false, "conditions"=>array("Usuario.id"=>$registro[$this->modelClass]['user_id'])));
		$grupos = $modelUsuario->Grupo->find("all", array(	"checkSecurity"	=> false,
															"recursive"		=> -1,
															"conditions"	=> array(
																"(Grupo.id) & " . $registro[$this->modelClass]['group_id'] . " >" => 0)));
		$roles = $modelUsuario->Rol->find("all", array(	"checkSecurity"	=> false,
															"recursive"		=> -1,
															"conditions"	=> array(
																"(Rol.id) & " . $registro[$this->modelClass]['role_id'] . " >" => 0)));

		/**
		* Busco los datos del usuario que tengo en la session (el que esta actualmente logueado).
		*/
		$usuarioSession = $this->Session->read("__Usuario");

		/**
		* Solo el root, el dueno o alguien con el rol administradores del grupo dueno del registro
		* podra cambiar los permisos.
		*/
		$registro['puedeCambiarPermisos'] = false;
		if((int)$usuarioSession['Usuario']['id'] === 1
			|| $registro[$this->modelClass]['user_id'] === $usuarioSession['Usuario']['id']
			|| ((int)$usuarioSession['Usuario']['roles'] & 1 === 1
				&& (int)$registro[$this->modelClass]['group_id'] & $usuarioSession['Usuario']['grupos'] > 0)) {
			$registro['puedeCambiarPermisos'] = true;
			
			/**
			* A los grupos del registro, agrego tambien los del usuario, asi puede agregarlos si este lo desea.
			* siempre y cuando pueda cambiar los permisos.
			*/
			$gruposId = Set::extract("/Grupo/id", $grupos);
			foreach($usuarioSession['Grupo'] as $v) {
				if(!in_array($v['id'], $gruposId)) {
					$grupos[]['Grupo'] = $v;
				}
			}
			foreach($grupos as $k=>$grupo) {
				if($registro['puedeCambiarPermisos'] === true) {
					if(((int)$registro[$this->modelClass]['group_id'] & (int)$grupo['Grupo']['id']) > 0) {
						$grupos[$k]['Grupo']['posible_accion'] = "quitar";
					}
					else {
						$grupos[$k]['Grupo']['posible_accion'] = "agregar";
					}
				}
			}

			/**
			* A los roles del registro, agrego tambien los del usuario, asi puede agregarlos si este lo desea.
			* siempre y cuando pueda cambiar los permisos.
			*/
			$rolesId = Set::extract("/Rol/id", $roles);
			foreach($usuarioSession['Rol'] as $v) {
				if(!in_array($v['id'], $rolesId)) {
					$roles[]['Rol'] = $v;
				}
			}
			foreach($roles as $k=>$rol) {
				if($registro['puedeCambiarPermisos'] === true) {
					if(((int)$registro[$this->modelClass]['role_id'] & (int)$rol['Rol']['id']) > 0) {
						$roles[$k]['Rol']['posible_accion'] = "quitar";
					}
					else {
						$roles[$k]['Rol']['posible_accion'] = "agregar";
					}
				}
			}
		}
		
		$permisos = str_split(str_pad(base_convert($registro[$this->modelClass]['permissions'], 10, 2), 9, "0", STR_PAD_LEFT));
		foreach($permisos as $k=>$v) {
			switch($k) {
				case 0:
					$pd['leer'] = $v;
					break;
				case 1:
					$pd['escribir'] = $v;
					break;
				case 2:
					$pd['eliminar'] = $v;
					break;
				case 3:
					$pg['leer'] = $v;
					break;
				case 4:
					$pg['escribir'] = $v;
					break;
				case 5:
					$pg['eliminar'] = $v;
					break;
				case 6:
					$po['leer'] = $v;
					break;
				case 7:
					$po['escribir'] = $v;
					break;
				case 8:
					$po['eliminar'] = $v;
					break;
			}
		}
		
		$registro['Usuario'] = $usuario['Usuario'];
		$registro['Grupo'] = $grupos;
		$registro['Rol'] = $roles;
		$registro['Usuario']['permisos'] = $pd;
		$registro['Grupos']['permisos'] = $pg;
		$registro['Otros']['permisos'] = $po;

		$this->set("registro", $registro);
		$this->render(".." . DS . "elements" . DS . "desgloses" . DS . "permisos");
		
	}


	
/**
* Imprimir.
*/    
	function imprimir() {
	}

	
/**
 * Seteos.
 * Metodo que debe ser sobreescrito en cada controlador si se desea hacer seteos especificos para la vista.
 * Realiza los seteos especificos (valores por defecto) al agregar y/o editar.
 */
	function __seteosDeprecated() {
	}
	

/**
 * afterAdd.
 * Metodo que debe ser sobreescrito en cada controlador si se desea hacer acciones posteriores al ingreso CORRECTO
 * de un nuevo registro.
	function afterAdd() {
	}
 */
	
	
/**
 * Antes de pintar la vista, en caso de que sea un index, add o edit, llamo al metodo seteos
 */
	function beforeRender() {
		/**
		* Ante un request AJAX debo quitar el debug, sino no funcionara (json, por ejemplo,
		* porque le llegara el tiempo de render que cakephp agrega solo.
		*/
		//if($this->params['isAjax']) {
		//	Configure::write('debug', 0);
		//}
		
		//if($this->action == "index" || $this->action == "add" || $this->action == "edit") {
		//	$this->__seteos();
		//}
	}

   
      function isAuthorized_deprecated() {
  
          return true;
  
      }

/**
 * Antes de ejecutar una action controlo seguridad.
 * Si la session caduco, lo mando nuevamente al login.
 */
    function beforeFilter() {
    	//if(!$this->Session->check("__Usuario")) {
    	//	$this->redirect("../usuarios/login");
    	//}
    	//else 
		//	return true;
		//return true;
		/**
		* En accionesWhiteList llevo las acciones que no deben chquearse la seguridad.
		*/
		if(!$this->Session->check("__Seguridad.accionesWhiteList")) {
			App::import("model", "Accion");
			$Accion = new Accion();
			$data = $Accion->find("all", array("checkSecurity"=>false, "contain"=>"Controlador", "conditions"=>array("Accion.seguridad"=>"No")));
			$accionesWhiteList = array();
			foreach($data as $v) {
				$accionesWhiteList[] = $v['Controlador']['nombre'] . "." . $v['Accion']['nombre'];
			}
			$this->Session->write("__Seguridad.accionesWhiteList", $accionesWhiteList);
		}
		else {
			$accionesWhiteList = $this->Session->read("__Seguridad.accionesWhiteList");
		}
		
		if(in_array($this->name . "." . $this->action, $accionesWhiteList)) {
			return true;
		}
    	elseif(!$this->Session->check("__Usuario")) {
    		$this->redirect("../usuarios/login");
    	}
    	/*
          $this->Auth->loginAction = array('controller' => 'usuarios', 'action' => 'login');
   
          $this->Auth->loginRedirect = array('controller' => 'relaciones', 'action' => 'index');
   
          $this->Auth->allow('index');
   
          $this->Auth->authorize = 'controller';
    		*/	
		/**
		 * Agrego soporte para que retorne json.
		 */
		$this->RequestHandler->setContent('json', 'text/x-json');
    	
    	return true;
    }


/**
 * afterFilter.
 */
	function afterFilter() {
		/**
		* Si es un request ajax, posiblemente sea un desglose.
		* Guardo en la session los desgloses que estan abiertos.
		*/
		if(isset($this->params['isAjax']) && isset($this->params['pass'][0]) && is_numeric($this->params['pass'][0])) {
			if($this->Session->check("desgloses")) {
				$desgloses = $this->Session->read("desgloses");
			}
			$id = $this->name . "-" . $this->action . "-" . $this->params['pass'][0];
			$desgloses[$id] = 1;
			$this->Session->write("desgloses", $desgloses);
		}
	}


/**
 * limpiar_busquedas.
 * Mediante un request ajax desde javascript borro de la session TODOS los filtros que esten seteados.
 * Luego, con el mismo js recargo la pagina y dara el efecto de limpiar las busquedas.
 */
	function limpiar_busquedas() {
		$this->Session->del("filtros");
		$this->autoRender = false;
	}


/**
 * cerrarDesgloses.
 * Mediante un request ajax desde javascript borro de la session TODOS los desgloses que esten abiertos.
 * Luego, con el mismo js recargo la pagina y dara el efecto de cerrar desgloses.
 */
	function cerrar_desgloses() {
		$this->Session->del("desgloses");
		$this->autoRender = false;
	}


/**
 * quitarDesglose.
 * Saca de la session los desgloses que han sido cerrados.
 */
	function quitarDesglose($nombreDesglose) {
		/**
		* Saco de la session los desgloses que han sido cerrados.
		*/
		if($this->Session->check("desgloses")) {
			$desgloses = $this->Session->read("desgloses");
			unset($desgloses[$nombreDesglose]);
			$this->Session->write("desgloses", $desgloses);
		}
		exit();
	}
}
?>