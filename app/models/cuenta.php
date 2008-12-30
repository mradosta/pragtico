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

	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array(	"index"=>array("contain"=>array(	"Empleador",
																"Sucursal.Banco")),
								"edit"=>array("contain"=>array(	"Empleador",
																"Sucursal.Banco")));
	
	
	var $validate = array(
        'emplador_id' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar el empleador.')
        )        
	);

	var $belongsTo = array(	'Empleador' =>
                        array('className'    => 'Empleador',
                              'foreignKey'   => 'empleador_id'),
							'Sucursal' =>
                        array('className'    => 'Sucursal',
                              'foreignKey'   => 'sucursal_id'));
}
?>