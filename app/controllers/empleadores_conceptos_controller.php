<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a la relacion que existe
 * entre los empleadores y los conceptos.
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
 * La clase encapsula la logica de negocio asociada a la relacion que existe
 * entre los empleadores y los conceptos.
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class EmpleadoresConceptosController extends AppController {

	function add() {
		/**
		* Detecto si viene de un addRapido.
		*/
		if(!empty($this->data['Form']['tipo']) && $this->data['Form']['tipo'] == "addRapido" && !empty($this->data['EmpleadoresConcepto']['empleador_id'])) {

			if($this->data['Form']['accion'] == "grabar") {
				$registros = explode("*||*", $this->data['Form']['valores_derecha']);

				foreach($registros as $registro) {
					$seleccionados[] = array_shift(explode("|", $registro));
				}
				
				$conceptosSeleccionados = $this->EmpleadoresConcepto->Concepto->find("list", array("recursive"=>-1, "conditions"=>array("Concepto.codigo"=>$seleccionados)));
				$conceptosConvenio = $this->EmpleadoresConcepto->find("list", array("fields"=>"EmpleadoresConcepto.empleador_id", "contain"=>"Concepto", "conditions"=>array("EmpleadoresConcepto.empleador_id"=>$this->data['EmpleadoresConcepto']['empleador_id'])));
				
				$quitar = array_diff($conceptosConvenio, $conceptosSeleccionados);
				$agregar = array_diff($conceptosSeleccionados, $conceptosConvenio);
				$this->EmpleadoresConcepto->begin();
				$c = 0;
				foreach($quitar as $k=>$v) {
					if($this->EmpleadoresConcepto->del($k)) {
						$c++;
					}
				}
				
				$save['EmpleadoresConcepto']['empleador_id'] = $this->data['EmpleadoresConcepto']['empleador_id'];
				foreach($agregar as $k=>$v) {
					$this->EmpleadoresConcepto->create();
					$save['EmpleadoresConcepto']['concepto_id'] = $v;
					$this->EmpleadoresConcepto->set($save);
					if($this->EmpleadoresConcepto->save($save)) {
						$c++;
					}
				}
				if($c == count($agregar) + count($quitar)) {
					$this->Session->setFlash("La operacion se realizo con exito.", "ok", array("warnings"=>$this->{$this->modelClass}->getWarning()));
					$this->EmpleadoresConcepto->commit();
				}
				else {
					$dbError = $this->{$this->modelClass}->getError();
					$this->Session->setFlash("Los cambios no pudieron guardarse.", "error", array("errores"=>$dbError));
					$this->EmpleadoresConcepto->rollBack();
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

		if(!empty($this->passedArgs['EmpleadoresConcepto.empleador_id'])) {
			$this->EmpleadoresConcepto->Empleador->contain(array("Concepto"));
			$empleador = $this->EmpleadoresConcepto->Empleador->findById($this->passedArgs['EmpleadoresConcepto.empleador_id']);

			$conceptosAsignados = Set::extract("/Concepto", $empleador['Concepto']);
			$conceptosAsignadosCodigos = Set::extract("/Concepto/codigo", $conceptosAsignados);
			$conceptosNoAsignados = $this->EmpleadoresConcepto->Concepto->find("all",
				array(	"recursive"	=>	-1,
						"conditions"=>
							array("NOT"=>array("Concepto.codigo"=>$conceptosAsignadosCodigos))));
			
			$this->set("empleador", $empleador);
			$this->set("datosIzquierda", $conceptosNoAsignados);
			$this->set("datosDerecha", $conceptosAsignados);
		}
		else {
			$this->Session->setFlash("Debe seleccionar un Empleador.", 'error');
			$this->History->goBack(2);
		}
	}

	function actualizarTablaIzquierda() {
		if(isset($this->params['named']['partialText'])) {
			$this->params['named']['partialText'] = str_replace("[EXPANSOR]", "%", str_replace("[SPACE]", " ", $this->params['named']['partialText']));
			$condiciones = array("Concepto.nombre like"=>$this->params['named']['partialText']);
			unset($this->params['named']['partialText']);
		}
		if(isset($this->params['named']['selectedId'])){
			$condiciones = array("Concepto.id"=>$this->params['named']['selectedId']);
			unset($this->params['named']['selectedId']);
		}

		$conceptos = $this->EmpleadoresConcepto->Concepto->find("all", array("conditions"=>$condiciones));
		$data = array();
		foreach($conceptos as $v) {
			$data[$v['Concepto']['id']] = $v['Concepto']['nombre'];
		}
		$tablaSiemple = $this->Util->generarCuerpoTablaSimple($data);
		$this->set("cuerpo", $tablaSiemple['cuerpo']);
		$this->set("encabezados", $tablaSiemple['encabezados']);
		$this->render("../elements/tablas_from_to/tabla");
	}


}
?>
