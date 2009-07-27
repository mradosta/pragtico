<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los convenios.
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
 * La clase encapsula la logica de negocio asociada a los convenios.
 *
 * Son los convenios colectivos.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class ConveniosController extends AppController {


    var $paginate = array(
        'order' => array(
            'Convenio.nombre' => 'asc'
        )
    );


/**
 * Permite descargar el archivo del convenio colectivo.
 */
	function descargar($id) {
		$convenio = $this->Convenio->findById($id);
		$archivo['data'] = $convenio['Convenio']['file_data'];
		$archivo['size'] = $convenio['Convenio']['file_size'];
		$archivo['type'] = $convenio['Convenio']['file_type'];
		$archivo['name'] = $this->Util->getFileName($convenio['Convenio']['nombre'], $convenio['Convenio']['file_type']);
		$this->set("archivo", $archivo);
		$this->render("../elements/descargar", "descargar");
	}


/**
 * Categorias.
 * Muestra via desglose las categorias de los convenios colectivos.
 */
	function categorias($id) {
		$this->Convenio->contain(array("ConveniosCategoria.ConveniosCategoriasHistorico"));
		$this->data = $this->Convenio->read(null, $id);
	}


/**
 * Antiguedades.
 * Muestra via desglose las antiguedades de los convenios colectivos.
 */
	function antiguedades($id) {
		$this->Convenio->contain(array("ConveniosAntiguedad"));
		$this->data = $this->Convenio->read(null, $id);
	}


/**
 * Conceptos.
 * Muestra via desglose los conceptos asociados a este convenio colectivo.
 */
	function conceptos($id) {
		$this->Convenio->contain(array("Concepto"));
		$this->data = $this->Convenio->read(null, $id);
	}
	

/**
 * Informaciones.
 * Muestra via desglose los conceptos asociados a este convenio colectivo.
 */
	function informaciones($id) {
		$this->Convenio->contain(array("ConveniosInformacion.Informacion"));
		$this->data = $this->Convenio->read(null, $id);
	}
	
/**
 * Asigna un concepto a todos los trabajadores de todos los empleadores de un convenio.
 */
	function manipular_concepto($accion = null) {
		if (!empty($this->params['named']['concepto_id']) && !empty($this->params['named']['convenio_id'])
			&& is_numeric($this->params['named']['concepto_id']) && is_numeric($this->params['named']['convenio_id'])
			&& !empty($accion)) {
			$this->Convenio->ConveniosCategoria->contain();
			$conveniosCategoria = $this->Convenio->ConveniosCategoria->find("list", array("conditions"=>array("ConveniosCategoria.convenio_id"=>$this->params['named']['convenio_id'])));
			$this->Convenio->ConveniosCategoria->Relacion->contain();
			
			$relaciones = $this->Convenio->ConveniosCategoria->Relacion->find("list", array("fields"=>array("Relacion.id"), "conditions"=>array("Relacion.convenios_categoria_id"=>array_values($conveniosCategoria))));
			$c = $this->Convenio->ConveniosCategoria->Relacion->RelacionesConcepto->Concepto->agregarQuitarConcepto($relaciones, array($this->params['named']['concepto_id']), array("accion"=>$accion));
			if ($c > 0) {
				$this->Session->setFlash("El concepto se pudo " . $accion . " correctamente a " . $c . " trabajadores.", "ok");
			}
			else {
				$this->Session->setFlash("El concepto no se lo pudo " . $accion . " a ningun trabajador. Puede que ya haya estado asignado/quitado.", "warning");
			}
		}
		$this->redirect("index");
	}
}
?>