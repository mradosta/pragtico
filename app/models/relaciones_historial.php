<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada al historial de las relaciones laborales..
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
 * La clase encapsula la logica de acceso a datos asociada al historial de las relaciones laborales.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class RelacionesHistorial extends AppModel {

    var $permissions = array('permissions' => 288, 'group' => 'default', 'role' => 'all');

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


    function beforeValidate($options = array()) {
        if (!empty($this->data['RelacionesHistorial']['fin']) && $this->data['RelacionesHistorial']['fin'] < date('Y-m-d')) {
            return false;
        } else {
            return parent::beforeValidate($options);
        }
    }
    
    function beforeSave($options = array()) {
        $this->Relacion->recursive = -1;
        $relation = $this->Relacion->findById($this->data['RelacionesHistorial']['relacion_id']);
        $this->data['RelacionesHistorial']['inicio'] = $relation['Relacion']['ingreso'];
        return parent::beforeSave($options);
    }

    function afterSave($created) {
        if (!empty($this->data['RelacionesHistorial']['relacion_id']) &&
            !empty($this->data['RelacionesHistorial']['liquidacion_final']) &&
            $this->data['RelacionesHistorial']['liquidacion_final'] == 'Suspender' &&
            !empty($this->data['RelacionesHistorial']['estado']) &&
            $this->data['RelacionesHistorial']['estado'] == 'Confirmado') {

            return $this->Relacion->save(array('Relacion' => array(
                'estado'    => 'Suspendida',
                'id'        => $this->data['RelacionesHistorial']['relacion_id'])));
        } else {
            return parent::afterSave($created);
        }
    }

}
?>