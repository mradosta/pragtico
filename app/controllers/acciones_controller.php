<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las acciones.
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
 * La clase encapsula la logica de negocio asociada a las acciones.
 *
 * Se refiere a las actions de los controllers de cakephp.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class AccionesController extends AppController {

    var $paginate = array(
        'order' => array(
            'Controlador.nombre'    => 'asc',
            'Accion.nombre'         => 'asc'
        )
    );

}
?>