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


	var $helpers = array("Documento");
	
    function solicitud_tarjetas_debito() {
        $data = $this->Trabajador->find('all', array(
            'Trabajador.solicitar_tarjeta_debito' => 'Si'
        ));
        
        if (!empty($data)) {
            d($data);
        } else {
            $this->Session->setFlash('No se encontraron trabajadores a los cuales solicitar tarjeta de debito.', 'error');
        }
        $this->History->goBack(1);
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