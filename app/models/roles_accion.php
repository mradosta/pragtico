<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a la relacion entre grupos y acciones.
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
 * La clase encapsula la logica de acceso a datos asociada a la relacion entre grupos y acciones.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class RolesAccion extends AppModel {

    var $permissions = array('permissions' => 508, 'group' => 'none', 'role' => 'higher');

	var $modificadores = array(	'edit'	=>array('contain'=>array(	'Accion.Controlador')),
								'add'	=>array('contain'=>array(	'Accion.Controlador')));

	var $belongsTo = array('Rol', 'Accion');

}
?>