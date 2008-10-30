<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las horas de una relacion laboral.
 * Las horas puedenser horas extras, horas de enfermedad, etc.
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
 * La clase encapsula la logica de negocio asociada a las horas de una relacion laboral.
 * Las horas puedenser horas extras, horas de enfermedad, etc.
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */

 //App::import('Vendor', "oleread", true, array(APP . "vendors" . DS . "phpExcelReader" . DS . "Excel"), "oleread.inc");
 //App::import('Vendor', "reader", true, array(APP . "vendors" . DS . "phpExcelReader" . DS . "Excel"), "reader.php");
 
class HorasController extends AppController {


/**
 * Add.
 */
	function add() {
        $this->set("estados", array("Pendiente"=>"Pendiente", "Confirmada"=>"Confirmada"));
        parent::add();
	}
	
	
/**
 * Index.
 */	
	function xindex() {
		$this->set("estados", array("Liquidada"=>"Liquidada", "Pendiente"=>"Pendiente"));
		$this->paginate = am($this->paginate, array('conditions' => array("Hora.estado"=>array("Liquidada", "Pendiente"))));
		parent::index();
	}


/**
* Permite confirmar las horas importadas desde la planilla.
*/
	function confirmar() {
		$ids = $this->Util->extraerIds($this->data['seleccionMultiple']);
		if(!empty($ids)) {
			if($this->Hora->updateAll(array("estado"=>"'Pendiente'"), array("Hora.id"=>$ids))) {
				$this->Session->setFlash("Se confirmaron correctamente las horas importadas.", "ok");
			}
			else {
				$this->Session->setFlash("Ocurrio un error al intentar confirmar las horas importadas.", "error");
			}
		}
		$this->redirect("index");
	}
/**
 * importarPlanillas.
 *
 * Importa las planillas extendida o resumida para el ingreso masivo de horas.
 */
	function importar_planilla() {
		/**
		* Me aseguro de mostrar solo las que estan en estodo Temporal (Sin confirmar).
		*/
		$this->__filasPorPagina();
		$this->paginate = am($this->paginate, array('conditions' => array("Hora.estado"=>"Temporal")));
		
		if(!empty($this->data['Formulario']['accion'])) {
			if($this->data['Formulario']['accion'] == "importar") {
				if(!empty($this->data['Hora']['planilla']['tmp_name'])) {
					set_include_path(get_include_path() . PATH_SEPARATOR . APP . "vendors" . DS . "PHPExcel" . DS . "Classes");
					App::import('Vendor', "IOFactory", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel"), "IOFactory.php");
					$objReader = PHPExcel_IOFactory::createReader('Excel5');
					$objPHPExcel = $objReader->load($this->data['Hora']['planilla']['tmp_name']);
					$mapeo[] = array("Horas"	=> array("Normal"			=> "E"));
					$mapeo[] = array("Horas"	=> array("Extra 50%"		=> "F"));
					$mapeo[] = array("Horas"	=> array("Extra 100%"		=> "G"));
					$mapeo[] = array("Horas"	=> array("Ajuste Normal"	=> "H"));
					$mapeo[] = array("Horas"	=> array("Ajuste Extra 50%"	=> "I"));
					$mapeo[] = array("Horas"	=> array("Ajuste Extra 100%"=> "J"));
					$mapeo[] = array("Ausencias"=> array("Motivo"			=> "K"));
					$mapeo[] = array("Ausencias"=> array("Dias"				=> "L"));
					$mapeo[] = array("Vales"	=> array("Importe"			=> "M"));
					for($i=10; $i<=$objPHPExcel->getActiveSheet()->getHighestRow(); $i++) {
						foreach($mapeo as $v) {
							foreach($v as $k=>$v1) {
								$data[$k][$objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue()][key($v1)] = $objPHPExcel->getActiveSheet()->getCell($v1[key($v1)] . $i)->getValue();
							}
						}
					}
				
					$data = new Spreadsheet_Excel_Reader();
					$data->setOutputEncoding("CP1251");
					$data->read($this->data['Hora']['planilla']['tmp_name']);
					/**
					* Solo resumida.
					*/
					$periodoDefault = null;
					if(preg_match(VALID_PERIODO, $this->data['Hora']['periodo'])) {
						$periodoDefault = $this->data['Hora']['periodo'];
					}
					/**
					* Recorro cada fila de la hoja excel.
					*/
					foreach($data->sheets[0]['cells'] as $k=>$v) {
						/**
						* Las filas de titulos y cabeceras las descarto.
						*/
						if($k<5) {
							continue;
						}
						$tipos[6] = "Periodo";
						$tipos[7] = "Normal";
						$tipos[8] = "Extra 50%";
						$tipos[9] = "Extra 100%";
						$tipos[10] = "Ajuste Normal";
						$tipos[11] = "Ajuste Extra 50%";
						$tipos[12] = "Ajuste Extra 100%";
						
						$periodo = $cantidad = $tipo = $observacion = null;
						if(!empty($v[14])) {
							$observacion = $v[14];
						}
						if(!empty($v[6])) {
							$periodo = $v[6];
							$periodo = strtoupper($periodo);
							if(!preg_match(VALID_PERIODO, $periodo)) {
								if(!empty($periodoDefault)) {
									$periodo = $periodoDefault;
								}
								else {
									continue;
								}
							}
						}
						elseif(!empty($periodoDefault)) {
							$periodo = $periodoDefault;
						}
						else {
							continue;
						}

						/**
						* Recorro cada columna, omitiendo aquellas que no tienen valores de las horas.
						*/
						foreach($v as $k1=>$v1) {
							if($k1 < 7 || $k1 > 12) {
								continue;
							}
							
							$cantidad = str_replace(",", ".", $v1);
							$tipo = $tipos[$k1];
						
							if(is_numeric($cantidad) && !empty($periodo)) {
								$save = array();
								$save['Hora']['relacion_id'] = trim(array_shift(explode("||", $v[1])));
								$save['Hora']['periodo'] = $periodo;
								$save['Hora']['tipo'] = $tipo;
								$save['Hora']['estado'] = "Temporal";
								$save['Hora']['cantidad'] = $cantidad;
								$save['Hora']['observacion'] = $observacion;
								$this->Hora->create();
								$this->Hora->save($save);
							}
						}
					}
				}
			}
			elseif ($this->data['Formulario']['accion'] == "cancelar") {
				$this->redirect("index");
			}
		}
		/**
		* Pagino los resultados
		*/
		$this->Hora->contain(array("Relacion", "Relacion.Trabajador", "Relacion.Empleador"));
		$resultados = $this->Paginador->paginar(array("Hora.estado"=>"Temporal"));
		$group = "Hora.relacion_id, Hora.tipo, Hora.periodo, Hora.estado";
		foreach($resultados['registros'] as $k=>$v) {
			$condiciones = array("Hora.relacion_id"=>$v['Hora']['relacion_id'], "Hora.tipo"=>$v['Hora']['tipo'], "Hora.periodo"=>$v['Hora']['periodo'], "Hora.estado"=>array("Pendiente", "Liquidada"));
			$cantidad = $this->Hora->find("all", array("fields"=>"SUM(Hora.cantidad) as cantidad", "group"=>$group, "conditions"=>$condiciones));
			if(!empty($cantidad[0][0]['cantidad'])) {
				$resultados['registros'][$k]['Hora']['confirmadas'] = $cantidad[0][0]['cantidad'];
			}
			else {
				$resultados['registros'][$k]['Hora']['confirmadas'] = 0;
			}
		}
		//d($resultados);
		//d($resultados['totales']);
		$this->set('registros', $resultados['registros']);
		$this->set('totales', $resultados['totales']);
	}

}

?>