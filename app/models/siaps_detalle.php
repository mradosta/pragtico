<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada al detalle de cada version de Siap.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.models
 * @since			Pragtico v 1.0.0
 * @version			1.0.0
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada al detalle de cada version de Siap.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class SiapsDetalle extends AppModel {

/*	
	var $unique = array("controlador_id", "nombre");

	var $order = array('Accion.nombre'=>'asc');

	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre de la accion.')
        ),
        'controlador_id' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar un controlador.')
        )
	);

	var $belongsTo = array(	'Controlador' =>
                        array('className'    => 'Controlador',
                              'foreignKey'   => 'controlador_id'));
	*/
	var $belongsTo = array(	'Siap' =>
                        array('className'    => 'Siap',
                              'foreignKey'   => 'siap_id'));

}
?>