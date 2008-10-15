<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los trabajadores.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.org
 * @package			pragtico
 * @subpackage		app.models
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a los trabajadores.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Trabajador extends AppModel {

	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array("edit"=>array("contain"=>array(	"Sucursal.Banco",
																"Localidad",
																"ObrasSocial")),
								"add" =>array(								
										"valoresDefault"=>array("pais"=>"Argentina",
																"nacionalidad"=>"Argentina")));

	var $validate = array(
        'apellido' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el apellido del trabajador.')
        ),
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe especificar el nombre del trabajador.')
        ),
        'cuil' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe especificar el cuil del trabajador.'),
			array(
				'rule'	=> 'validCuitCuil',
				'message'	=>'El numero de Cuil ingresado no es valido.')
				
        ),
        'jubilacion' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe seleccionar un tipo de regimen jubilatorio.')
        ),
        'cbu' => array(
			array(
				'rule'	=> 'validCbu',
				'message'	=>'El Cbu ingresado no es valido.')
        ),
        'ingreso' => array(
			array(
				'rule'	=> VALID_DATE,
				'message'	=>'La fecha no es valida.'),
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe ingresar una fecha o seleccionarla desde el calendario.')
				
        ),
        'email' => array(
			array(
				'rule'	=> VALID_MAIL,
				'message'	=>'El email no es valido.')
        ),
        'provincia_id' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe seleccionar la provincia.')
        )
        
	);

	var $belongsTo = array(	'Sucursal' =>
                        array('className'    => 'Sucursal',
                              'foreignKey'   => 'sucursal_id'),
							'Localidad' =>
                        array('className'    => 'Localidad',
                              'foreignKey'   => 'localidad_id'),
							'Condicion' =>
                        array('className'    => 'Condicion',
                              'foreignKey'   => 'condicion_id'),
							'ObrasSocial' =>
                        array('className'    => 'ObrasSocial',
                              'foreignKey'   => 'obra_social_id'));

	var $hasAndBelongsToMany = array('Empleador' =>
						array('with' => 'Relacion'));



/**
 * Antes de guardar, saco las propiedades del archivo y lo guardo como campo binary de la base.
 */

	function beforeSave() {
		$this->getFile();
		/**
		* Si no cargo el documento, lo obtengo desde el cuit.
		*/
		if(empty($this->data['Trabajador']['numero_documento']) && !empty($this->data['Trabajador']['cuil'])) {
			$this->data['Trabajador']['numero_documento'] = substr(str_replace("-", "", $this->data['Trabajador']['cuil']), 2, 8);
		}
		
		/**
		* Si tengo la sucursal y la cuenta, puedo generar el CBU.
		*/
		if((empty($this->data['Trabajador']['cbu']) || strlen($this->data['Trabajador']['cbu']) <> 22)
			&& !empty($this->data['Trabajador']['sucursal_id'])
			&& !empty($this->data['Trabajador']['cuenta'])) {

			$sucursal = $this->Sucursal->findById($this->data['Trabajador']['sucursal_id']);
			$parteA = str_pad($sucursal['Banco']['codigo'], 3, "0", STR_PAD_LEFT) . str_pad($sucursal['Sucursal']['codigo'], 4, "0", STR_PAD_LEFT);
			$parteB = str_pad($this->data['Trabajador']['cuenta'], 13, "0", STR_PAD_LEFT);
			$digitoVerificadorParteA = $this->__getDigitoVerificador($parteA);
			$digitoVerificadorParteB = $this->__getDigitoVerificador($parteB);
			
			$cbu = $parteA . $digitoVerificadorParteA . $parteB . $digitoVerificadorParteB;
			$this->data['Trabajador']['cbu'] = $cbu;
		}

		/**
		* Si las foraneas opcionales no las saco del array, en caso de que esten vacias, el framework intentara
		* guardarlas con el valor vacio, y este fallara.
		*/
		if(empty($this->data['Trabajador']['localidad_id'])) {
			unset($this->data['Trabajador']['localidad_id']);
		}
		if(empty($this->data['Trabajador']['sucursal_id'])) {
			unset($this->data['Trabajador']['sucursal_id']);
		}
		if(empty($this->data['Trabajador']['obra_social_id'])) {
			unset($this->data['Trabajador']['obra_social_id']);
		}
		if(empty($this->data['Trabajador']['condicion_id'])) {
			unset($this->data['Trabajador']['condicion_id']);
		}
		
		return parent::beforeSave();
	}
}
?>
