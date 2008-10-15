<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los grupos de usuarios.
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
 * La clase encapsula la logica de acceso a datos asociada a los grupos de usuarios.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Grupo extends AppModel {

	var $order = array("Grupo.nombre asc");

	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar el nombre del grupo.')
        )
	);

	var $hasMany = array(	'GruposParametro' =>
                        array('className'    => 'GruposParametro',
                              'foreignKey'   => 'grupo_id'));
	
	var $belongsTo = array(	'Empleador' =>
                        array('className'    => 'Empleador',
                              'foreignKey'   => 'empleador_id'));

	var $hasAndBelongsToMany = array(	'Usuario' =>
						array('with' => 'GruposUsuario'),
										'Accion' =>
						array('with' => 'GruposAccion'));


	function beforeSave() {
		/**
		* Es un add.
		* Como uso matematica binaria, el proximo ID debe ser generado por mi como potencia de 2 del anterior.
		*/
		if(empty($this->data['Grupo']['id'])) {
			$this->recursive = -1;
			$grupo = $this->find("first", array("checkSecurity"=>false, "fields"=>array("MAX(Grupo.id) AS maximo")));
			$ultimoGrupo = $grupo[0]['maximo'];
			$this->data['Grupo']['id'] = $ultimoGrupo * 2;
		}
		return parent::beforeSave();
	}

}
?>