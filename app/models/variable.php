<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las variables.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.models
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a las variables.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Variable extends AppModel {

    protected $__permissions = 500;
    
    var $order = array('Variable.nombre' => 'asc');
	
    var $validate = array(
        'nombre' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar el nombre de la variable.'),
			array(
				'rule'		=> '/^#[a-z,0-9,_]+$/',
				'message'	=> 'El nombre de la variable debe comenzar con el signo # y solo puede contener letras minusculas y guion/es bajo/s (_).')
        )        
	);

    var $breadCrumb = array('fields'    => array('Variable.nombre'));
    

}
?>