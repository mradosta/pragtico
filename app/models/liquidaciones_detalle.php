<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los detalles de las liquidaciones.
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
 * La clase encapsula la logica de acceso a datos asociada a los detalles de las liquidaciones.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class LiquidacionesDetalle extends AppModel {

	var $unique = array("liquidacion_id", "concepto_id");

	var $belongsTo = array(	'Concepto' =>
                        array('className'   => 'Concepto',
                              'foreignKey' 	=> 'concepto_id'));

}
?>