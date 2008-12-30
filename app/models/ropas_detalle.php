<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las prendas
 * que se le entregan con la orden por ropa a un trabajador de una relacion laboral.
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
 * La clase encapsula la logica de acceso a datos asociada a las prendas
 * que se le entregan con la orden por ropa a un trabajador de una relacion laboral.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class RopasDetalle extends AppModel {

	var $unique = array("ropa_id", "prenda");
	
	var $validate = array(
        'modelo' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe ingresar una fecha.')
        ));
		
	var $belongsTo = array(	'Ropa' =>
                        array('className'    => 'Ropa',
                              'foreignKey'   => 'ropa_id'));

}
?>
