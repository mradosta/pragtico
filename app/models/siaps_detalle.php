<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada al detalle de cada version de Siap.
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
 * La clase encapsula la logica de acceso a datos asociada al detalle de cada version de Siap.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class SiapsDetalle extends AppModel {

    var $paginate = array(
        'order' => array(
            'Siap.version'          => 'desc',
            'SiapsDetalle.desde'    => 'asc'
        )
    );
    
	var $belongsTo = array('Siap');

}
?>