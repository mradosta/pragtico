<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a la relacion que existe
 * entre los convenios colectivos y los conceptos.
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
 * La clase encapsula la logica de negocio asociada a la relacion que existe
 * entre los convenios colectivos y los conceptos.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class ConveniosConceptosController extends AppController {

	function add() {
		/**
		* Detecto si viene de un addRapido.
		*/
		if (!empty($this->data['Form']['tipo']) && $this->data['Form']['tipo'] == "addRapido" && !empty($this->data['ConveniosConcepto']['convenio_id'])) {

			if ($this->data['Form']['accion'] == "grabar") {
				$registros = explode("*||*", $this->data['Form']['valores_derecha']);

				foreach ($registros as $registro) {
					$seleccionados[] = array_shift(explode("|", $registro));
				}
				
				$conceptosSeleccionados = $this->ConveniosConcepto->Concepto->find("list", array("recursive"=>-1, "conditions"=>array("Concepto.codigo"=>$seleccionados)));
				$conceptosConvenio = $this->ConveniosConcepto->find("list", array("fields" => "ConveniosConcepto.concepto_id", "contain" => "Concepto", "conditions"=>array("ConveniosConcepto.convenio_id"=>$this->data['ConveniosConcepto']['convenio_id'])));
				
				$quitar = array_diff($conceptosConvenio, $conceptosSeleccionados);
				$agregar = array_diff($conceptosSeleccionados, $conceptosConvenio);
				$this->ConveniosConcepto->begin();
				$c = 0;
				foreach ($quitar as $k=>$v) {
					if ($this->ConveniosConcepto->del($k)) {
						$c++;
					}
				}
				
				$save['ConveniosConcepto']['convenio_id'] = $this->data['ConveniosConcepto']['convenio_id'];
				foreach ($agregar as $k=>$v) {
					$this->ConveniosConcepto->create();
					$save['ConveniosConcepto']['concepto_id'] = $v;
					$this->ConveniosConcepto->set($save);
					if ($this->ConveniosConcepto->save($save)) {
						$c++;
					}
				}
				if ($c == count($agregar) + count($quitar)) {
					$this->Session->setFlash("La operacion se realizo con exito.", "ok", array("warnings"=>$this->{$this->modelClass}->getWarning()));
					$this->ConveniosConcepto->commit();
				}
				else {
					$dbError = $this->{$this->modelClass}->getError();
					$this->Session->setFlash("Los cambios no pudieron guardarse.", "error", array("errores"=>$dbError));
					$this->ConveniosConcepto->rollBack();
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

		if (!empty($this->passedArgs['ConveniosConcepto.convenio_id'])) {
			$this->ConveniosConcepto->Convenio->contain(array("Concepto"));
			$convenio = $this->ConveniosConcepto->Convenio->findById($this->passedArgs['ConveniosConcepto.convenio_id']);

			$conceptosAsignados = Set::extract("/Concepto", $convenio);
			$conceptosAsignadosCodigos = Set::extract("/Concepto/codigo", $conceptosAsignados);
			$conceptosNoAsignados = $this->ConveniosConcepto->Concepto->find("all",
				array(	"recursive"	=>	-1,
						"conditions"=>
							array("NOT"=>array("Concepto.codigo"=>$conceptosAsignadosCodigos))));
			
			$this->set("convenio", $convenio);
			$this->set("datosIzquierda", $conceptosNoAsignados);
			$this->set("datosDerecha", $conceptosAsignados);
		}
		else {
			$this->Session->setFlash("Debe seleccionar un Convenio.", 'error');
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

		$conceptos = $this->ConveniosConcepto->Concepto->find("all", array("conditions"=>$condiciones));
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
