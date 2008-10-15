<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los parametros de los grupos.
 *
 * Los parametro son datos relacionados a los grupos. Se refiere a cualquier dato adicinal de un grupo
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
 * La clase encapsula la logica de acceso a datos asociada a los parametros de los grupos.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class GruposParametro extends AppModel {

	var $order = array('GruposParametro.nombre'=>'asc');

	var $validate = array(
        'grupo_id' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar el grupo.')
        ),
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre del parametro.')
        ),
        'valor' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el valor del parametro.')
        )
	);

	var $belongsTo = array(	'Grupo' =>
                        array('className'    => 'Grupo',
                              'foreignKey'   => 'grupo_id'));

}
?>