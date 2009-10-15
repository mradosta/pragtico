<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los empleadores.
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
 * La clase encapsula la logica de acceso a datos asociada a los empleadores.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Empleador extends AppModel {

    var $permissions = array('permissions' => 496, 'group' => 'default', 'role' => 'all');

	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array('index' 	=> array('contain' => array()),
							   'edit'  	=> array('contain' => array('Localidad', 'Actividad', 'EmployersType')),
								'add'  	=> array(								
										'valoresDefault'=>array('alta' => array('date' => 'Y-m-d'),
																'pais' => 'Argentina')));

	var $breadCrumb = array('format' 	=> '(%s) %s',
							'fields' 	=> array('Empleador.cuit', 'Empleador.nombre'));

	var $opciones = array('pago' => array(	'0' => 'Ultimo Dia Habil del Periodo',
											'1' => 'Primer Dia Habil',
											'2' => 'Segundo Dia Habil',
											'3' => 'Tercer Dia Habil',
											'4' => 'Cuarto Dia Habil',
											'5' => 'Quinto Dia Habil'));
	
	var $validate = array( 
        'nombre' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar el nombre del empleador.')
        ),
        'cuit' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar el cuit del empleador.'),
			array(
				'rule'		=> 'validCuitCuil',
				'message'	=> 'El numero de Cuit ingresado no es valido.')
        ),
        'alta' => array(
			array(
				'rule'		=> VALID_DATE,
				'message'	=> 'La fecha no es valida.'),
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe ingresar una fecha o seleccionarla desde el calendario.')
				
        ),
        'email' => array(
			array(
				'rule'		=> VALID_MAIL,
				'message'	=> 'El email no es valido.')
        ),
        'provincia_id' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe seleccionar la provincia.')
        ),
        'localidad_id' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe seleccionar la localidad.')
        )
	);

	var $belongsTo = array('Localidad', 'Actividad', 'EmployersType');
	
	var $hasMany = array(	'Relacion',
                            'Area',
							'Suss',                              
							'Recibo',
	   						'Cuenta');

	var $hasAndBelongsToMany = array(	'Trabajador' =>
									array('with' => 'Relacion'),
										'Rubro' =>
									array('with' => 'EmpleadoresRubro'),
										'Concepto' =>
									array('with' => 'EmpleadoresConcepto'),
										'Coeficiente' =>
									array('with' => 'EmpleadoresCoeficiente'));



	/**
	function beforeSave() {
		if (empty($this->id)) {
	    	$this->data = array_merge($this->data, array('Area' => array(array('nombre' => 'General'))));
		}
		return parent::beforeSave();
	}
	*/
}
?>