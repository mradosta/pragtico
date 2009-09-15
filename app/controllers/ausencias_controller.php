<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las ausencias.
 * Las ausencias son cuando un trabajador no se presenta a trabajar a un empleador (una relacion laboral).
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
 * La clase encapsula la logica de negocio asociada a las ausencias.
 * Se refiere a cuando un trabajador no se presenta a trabajar para con un empleador (una relacion laboral).
 *
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class AusenciasController extends AppController {


    var $paginate = array(
        'order' => array(
            'Ausencia.desde' => 'desc'
        )
    );

    var $helpers = array('Documento');

    function reporte_ausencias_liquidadas() {
        
        if (!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] === 'generar') {

            $conditions['(Liquidacion.group_id & ' . $this->data['Condicion']['Bar-grupo_id'] . ') >'] = 0;
            
            if (!empty($this->data['Condicion']['Bar-empleador_id'])) {
                $conditions['Liquidacion.empleador_id'] = $this->data['Condicion']['Bar-empleador_id'];
            }

            if (!empty($this->data['Condicion']['Bar-periodo_largo'])) {
                $period = $this->Util->format($this->data['Condicion']['Bar-periodo_largo'], 'periodo');
                $conditions['Liquidacion.ano'] = $period['ano'];
                $conditions['Liquidacion.mes'] = $period['mes'];
            }
            
            $conditions['Liquidacion.tipo'] = 'Normal';
            $conditions['Liquidacion.estado'] = 'Confirmada';

            $this->Ausencia->AusenciasSeguimiento->Liquidacion->Behaviors->detach('Permisos');
            $this->Ausencia->AusenciasSeguimiento->Behaviors->detach('Permisos');
            //        'Liquidacion.LiquidacionesDetalle',
            $this->set('data', $this->Ausencia->AusenciasSeguimiento->find('all', array(
                'contain'       => array(
                    'Ausencia' => array('order' => array('Ausencia.relacion_id'),
                    'AusenciasMotivo',
                    'Relacion' => array('Empleador', 'Trabajador'))),
                'conditions'    => array(
                    'AusenciasSeguimiento.liquidacion_id' => Set::extract('/Liquidacion/id', $this->Ausencia->AusenciasSeguimiento->Liquidacion->find('all',
                        array(  'recursive'     => -1,
                                'fields'        => array('Liquidacion.id'),
                                'conditions'    => $conditions)))))));

            $this->set('fileFormat', $this->data['Condicion']['Bar-file_format']);
        }
    }

    
    function index() {
        if (!empty($this->data['Condicion']['AusenciasSeguimiento-estado'])) {
            
            $this->Paginador->setWhiteList('AusenciasSeguimiento-estado');
            $this->Paginador->setCondition(array('Ausencia.id' => array_unique(Set::extract('/AusenciasSeguimiento/ausencia_id', $this->Ausencia->AusenciasSeguimiento->find('all', array(  'recursive' => -1,
                    'conditions' => array(
                        'AusenciasSeguimiento.estado' => $this->data['Condicion']['AusenciasSeguimiento-estado'])))))));
            //unset($this->data['Condicion']['AusenciasSeguimiento-estado']);
        }
        return parent::index();
    }

    
/**
* Permite confirmar las ausencias.
*/
	function confirmar() {
		$ids = $this->Util->extraerIds($this->data['seleccionMultiple']);
		if (!empty($ids)) {
			if ($this->Ausencia->AusenciasSeguimiento->updateAll(array("estado" => "'Confirmado'"), array("AusenciasSeguimiento.ausencia_id"=>$ids))) {
				$this->Session->setFlash("Se confirmaron correctamente las ausencias seleccionadas.", "ok");
			}
			else {
				$this->Session->setFlash("Ocurrio un error al intentar confirmar las ausencias.", "error");
			}
		}
		$this->redirect("index");
	}


/**
 * seguimientos.
 * Muestra via desglose el seguimiento de la ausencia.
 */
	function seguimientos($id) {
		$this->Ausencia->contain(array("AusenciasSeguimiento"));
		$this->data = $this->Ausencia->read(null, $id);
	}

    
/**
 * trabajadores.
 * Muestra via desglose los trabajdores.
 */
    function trabajadores($id) {
        $this->Ausencia->contain(array('Relacion.Trabajador.Localidad'));
        $this->data = $this->Ausencia->read(null, $id);
    }
    

/**
* Imprimir.
*/    
	function imprimir() {
		$this->Ausencia->contain(array("Relacion.Empleador", "Relacion.Trabajador"));
		$condiciones = $this->Paginador->generarCondicion($this->data);
		$registros = $this->Ausencia->findAll($condiciones, null, "Ausencia.desde, Relacion.id");
		$this->set("registros", $registros);
	}


}
?>