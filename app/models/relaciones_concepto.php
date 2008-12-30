<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los conceptos
 * propios de las relaciones laborales existentes entre trabajadores y empleadores.
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
 * La clase encapsula la logica de acceso a datos asociada a los conceptos propios de las
 * relaciones laborales que hay entre en un trabajador y un empleador.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class RelacionesConcepto extends AppModel {

	var $modificadores = array(	'index'	=>
			array('contain'	=> array('Relacion'	=> array('Empleador', 'Trabajador'), 'Concepto')),
								'edit'	=>
			array('contain'	=> array('Relacion'	=> array('Empleador', 'Trabajador'), 'Concepto')));
	
	var $validate = array(
        'relacion_id__' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY, 
				'message'	=> 'Debe seleccionar la relacion laboral.'
		)),
        'concepto_id__' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY, 
				'message'	=> 'Debe seleccionar un concepto.'
		)));
	
	var $belongsTo = array('Relacion', 'Concepto');

}
?>