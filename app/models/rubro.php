<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los rubros de las empresas.
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
 * La clase encapsula la logica de acceso a datos asociada a los rubros de las empresas.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Rubro extends AppModel {

    var $permissions = array('permissions' => 508, 'group' => 'none', 'role' => 'higher');

	var $validate = array(
        'nombre' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar el nombre del rubro.')
        )
	);

	var $hasAndBelongsToMany = array('Empleador' =>
						array('with' => 'EmpleadoresRubro'));
}
?>