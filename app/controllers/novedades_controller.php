<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las novedades.
 * Una novedad es un ingreso de datos al sistema no confirmado aun.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.controllers
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a las novedades.
 * Una novedad es un ingreso de datos al sistema no confirmado aun.
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */

class NovedadesController extends AppController {

	//function index() {
		//d($this->data);
	//}
	function novedades() {
		$tipos = array("Excel5"=>"Excel", "Excel2007"=>"Excel 2007");
		if(!empty($this->data)) {
			$registros = $this->Relacion->find("all",
				array("contain"	=> array("ConveniosCategoria", "Trabajador", "Empleador"),
					"conditions"=> array("Relacion.empleador_id"=>$this->data['Condicion']['Relacion-empleador_id'])));
			$this->set("motivos", $this->Relacion->Ausencia->AusenciasMotivo->find("list",
				array("fields"	=> array("AusenciasMotivo.id", "AusenciasMotivo.motivo"))));
			$this->set("registros", $registros);
			$this->set("tipo", $this->data['Condicion']['Bar-tipo']);
			$this->layout = "ajax";
		}
		$this->set("tipos", $tipos);
		$this->data['Condicion']['Bar-tipo'] = "Excel2007";
	}
}

?>