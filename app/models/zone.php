<?php
/**
 * Database abstraction layer.
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
 * Afip Zones.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Zone extends AppModel {

    var $permissions = array('permissions' => 508, 'group' => 'none', 'role' => 'higher');
    
    var $validate = array(
        'name' => array(
            array(
                'rule'      => VALID_NOT_EMPTY,
                'message'   => 'Zone name must not be empty.')
    ));
}
?>