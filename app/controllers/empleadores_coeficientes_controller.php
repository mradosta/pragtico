<?php
/**
 * Este archivo contiene toda la logica de negocio asociada al manejo de los coefientes
 * relacionados a cada empleador.
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
 * La clase encapsula la logica de negocio asociada al manejo de los coefientes
 * relacionados a cada empleador.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class EmpleadoresCoeficientesController extends AppController {


    function add_rapido() {
        $empleadoresCoeficientes = Set::combine($this->EmpleadoresCoeficiente->find('all', array(
          'recursive'   => -1,
          'conditions'  => array('EmpleadoresCoeficiente.empleador_id' => $this->params['named']['EmpleadoresCoeficiente.empleador_id']))), '{n}.EmpleadoresCoeficiente.coeficiente_id', '{n}.EmpleadoresCoeficiente');
        foreach ($this->EmpleadoresCoeficiente->Coeficiente->find('all', array(
            'order' => array('Coeficiente.tipo', 'Coeficiente.nombre'))) as $v) {
            $v['EmpleadoresCoeficiente']['id'] = null;
            $v['EmpleadoresCoeficiente']['porcentaje'] = 0;
            if (isset($empleadoresCoeficientes[$v['Coeficiente']['id']])) {
                $v['EmpleadoresCoeficiente']['id'] = $empleadoresCoeficientes[$v['Coeficiente']['id']]['id'];
                $v['EmpleadoresCoeficiente']['porcentaje'] = $empleadoresCoeficientes[$v['Coeficiente']['id']]['porcentaje'];
            }
            $coefientes[] = $v;
        }
        $this->set('coefientes', $coefientes);
    }

    function save() {
        if (!empty($this->data['Form']['accion']) && $this->data['Form']['accion'] === 'grabar') {
            foreach ($this->data as $k => $v) {
                if (!empty($v['EmpleadoresCoeficiente']['delete']) && !empty($v['EmpleadoresCoeficiente']['id'])) {
                    $delete[] = $v['EmpleadoresCoeficiente']['id'];
                } elseif (!empty($v['EmpleadoresCoeficiente']['porcentaje'])) {
                    $data[] = $v;
                }
            }
            if (!empty($delete)) {
                $this->EmpleadoresCoeficiente->deleteAll(array('EmpleadoresCoeficiente.id' => $delete));
            }
            $this->data['Form']['accion'] = 'grabar';
        }
        if (!empty($data)) {
            return parent::save($data);
        } else {
            return parent::save();
        }
    }
    
}
?>