<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las modalidades de contratacion (SIAP).
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
 * La clase encapsula la logica de acceso a datos asociada a las modalidades de contratacion (SIAP).
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Modalidad extends AppModel {

	var $order = array('Modalidad.nombre' => 'asc');

	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre de la modalidad.')
        ),
        'codigo' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el codigo de la modalidad.')
        )
	);

}
?>