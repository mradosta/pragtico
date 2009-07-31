<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada al Siap.
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
 * La clase encapsula la logica de acceso a datos asociada al Siap.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Siap extends AppModel {

    var $permissions = array('permissions' => 508, 'group' => 'none', 'role' => 'higher');

	var $validate = array(
        'version' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar la version de SIAP.')
        )
	);

	var $hasMany = array(	'SiapsDetalle' =>
                        array('className'    => 'SiapsDetalle',
                              'foreignKey'   => 'siap_id'));

}
?>