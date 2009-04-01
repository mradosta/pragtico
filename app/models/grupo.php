<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los grupos de usuarios.
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
 * La clase encapsula la logica de acceso a datos asociada a los grupos de usuarios.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Grupo extends AppModel {

	var $order = array('Grupo.nombre asc');

	var $validate = array(
        'nombre' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar el nombre del grupo.')
        )
	);

	var $hasMany = array(	'GruposParametro' =>
                        array('className'    => 'GruposParametro',
                              'foreignKey'   => 'grupo_id'));
	
	var $hasAndBelongsToMany = array(	'Usuario' =>
						array('with' => 'GruposUsuario'));


	function beforeValidate() {
		/**
		* Es un add.
		* Como uso matematica binaria, el proximo ID debe ser generado por mi como potencia de 2 del anterior.
		*/
		if (empty($this->data['Grupo']['id'])) {
			$this->Behaviors->detach('Permisos');
			$group = $this->find('first', array(
					'fields' 		=> array('MAX(Grupo.id) AS last'),
					'recursive' 	=> -1));
			$this->data['Grupo']['id'] = $group['Grupo']['last'] * 2;
			$this->Behaviors->attach('Permisos');
		}
		return parent::beforeValidate();
	}

}
?>