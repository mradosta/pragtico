<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada al seguimiento de una ausencia.
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
 * La clase encapsula la logica de acceso a datos asociada al seguimiento de una ausencia.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class AusenciasSeguimiento extends AppModel {

	var $unique = array("ausencia_id", "desde");

	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array("edit"=>array("contain"=>array("Ausencia.AusenciasMotivo")));

	var $validate = array(
        'desde' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar la fecha desde que se inicio el seguimiento de la ausencia.'),
			array(
				'rule'	=> VALID_DATE, 
				'message'	=>'Debe especificar una fecha valida.')
				
        ),
        'hasta' => array(
			array(
				'rule'	=> VALID_DATE_NULO, 
				'message'	=>'Debe especificar una fecha valida.'),
			array(
				'rule'	=> 'validUnoPorLoMenos',
				'opciones'=> array('otrosCampos' =>array('dias')),
				'message'	=>'Debe necesariamente ingresar o la fecha hasta o el numero de dias.'),
			array(
				'rule'	=> 'validRango',
				'opciones'=> array('limiteInferior'=>'desde', 'condicion'=>'>='),
				'message'	=>'La fecha hasta debe ser mayor o igual a la fecha desde.'),
        ),
        'dias' => array(
			array(
				'rule'	=> VALID_NUMBER_NULO, 
				'message'	=>'Debe especificar un numero valido de dias.'),
			array(
				'rule'	=> 'validUnoPorLoMenos',
				'opciones'=> array('otrosCampos' =>array('hasta')),
				'message'	=>'Debe necesariamente ingresar el numero de dias o la fecha hasta.')
        ),
        'ausencia_id__' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe seleccionar la ausencia a la cual se le esta realizando el seguimiento.')
        )        
	);

	var $belongsTo = array(	'Ausencia' =>
                        array('className'    => 'Ausencia',
                              'foreignKey'   => 'ausencia_id'));


/**
 * Si seteo los dias de la ausencia y la fecha hasta, dejo solo los dias.
 */
	function beforeValidate() {
		if(!empty($this->data['AusenciasSeguimiento']['dias']) && !empty($this->data['AusenciasSeguimiento']['hasta'])) {
			unset($this->data['AusenciasSeguimiento']['hasta']);
		}
	}
/**
 * Antes de grabar debo asegurarme de que la cantidad de dias este seteada.
 * Puede que el usuario haya cargado la cantidad de dias, pero puede haber cargado una fecha desde y una fecha hasta, \
 * en cuyo caso debere calcularlo.
 */
	function beforeSave() {
		$this->getFile();
		if(empty($this->data['AusenciasSeguimiento']['dias']) && !empty($this->data['AusenciasSeguimiento']['hasta'])) {
			$options = array("desde"=>$this->data['AusenciasSeguimiento']['desde'], "hasta"=>$this->data['AusenciasSeguimiento']['hasta']);
			if($intervalo = $this->dateDiff($options)) {
				$this->data['AusenciasSeguimiento']['dias'] = $intervalo['dias'] + 1;
			}
		}
		return parent::beforeSave();
	}

}
?>