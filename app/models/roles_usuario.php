<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a la relacion entre roles y usuarios.
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
 * La clase encapsula la logica de acceso a datos asociada a la relacion entre roles y usuarios.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class RolesUsuario extends AppModel {

	var $validate = array(
        'usuario_id' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar el usuario.')
        ),
        'rol_id' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar el rol.')
        )
	);

	var $belongsTo = array(	'Rol' =>
                        array('className'    => 'Rol',
                              'foreignKey'   => 'rol_id'),
							'Usuario' =>
                        array('className'    => 'Usuario',
                              'foreignKey'   => 'usuario_id'));

}
?>