<?php 
/**
 * Tamanio maximo del array que contendra el historial de navegacion.
 */
define('MAX_HISTORY', 10);
/*
 * HistoryComponent: User navigation history
 * @author: Studio Sipak
 * @website: http://webdesign.janenanneriet.nl
 * @license: MIT
 * @version: 0.1
 * */
class HistoryComponent extends Object
{
    var $__historia = array();
    var $started = false;
    var $controller;

    function startup(&$controller) {
    
        /**
        * Prevengo que entre mas de una vez.
        */
        if(!$this->started) {
        
            $this->started = true;
            $this->controller = $controller;
            
			$this->_addUrl();
        }
    }

    function goBack($pos = 1) {
        if(is_numeric($pos)) {
	        $history = array();
	        foreach($this->__historia as $k=>$v) {
	        	if($v !== "/fake/url/do_not_add") {
	        		$history[] = $v;
	        	}
	        }
	        $this->__historia = $history;
	        $pos = count($this->__historia) - $pos;
			//d($pos);
	        
        	if(isset($this->__historia[$pos])){
				//file_put_contents("/tmp/historia.txt", "\n\n=========================", FILE_APPEND);
				//file_put_contents("/tmp/historia.txt", "\nBACK A: " . $this->__historia[$pos] . "(" . $pos . ")\n\n", FILE_APPEND);
        		$this->controller->redirect($this->__historia[$pos], true);
        	}
        }
    }

    function show() {
        return $this->__historia;
    }

	/**
	* Agrega una url al stack del history, que nunca se usara.
	*/
    function addFakeUrl() {
		if(count($this->controller->Session->read('historia')) == MAX_HISTORY) {
			array_shift($this->__historia);
		}
		$this->__historia[] = "/fake/url/do_not_add";
		$this->controller->Session->write('historia', $this->__historia);
    }

    function _addUrl() {
		$url = $this->controller->referer();
		Debugger::output($url); 
    	if(empty($url)) {
    		return;
    	}
		$this->__historia = $this->controller->Session->read('historia');
		$cantidad = count($this->__historia);
		/**
		* Prevengo que se inserte en la history dos veces el mismo.
		* Por ejemplo, cuando un validate no valida, etc.
		*/
		
		/**
		* Cuando abro una lov o un desglose ajax, o cancelo no guardo esto en el history.
		*/
		if(    (!empty($this->controller->data['Form']['accion']) && $this->controller->data['Form']['accion'] === "cancelar")
			|| (!empty($this->controller->params['named']['layout']) && $this->controller->params['named']['layout'] === "lov")
			|| (!empty($this->controller->data['Formulario']['layout']) && $this->controller->data['Formulario']['layout'] === "lov")
			|| (!empty($this->controller->data['Formulario']['layout']) && $this->controller->data['Formulario']['layout'] === "lov")
			|| (!empty($this->controller->params['action']) && ($this->controller->params['action'] === "listable" || $this->controller->params['action'] === "descargar"))
			|| (!empty($this->controller->params['isAjax']))) {
			return;
		}
		
		if($url != $this->__historia[$cantidad - 1]) {
			if($cantidad == MAX_HISTORY) {
				array_shift($this->__historia);
			}
			$this->__historia[] = $url;
		}
		else {
			return;
		}
		
		/*
		file_put_contents("/tmp/historia.txt", "=========================", FILE_APPEND);
		foreach($this->__historia as $k=>$v) {
			file_put_contents("/tmp/historia.txt", "\n(" . $k . ")" . $v, FILE_APPEND);
		}
		file_put_contents("/tmp/historia.txt", "\n\n=========================", FILE_APPEND);
		*/
		$this->controller->Session->write('historia', $this->__historia);
    }
	
	
    function _addUrl_old($params) {
    	if(empty($params['url']['url'])) {
    		return;
    	}
		$this->__historia = $this->controller->Session->read('historia');
		$url = trim("/" . $params['url']['url']);
		$cantidad = count($this->__historia);
		/**
		* Prevengo que se inserte en la history dos veces el mismo.
		* Por ejemplo, cuando un validate no valida, etc.
		*/
		
		//if(empty($this->__historia)) {
		//	$this->__historia[] = $url;
		//}
		//else {
			/**
			* Cuando abro una lov o un desglose ajax, o cancelo no guardo esto en el history.
			*/
			if(    (!empty($this->controller->data['Form']['accion']) && $this->controller->data['Form']['accion'] === "cancelar")
				|| (!empty($this->controller->params['named']['layout']) && $this->controller->params['named']['layout'] === "lov")
				|| (!empty($this->controller->data['Formulario']['layout']) && $this->controller->data['Formulario']['layout'] === "lov")
				|| (!empty($this->controller->data['Formulario']['layout']) && $this->controller->data['Formulario']['layout'] === "lov")
				|| (!empty($this->controller->params['action']) && ($this->controller->params['action'] == "listable" || $this->controller->params['action'] == "descargar"))
				|| (!empty($this->controller->params['isAjax']))) {
				return;
			}
			
			$parsedUrl = Router::parse($url);
			$parsedHistoryUrl = Router::parse($this->__historia[$cantidad - 1]);
			$actual = $parsedUrl['controller'] . "/" . $parsedUrl['action'];
			$history = $parsedHistoryUrl['controller'] . "/" . $parsedHistoryUrl['action'];
			if($actual != $history) {
				if($cantidad == MAX_HISTORY) {
					array_shift($this->__historia);
				}
				$this->__historia[] = $url;
			}
			else {
				return;
			}
		//}
		file_put_contents("/tmp/historia.txt", "=========================", FILE_APPEND);
		foreach($this->__historia as $k=>$v) {
			file_put_contents("/tmp/historia.txt", "\n(" . $k . ")" . $v, FILE_APPEND);
		}
		file_put_contents("/tmp/historia.txt", "\n\n=========================", FILE_APPEND);
		$this->controller->Session->write('historia', $this->__historia);
    }
	
}
?>