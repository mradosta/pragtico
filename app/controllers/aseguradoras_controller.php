<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las aseguradoras.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Pragmatia de RPB S.A.
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
 * La clase encapsula la logica de negocio asociada a las aseguradoras.
 *
 * Se refiere a las aseguradoras (ARTs).
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class AseguradorasController extends AppController {

    var $paginate = array(
        'order' => array(
            'Aseguradora.nombre' => 'asc'
        )
    );    

}
?>