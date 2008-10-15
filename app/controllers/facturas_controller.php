<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a la facturacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.controllers
 * @since			Pragtico v 1.0.0
 * @version			1.0.0
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a la facturacion.
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class FacturasController extends AppController {

	var $uses = array("Factura", "Empleador");
	var $helpers = array("Pdf");


	function resumen() {
		if(!isset($this->data['Resumen']['tipo'])) {
			$this->data['Resumen']['tipo'] = "resumido";
		}
		if(!empty($this->data['Condicion']['Liquidacion-periodo']) && !empty($this->data['Condicion']['Liquidacion-empleador_id'])) {
			$periodo = $this->Util->traerPeriodo($this->data['Condicion']['Liquidacion-periodo']);
			if(!empty($periodo)) {
				$this->data['Condicion']['Liquidacion-ano'] = $periodo['ano'];
				$this->data['Condicion']['Liquidacion-mes'] = $periodo['mes'];
				$this->data['Condicion']['Liquidacion-periodo'] = $periodo['periodo'];
				$condiciones = $this->Paginador->generarCondicion($this->data);
				//$this->data['Resumen']['tipo'] = "resumido";
				$registros = $this->Factura->resumen($condiciones, $this->data['Resumen']['tipo']);
				$this->set("registros", $registros);
				$this->set("condiciones", $condiciones);
				//$this->render("resumen_resumido_pdf", "pdf");
				$this->render("resumen_" . $this->data['Resumen']['tipo'] . "_pdf", "pdf");
			}
		}
		$this->set("tipos", array("resumido"=>"Resumido", "detallado"=>"Detallado"));
	}
	
	
	function prefacturar() {
		if(!empty($this->data['Condicion']['Liquidacion-empleador_id']) && !empty($this->data['Condicion']['Liquidacion-periodo']) && $this->data['Formulario']['accion'] == "buscar") {
			/**
			* Obtengo el periodo separado por ano, mes y periodo propiamente dicho.
			//$periodo = $this->Util->traerPeriodo($this->data['Facturacion']['periodo']);
			if($periodo === false) {
				$this->Session->setFlash("Debe especificar un periodo valido.", "error");
				$this->redirect("prefacturar");
			}
			*/
			//$condiciones['Liquidacion.empleador_id'] = $this->data['Condicion']['Relacion-empleador_id'];
			//$condiciones['Liquidacion.mes'] = $periodo['mes'];
			//$condiciones['Liquidacion.ano'] = $periodo['ano'];
			//$condiciones['Liquidacion.periodo'] = $this->data['Condicion']['periodo'];
			//$condiciones['Liquidacion.estado'] = $this->data['Facturacion']['estado'];
			//d($this->data);
			//$facturacion = $this->Factura->calcularCoeficientes($this->data);
			$ids = $this->Factura->prefacturar($this->data);
			unset($this->data['Condicion']);
			//d($facturacion);
			//$facturacion = $this->Factura->calcularCoeficientes($condiciones);
			//$this->Empleador->contain();
			//$this->set("registros", am($this->Empleador->findById($condiciones['Liquidacion.empleador_id']), array("coefientes"=>$facturacion)));
		}
		else {
			$ids = false;
			$this->data['Facturacion']['estado'] = "indistinto";
		}
		$this->Factura->contain(array("Empleador", "FacturasDetalle"));
		$resultados = $this->Paginador->paginar(array("Factura.id"=>$ids));
        $this->set('registros', $resultados['registros']);
		$this->set("estados", array("Confirmada"=>"Solo Liquidaciones Confirmadas", "Sin Confirmar"=>"Solo Liquidaciones Sin Confirmar", "indistinto"=>"Indistinto"));
	}
	

/**
 * Detalles
 * Muestra via desglose los detalles de una factura.
 */
	function detalles($id) {
		$this->Factura->contain("FacturasDetalle.Coeficiente");
		$this->data = $this->Factura->read(null, $id);
	}

}
?>