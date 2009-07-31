<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las situaciones (SICOSS).
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
 * La clase encapsula la logica de acceso a datos asociada a las situaciones (SICOSS).
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Situacion extends AppModel {

	var $validate = array(
        'nombre' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar el nombre de la situacion.')
        ),
        'codigo' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar el codigo de la situacion.')
        )
	);

	var $hasMany = array('AusenciasMotivo');
	
}
?>