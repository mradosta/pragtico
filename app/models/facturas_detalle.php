<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los detalles de las facturas.
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
 * La clase encapsula la logica de acceso a datos asociada a los detalles de las facturas.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class FacturasDetalle extends AppModel {

	var $unique = array("factura_id", "coeficiente_id");
	
	var $belongsTo = array(	'Factura' =>
                        array('className'   => 'Factura',
                              'foreignKey' 	=> 'factura_id'),
							'Coeficiente' =>
                        array('className'   => 'Coeficiente',
                              'foreignKey' 	=> 'coeficiente_id'));

}
?>