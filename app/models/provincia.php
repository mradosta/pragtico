<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las provincias.
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
 * La clase encapsula la logica de acceso a datos asociada a las provincias.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Provincia extends AppModel {


	var $order = array('Provincia.nombre' => 'asc');
	
	
	var $validate = array( 
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre de la provincia.')
        )
	);


	var $hasMany = array(	'Localidad' =>
                        array('className'    => 'Localidad',
                              'foreignKey'   => 'provincia_id'));

}
?>
