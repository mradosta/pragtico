<?php
/**
 * History Component.
 * Se encarga resolver la navegacion para atras.
 * 
 * Me baso parcialmente en el trabajo de Studio Sipak
 * website: http://webdesign.janenanneriet.nl
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
 * Tamanio maximo del array que contendra el historial de navegacion.
 */
define('MAX_HISTORY', 10);

/**
 * La clase encapsula la logica necesaria para resolver la navegacion.
 *
 * @package     pragtico
 * @subpackage  app.controllers.components
 */
class HistoryComponent extends Object {
    
	
/**
 * Guarda las paginas por las que fue pasando.
 *
 * @var array
 * @access private
 */
	private $__historia = array();
	
	
/**
 * Indica si ya fue instanciado el component.
 *
 * @var boolean
 * @access private
 */
    var $__started= false;
	
	
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
    
        /**
        * Prevengo que entre mas de una vez.
        */
        if (!$this->__started) {
            $this->__started = true;
            $this->controller = $controller;
			$this->_addUrl();
        }
    }

	
/**
 * Vuelve a una pagina visitada anterior.
 *
 * @param integer $pos Cuantas paginas atras quiero volverme.
 * @return void
 * @access public
 */
	
    function goBack($pos = 1) {
        if (is_numeric($pos)) {
	        $history = array();
	        foreach ($this->__historia as $k=>$v) {
	        	if ($v !== "/fake/url/do_not_add") {
	        		$history[] = $v;
	        	}
	        }
	        $this->__historia = $history;
	        $pos = count($this->__historia) - $pos;
			//d($pos);
	        
        	if (isset($this->__historia[$pos])){
				//file_put_contents("/tmp/historia.txt", "\n\n=========================", FILE_APPEND);
				//file_put_contents("/tmp/historia.txt", "\nBACK A: " . $this->__historia[$pos] . "(" . $pos . ")\n\n", FILE_APPEND);
        		$this->controller->redirect($this->__historia[$pos], true);
        	}
        }
    }

	
/**
 * Me muesta la historia guardada.
 *
 * @return void
 * @access public
 */
    function show() {
        return $this->__historia;
    }

	
/**
 * Agrega una url al stack del history, que nunca se usara. 
 *
 * @return void
 * @access public
 */
    function addFakeUrl() {
		if (count($this->controller->Session->read('historia')) == MAX_HISTORY) {
			array_shift($this->__historia);
		}
		$this->__historia[] = "/fake/url/do_not_add";
		$this->controller->Session->write('historia', $this->__historia);
    }

    
/**
 * Agrega una url al stack del history,
 *
 * @return void
 * @access public
 */
	function _addUrl() {
		$url = $this->controller->referer();
    	if (empty($url)) {
    		return;
    	}
		
		$this->__historia = $this->controller->Session->read('historia');
		$cantidad = count($this->__historia);
		
		/**
		* Cuando abro una lov o un desglose ajax, o cancelo no guardo esto en el history.
		*/
		if (    (!empty($this->controller->data['Form']['accion']) && $this->controller->data['Form']['accion'] === "cancelar")
			|| (!empty($this->controller->params['named']['layout']) && $this->controller->params['named']['layout'] === "lov")
			|| (!empty($this->controller->data['Formulario']['layout']) && $this->controller->data['Formulario']['layout'] === "lov")
			|| (!empty($this->controller->data['Formulario']['layout']) && $this->controller->data['Formulario']['layout'] === "lov")
			|| (!empty($this->controller->params['action']) && ($this->controller->params['action'] === "listable" || $this->controller->params['action'] === "descargar"))
			|| (!empty($this->controller->params['isAjax']))) {
			return;
		}
		
		/**
		* Prevengo que se inserte en la history dos veces el mismo.
		* Por ejemplo, cuando un validate no valida, etc.
		*/
		if ($url != $this->__historia[$cantidad - 1]) {
			if ($cantidad == MAX_HISTORY) {
				array_shift($this->__historia);
			}
			$this->__historia[] = $url;
		}
		else {
			return;
		}
		
		$this->controller->Session->write('historia', $this->__historia);
    }

}
?>