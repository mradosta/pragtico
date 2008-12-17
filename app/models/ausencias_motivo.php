<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los motivos de las ausencias.
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
 * La clase encapsula la logica de acceso a datos asociada a los motivos de las ausencias.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class AusenciasMotivo extends AppModel {

	var $validate = array(
        'motivo' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el motivo de la ausencia.')
        ),
        'tipo' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar el tipo correspondiente al motivo.')
        )        
	);

	var $belongsTo = array('Situacion');
}
?>