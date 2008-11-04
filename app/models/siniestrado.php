<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los siniestrados.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.models
 * @since			Pragtico v 1.0.0
 * @version			$Revision: 11 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2008-10-15 16:31:33 -0300 (mié, 15 oct 2008) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a los siniestrados.
 *
 * Se refiere a los siniestrados de SIAP.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Siniestrado extends AppModel {

	
	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre del siniestrado.')
        ),
        'codigo' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el codigo del siniestrado.')
        )
	);

}
?>