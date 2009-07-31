<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las areas de los empleadores.
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
 * La clase encapsula la logica de acceso a datos asociada a las areas de los empleadores.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class ObrasSocial extends AppModel {

    var $permissions = array('permissions' => 508, 'group' => 'none', 'role' => 'higher');

	var $validate = array(
        'codigo' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar el codigo de la obra social.')
        ),
        'nombre' => array(
            array(
                'rule'  => VALID_NOT_EMPTY,
                'message'   => 'Debe especificar el nombre de la obra social.')
        )
	);

    var $breadCrumb = array('format'    => '(%s) - %s',
                            'fields'    => array('ObrasSocial.codigo', 'ObrasSocial.nombre'));

}
?>
