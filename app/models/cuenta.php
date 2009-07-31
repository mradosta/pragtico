<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las cuentas bancarias de los empleadores.
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
 * La clase encapsula la logica de acceso a datos asociada a las cuentas bancarias de los empleadores.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Cuenta extends AppModel {

    var $permissions = array('permissions' => 508, 'group' => 'none', 'role' => 'higher');

	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array(	'index'	=> array('contain' => array('Empleador',
																	'Sucursal.Banco')),
								'edit'	=> array('contain' => array('Empleador',
																	'Sucursal.Banco')));
	
	
	var $validate = array(
        'emplador_id' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe seleccionar el empleador.')
        ),
        'cbu' => array(
			array(
				'rule'		=> 'validCbu',
				'message'	=> 'El Cbu ingresado no es valido.')
        )
	);
    var $breadCrumb = array('format'    => '%s %s para %s',
                            'fields'    => array('Cuenta.tipo', 'Cuenta.cbu', 'Empleador.nombre'));
	var $belongsTo = array('Empleador', 'Sucursal');
}
?>