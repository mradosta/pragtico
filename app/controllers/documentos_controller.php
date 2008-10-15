<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los documentos modelo del sistema.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.controllers
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a los documentos modelo del sistema.
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class DocumentosController extends AppController {

/**
 * Permite la generacion de un documento a partir de una plantilla rtf.
 */
	function generar() {
		if(empty($this->data['Documento']['id'])) {
			/**
			* Me aseguro de cargar el model que utilizare, y lo llevo a recursive level 2, de modo de contar con la
			* mayor cantidad de datos posible.
			*/
			App::import("model", $this->params['named']['model']);
			
			$model = new $this->params['named']['model']();
			if(!empty($this->params['named']['contain'])) {
				$this->set("contain", $this->params['named']['contain']);
				$model->contain(unserialize(str_replace("**", "\"", $this->params['named']['contain'])));
			}
			$data = $model->findById($this->params['named']['id']);
			ob_start();
			debug($this->__removerFrameworkData($data), true, false);
			$out = ob_get_clean();
			$out = str_replace("Array", "", $out);
			$documentos = $this->Documento->find("list", array("conditions"=>array("Documento.model"=>$this->params['named']['model']), "fields"=>array("Documento.id", "Documento.nombre")));
			$this->set("documentos", $documentos);
			$this->set("data", $out);
			$this->set("model", $this->params['named']['model']);
			$this->set("id", $this->params['named']['id']);
		}
		else {
			/**
			* Me aseguro de cargar el model que utilizare, y lo llevo a recursive level 2, de modo de contar con la
			* mayor cantidad de datos posible.
			*/
			App::import("model", $this->data['Extra']['model']);
			$model = new $this->data['Extra']['model']();
			if(!empty($this->data['Extra']['contain'])) {
				$model->contain(unserialize(str_replace("**", "\"", $this->data['Extra']['contain'])));
			}
			$data = $model->findById($this->data['Extra']['id']);
			$documento = $this->Documento->findById($this->data['Documento']['id']);
			$reemplazarTexto['patrones'] = unserialize($documento['Documento']['patrones']);
			$reemplazarTexto['reemplazos'] = $data;
			
			$archivo['data'] = $documento['Documento']['file_data'];
			$archivo['type'] = $documento['Documento']['file_type'];
			$archivo['name'] = $this->Util->getFileName($documento['Documento']['nombre'], $documento['Documento']['file_type']);

			$this->set("reemplazarTexto", $reemplazarTexto);
			$this->set("archivo", $archivo);
			$this->render("../elements/descargar", "descargar");
		}
	}


/**
 * Quita los campos propios del framework, ya que el usuario no tiene porque verlos, lo confunde.
 */
	function __removerFrameworkData($array) {
		if (!is_array($array)) {
			$array;
		}

		$removeKey = array("user_id", "role_id", "group_id", "permissions", "write", "delete", "created", "modified", "file_data", "file_size", "file_type");
		foreach($array as $k=>$v) {
			if(is_array($v)) {
				foreach($v as $k1=>$v1) {
					if(is_array($v1)) {
						foreach($v1 as $k2=>$v2) {
							if(is_array($v2)) {
								foreach($v2 as $k3=>$v3) {
									if(in_array($k3, $removeKey)) {
										unset($array[$k][$k1][$k2][$k3]);
									}
								}
							}
							elseif(in_array($k2, $removeKey)) {
								unset($array[$k][$k1][$k2]);
							}
						}
					}
					elseif(in_array($k1, $removeKey)) {
						unset($array[$k][$k1]);
					}
				}
			}
			elseif(in_array($k, $removeKey)) {
				unset($array[$k]);
			}
		}
		return $array;
	}


/**
 * Si lo subio correctamente, lo graba en la session para luego poder hacer un preview.
 */
	function __getFile() {
		if(!empty($this->data['Documento']['archivo'])) {
			if(isset($this->data['Documento']['archivo']['error']) && $this->data['Documento']['archivo']['error'] === 0) {
				$contenido = fread(fopen($this->data['Documento']['archivo']['tmp_name'], "r"), $this->data['Documento']['archivo']['size']);
				$archivo['file_size'] = $this->data['Documento']['archivo']['size'];
				$archivo['file_type'] = $this->data['Documento']['archivo']['type'];
				$archivo['file_data'] = $contenido;
				$this->Session->write("tmpArchivo", $archivo);
				return true;
			}
			else {
				$this->Documento->dbError['errorDescripcion'] = "El archivo no se subio correctamente. Intentelo nuevamente.";
				return false;
			}
		}
	}


/**
 * El metodo add debe primero buscar los patrones dentro del documento que proporciono el usuario, luego
 * presentara un preview de los patrones encontrados, y si el usuario lo confirma, se graba.
 */
	function add() {
		if($this->__getFile() && !isset($this->data['Form']['confirmar'])) {
			$contenido = file_get_contents($this->data['Documento']['archivo']['tmp_name']);
			if(preg_match_all("/#\*(.+)\*#/U", $contenido, $matches)) {
				$this->data['Documento']['patrones'] = serialize($matches[1]);
				$this->set("patrones", $matches[1]);
			}
			else {
				$this->Session->setFlash("No se encontraron patrones en el archivo origen. Verifiquelo y reintentelo nuevamente.", "error");
			}
		}
		else {
			if($this->Session->check("tmpArchivo")) {
				$archivo = $this->Session->read("tmpArchivo");
				$this->Session->del("tmpArchivo");
				$this->data['Documento']['file_type'] = $archivo['file_type'];
				$this->data['Documento']['file_size'] = $archivo['file_size'];
				$this->data['Documento']['file_data'] = $archivo['file_data'];
				unset($this->data['Form']['confirmar']);
				parent::add();
			}
		}
		$modelsTmp = Configure::listObjects('model');
		sort($modelsTmp);
		foreach($modelsTmp as $v) {
			$models[$v] = $v;
		}
		$this->set("models", $models);
	}


/**
 * Permite descargar el archivo del documento.
 */
	function descargar($id) {
		$documento = $this->Documento->findById($id);
		$archivo['data'] = $documento['Documento']['file_data'];
		$archivo['size'] = $documento['Documento']['file_size'];
		$archivo['type'] = $documento['Documento']['file_type'];
		$archivo['name'] = $this->Util->getFileName($documento['Documento']['nombre'], $documento['Documento']['file_type']);
		$this->set("archivo", $archivo);
		$this->render("../elements/descargar", "descargar");
	}	
}
?>