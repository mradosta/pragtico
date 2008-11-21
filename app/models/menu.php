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
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
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
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar el nombre del menu.')
        ),
        'orden' => array(
			array(
				'rule'		=> '/^[0-9]+$|^$/',
				'message'	=> 'Debe especificar un numero entero para el orden o dejarlo en blanco.')
        )
	);


	var $hasAndBelongsToMany = array(	'Rol' =>	array('with' => 'RolesMenu'));
	
	var $belongsTo = array( 'Parentmenu' 	=>
					array(	'className'  	=> 'Menu',
							'foreignKey' 	=> 'parent_id'));


	var $hasMany = array(   'Childmenu' 	=>
					array(	'className'    	=> 'Menu',
							'foreignKey'   	=> 'parent_id'));
	
/**
 * xxxxxxxx
 */
	function beforeSave($options) {
		/**
		* Si no cargo nada en la etiqueta, pongo el nombre como etiqueta.
		*/
		if(empty($this->data['Menu']['etiqueta'])) {
			$this->data['Menu']['etiqueta'] = ucfirst($this->data['Menu']['nombre']);
		}
		
		/**
		* Si no cargo nada en el controller, pongo el nombre como controller.
		*/
		if(empty($this->data['Menu']['controller'])) {
			$this->data['Menu']['controller'] = $this->data['Menu']['nombre'];
		}
		
		/**
		* Si no cargo nada en la action, pongo index como action.
		*/
		if(empty($this->data['Menu']['action'])) {
			$this->data['Menu']['action'] = "index";
		}
		
		return parent::beforeSave($options);
	}

}
?>