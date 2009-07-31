<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a la informacion adicional relacionada a un convenio.
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
 * La clase encapsula la logica de acceso a datos asociada a la informacion adicional relacionada a un convenio.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class ConveniosInformacion extends AppModel {

    var $breadCrumb = array('format'    => '%s de %s',
                            'fields'    => array('Informacion.nombre', 'Convenio.nombre'));
    
	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array('index'=>array('contain'=>array('Convenio', 'Informacion')));
	
	var $validate = array(
        'valor' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar un valor para la variable.')
        )
	);
	
	var $belongsTo = array(	'Convenio' =>
                        array('className'    => 'Convenio',
                              'foreignKey'   => 'convenio_id'),
							'Informacion' =>
                        array('className'    => 'Informacion',
                              'foreignKey'   => 'informacion_id'));

}
?>