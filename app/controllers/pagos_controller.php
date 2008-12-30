<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los pagos que se le realizan a las relaciones laborales.
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
 * La clase encapsula la logica de negocio asociada a los pagos que se le realizan a las relaciones laborales.
 *
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class PagosController extends AppController {


	var $helpers = array("Pdf");

	function __seteos() {
		/**
		* El periodo viene de la forma completa (YYYYMM1Q), para entrar a la query lo deje de la forma reducida,
		* ahora vuelvo a dejar lo de la forma completa.
		*/
		$tmp = $this->Session->read("filtros.Pagos.index.condiciones");
		if (!empty($tmp['Liquidacion.periodo like']) && !empty($tmp['Liquidacion.ano']) && !empty($tmp['Liquidacion.mes'])) {
			$this->data['Condicion']['Liquidacion-periodo'] = $tmp['Liquidacion.ano'] . $tmp['Liquidacion.mes'] . str_replace("%", "", $tmp['Liquidacion.periodo like']);
		}
	}


	
	function index() {
		if (!empty($this->data['Condicion']['Liquidacion-periodo'])) {
			$periodo = $this->Util->traerPeriodo($this->data['Condicion']['Liquidacion-periodo']);
			if (!empty($periodo)) {
				$this->data['Condicion']['Liquidacion-ano'] = $periodo['ano'];
				$this->data['Condicion']['Liquidacion-mes'] = $periodo['mes'];
				$this->data['Condicion']['Liquidacion-periodo'] = $periodo['periodo'];
			}
			//$this->paginate = array_merge($this->paginate, array("contain"=>array('PagosForma', 'Liquidacion', 'Relacion' => array('Empleador', 'Trabajador'))));
		}
		parent::index();
	}


/**
 * formas.
 * Muestra via desglose las formas de pago relacionadas a este pago.
 */
	function formas($id) {
		$this->Pago->contain(array("PagosForma"));
		$this->data = $this->Pago->read(null, $id);
	}



	function revertir_pago($id) {
		if ($this->Pago->revertir($id)) {
			$this->Session->setFlash("El pago se revirtio correctamente.", "ok");
		}
		else {
			$errores = $this->Pago->getError();
			$this->Session->setFlash("No fue posible revertir el Pago.", "error", array("errores"=>$errores));
		}
		$this->History->goBack();
	}

	
/**
 *
 */
	function detalle_cambio() {

		$pagos = null;
		//d($this->data['Condicion']);
		if (!empty($this->data['Condicion']['Relacion-empleador_id']) && !empty($this->data['Condicion']['Liquidacion-periodo']) && preg_match(VALID_PERIODO, $this->data['Condicion']['Liquidacion-periodo'], $matches)) {
			unset($this->data['Condicion']['Liquidacion-periodo']);
			$this->data['Condicion']['Liquidacion-ano'] = $matches[1];
			$this->data['Condicion']['Liquidacion-mes'] = $matches[2];
			$this->data['Condicion']['Liquidacion-periodo'] = $matches[3];
			
			$condiciones = $this->Paginador->generarCondicion($this->data);
			$pagos = $this->Pago->traerDetalleCambio($condiciones);
			if (empty($pagos)) {
				$this->Session->setFlash("No se encontraron datos con los criterios especificados. Verifique.", "error");
				$this->History->goBack(2);
			}
			else {
				$this->set("condiciones", $this->data['Condicion']);
				$this->set("data", $pagos);
				$this->layout = "pdf";
			}
		}
		else {
			$this->Session->setFlash("Debe ingresar el periodo y seleccionar el/los empleador/es.", "error");
			$this->History->goBack(2);
		}
    }

    
/**
 * Busca las cuentas relacionadas con un empleador. Esta funcion esta preparada para generar datos que se
 * pintaran en un control relacionado via Json.
 *
 * @param number $id Id del empleador del cual se desea recuperar sus cuentas relacionadas.
 * @return	void
 * @access public
 */
	function cuentas_relacionado($id) {
		if (is_numeric($id)) {
			$empleador = $this->Pago->Relacion->Empleador->findById($id);
			if (!empty($empleador['Cuenta'])) {
				$this->Pago->Relacion->Empleador->contain(array("Cuenta"));
				$c=0;
				foreach ($empleador['Cuenta'] as $k=>$v) {
					$this->Pago->Relacion->Empleador->Cuenta->Sucursal->contain(array("Banco"));
					$sucursal = $this->Pago->Relacion->Empleador->Cuenta->Sucursal->findById($v['sucursal_id']);
					$cuentas[$c]['optionValue'] = $v['id'];
					$cuentas[$c]['optionDisplay'] = $sucursal['Banco']['nombre'] . ", " . $sucursal['Sucursal']['direccion'];
					$c++;
				}
				
				$this->set("data", $cuentas);
				$this->render("../elements/json");
			}
		}
	}
	

/**
 * Permite la registracion masiva de pagos por algun medio (formas).
 *
 * @param string $tipo Las formas de pago masivo: efectivo, beneficios, deposito
 * @return	void
 * @access public
 */
	function registrar_pago_masivo($tipo) {
		if (!empty($tipo) && is_string($tipo) && in_array($tipo, array("efectivo", "beneficios", "deposito"))) {
			$ids = $this->Util->extraerIds($this->data['seleccionMultiple']);

			$cantidad = $this->Pago->registrarPago($ids, $tipo);
			if ($cantidad) {
				$this->Session->setFlash("Se confirmaron correctamente " . $cantidad . " de " . count($ids) . " pagos con " . ucfirst($tipo) . ".", "ok");
			}
			else {
				$this->Session->setFlash("Ocurrio un error al intentar confirmar los pagos con " . ucfirst($tipo) . ".", "error");
			}
		}
		$this->redirect("index");
	}	


/**
 * Permite generar un archivo con el formato especificado por cada banco para la acreditacion de haberes en las
 * cuentas de los trabajadores.
 *
 * @return	void
 * @access public
 */
	function generar_soporte_magnetico() {
		if (!empty($this->data['Soporte']['pago_id'])
			&& !empty($this->data['Soporte']['cuenta_id'])
			&& !empty($this->data['Soporte']['empleador_id'])) {
			
			$pagosIds = unserialize($this->data['Soporte']['pago_id']);
			$opciones = array(	"pago_id"				=> $pagosIds,
								"fecha_acreditacion"	=> "",
								"cuenta_id"				=> $this->data['Soporte']['cuenta_id'],
								"empleador_id"			=> $this->data['Soporte']['empleador_id']);
								
			if (!empty($this->data['Soporte']['fecha_acreditacion'])) {
				$opciones['fecha_acreditacion'] = $this->data['Soporte']['fecha_acreditacion'];
			}
			$archivo = $this->Pago->generarSoporteMagnetico($opciones);
			if (!empty($archivo)) {
				$this->set("archivo", array("contenido"=>$archivo['contenido'], "nombre"=>$archivo['banco'] . "-" . date("Y-m-d") . ".txt"));
				$this->render(".." . DS . "elements" . DS . "txt", "txt");
			}
			else {
				$this->Session->setFlash("Ocurrio un error al intentar generar el soporte magnetico. Ningun pago seleccionado es posible realizarlo con la cuenta seleccionada.", "error");
				$this->History->goBack();
			}
		}
		elseif (isset($this->data['seleccionMultiple'])) {
			$ids = $this->Util->extraerIds($this->data['seleccionMultiple']);
			$pagos = $this->Pago->find("all", array("contain" => "PagosForma", "conditions"=>array("Pago.moneda" => "Pesos", "Pago.estado" => "Pendiente", "Pago.id"=>$ids)));
			if (empty($pagos)) {
				$this->Session->setFlash("Ocurrio un error al intentar generar el soporte magnetico. Ningun pago seleccionado es valido.", "error");
				$this->History->goBack();
			}
			$this->set("ids", serialize($ids));
		}
	}

}
?>