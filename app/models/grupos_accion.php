<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a la relacion entre grupos y acciones.
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
 * La clase encapsula la logica de acceso a datos asociada a la relacion entre grupos y acciones.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class GruposAccion extends AppModel {

	var $belongsTo = array(	'Grupo' =>
                        array('className'    => 'Grupo',
                              'foreignKey'   => 'grupo_id'),
							'Accion' =>
                        array('className'    => 'Accion',
                              'foreignKey'   => 'accion_id'));

}
?>