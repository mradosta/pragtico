<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los conceptos de los recibos.
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
 * La clase encapsula la logica de negocio asociada a los conceptos de los recibos.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class RecibosConceptosController extends AppController {


	function add() {
		/**
		* Detecto si viene de un addRapido.
		*/
		if (!empty($this->data['Form']['tipo']) && $this->data['Form']['tipo'] == "addRapido" && !empty($this->data['RecibosConcepto']['recibo_id'])) {

			if ($this->data['Form']['accion'] == "grabar") {
				$registros = explode("*||*", $this->data['Form']['valores_derecha']);

				foreach ($registros as $registro) {
					$seleccionados[] = array_shift(explode("|", $registro));
				}
				
				$conceptosSeleccionados = $this->RecibosConcepto->Concepto->find("list", array("recursive"=>-1, "conditions"=>array("Concepto.codigo"=>$seleccionados)));
				$conceptosConvenio = $this->RecibosConcepto->find("list", array("fields" => "RecibosConcepto.recibo_id", "contain" => "Concepto", "conditions"=>array("RecibosConcepto.recibo_id"=>$this->data['RecibosConcepto']['recibo_id'])));
				
				$quitar = array_diff($conceptosConvenio, $conceptosSeleccionados);
				$agregar = array_diff($conceptosSeleccionados, $conceptosConvenio);
				$this->RecibosConcepto->begin();
				$c = 0;
				foreach ($quitar as $k=>$v) {
					if ($this->RecibosConcepto->del($k)) {
						$c++;
					}
				}
				
				$save['RecibosConcepto']['recibo_id'] = $this->data['RecibosConcepto']['recibo_id'];
				foreach ($agregar as $k=>$v) {
					$this->RecibosConcepto->create();
					$save['RecibosConcepto']['concepto_id'] = $v;
					$this->RecibosConcepto->set($save);
					if ($this->RecibosConcepto->save($save)) {
						$c++;
					}
				}
				if ($c == count($agregar) + count($quitar)) {
					$this->Session->setFlash("La operacion se realizo con exito.", "ok", array("warnings"=>$this->{$this->modelClass}->getWarning()));
					$this->RecibosConcepto->commit();
				}
				else {
					$dbError = $this->{$this->modelClass}->getError();
					$this->Session->setFlash("Los cambios no pudieron guardarse.", "error", array("errores"=>$dbError));
					$this->RecibosConcepto->rollBack();
				}
			}
			$this->History->goBack(2);
		}
		else {
			parent::add();
		}
	}

/**
* Permite realizar un add mediante tablas fromto.
*/
	function add_rapido() {

		if (!empty($this->passedArgs['RecibosConcepto.recibo_id'])) {
			$this->RecibosConcepto->Recibo->contain(array("RecibosConcepto.Concepto", "Empleador"));
			$recibo = $this->RecibosConcepto->Recibo->findById($this->passedArgs['RecibosConcepto.recibo_id']);

			$conceptosAsignados = Set::extract("/Concepto", $recibo['RecibosConcepto']);
			$conceptosAsignadosCodigos = Set::extract("/Concepto/codigo", $conceptosAsignados);
			$conceptosNoAsignados = $this->RecibosConcepto->Concepto->find("all",
				array(	"recursive"	=>	-1,
						"conditions"=>
							array("NOT"=>array("Concepto.codigo"=>$conceptosAsignadosCodigos))));
			
			$this->set("recibo", $recibo);
			$this->set("datosIzquierda", $conceptosNoAsignados);
			$this->set("datosDerecha", $conceptosAsignados);
		}
		else {
			$this->Session->setFlash("Debe seleccionar un Recibo.", 'error');
			$this->History->goBack(2);
		}
	}
  	
  	
/**
* Permite realizar un add mediante tablas fromto.
*/
	function add_rapido_XXX() {
		$reciboId = $this->passedArgs['RecibosConcepto.recibo_id'];
		$recibo = $this->RecibosConcepto->Recibo->findById($reciboId);
		if (!empty($recibo['RecibosConcepto'])) {
			$idsConceptosAsignados = Set::combine($recibo['RecibosConcepto'], "{n}.concepto_id", "{n}.concepto_id");
		}
		else {
			$idsConceptosAsignados = array();
		}
		
		$this->RecibosConcepto->Concepto->contain();
		$conceptos = $this->RecibosConcepto->Concepto->findConceptos("Todos");
		$conceptosAsignados = $conceptosNoAsignados = array();
		foreach ($conceptos as $codigo=>$concepto) {
			if (in_array($concepto['id'], $idsConceptosAsignados)) {
				$conceptosAsignados[] = $concepto;
			}
			else {
				if ($concepto['imprimir'] == "Si" || $concepto['imprimir'] == "Solo con valor") {
					$conceptosNoAsignados[$codigo] = $concepto;
				}
			}
		}

		unset($recibo['RecibosConcepto']);
		$this->set("recibo", $recibo);
		$this->set("datosIzquierda", $conceptosNoAsignados);
		$this->set("datosDerecha", $conceptosAsignados);
		//$this->set("datosDerecha", $conceptosNoAsignados);
		$this->render("add_rapido");
	}


	function actualizarTablaIzquierda() {
		if (isset($this->params['named']['partialText'])) {
			$this->params['named']['partialText'] = str_replace("[EXPANSOR]", "%", str_replace("[SPACE]", " ", $this->params['named']['partialText']));
			$acciones = $this->RecibosConcepto->Concepto->find("all", array("conditions"=>array("Concepto.nombre like"=>$this->params['named']['partialText'] . "%"), "order"=>array("Concepto.nombre")));
		}
		else {
			$acciones = $this->RecibosConcepto->Concepto->find("all", array("conditions"=>array("Concepto.id"=>$this->params['named']['selectedId']), "order"=>array("Concepto.nombre")));
		}
		$data = $this->Util->combine($acciones, "{n}.Concepto.id", "{n}.Concepto.nombre");
		$tablaSiemple = $this->Util->generarCuerpoTablaSimple($data);
		$this->set("cuerpo", $tablaSiemple['cuerpo']);
		$this->set("encabezados", $tablaSiemple['encabezados']);
		$this->render("../elements/tablas_from_to/tabla");
	}



}
?>