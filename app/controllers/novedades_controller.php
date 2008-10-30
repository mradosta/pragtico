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

	var $helpers = array("Documento");
	
/**
 * Confirma las novedades seleccionadas.
 */
	function confirmar() {
		if(!empty($this->data['Formulario']['accion']) && $this->data['Formulario']['accion'] === "confirmar") {
			if($cantidad = $this->Novedad->distribuir($this->Util->extraerIds($this->data['seleccionMultiple']))) {
				$this->Session->setFlash("Se confrmaron correctamente " . $cantidad . " novedades", "ok");
			}
			else {
				$this->Session->setFlash("No fue posible confirmar las novedades", "error");
			}
		}
		$this->redirect("index");
	}

/**
 * detalles.
 * Muestra via desglose los detalles de la novedad.
 */
	function detalles($id) {
		$this->Novedad->recursive = -1;
		$data = $this->Novedad->read(null, $id);
		$this->set("data", unserialize($data['Novedad']['data']));
	}

	
/**
 * Importa una planilla en formato Excel2007 o Excel5 con las novedades.
 */
	function importar_planilla() {
		if(!empty($this->data['Formulario']['accion'])) {
			if($this->data['Formulario']['accion'] === "importar") {
				if(!empty($this->data['Novedad']['planilla']['tmp_name'])) {
					set_include_path(get_include_path() . PATH_SEPARATOR . APP . "vendors" . DS . "PHPExcel" . DS . "Classes");
					App::import('Vendor', "IOFactory", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel"), "IOFactory.php");
					
					if(preg_match("/.*\.xls$/", $this->data['Novedad']['planilla']['name'])) {
						$objReader = PHPExcel_IOFactory::createReader('Excel5');
					}
					elseif(preg_match("/.*\.xlsx$/", $this->data['Novedad']['planilla']['name'])) {
						$objReader = PHPExcel_IOFactory::createReader('Excel2007');
					}
					$objPHPExcel = $objReader->load($this->data['Novedad']['planilla']['tmp_name']);
					$mapeo[] = array("Horas"		=> array("Normal"						=> "E"));
					$mapeo[] = array("Horas"		=> array("Extra 50%"					=> "F"));
					$mapeo[] = array("Horas"		=> array("Extra 100%"					=> "G"));
					$mapeo[] = array("Horas"		=> array("Ajuste Normal"				=> "H"));
					$mapeo[] = array("Horas"		=> array("Ajuste Extra 50%"				=> "I"));
					$mapeo[] = array("Horas"		=> array("Ajuste Extra 100%"			=> "J"));
					$mapeo[] = array("Horas"		=> array("Normal Nocturna"				=> "K"));
					$mapeo[] = array("Horas"		=> array("Extra Nocturna 50%"			=> "L"));
					$mapeo[] = array("Horas"		=> array("Extra Nocturna 100%"			=> "M"));
					$mapeo[] = array("Horas"		=> array("Ajuste Normal Nocturna"		=> "N"));
					$mapeo[] = array("Horas"		=> array("Ajuste Extra Nocturna 50%"	=> "O"));
					$mapeo[] = array("Horas"		=> array("Ajuste Extra Nocturna 100%"	=> "P"));
					$mapeo[] = array("Ausencias"	=> array("Motivo"						=> "Q"));
					$mapeo[] = array("Ausencias"	=> array("Dias"							=> "R"));
					$mapeo[] = array("Vales"		=> array("Importe"						=> "S"));
					for($i=10; $i<=$objPHPExcel->getActiveSheet()->getHighestRow(); $i++) {
						foreach($mapeo as $v) {
							foreach($v as $k=>$v1) {
								$valor = $objPHPExcel->getActiveSheet()->getCell($v1[key($v1)] . $i)->getValue();
								if(!empty($valor)) {
									$datos[$k][$objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue()][key($v1)] = $objPHPExcel->getActiveSheet()->getCell($v1[key($v1)] . $i)->getValue();
								}
							}
						}
					}
					if($this->Novedad->grabar($datos, $this->data['Novedad']['periodo'])) {
						$this->redirect("index");
					}
				}
			}
			elseif ($this->data['Formulario']['accion'] == "cancelar") {
				$this->redirect("index");
			}
		}
		$this->data['Novedad']['formato'] = "Excel2007";
	}
	
	
/**
 * Genera una planilla en formato Excel2007 o Excel5 para el ingreso de novedades.
 */
	function generar_planilla() {
		if(!empty($this->data)) {
			$formatoDocumento = $this->data['Condicion']['Novedad-formato'];
			$tipos = $this->data['Condicion']['Novedad-tipo'];
			unset($this->data['Condicion']['Novedad-formato']);
			unset($this->data['Condicion']['Novedad-tipo']);
			$conditions = $this->Paginador->generarCondicion();
			$registros = $this->Novedad->Relacion->find("all",
				array("contain"	=> array("ConveniosCategoria", "Trabajador", "Empleador"),
					"conditions"=> $conditions));
			$this->set("motivos", $this->Novedad->Relacion->Ausencia->AusenciasMotivo->find("list",
				array("fields"	=> array("AusenciasMotivo.id", "AusenciasMotivo.motivo"))));
			$this->set("registros", $registros);
			$this->set("formatoDocumento", $formatoDocumento);
			$this->set("tipos", $tipos);
			$this->layout = "ajax";
		}
		/**
		* Fijo lo que viene preseleccionado.
		*/
		$this->data['Condicion']['Novedad-formato'] = "Excel2007";
		$this->data['Condicion']['Novedad-tipo'] = array("Horas", "Ausencias", "Vales");
		//$this->Novedad->getIngresosPosibles();
	}
	

	
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