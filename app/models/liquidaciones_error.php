<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los datos errores de las liquidaciones.
 * Al momento que el formulador va calculando, pueden generarse errores en los datos, enlas formulas, etc
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
 * La clase encapsula la logica de acceso a datos asociada a los errores de las liquidaciones.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class LiquidacionesError extends AppModel {

	var $unique = array("liquidacion_id");

}
?>