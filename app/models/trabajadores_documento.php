<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a la informacion de los trabajadores.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link			http://www.pragmatia.org
 * @package         pragtico
 * @subpackage      app.models
 * @since           Pragtico v 1.0.0
 * @version         $Revision: 1139 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2009-11-13 12:28:43 -0300 (Fri, 13 Nov 2009) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a la informacion de los trabajadores.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class TrabajadoresDocumento extends AppModel {

    var $permissions = array('permissions' => 496, 'group' => 'default', 'role' => 'all');

    var $belongsTo = array('Trabajador');

    var $validate = array(
        'nombre' => array(
            array(
                'rule'      => VALID_NOT_EMPTY,
                'message'   => 'Debe especificar el nombre del documento.')
        ),
    );

    var $breadCrumb = array('format'    => '%s',
                            'fields'    => array('TrabajadoresDocumento.nombre'));


	function beforeSave() {
		$this->getFile();
		return parent::beforeSave();
	}

}
?>
