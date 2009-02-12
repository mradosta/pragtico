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
 * @version         $Revision: 234 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2009-01-21 11:36:13 -0200 (mié, 21 ene 2009) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * Afip Zones.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Zone extends AppModel {
    
    var $validate = array(
        'name' => array(
            array(
                'rule'      => VALID_NOT_EMPTY,
                'message'   => 'Zone name must not be empty.')
    ));
}
?>