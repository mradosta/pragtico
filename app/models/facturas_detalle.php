<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los detalles de las facturas.
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
 * La clase encapsula la logica de acceso a datos asociada a los detalles de las facturas.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class FacturasDetalle extends AppModel {

	var $belongsTo = array('Factura', 'Coeficiente');

}
?>