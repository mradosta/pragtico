<?php
/**
 * Formulador Component.
 * Se encarga resolver las formulas.
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
 
 set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes');
 
/**
 * La clase encapsula la logica necesaria para resolver una formula.
 * Parsea la formula proveniente del sistema, y la deja de la forma en que PHPExcel la necesita para funcionar correctamente.
 *
 * @package     pragtico
 * @subpackage  app.controllers.components
 */
class FormuladorComponent extends Object {

	
/**
 * La instancia de PHPExcel que utilizare para resolver las formulas.
 *
 * @var object
 * @access private
 */
	private $__formulas = null;

	
/**
 * Inicializa el Component para usar en el controller.
 *
 * @param object $controller Una referencia al controller que esta instanciando el component.
 * @return void
 * @access public
 */
    function startup(&$controller) {
		App::import('Vendor', 'formulas', 'pragmatia');
		$this->__formulas = new Formulas();
	}


/**
 * Resuelve una formula.
 *
 * @return mixed Un string o un valor numerico. N/A en caso de error en la formula.
 * @access public
 */
	function resolver($formula) {
		return $this->__formulas->resolver($formula);
	}

}