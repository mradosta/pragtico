<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los empleadores.
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
 * La clase encapsula la logica de negocio asociada a los empleadores.
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class EmpleadoresController extends AppController {
	
/**
 * Add.
 */
	function add() {
		/**
		* Si esta grabando y selecciona que desea crear el area general por defecto, se la creo.
		*/
        if(!empty($this->data['Area']['crear_area_general']) && !empty($this->data['Form']['accion']) && $this->data['Form']['accion'] == "grabar") {
        	unset($this->data['Area']['crear_area_general']);
	        $this->data = am($this->data, array("Area"=>array(array("nombre"=>"General"))));
        }
        parent::add();
	}

/**
 * Cuentas
 * Muestra via desglose las Cuentas de un empleador.
 */
	function cuentas($id) {
		$this->Empleador->contain(array("Cuenta", "Cuenta.Sucursal.Banco"));
		$this->data = $this->Empleador->read(null, $id);
	}


/**
 * Suss
 * Muestra via desglose pagos de Suss realizados por un empleador.
 */
	function suss($id) {
		$this->Empleador->contain("Suss.Banco");
		$this->data = $this->Empleador->read(null, $id);
	}


/**
 * Areas
 * Muestra via desglose las Areas de un empleador.
 */
	function areas($id) {
		$this->Empleador->contain(array("Area"));
		$this->data = $this->Empleador->read(null, $id);
	}

	
/**
 * Recibos
 * Muestra via desglose los Recibos de un empleador.
 */
	function recibos($id) {
		$this->Empleador->contain(array("Recibo"));
		$this->data = $this->Empleador->read(null, $id);
	}
	
	
/**
 * Relaciones
 * Muestra via desglose las Relaciones Laborales existentes entre un trabajador y un empleador.
 */
	function relaciones($id) {
		$this->Empleador->contain(array("Trabajador"));
		$this->data = $this->Empleador->read(null, $id);
	}
	

/**
 * Conceptos.
 * Muestra via desglose los Conceptos relacionados al empleador (conceptos por defecto,
 * que se asignaran al crear una nueva relacion).
 */
	function conceptos($id) {
		$this->Empleador->contain(array("Concepto"));
		$this->data = $this->Empleador->read(null, $id);
	}


/**
 * Coeficientes.
 * Muestra via desglose los Coeficientes relacionados al empleador (coeficientes 
 * que se usaran al momento de facturar).
 */
	function coeficientes($id) {
		$this->Empleador->contain(array("Coeficiente"));
		$this->data = $this->Empleador->read(null, $id);
	}


/**
 * rubros
 * Muestra via desglose los Rubros de un empleador.
 */
	function rubros($id) {
		$this->Empleador->contain(array("Rubro"));
		$this->data = $this->Empleador->read(null, $id);
	}
	

/**
 * Asigna un recibo a los trabajadores del empleador.
 */
	function asignar_recibo() {
		if(!empty($this->params['named']['recibo_id']) && !empty($this->params['named']['empleador_id'])
			&& is_numeric($this->params['named']['recibo_id']) && is_numeric($this->params['named']['empleador_id'])) {
			$this->Empleador->contain("Trabajador", "Recibo.RecibosConcepto");
			$empleador = $this->Empleador->findById($this->params['named']['empleador_id']);
			
			foreach($empleador['Recibo'] as $r) {
				if($this->params['named']['recibo_id'] == $r['id']) {
					$recibo = $r;
					break;
				}
			}
			if(!empty($recibo)) {
				foreach($empleador['Trabajador'] as $k=>$v) {
					$relaciones[] = $v['Relacion']['id'];
				}
				foreach($recibo['RecibosConcepto'] as $r) {
					$conceptos[] = $r['concepto_id'];
				}
			}

			$c = $this->Empleador->EmpleadoresConcepto->Concepto->agregarQuitarConcepto($relaciones, $conceptos, array("accion"=>"agregar"));
			if($c > 0) {
				$this->Session->setFlash("Los conceptos del recibo se asignaron correctamente a " . $c . " trabajadores.", "ok");
			}
			else {
				$this->Session->setFlash("Los conceptos del recibo no se asignaron a ningun trabajador. Puede que ya hayan estado asignados.", "warning");
			}
		}
		$this->redirect("index");
	}


/**
 * Asigna un concepto a todos los trabajadores de un empleador.
 */
	function manipular_concepto($accion = null) {
		if(!empty($this->params['named']['concepto_id']) && !empty($this->params['named']['empleador_id'])
			&& is_numeric($this->params['named']['concepto_id']) && is_numeric($this->params['named']['empleador_id'])
			&& !empty($accion)) {
			$this->Empleador->contain("Trabajador");
			$empleador = $this->Empleador->findById($this->params['named']['empleador_id']);
			
			foreach($empleador['Trabajador'] as $k=>$v) {
				$relaciones[] = $v['Relacion']['id'];
			}
				
			$c = $this->Empleador->EmpleadoresConcepto->Concepto->agregarQuitarConcepto($relaciones, array($this->params['named']['concepto_id']), array("accion"=>$accion));
			if($c > 0) {
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