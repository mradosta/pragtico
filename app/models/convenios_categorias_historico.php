<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada al historico de las categorias de los convenios.
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
 * La clase encapsula la logica de acceso a datos asociada al historico de las categorias de los convenios.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class ConveniosCategoriasHistorico extends AppModel {

	var $unique = array('convenios_categoria_id', 'desde');
	
	/**
	* Establece modificaciones al comportamiento estandar de app_controller.php
	*/
	var $modificadores = array('index' 	=> array('contain' => array('ConveniosCategoria.Convenio')),
							   'edit' 	=> array('contain' => array('ConveniosCategoria.Convenio')));
	
	var $breadCrumb = array('format' 	=> '%s (%s)',
							'fields' 	=> array('ConveniosCategoria.nombre', 'ConveniosCategoria.Convenio.nombre'));

	var $belongsTo = array(	'ConveniosCategoria' =>
                        array('className'    => 'ConveniosCategoria',
                              'foreignKey'   => 'convenios_categoria_id'));
}

?>