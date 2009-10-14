<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los familiares de los trabajadores.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.controllers
 * @since           Pragtico v 1.0.0
 * @version         $Revision: 996 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2009-09-19 14:06:21 -0300 (Sat, 19 Sep 2009) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a los familiares de los trabajadores.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class FamiliaresController extends AppController {

    var $paginate = array(
        'order' => array(
            'Familiar.trabajador_id'    => 'asc',
            'Familiar.apellido'         => 'asc',
            'Familiar.nombre'           => 'asc'
        )
    );


}
?>