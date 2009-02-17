<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las vacaciones.
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
 * La clase encapsula la logica de acceso a datos asociada a las vacaciones.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Vacacion extends AppModel {

	var $recursive = 2;
	var $validate = array( 
        'desde' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar la fecha de inicio de las vacaciones.'),
			array(
				'rule'	=> VALID_DATE, 
				'message'	=> 'Debe especificar una fecha valida.')
				
        ),
        'hasta' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar la fecha de fin de las vacaciones.'),
			array(
				'rule'	=> VALID_DATE, 
				'message'	=> 'Debe especificar una fecha valida.')
				
        ),
        'relacion_id__' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar la relacion laboral que toma las vacaciones.')
        )        
	);
	
	var $belongsTo = array(	'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'));


}
?>