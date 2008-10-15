<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las localidades.
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
 * La clase encapsula la logica de acceso a datos asociada a las localidades.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Localidad extends AppModel {

	var $order = array('Localidad.nombre'=>'asc');
	//var $recursive = 2;
	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre de la localidad.')
        ),
        'codigo_zona' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el codigo de la zona para AFIP.')
        ),
        'provincia_id' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar la provincia.')
        )
	);

	var $belongsTo = array(	'Provincia' =>
                        array('className'    => 'Provincia',
                              'foreignKey'   => 'provincia_id'));

}
?>