<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada al seguimiento de una ausencia.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.models
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada al seguimiento de una ausencia.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class AusenciasSeguimiento extends AppModel {

    var $permissions = array('permissions' => 496, 'group' => 'default', 'role' => 'all');

	/**
	* Estados:
	* 		Pendiente: Se cargo pero no se confirmo. No se tendra en cuenta para la liquidacion.
	*		Confirmado: Se cargo y se confirmo. Se liquidara.
	* 		Liquidado: Se cargo, se confirmo y se liquido. No se volvera a liquidar.
	var $opciones = array('estado'=> array(		'Confirmado'	=> 'Confirmado',
												'Pendiente'		=> 'Pendiente'));
    */
	

	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array('edit'=>array('contain'	=> array('Ausencia.AusenciasMotivo')),
								'add'  	=> array(								
										'valoresDefault'=> array('dias' => '1')));

	var $validate = array(
        'dias' => array(
			array(
				'rule'		=> VALID_NUMBER, 
				'message'	=> 'Debe especificar un numero valido de dias.'),
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar la cantidad de dias.')
        ),
        'ausencia_id__' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY, 
				'message'	=> 'Debe seleccionar la ausencia a la cual se le esta realizando el seguimiento.')
        )        
	);

	var $belongsTo = array('Ausencia', 'Liquidacion');

}
?>