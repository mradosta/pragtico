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
	
	function imprimir() {
		$this->set("registros", $this->Trabajador->find('all'));
		$this->layout = "ajax";
		//$this->render("index", "ajax");
		//$this->render("index");
	}

	/*
	function solicitar_tarjetas_debito() {
		$columns = array(	'cuil' 		=> 'Trabajador.cuil', 
							'tipo_doc'	=> 'Trabajador.tipo_documento', 
	   						'documento'	=> 'Trabajador.numero_documento',
		   					'apellido'	=> 'Trabajador.apellido',
							'nombre'	=> 'Trabajador.nombre', 
	   						'calle'		=> 'Trabajador.direccion',
		   					'numero'	=> '',
		  					'piso'		=> '',
		 					'depto'		=> '',
							'barrio'	=> 'Trabajador.barrio',
	   						'localidad'	=>
							'provincia' =>
							'cod'		=>
							'telefono'	=>
							'sexo'		=> 
							'estado'	=>
							'fecing'	=>
							'fecnac'	=>
							'empresa'	=>
							'suc'		=>
							'cp'		=>
							'cod dist'	=>
							'cod prov');
		d($columns);

		d(strtolower("'CUIL', 'TIPO_DOC', 'DOCUMENTO', 'APELLIDO', 'NOMBRE', 'CALLE', 'NUMERO', 'PISO', 'DEPTO', 'BARRIO', 'LOCALIDAD', 'PROVINCIA', 'COD', 'TELEFONO', 'SEXO', 'ESTADO', 'FECING', 'FECNAC', 'EMPRESA', 'SUC', 'CP', 'COD DIST', 'COD PROV'"));
		$conditions = array('Trabajador.cbu' => '', 'Trabajador.solicitar_tarjeta_debito' => 'Si');
		$data = $this->Trabajador->find('all', array('conditions' => $conditions, 'recursive' => -1, 'limit' => 5));
		d($data);
	}
	*/
	

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