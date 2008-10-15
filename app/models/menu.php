<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los menus.
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
 * La clase encapsula la logica de acceso a datos asociada a los menus.
 *
 * Se refiere a los menus que usa el sistema.
 * 
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Menu extends AppModel {

	var $order = array('Menu.orden' => 'asc');
	
	var $validate = array(
        'nombre' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe especificar el nombre del menu.')
        ),
        'orden' => array(
			array(
				'rule'	=> '/^[0-9]+$|^$/',
				'message'	=>'Debe especificar un numero entero para el orden o dejar en blanco.')
        )
	);


	var $hasAndBelongsToMany = array(	'Rol' =>
						array('with' => 'RolesMenu'));
						
	function beforeSave() {
		/**
		* Si el menu padre no esta seleccionada, la saco del array asi no intenta guardarlo.
		* Si intenta guardarlo sin valor, fallara la FK.
		*/
		if(empty($this->data['Menu']['parent_id'])) {
			unset($this->data['Menu']['parent_id']);
		}
			
		/**
		* Si no cargo nada en la etiqueta, Pongo el nombre como etiqueta.
		*/
		if(empty($this->data['Menu']['etiqueta'])) {
			$this->data['Menu']['etiqueta'] = ucfirst($this->data['Menu']['nombre']);
		}
		return parent::beforeSave();
	}

}
?>