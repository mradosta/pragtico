<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a la informacion adicional relacionada a un convenio.
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
 * La clase encapsula la logica de acceso a datos asociada a la informacion adicional relacionada a un convenio.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class ConveniosInformacion extends AppModel {

	//var $order = array('Concepto.codigo'=>'asc');
	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array("index"=>array("contain"=>array("Convenio", "Informacion")));
	
	
	var $belongsTo = array(	'Convenio' =>
                        array('className'    => 'Convenio',
                              'foreignKey'   => 'convenio_id'),
							'Informacion' =>
                        array('className'    => 'Informacion',
                              'foreignKey'   => 'informacion_id'));

}
?>
