<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las acciones.
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
 * La clase encapsula la logica de acceso a datos asociada a las acciones.
 *
 * Se refiere a las actions de los controllers del framework cakephp.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Accion extends AppModel {

	
	var $unique = array("controlador_id", "nombre");

	var $order = array('Accion.nombre' => 'asc');

	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre de la accion.')
        ),
        'controlador_id' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar un controlador.')
        )
	);

	var $belongsTo = array(	'Controlador' =>
                        array('className'    => 'Controlador',
                              'foreignKey'   => 'controlador_id'));
	

}
?>