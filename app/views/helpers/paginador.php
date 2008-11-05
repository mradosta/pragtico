<?php
/**
 * Helper que me facilita la paginacion.
 *
 * Permite simplificar la creacion de los links para la nevagacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.views.helpers
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * Clase que contiene el helper para la paginacion.
 *
 * @package		pragtico
 * @subpackage	app.views.helpers
 */
class PaginadorHelper extends AppHelper {

/**
 * Los helpers que utilizare.
 *
 * @var arraya
 * @access public.
 */
	var $helpers = array("Paginator", "Formulario");

	
/**
 * Generea el link que permite ordenar una columna.
 *
 * @param  string $title Titulo del link (lo que se mostrara).
 * @param  string $key El nombre de la clave del recordset que debe ordenarse.
 * @param  array $options Las opciones posibles para el orden.
 * @return string Un link que permitira ordenar una columna en forma ascendente en forma predeterminada.
 * @access public.
 */
	function sort($title, $key = null, $options = array()) {
		$options['class'] = "sin_orden";
		$options['title'] = "Ordenar en forma ascendente";
		$options['url'] = array();
		
		$modelClass = Inflector::classify($this->params['controller']);
		if(isset($this->params['paging'][$modelClass]['options']['order'])) {
			if($options['model'] . "." . $this->Paginator->sortKey() == key($this->params['paging'][$modelClass]['options']['order'])) {
				if($key == $this->Paginator->sortKey()) {
					if($this->Paginator->sortDir() == "asc") {
						$options['class'] = "asc_orden";
						$options['title'] = "Ordenar en forma descendente";
						$options['url'] = array("direction"=>"desc");
					}
					else {
						$options['class'] = "desc_orden";
						$options['title'] = "Ordenar en forma ascendente";
						$options['url'] = array("direction"=>"asc");
					}
				}
			}
		}
		
		/**
		* Si no hay nada, puede que sea la primera vez que entra y puede que el model tenga un orden por defecto.
		*/
		else {
			$instanciaModel =& ClassRegistry::getObject($modelClass);
			if(!empty($instanciaModel->order)) {
				if(!empty($instanciaModel->order[$modelClass . "." . $key])) {
					if($instanciaModel->order[$modelClass . "." . $key] == "desc") {
						$options['class'] = "desc_orden";
						$options['title'] = "Ordenar en forma ascendente";
						$options['url'] = array("direction"=>"asc");
					}
					elseif($instanciaModel->order[$modelClass . "." . $key] == "asc") {
						$options['class'] = "asc_orden";
						$options['title'] = "Ordenar en forma descendente";
						$options['url'] = array("direction"=>"desc");
					}
				}
				else {
					$options['class'] = "sin_orden";
					$options['title'] = "Ordenar en forma ascendente";
					$options['url'] = array("direction"=>"asc");
				}
			}
		}
		

		/**
		* Me aseguro de no perder ningun parametro que venga via url.
		* Saco los propios del paginador.
		*/
		foreach(array("named", "pass") as $nombre) {
			if(!empty($this->params[$nombre])) {
				unset($this->params[$nombre]['direction']);
				unset($this->params[$nombre]['sort']);
				unset($this->params[$nombre]['page']);
				$options['url'] = array_merge($options['url'], $this->params[$nombre]);
			}
		}
		$model = $options['model'];
		unset($options['model']);
		return $this->Paginator->sort($title, $model . "." . $key, $options);
	}
	
	
/**
 * Generea un bloque con los objetos propios del paginador (posicion dentro del recordset y flechas de navegacion).
 *
 * @param  string $accion Indica el bloque que se generara:
 *						- posicion		(el numero de registro, de pagina, ...)
 *						- navegacion 	(las flechas)
 * @param  array $options Las opciones posibles para la creacion del bloque.
 * @return string Un bloque HTML.
 * @access public.
 */
	function paginador($accion = "posicion", $opciones = array()) {
		/**
		* Si no estan seteadas la variables de la paginacion, no hago nada con el paginador.
		*/
		$model = Inflector::classify($this->Paginator->params['controller']);
		if(empty($this->Paginator->params['paging'][$model]['count'])) {
			return "";
		}
		
		switch ($accion) {
			case "posicion": 
				return $this->Paginator->counter(array('format'=>'Pagina %page% de %pages%, %current% de %count%'));
			break;
			
			case "navegacion":

				$out = array();
				
				if($this->traerPreferencia("paginacion") == "ajax") {
					$targetId = "index";
					//$targetId = "contenido";
					if($this->traerPreferencia("lov_apertura") != "popup" && !empty($opciones['url']['targetId'])) {
						$targetId = $opciones['url']['targetId'];
					}
					$this->Paginator->options(am(array('update'=>$targetId), $this->Paginator->options, $opciones));
				}
				$params=$this->Paginator->params();
				
				
				$retorno = null;
				if (isset($params['page']) && $params['page']>1) {
					$retorno.= $this->Paginator->link($this->Formulario->image("primera.gif", array("alt"=>"Ir al primer registro")), array('page'=>1), array_merge(array('escape'=>false), $opciones));
				}
				else {
					$retorno.= $this->Formulario->image("primeraoff.gif");
				}
				$out[] = $this->Formulario->tag("span", $retorno);

				
				$retorno = null;
				$prev = $this->Paginator->prev($this->Formulario->image("anterior.gif", array("alt"=>"Ir al registro anterior")), array_merge(array('escape'=>false), $opciones));
				if (is_null($prev)) {
					$retorno.= $this->Formulario->image("anterioroff.gif");
				}
				else {
					$retorno.= $prev;
				}
				$out[] = $this->Formulario->tag("span", $retorno);

				
				$retorno = null;
				$next = $this->Paginator->next($this->Formulario->image("siguiente.gif", array("alt"=>"Ir al siguiente registro")), array_merge($opciones, array('escape'=>false)));
				if (is_null($next)) {
					$retorno.= $this->Formulario->image("siguienteoff.gif");
				}
				else {
					$retorno.= $next;
				}
				$out[] = $this->Formulario->tag("span", $retorno);

				
				$retorno = null;
				if (isset($params['page']) && $params['page']<$params['pageCount']) {
					$retorno.= $this->Paginator->link($this->Formulario->image("ultima.gif", array("alt"=>"Ir al ultimo registro")), array('page'=>$params['pageCount']), array_merge(array('escape'=>false), $opciones));
				}
				else {
					$retorno.= $this->Formulario->image("ultimaoff.gif");
				}
				$out[] = $this->Formulario->tag("span", $retorno);
				
				return implode("", $out);
			break;
		}
	}
}
?>