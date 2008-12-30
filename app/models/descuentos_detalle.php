<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los detalles de los descuentos.
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
 * La clase encapsula la logica de acceso a datos asociada a los detalles de los descuentos.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class DescuentosDetalle extends AppModel {
	

	var $belongsTo = array(	'Descuento' =>
                        array('className'    => 'Descuento',
                              'foreignKey'   => 'descuento_id'));
	
}
?>