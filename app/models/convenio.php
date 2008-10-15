<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los convenios.
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
 * La clase encapsula la logica de acceso a datos asociada a los convenios.
 *
 * Se refiere a los convenios colectivos.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Convenio extends AppModel {

	//var $recursive = -1;
	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	//var $modificadores = array("index"=>array("contain"=>array("Convenio")));
	
	var $order = array('Convenio.nombre'=>'asc');

	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre del convenio colectivo.')
        ),
        'actualizacion' => array(
			array(
				'rule'	=> VALID_DATE,
				'message'	=>'La fecha no es valida.'),
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'=>'Debe especificar la fecha de la ultima actualizacion del convenio.')
        ),
        'numero' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el numero del convenio colectivo.')
        )
	);


	var $hasMany = array(	'ConveniosCategoria' =>
                        array('className'    => 'ConveniosCategoria',
                              'foreignKey'   => 'convenio_id'),
							'ConveniosInformacion' =>
                        array('className'    => 'ConveniosInformacion',
                              'foreignKey'   => 'convenio_id'));
                              
	var $hasAndBelongsToMany = array('Concepto' =>
						array('with' => 'ConveniosConcepto'));


/**
 * Antes de guardar, saco las propiedades del archivo y lo guardo como campo binary de la base.
 */
	function beforeSave() {
		if($this->getFile()) {
			return parent::beforeSave();
		}
		else {
			return false;
		}
	}
}
?>