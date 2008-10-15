<?php
/**
 * Este es un helper CakePHP que sirve para dar distintos tipos personalizados de formato
 */

class PaginadorHelper extends AppHelper {

	var $helpers=array("Paginator", "Formulario", "Ajax");

/**
 * Generates a sorting link
 *
 * @param  string $title Title for the link.
 * @param  string $key The name of the key that the recordset should be sorted.
 * @param  array $options Options for sorting link. See #options for list of keys.
 * @return string A link sorting default by 'asc'. If the resultset is sorted 'asc' by the specified
 *                key the returned link will sort by 'desc'.
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
				$options['url'] = am($options['url'], $this->params[$nombre]);
			}
		}
		$model = $options['model'];
		unset($options['model']);
		return $this->Paginator->sort($title, $model . "." . $key, $options);
	}
		
	function paginador($accion, $opciones = array()) {
		$retorno = "";
		/**
		* Si no estan seteadas la variables de la paginacion, no hago nada con el paginador.
		*/
		$model = Inflector::classify($this->Paginator->params['controller']);
		if(empty($this->Paginator->params['paging'][$model]['count']) || $this->Paginator->params['paging'][$model]['count'] == 0) {
			return $retorno;
		}
		
		switch ($accion) {
			case "posicion": {
				$retorno.="\n".$this->Paginator->counter(array('format'=>'Pagina %page% de %pages%, %current% de %count%'));
				break;
			}
			case "navegacion":

				if($this->traerPreferencia("paginacion") == "ajax") {
					$targetId = "index";
					//$targetId = "contenido";
					if($this->traerPreferencia("lov_apertura") != "popup" && !empty($opciones['url']['targetId'])) {
						$targetId = $opciones['url']['targetId'];
					}
					$this->Paginator->options(am(array('update'=>$targetId), $this->Paginator->options, $opciones));
				}
				
				$params=$this->Paginator->params();
				$retorno.="\n<span>";
				if (isset($params['page']) && $params['page']>1) {
					$retorno.="\n".$this->Paginator->link($this->Formulario->image("primera.gif", array("alt"=>"Ir al primer registro")), array('page'=>1), am(array('escape'=>false), $opciones));
				}
				else {
					$retorno.= $this->Formulario->image("primeraoff.gif");
				}
				$retorno.="\n</span>";

				$retorno.="\n<span>";
				$prev = $this->Paginator->prev($this->Formulario->image("anterior.gif", array("alt"=>"Ir al registro anterior")), am(array('escape'=>false), $opciones));
				if (is_null($prev))
					$retorno.= $this->Formulario->image("anterioroff.gif");
				else
					$retorno.="\n" . $prev;
				$retorno.="\n</span>";

				$retorno.="\n<span>";
				$next = $this->Paginator->next($this->Formulario->image("siguiente.gif", array("alt"=>"Ir al siguiente registro")), am($opciones, array('escape'=>false)));

				if (is_null($next))
					$retorno.= $this->Formulario->image("siguienteoff.gif");
				else
					$retorno.="\n" . $next;
				$retorno.="\n</span>";
				
				$retorno.="\n<span>";
				if (isset($params['page']) && $params['page']<$params['pageCount']) {
					$retorno.="\n".$this->Paginator->link($this->Formulario->image("ultima.gif", array("alt"=>"Ir al ultimo registro")), array('page'=>$params['pageCount']), am(array('escape'=>false), $opciones));
				}
				else
				{
					$retorno.= $this->Formulario->image("ultimaoff.gif");
				}
				$retorno.="\n</span>";
				break;
			}
		return $retorno;
	}
}
?>