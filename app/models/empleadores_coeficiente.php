<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los coeficientes
 * de los empleadores.
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
 * La clase encapsula la logica de acceso a datos asociada a los coeficientes
 * de los empleadores.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class EmpleadoresCoeficiente extends AppModel {

	var $modificadores = array('index'=>array('contain'=>array('Empleador', 'Coeficiente')));
	
	var $validate = array(
        'emplador_id' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe seleccionar el empleador.')
        ),
        'coeficiente_id' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe seleccionar el coeficiente.')
        ),
        'valor' => array(
			array(
				'rule'	=> VALID_NUMBER, 
				'message'	=> 'Debe especificar el valor del coeficiente.')
        )        
	);

	var $belongsTo = array(	'Empleador' =>
                        array('className'    => 'Empleador',
                              'foreignKey'   => 'empleador_id'),
							'Coeficiente' =>
                        array('className'    => 'Coeficiente',
                              'foreignKey'   => 'coeficiente_id'));
}
?>