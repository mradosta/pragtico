<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los recibos.
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
 * La clase encapsula la logica de acceso a datos asociada a los recibos.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Recibo extends AppModel {

	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe ingresar un nombre para el recibo.'))
	);

	var $belongsTo = array(	'Empleador' =>
                        array('className'    => 'Empleador',
                              'foreignKey'   => 'empleador_id'));
	
	var $hasMany = array(	'RecibosConcepto' =>
					array('className'    => 'RecibosConcepto',
						  'foreignKey'   => 'recibo_id'));

 
}
?>