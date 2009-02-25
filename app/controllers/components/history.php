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
 * La clase encapsula la logica necesaria para resolver la navegacion.
 *
 * @package     pragtico
 * @subpackage  app.controllers.components
 */
class HistoryComponent extends Object {
    
	
/**
 * Actions not to be saved.
 *
 * @var array
 * @access private
 */
	private $__blackListedActions = array('listable', 'save', 'delete');
	
	
/**
 * Indica si ya fue instanciado el component.
 *
 * @var boolean
 * @access private
 */
    var $__started = false;
	
	
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
 * Goes to a previews visited page.
 *
 * @param integer $pos Position in history where to go back.
 * @return void
 * @access public
 */
	
    function goBack($pos = 1) {
	    $history = array_reverse($this->controller->Session->read('__history'));
		
		/*
		$this->log('=================');
		$this->log('Me voy a:');
		$this->log(Router::url($history[$pos]));
		$this->log('=================');
		*/
        $this->controller->redirect($history[$pos], true);
    }


/**
 * Agrega una url al stack del history, que nunca se usara. 
 *
 * @return void
 * @access public
 */
    function addFakeUrl_deprecated() {
		if (count($this->controller->Session->read('historia')) == MAX_HISTORY) {
			array_shift($this->__historia);
		}
		$this->__historia[] = "/fake/url/do_not_add";
		$this->controller->Session->write('historia', $this->__historia);
    }

    
/**
 * Adds current url to history stack,
 *
 * @return void
 * @access private
 */
	function _addUrl() {

		if (in_array($this->controller->action, $this->__blackListedActions)
			|| $this->controller->params['isAjax'] === true) {
			return;
		}
		
		$url['controller'] = strtolower($this->controller->name);
		$url['action'] = $this->controller->action;
		$url = array_merge($url, $this->controller->params['pass']);
		$url = array_merge($url, $this->controller->params['named']);

		$history = $this->controller->Session->read('__history');
		if (empty($history)) {
			$this->controller->Session->write('__history', array($url));
		} else {
			
			$count = count($history);
			$history[$count] = $url;
			
			if (serialize($history[$count - 1]) !== serialize($url)) {
				$this->controller->Session->write('__history', array_slice($history, -3));

				/*
				$this->log('=================');
				$this->log('Agrego a __history:');
				$this->log($url);
				$this->log('=================');
				
				$this->log('=================');
				$this->log('__history lo guardo asi:');
				$this->log(array_reverse($history));
				$this->log('=================');
    */
			}
		}
    }

}
?>