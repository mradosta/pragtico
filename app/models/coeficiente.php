<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los coeficientes.
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
 * La clase encapsula la logica de acceso a datos asociada a los coeficientes.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Coeficiente extends AppModel {

	var $order = array('Coeficiente.tipo'=>'desc');

	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre del coeficiente.')
        ),
        'tipo' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el tipo del coeficiente.')
        ),
        'valor' => array(
			array(
				'rule'	=> VALID_NUMBER, 
				'message'	=>'Debe especificar el valor del coeficiente.')
        )        
	);

}
?>