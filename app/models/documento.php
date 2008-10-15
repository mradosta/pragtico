<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los documentos modelo del sistema.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pragmatia de RPB S.A.
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
 * La clase encapsula la logica de acceso a datos asociada a los documentos modelo del sistema.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Documento extends AppModel {


	var $order = array('Documento.nombre' => 'asc');
	
	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe especificar el nombre del documento modelo.')
        )
	);


	//function getModels

}
?>
