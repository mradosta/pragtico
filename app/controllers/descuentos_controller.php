<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los descuentos.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.controllers
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a los descuentos.
 *
 * Se refiere a los posibles descuentos que puede tener un trabajador de una relacion laboral,
 * puede ser un vale, un embargo, un prestamo, etc.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class DescuentosController extends AppController {

    var $paginate = array(
        'order' => array(
            'Descuento.alta' => 'desc'
        )
    );


/**
 * detalles.
 * Muestra via desglose los detalles de un descuento.
 */
	function detalles($id) {
		$this->Descuento->contain("DescuentosDetalle");
		$this->data = $this->Descuento->read(null, $id);
	}

}
?>