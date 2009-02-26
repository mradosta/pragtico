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
 * Prevent the re-initialization of the component.
 *
 * @var boolean
 * @access private
 */
    var $__started = false;
	
	
/**
 * The Controller who instantiated the componet.
 *
 * @var array
 * @access public
 */
	var $controller;

	
/**
 * Initialize the component.
 *
 * @param object $controller A reference to the controller.
 * @return void
 * @access public
 */
    function startup(&$controller) {

        /**
         * Prevent to be executed more than once.
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
 * Adds current url to history stack,
 *
 * @return void
 * @access private
 */
	function _addUrl() {

		if (in_array($this->controller->action, $this->__blackListedActions)
			|| $this->controller->params['isAjax'] === true
		    || (isset($this->controller->params['named']['layout']) && $this->controller->params['named']['layout'] === 'lov')) {
			return;
		}
		
		//$url['controller'] = strtolower($this->controller->name);
		$url['controller'] = $this->controller->name;
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