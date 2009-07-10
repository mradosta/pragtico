<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los trabajadores.
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
 * La clase encapsula la logica de negocio asociada a los trabajadores.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class TrabajadoresController extends AppController {


	var $helpers = array('Documento');


    function importar_cbus() {
        if (!empty($this->data['Formulario']['accion'])) {
            if ($this->data['Formulario']['accion'] === 'importar') {
                if (!empty($this->data['Trabajador']['planilla']['tmp_name'])) {
                    set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes');
                    App::import('Vendor', 'IOFactory', true, array(APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel'), 'IOFactory.php');
                    
                    if (preg_match("/.*\.xls$/", $this->data['Trabajador']['planilla']['name'])) {
                        $objReader = PHPExcel_IOFactory::createReader('Excel5');
                    } elseif (preg_match("/.*\.xlsx$/", $this->data['Trabajador']['planilla']['name'])) {
                        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                    }
                    $objPHPExcel = $objReader->load($this->data['Trabajador']['planilla']['tmp_name']);

                    $c = 0;
                    $this->Trabajador->unbindModel(array('belongsTo' => array_keys($this->Trabajador->belongsTo)));
                    for($i = 3; $i <= $objPHPExcel->getActiveSheet()->getHighestRow(); $i++) {
                        if ($this->Trabajador->updateAll(array(
                            'Trabajador.solicitar_tarjeta_debito'   => "'No'",
                            'Trabajador.cbu'                        => "'" .
                            str_replace('\'', '', $objPHPExcel->getActiveSheet()->getCell('i' . $i)->getValue()) . "'"),
                                                            array("REPLACE(Trabajador.cuil, '-', '') like"   =>
                            $objPHPExcel->getActiveSheet()->getCell('F' . $i)->getValue()))) {
                                $c++;
                            }
                    }

                    if ($c > 0) {
                        $this->Session->setFlash('Se actualizaron ' . $c . ' Cbus.', 'ok');
                    } else {
                        $this->Session->setFlash('No fue posible actualizar ningun Cbu. Verifique la planilla', 'error');
                    }
                    $this->redirect('index');
                }
            } elseif ($this->data['Formulario']['accion'] === 'cancelar') {
                $this->redirect('index');
            }
        }
    }


    function __generateDebitCardFile($conditions) {

        $data = $this->Trabajador->find('all', array(
            'contain'       => array('Localidad.Provincia', 'Empleador'),
            'conditions'    => $conditions));
        
        if (!empty($data)) {
            $this->set('data', $data);
            /** Update state to avoid selecting again next time */
            $this->Trabajador->updateAll(
                array('Trabajador.solicitar_tarjeta_debito' => "'Solicitud en Proceso'"),
                array('Trabajador.id' => Set::extract('/Trabajador/id', $data)));

        } else {
            $this->Session->setFlash('No se encontraron trabajadores a los cuales solicitar tarjeta de debito.', 'error');
            $this->History->goBack();
        }
    }

    
    function solicitar_tarjetas_debito() {


        $groups = $this->Util->getUserGroups();
        if (empty($groups)) {
            $conditions['Trabajador.solicitar_tarjeta_debito'] = 'Si';
            $this->__generateDebitCardFile($conditions);
        } elseif (!empty($groups) && empty($this->data)) {
            $this->set('grupos', $groups);
        } elseif (!empty($this->data['Condicion']['Trabajador-grupo_id'])) {
            $conditions['Trabajador.solicitar_tarjeta_debito'] = 'Si';
            $conditions['(Trabajador.group_id & ' . $this->data['Condicion']['Trabajador-grupo_id'] . ') >'] = 0;
            $this->__generateDebitCardFile($conditions);
        }
    }
	

/**
 * Permite descargar y/o mostrar la foto del trabajador.
 */
	function descargar($id) {
		$trabajador = $this->Trabajador->findById($id);
		$archivo['data'] = $trabajador['Trabajador']['file_data'];
		$archivo['size'] = $trabajador['Trabajador']['file_size'];
		$archivo['type'] = $trabajador['Trabajador']['file_type'];
		$archivo['name'] = $this->Util->getFileName($trabajador['Trabajador']['nombre'], $trabajador['Trabajador']['file_type']);
		$this->set("archivo", $archivo);
		if (!empty($this->params['named']['mostrar']) && $this->params['named']['mostrar'] == true) {
			$this->set("mostrar", true);
		}
		$this->render("../elements/descargar", "descargar");
	}

/**
 * Relaciones.
 * Muestra via desglose las Relaciones Laborales existentes entre un trabajador y un empleador.
 */
	function relaciones($id) {
		$this->Trabajador->contain(array("Empleador"));
		$this->data = $this->Trabajador->read(null, $id);
	}




}
?>