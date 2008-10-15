<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los conceptos
 * propios de las relaciones laborales existentes entre trabajadores y empleadores.
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
 * La clase encapsula la logica de acceso a datos asociada a los conceptos propios de las
 * relaciones laborales que hay entre en un trabajador y un empleador.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class RelacionesConcepto extends AppModel {

	var $modificadores = array(	"index"=>array("contain"=>array("Relacion.Empleador",
																"Relacion.Trabajador",
																"Concepto")),
								"edit"=>array("contain"=>array(	"Relacion.Empleador",
																"Relacion.Trabajador",
																"Concepto")));
																
	var $unique = array("relacion_id", "concepto_id");
	
	var $validate = array(
        'relacion_id__' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar la relacion laboral.')
        ),
        'concepto_id__' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar un concepto.')
        ));
	
	var $belongsTo = array(	'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'),
							'Concepto' =>
                        array('className'    => 'Concepto',
                              'foreignKey'   => 'concepto_id'));

}
?>
