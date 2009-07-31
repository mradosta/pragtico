<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los recibos.
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
 * La clase encapsula la logica de acceso a datos asociada a los recibos.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Recibo extends AppModel {

    var $permissions = array('permissions' => 496, 'group' => 'default', 'role' => 'all');

	var $validate = array(
        'nombre' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe ingresar un nombre para el recibo.'))
	);

	var $belongsTo = array('Empleador');
	
	var $hasMany = array(	'RecibosConcepto' =>
					array('className'    => 'RecibosConcepto',
						  'foreignKey'   => 'recibo_id'));

 
}
?>