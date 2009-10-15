<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada al historico de relaciones laborales..
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.models
 * @since           Pragtico v 1.0.0
 * @version         $Revision: 812 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2009-07-31 16:14:15 -0300 (Fri, 31 Jul 2009) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociadaal historico de relaciones laborales.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class RelacionesHistorica extends AppModel {

    var $permissions = array('permissions' => 496, 'group' => 'default', 'role' => 'all');

    var $validate = array(
        'relacion_id' => array(
            array(
                'rule'      => VALID_NOT_EMPTY,
                'message'   => 'Debe seleccionar la relacion laboral.')
        )
    );

    var $modificadores = array( 'index' =>
            array('contain' => array('Relacion' => array('Empleador', 'Trabajador'))),
                                'edit'  =>
            array('contain' => array('Relacion' => array('Empleador', 'Trabajador'))));
    
    var $belongsTo = array('Relacion', 'EgresosMotivo');


}
?>