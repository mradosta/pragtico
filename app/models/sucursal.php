<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las sucursales de los bancos.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.models
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a las sucursales de los bancos.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Sucursal extends AppModel {

	var $unique = array("banco_id", "codigo");
	
	var $validate = array(
        'codigo' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el codigo de la sucursal del banco.'),
			array(
				'rule'	=> VALID_NUMBER, 
				'message'	=>'El codigo de la sucursal del banco debe ser numerico.')
	    )
	);

	var $belongsTo = array(	'Banco' =>
                        array('className'    => 'Banco',
                              'foreignKey'   => 'banco_id'));

}
?>