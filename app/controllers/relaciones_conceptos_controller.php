<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los conceptos propios
 * de las relaciones laborales que existen entre los trabajador y los empleadores .
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pragmatia de RPB S.A.
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
 * La clase encapsula la logica de negocio asociada a los conceptos de las relaciones laborales.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class RelacionesConceptosController extends AppController {


	function add() {
		/**
		* Detecto si viene de un addRapido.
		*/
		if (!empty($this->data['Form']['tipo']) && $this->data['Form']['tipo'] == "addRapido" && !empty($this->data['RelacionesConcepto']['relacion_id'])) {

			if ($this->data['Form']['accion'] == "grabar") {
				$registros = explode("*||*", $this->data['Form']['valores_derecha']);

				foreach ($registros as $registro) {
					$seleccionados[] = array_shift(explode("|", $registro));
				}
				
				$conceptosSeleccionados = $this->RelacionesConcepto->Concepto->find("list", array("recursive"=>-1, "conditions"=>array("Concepto.codigo"=>$seleccionados)));
				$conceptosRelacion = $this->RelacionesConcepto->find("list", array("fields" => "RelacionesConcepto.concepto_id", "contain" => "Concepto", "conditions"=>array("RelacionesConcepto.relacion_id"=>$this->data['RelacionesConcepto']['relacion_id'])));
				
				$quitar = array_diff($conceptosRelacion, $conceptosSeleccionados);
				$agregar = array_diff($conceptosSeleccionados, $conceptosRelacion);
				$this->RelacionesConcepto->begin();
				$c = 0;
				foreach ($quitar as $k=>$v) {
					if ($this->RelacionesConcepto->del($k)) {
						$c++;
					}
				}
				
				$save['RelacionesConcepto']['relacion_id'] = $this->data['RelacionesConcepto']['relacion_id'];
				foreach ($agregar as $k=>$v) {
					$this->RelacionesConcepto->create();
					$save['RelacionesConcepto']['concepto_id'] = $v;
					$this->RelacionesConcepto->set($save);
					if ($this->RelacionesConcepto->save($save)) {
						$c++;
					}
				}
				if ($c == count($agregar) + count($quitar)) {
					$this->Session->setFlash("La operacion se realizo con exito.", "ok", array("warnings"=>$this->{$this->modelClass}->getWarning()));
					$this->RelacionesConcepto->commit();
				}
				else {
					$dbError = $this->{$this->modelClass}->getError();
					$this->Session->setFlash("Los cambios no pudieron guardarse.", "error", array("errores"=>$dbError));
					$this->RelacionesConcepto->rollBack();
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

		if (!empty($this->passedArgs['RelacionesConcepto.relacion_id'])) {
			$this->RelacionesConcepto->Relacion->contain("ConveniosCategoria", "Trabajador", "Empleador", "RelacionesConcepto.Concepto");
			$relacion = $this->RelacionesConcepto->Relacion->findById($this->passedArgs['RelacionesConcepto.relacion_id']);

			$conceptosAsignados = Set::extract("/Concepto", $relacion['RelacionesConcepto']);
			$conceptosAsignadosCodigos = Set::extract("/Concepto/codigo", $conceptosAsignados);
			$conceptosNoAsignados = $this->RelacionesConcepto->Concepto->find("all",
				array(	"recursive"	=>	-1,
						"conditions"=>
							array("NOT"=>array("Concepto.codigo"=>$conceptosAsignadosCodigos))));
			
			$this->set("relacion", $relacion);
			$this->set("datosIzquierda", $conceptosNoAsignados);
			$this->set("datosDerecha", $conceptosAsignados);
		}
		else {
			$this->Session->setFlash("Debe seleccionar una relacion.", 'error');
			$this->History->goBack(2);
		}
	}


	function actualizarTablaIzquierda() {
		if (isset($this->params['named']['partialText'])) {
			$this->params['named']['partialText'] = str_replace("[EXPANSOR]", "%", str_replace("[SPACE]", " ", $this->params['named']['partialText']));
			$condiciones = array("Concepto.nombre like"=>$this->params['named']['partialText']);
			unset($this->params['named']['partialText']);
		}
		if (isset($this->params['named']['selectedId'])){
			$condiciones = array("Concepto.id"=>$this->params['named']['selectedId']);
			unset($this->params['named']['selectedId']);
		}

		$conceptos = $this->RelacionesConcepto->Concepto->find("all", array("conditions"=>$condiciones));
		$data = array();
		foreach ($conceptos as $v) {
			$data[$v['Concepto']['id']] = $v['Concepto']['nombre'];
		}
		$tablaSiemple = $this->Util->generarCuerpoTablaSimple($data);
		$this->set("cuerpo", $tablaSiemple['cuerpo']);
		$this->set("encabezados", $tablaSiemple['encabezados']);
		$this->render("../elements/tablas_from_to/tabla");
	}


}
?>