<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a la relacion que existe
 * entre los convenios colectivos y los conceptos.
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
 * entre los convenios colectivos y los conceptos.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class ConveniosConcepto extends AppModel {

	//var $order = array('Concepto.codigo' => 'asc');
	
	var $belongsTo = array(	'Convenio' =>
                        array('className'    => 'Convenio',
                              'foreignKey'   => 'convenio_id'),
							'Concepto' =>
                        array('className'    => 'Concepto',
                              'foreignKey'   => 'concepto_id'));

}
?>
