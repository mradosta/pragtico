<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a la relacion que existe
 * entre los empleadores y los conceptos.
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
 * La clase encapsula la logica de acceso a datos asociada a la relacion que existe
 * entre los empleadores y los conceptos.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class EmpleadoresConcepto extends AppModel {

	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array('index'=>array('contain'=>array('Concepto', 'Empleador')));
	
	var $belongsTo = array(	'Empleador' =>
                        array('className'    => 'Empleador',
                              'foreignKey'   => 'empleador_id'),
							'Concepto' =>
                        array('className'    => 'Concepto',
                              'foreignKey'   => 'concepto_id'));
    var $breadCrumb = array('format'    => '%s para %s',
                            'fields'    => array('Concepto.nombre', 'Empleador.nombre'));

}
?>
