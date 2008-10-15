<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las relaciones
 * laborales existentes entre trabajadores y empleadores.
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
 * La clase encapsula la logica de acceso a datos asociada a las relaciones.
 * Se refiere a las relaciones laborales que hay entre en un trabajador y un empleador.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Relacion extends AppModel {

	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array("index"=>array("contain"=>array("Trabajador", "Empleador")),
								"edit"=>array("contain"=>array("Trabajador",
												"Empleador",
												"Area",
												"ConveniosCategoria.Convenio")),
								"add" =>array(								
										"valoresDefault"=>array("ingreso"=>"date('d/m/Y')")));

	
	var $validate = array(
        'trabajador_id__' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe seleccionar un trabajador.')
        ),
        'empleador_id__' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe seleccionar un empleador.')
        ),
        'area_id' => array(
			array(
				'rule'	=> "/^[1-9]{1}[0-9]{0,10}$/",
				'message'	=>'Debe seleccionar un area.')
        ),
        'horas' => array(
			array(
				'rule'	=> VALID_NUMBER,
				'message'	=>'Debe ingresar un numero para las horas.')
        ),
        'ingreso' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe especificar la fecha inicio de la relacion laboral.'),
			array(
				'rule'	=> VALID_DATE,
				'message'	=>'Debe especificar una fecha valida.')

        ),
        'convenios_categoria_id__' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe seleccionar una categoria.')
        )
	);

	var $belongsTo = array(	'Trabajador' =>
                        array('className'    => 'Trabajador',
                              'foreignKey'   => 'trabajador_id'),
							'Empleador' =>
                        array('className'    => 'Empleador',
                              'foreignKey'   => 'empleador_id'),
							'Area' =>
                        array('className'    => 'Area',
                              'foreignKey'   => 'area_id'),
							'Situacion' =>
                        array('className'    => 'Situacion',
                              'foreignKey'   => 'situacion_id'),
							'ConveniosCategoria' =>
                        array('className'    => 'ConveniosCategoria',
                              'foreignKey'   => 'convenios_categoria_id'));

	
	var $hasMany = array(	'Ausencia' =>
                        array('className'    => 'Ausencia',
                              'foreignKey'   => 'relacion_id'),
							'Vacacion' =>
                        array('className'    => 'Vacacion',
                              'foreignKey'   => 'relacion_id'),
							'Ropa' =>
                        array('className'    => 'Ropa',
                              'foreignKey'   => 'relacion_id'),
                            'Hora' =>
                        array('className'    => 'Hora',
                              'foreignKey'   => 'relacion_id'),
                            'RelacionesConcepto' =>
                        array('className'    => 'RelacionesConcepto',
                              'foreignKey'   => 'relacion_id'),
							'Liquidacion' =>
                        array('className'    => 'Liquidacion',
                              'foreignKey'   => 'relacion_id'),
							'Pago' =>
                        array('className'    => 'Pago',
                              'foreignKey'   => 'relacion_id'),
							'Descuento' =>
                        array('className'    => 'Descuento',
                              'foreignKey'   => 'relacion_id'));

	
	var $hasAndBelongsToMany = array('Concepto' =>
								array('with' => 'RelacionesConcepto'));


	function beforeSave() {
		/**
		* Si no cargo el legajo, lo obtengo desde el documento del trabajador.
		*/
		if(empty($this->data['Relacion']['legajo']) && !empty($this->data['Relacion']['trabajador_id'])) {
			$this->Trabajador->recursive = -1;
			$trabajador = $this->Trabajador->findById($this->data['Relacion']['trabajador_id']);
			$this->data['Relacion']['legajo'] = $trabajador['Trabajador']['numero_documento'];
		}
	
		/**
		* Si las foraneas opcionales no las saco del array, en caso de que esten vacias, el framework intentara
		* guardarlas con el valor vacio, y este fallara.
		*/
		if(empty($this->data['Relacion']['actividad_id'])) {
			unset($this->data['Relacion']['actividad_id']);
		}
		if(empty($this->data['Relacion']['modalidad_id'])) {
			unset($this->data['Relacion']['modalidad_id']);
		}
		if(empty($this->data['Relacion']['situacion_id'])) {
			unset($this->data['Relacion']['situacion_id']);
		}
		return parent::beforeSave();
	}



}
?>
