<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las localidades.
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
 * La clase encapsula la logica de negocio asociada a las localidades.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class LocalidadesController extends AppController {


    var $paginate = array(
        'order' => array(
            'Provincia.nombre' => 'asc',
            'Localidad.nombre' => 'asc'
        )
    );

}	
?>