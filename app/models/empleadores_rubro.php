<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a la relacion entre un rubro y un empleador.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.models
 * @since			Pragtico v 1.0.0
 * @version			1.0.0
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a la relacion entre un rubro y un empleador.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class EmpleadoresRubro extends AppModel {

	var $belongsTo = array(	'Empleador' =>
                        array('className'    => 'Empleador',
                              'foreignKey'   => 'empleador_id'),
							'Rubro' =>
                        array('className'    => 'Rubro',
                              'foreignKey'   => 'rubro_id'));



}
?>