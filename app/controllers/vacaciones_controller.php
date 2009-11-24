<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las vacaciones.
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
 * La clase encapsula la logica de negocio asociada a las vacaciones.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class VacacionesController extends AppController {

    var $helpers = array('Documento');

    var $paginate = array(
		'conditions' => array(
			'Relacion.estado' => 'Activa'
		),
        'order' => array(
            'Vacacion.periodo' => 'desc'
        )
    );


    function notificaciones($id = null) {

        if (!empty($id)) {
            $ids[] = $id;
        } else {
            $ids = $this->Util->extraerIds($this->data['seleccionMultiple']);
        }

        $this->set('data', $this->Vacacion->find('all', array(
            'contain'       => array(
                'VacacionesDetalle' => array('conditions' => array(
                    'VacacionesDetalle.estado' => array('Confirmado', 'Liquidado'))),
                'Relacion' => array('Empleador', 'Trabajador')),
            'conditions'    => array('Vacacion.id' => $ids))));
    }

    function generar_dias() {
        if (!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] === 'generar') {
            if (empty($this->data['Condicion']['Bar-periodo_largo'])) {
                $this->Session->setFlash('Debe seleccionar el periodo.', 'error');
                $this->History->goBack();
            } elseif (!is_numeric($this->data['Condicion']['Bar-periodo_largo']) || $this->data['Condicion']['Bar-periodo_largo'] > 2035 || $this->data['Condicion']['Bar-periodo_largo'] < 2000) {
                $this->Session->setFlash('El periodo seleccionado no es correcto.', 'error');
                $this->History->goBack();
            } else {
                
                if (!empty($this->data['Condicion']['Bar-grupo_id'])) {
                    $conditions['(Relacion.group_id & ' . $this->data['Condicion']['Bar-grupo_id'] . ') >'] = 0;
                }
                
                if (!empty($this->data['Condicion']['Bar-empleador_id'])) {
                    $conditions['Relacion.empleador_id'] = explode('**||**', $this->data['Condicion']['Bar-empleador_id']);
                }

                /*
                $conditions['NOT'] = array('Relacion.id' =>
                    Set::extract('/Relacion/id', $this->Vacacion->find('all', array(
                        'contain'       => 'Relacion',
                        'conditions'    => array_merge($conditions, array(
                            'Vacacion.periodo' => $this->data['Condicion']['Bar-periodo_largo']))))));
                */

                $baseFormula = str_replace('#fecha_hasta_periodo_vacacional', 'date(' . str_replace('-', ',', $this->data['Condicion']['Bar-periodo_largo'] . '-12-31') . ')',  '=if(and(month(#fecha_ingreso)>6,year(#fecha_ingreso)=year(#fecha_hasta_periodo_vacacional),day(#fecha_ingreso)>=1),int(if(networkdays(#fecha_ingreso,#fecha_hasta_periodo_vacacional)=132,14,networkdays(#fecha_ingreso,#fecha_hasta_periodo_vacacional)/20)),if(and(month(#fecha_ingreso)<6,year(#fecha_ingreso)=year(#fecha_hasta_periodo_vacacional)),14,if((year(#fecha_hasta_periodo_vacacional)-year(#fecha_ingreso))<=5,14,if((year(#fecha_hasta_periodo_vacacional)-year(#fecha_ingreso))<=10,21,if((year(#fecha_hasta_periodo_vacacional)-year(#fecha_ingreso))<=15,28,35)))))');

                App::import('Vendor', 'formulas', 'pragmatia');
                $Formulas = new Formulas();

                foreach ($this->Vacacion->Relacion->find('all', array(
                    'contain'       => array('Vacacion' => array(
                        'conditions' => array('Vacacion.periodo' => $this->data['Condicion']['Bar-periodo_largo']))),
                    'conditions'    => $conditions)) as $relation) {

                    $formula = str_replace('#fecha_ingreso', 'date(' . str_replace('-', ',', $relation['Relacion']['ingreso']) . ')', $baseFormula);

                    if (empty($relation['Vacacion'])) {
                        $id = null;
                    } else {
                        $id = $relation['Vacacion'][0]['id'];
                    }
                    $saveAll[] = array('Vacacion' => array(
                        'id'            => $id,
                        'relacion_id'   => $relation['Relacion']['id'],
                        'periodo'       => $this->data['Condicion']['Bar-periodo_largo'],
                        'observacion'   => 'Generado / Actualizado el ' . date('Y-m-d'),
                        'corresponde'   => $Formulas->resolver($formula)));
                }

                if (!empty($saveAll) && $this->Vacacion->saveAll($saveAll)) {
                    $this->Session->setFlash('Se generaron / actualizaron ' . count($saveAll) . ' vacaciones.', 'ok');
                    $this->History->goBack(2);
                } else {
                    $this->Session->setFlash('No fue posible generar dias de vacaciones', 'error');
                    $this->History->goBack();
                }
            }
        }
    }

/**
 * Details.
 * Show hollyday's details as an ajax breakdown..
 */
    function detalles($id) {
        $this->Vacacion->contain(array('VacacionesDetalle'));
        $this->data = $this->Vacacion->read(null, $id);
    }    
}
?>