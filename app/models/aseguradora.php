<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las aseguradoras.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.org
 * @package			pragtico
 * @subpackage		app.models
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a las aseguradoras.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Aseguradora extends AppModel {

	var $order = array('Aseguradora.nombre'=>'asc');
	
	var $validate = array( 
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre de la aseguradora.')
        ),
        'codigo' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el codigo de la aseguradora.')
        )
	);


}
?>