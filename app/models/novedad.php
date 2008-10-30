<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las novedades.
 * Una novedad es un ingreso de datos al sistema no confirmado aun.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.models
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a las novedades.
 * Una novedad es un ingreso de datos al sistema no confirmado aun.
 *
 * @package		pragtico
 * @subpackage	app.models
 */
class Novedad extends AppModel {

/**
 * El orden por defecto.
 *
 * @var array
 * @access public
*/
	var $order = array("periodo", "tipo");


/**
 * Los modificaciones al comportamiento estandar de app_controller.php
 *
 * @var array
 * @access public
*/
	var $modificadores = array(	"index"=>array(	"contain"=>array("Relacion.Empleador",
																"Relacion.Trabajador")),
								"edit"=>array(	"contain"=>array("Relacion.Empleador",
																"Relacion.Trabajador")));

/**
 * Los permisos por defecto con los que se guardaran los datos de este model.
 *
 * @var integer
 * @access protected
 */
	//protected $__permissions = 122;
	
/**
 * Las opciones validadas de formatos de planillas que se podran generar e importar.
 *
 * @var array
 * @access public
 */
	var $opciones = array("formato"=>array("Excel5"=>"Excel", "Excel2007"=>"Excel 2007"));
	
	var $belongsTo = array(	'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'));


/**
 * Graba las novedades provenientes desde la planilla.
 * Maneja transacciones.
 *
 * @param array $datos Los datos a grabar.
 * @param array $periodo El periodo al cual se asignaran los datos de las novedades.
 * @return boolean True si fue posible guardar las novedades ingresadas, false en otro caso
 * @access public 
 */
 	function grabar($datos, $periodo) {
		if(!preg_match(VALID_PERIODO, $periodo) || empty($datos) || !is_array($datos)) {
			return false;
		}
		
		$this->begin();
		$cOk = $cTotal = 0;
		foreach($datos as $tipo => $data) {
			foreach($data as $relacion_id => $registro) {
				$save = null;
				$save['Novedad']['id'] = null;
				$save['Novedad']['periodo'] = $periodo;
				$save['Novedad']['tipo'] = $tipo;
				$save['Novedad']['relacion_id'] = $relacion_id;
				$save['Novedad']['data'] = serialize($registro);
				$cTotal++;
				if($this->save($save)) {
					$cOk++;
				}
			}
		}
		
		if($cTotal === $cOk) {
			$this->commit();
			return true;
		}
		else {
			$this->rollBack();
			return false;
		}
	}


/**
 * Distribuye las novedades en las diferecntes tablas (horas, ausencias, descuentos).
 *
 * @param array $ids Los ids de las novedades a distribuir en cada tabla.
 * @return mixed Cantidad de novedades distribuidas. False en caso de error o que no hayn podido confirmarse todos los ids.
 * @access public
 */
	function distribuir($ids) {
		$novedades = $this->find("all", array("conditions"=>array("Novedad.id"=>$ids), "recursive"=>-1));
		$c = $i = $ii = 0;
		
		foreach($novedades as $novedad) {
			$data = unserialize($novedad['Novedad']['data']);
			switch($novedad['Novedad']['tipo']) {
				case "Horas":
					foreach($data as $tipo=>$cantidad) {
						$saves[$i]['Hora']['id'] = null;
						$saves[$i]['Hora']['tipo'] = $tipo;
						$saves[$i]['Hora']['cantidad'] = $cantidad;
						$saves[$i]['Hora']['estado'] = "Confirmada";
						$saves[$i]['Hora']['relacion_id'] = $novedad['Novedad']['relacion_id'];
						$saves[$i]['Hora']['periodo'] = $novedad['Novedad']['periodo'];
						$saves[$i]['Hora']['observacion'] = "Ingresado desde planilla";
						$i++;
					}
				break;
				case "Ausencias":
					$motivo = $this->Relacion->Ausencia->AusenciasMotivo->findByMotivo($data['Motivo']);
					$periodo = $this->getPeriodo($novedad['Novedad']['periodo']);
					/**
					* Si no cargo el motivo, o este no exuste, lo pongo como justificado.
					*/
					if(empty($motivo['Motivo']['id'])) {
						$motivo['Motivo']['id'] = "1";
					}
					$saves[$i]['Ausencia']['id'] = null;
					$saves[$i]['Ausencia']['relacion_id'] = $novedad['Novedad']['relacion_id'];
					$saves[$i]['Ausencia']['ausencia_motivo_id'] = $motivo['Motivo']['id'];
					$saves[$i]['AusenciasSeguimiento'][$ii]['dias'] = $data['Dias'];
					$saves[$i]['AusenciasSeguimiento'][$ii]['desde'] = $this->format($periodo['fechaInicio'], "date");
					$saves[$i]['AusenciasSeguimiento'][$ii]['observacion'] = "Ingresado desde planilla";
					$saves[$i]['AusenciasSeguimiento'][$ii]['estado'] = "Confirmado";
					$ii++;
					$i++;
				break;
				case "Vales":
					$periodo = $this->getPeriodo($novedad['Novedad']['periodo']);
					$saves[$i]['Descuento']['id'] = null;
					$saves[$i]['Descuento']['alta'] = $this->format($periodo['fechaInicio'], "date");
					$saves[$i]['Descuento']['desde'] = $this->format($periodo['fechaInicio'], "date");
					$saves[$i]['Descuento']['relacion_id'] = $novedad['Novedad']['relacion_id'];
					$saves[$i]['Descuento']['monto'] = $data['Importe'];
					$saves[$i]['Descuento']['tipo'] = "Vale";
					$saves[$i]['Descuento']['descontar'] = array("1");
					$saves[$i]['Descuento']['concurrencia'] = "Permite superponer";
					$saves[$i]['Descuento']['estado'] = "Activo";
					$saves[$i]['Descuento']['observacion'] = "Ingresado desde planilla";
					$i++;
				break;
			}
		}
		
		$this->begin();
		foreach($saves as $save) {
			$keys = array_keys($save);
			if($this->Relacion->{$keys[0]}->save($save, true, array(), false)) {
				$c++;
			}
		}
		
		if($i === $c) {
			$this->deleteAll(array("Novedad.id"=>$ids), false, false, false);
			$this->commit();
			return $i;
		}
		else {
			$this->rollback();
			return false;
		}
	}

	function getIngresosPosibles() {
		$Concepto = new Concepto();
		$conceptos = $Concepto->find("all", array("conditions"=>array("Concepto.novedad"=>"Si"), "recursive"=>-1));
		//$
		d($conceptos);
		//$this->data['Condicion']['Novedad-tipo'] = array("Horas", "Ausencias", "Vales");
		//$this->Novedad->getTiposIngreso();
	}

}
?>