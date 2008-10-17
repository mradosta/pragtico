<?php
/**
 * El objeto de este helper es sobrecargar los metodos del FormHelper de cakePHP, adaptandolos a mis necesidades
 */

class FormularioHelper extends AppHelper {
	var $helpers = array('Html', 'Form', 'Ajax', 'Session', 'Javascript', 'Paginador', 'Formato');


/**
 * Returns a formatted block tag, i.e DIV, SPAN, P.
 *
 * @param string $name Tag name.
 * @param string $text String content that will appear inside the div element.
 *			If null, only a start tag will be printed
 * @param array $attributes Additional HTML attributes of the DIV tag
 * @param boolean $escape If true, $text will be HTML-escaped
 * @return string The formatted tag element
 */
	function tag($name, $text = null, $attributes = array(), $escape = false) {
		if(is_array($text)) {
			$text = implode("", $text);
		}
		$out = $this->Html->tag($name, $text, $attributes, $escape);
		return $out;
	}
	
	function pintarFieldsets($fieldsets, $opcionesFs = array()) {

		/**
		* En caso de que no valide, me vendra seteada la accion en el Form, y esto causara un error.
		* Por esto, tomo el recaudo y los desseteo, por si a caso....
		*/
		unset($this->data['Form']);
		
		/**
		* Busco el nombre de la clase del model a partir del controller,
		* que es con lo que cuento dentro del helper.
		*/
		$model = Inflector::classify($this->params["controller"]);

		/**
		* A la legend del fieldset le agrego la accion (Nuevo, Modificar).
		* Siempre y cuando no me venga especificado que no lo haga (concatenarAccionLegend = false).
		if(isset($opcionesFs['fieldset']['legend'])
			&& $opcionesFs['fieldset']['legend'] !== false
				&& !isset($opcionesFs['fieldset']['concatenarAccionLegend'])
				|| (isset($opcionesFs['fieldset']['concatenarAccionLegend'])
				&& $opcionesFs['fieldset']['concatenarAccionLegend'] === true)) {
		*/
		if(!isset($opcionesFs['fieldset']['concatenarAccionLegend']) || (isset($opcionesFs['fieldset']['concatenarAccionLegend']) && $opcionesFs['fieldset']['concatenarAccionLegend'] === false)) {
			$legend = "";
			if(in_array($this->action, array('update', 'edit', 'saveMultiple'))) {
				$legend = "Modificar ";
			}
			elseif(in_array($this->action, array('add'))) {
				$legend = "Nuevo ingreso de ";
			}
			elseif(in_array($this->action, array('index'))) {
				$legend = "Buscar ";
			}
			if(!empty($opcionesFs['fieldset']['legend'])) {
				$opcionesFs['fieldset']['legend'] = $legend . " " . $opcionesFs['fieldset']['legend'];
			}
			else {
				$opcionesFs['fieldset']['legend'] = $legend . " " . $model;
			}
		}


		/**
		* Me aseguro de trabajar siempre con un array. Si no lo es, lo convierto en uno.
		*/
		if(!isset($this->data[0])) {
			$this->data = array($this->data);
		}
		/**
		* Separo los fieldSets de master de los de details.
		*/
		foreach($fieldsets as $fieldset) {
			$classes = null;
			if(empty($fieldset['opciones'])) {
				$fieldset['opciones'] = array();
			}
			
			if(empty($fieldset['opciones']['fieldset']['class']) || strpos("master", $fieldset['opciones']['fieldset']['class']) !== false) {
				if(empty($fieldset['opciones']['fieldset']['class'])) {
					$fieldset['opciones']['fieldset']['class'] = "master";
				}
				$fieldsetsMaster[] = $fieldset;
				$classes[] = "master";
			}
			else {
				$fieldsetsDetail[] = $fieldset;
				$classes[] = "detail";
			}
		}

		$salida = null;
		$cantidadRegistros = count($this->data);
		foreach($this->data as $k=>$v) {

			foreach($fieldsetsMaster as $key=>$fieldset) {
				$salidaMaster = null;
				foreach($fieldset['campos'] as $campo=>$opcionesCampo) {

					if(preg_match("/^Condicion./", $campo)) {
						$tmpName = $campo;
						$tmpName = preg_replace("/^Condicion./", "", $tmpName);
						list($model, $field) = explode("-", $tmpName);

						if(substr($field, strlen($field) - 7) == "__desde") {
							$field = str_replace("__desde", "", $field);
						}
						elseif(substr($field, strlen($field) - 7) == "__hasta") {
							$field = str_replace("__hasta", "", $field);
						}
					}
					else {
						list($model, $field) = explode(".", $campo);
					}

					if(isset($v[$model][$field])) {
						$opcionesCampo['value'] = $v[$model][$field];
					}

					if(!empty($this->validationErrors[$model][$k][$field]) || !empty($this->validationErrors[$model][$k][$model][$field])) {
						if(empty($opcionesCampo['after'])) {
							$opcionesCampo['after'] = "";
						}
						if(!empty($this->validationErrors[$model][$k][$field])) {
							$opcionesCampo['after'] .= $this->Html->tag("div", $this->validationErrors[$model][$k][$field], array("class" => "error-message"));
						}
						else {
							$opcionesCampo['after'] .= $this->Html->tag("div", $this->validationErrors[$model][$k][$model][$field], array("class" => "error-message"));
						}
					}
					/**
					* Si no se trata de una edicionMultiple o un formulario en el que haya mas de un fs,
					* no la complico con arrays y me manejo con la forma de cakePHP.
					*/
					if($cantidadRegistros > 1) {
						$opcionesCampo['name'] = "data[" . $k . "][" . $model . "][" . $field . "]";
					}
					$this->data = $v;
					$salidaMaster .= $this->input($campo, $opcionesCampo);
				}
				$fsMaster[$k][] = $this->bloque($salidaMaster, $fieldset['opciones']);
				$salidaMaster = null;

				if(!empty($fieldsetsDetail)) {
					$salidaDetail = null;
					foreach($fieldsetsDetail as $key=>$fieldset) {
						$modelsDetail = null;
						foreach($fieldset['campos'] as $campo=>$opcionesCampoDetail) {
							list($modelDetail, $fieldDetail) = explode(".", $campo);
							$modelsDetail[$modelDetail] = $modelDetail;
						}
						
						foreach($modelsDetail as $modelDetail) {
							/**
							* Cuando sea un nuevo registro, esto estara vacio. Lo creo vacio para que de una vuelta.
							*/
							if(empty($v[$modelDetail])) {
								$v[$modelDetail] = array(array());
							}
							
							foreach($v[$modelDetail] as $kDetail=>$vDetail) {
								$this->data = $vDetail;
								foreach($fieldset['campos'] as $campo=>$opcionesCampoDetail) {
									list($modelDetail, $fieldDetail) = explode(".", $campo);
									$opcionesCampoDetail['error'] = false;
									if(!empty($this->validationErrors[$model][$k][$modelDetail][$kDetail][$fieldDetail])) {
										if(empty($opcionesCampoDetail['after'])) {
											$opcionesCampoDetail['after'] = "";
										}
										$opcionesCampoDetail['after'] .= $this->Html->tag("div", $this->validationErrors[$model][$k][$modelDetail][$kDetail][$fieldDetail], array("class" => "error-message"));
									}

									if(isset($vDetail[$fieldDetail])) {
										$opcionesCampoDetail['value'] = $vDetail[$fieldDetail];
									}
									if($cantidadRegistros > 1) {
										$opcionesCampoDetail['name'] = "data[" . $k . "][" . $modelDetail . "][" . $kDetail . "][" . $fieldDetail . "]";
									}
									else {
										$opcionesCampoDetail['name'] = "data[" . $modelDetail . "][" . $kDetail . "][" . $fieldDetail . "]";
									}
									$salidaDetail .= $this->input($campo, $opcionesCampoDetail);
								}
								$fsDetail[$k][] = $this->bloque($salidaDetail, $fieldset['opciones']);
								$salidaDetail = null;
							}
						}
					}
				}
			}
		}

		if(count($fsMaster) > 1) {
			if(empty($opcionesFs['fieldset']['legend'])) {
				$legend = " (Registro ##NUMERO## de " . count($fsMaster) . ")";
			}
			else {
				$legend = $opcionesFs['fieldset']['legend'] . " (Registro ##NUMERO## de " . count($fsMaster) . ")";
			}
		}
		
		$salida = null;
		foreach($fsMaster as $k=>$v) {
			$return = null;
			foreach($v as $contenidoMaster) {
				$return .= $contenidoMaster;
			}
			if(!empty($fsDetail[$k])) {
				foreach($fsDetail[$k] as $contenidoDetail) {
					$return .= $contenidoDetail;
				}
			}
			if(count($fsMaster) > 1) {
				$k++;
				$opcionesFs['fieldset']['legend'] = str_replace("##NUMERO##", $k, $legend);
			}
			$opcionesFs['fieldset']['class'] = "fieldset_multiple";
			$salida .= $this->bloque($return, $opcionesFs);
			//$salida .= $this->tag("fieldset", $return, $opcionesFs);
		}
		return $salida;
	}



/**
 * Agrega a una variable privada (mia) de la clase View codigos Js.
 * Los puede searar en tres posibles ubicaciones/tipos donde iran (siempre al header):
 *			- ready:	Va a la funcion ready de JS.
 *			- view:		Va en el header, pero no dentro de la funcion ready de js.
 *			- links:	Crea los links a arcihvos js.
 */
	
	function addScript($script, $ubicacion = "ready") {
		$view =& ClassRegistry::getObject('view');
		if(in_array($ubicacion, array("ready", "links", "view"))) {
			$view->__myScripts[$ubicacion][] = $script;
		}
	}

/**
 * Returns a JavaScript script tag.
 *
 * @param  string $script The JavaScript to be wrapped in SCRIPT tags.
 * @param  boolean $allowCache Allows the script to be cached if non-event caching is active
 * @param  boolean $safe Wraps the script in an HTML comment and a CDATA block
 * @return string The full SCRIPT element, with the JavaScript inside it.
 */
	function codeBlock($script = null, $options = array()) {
		if(!empty($options['ready']) && $options['ready'] === true) {
			$script = "jQuery(document).ready(function($) {" . $script . "});";
		}
		if(isset($options['script']) && $options['script'] === false) {
			return $script;
		}
		
		return $this->Javascript->codeBlock($script, true, false);
	}

/**
 * Returns a formatted DIV tag for HTML FORMs.
 *
 * @param string $class CSS class name of the div element.
 * @param string $text String content that will appear inside the div element.
 *			If null, only a start tag will be printed
 * @param array $attributes Additional HTML attributes of the DIV tag
 * @param boolean $escape If true, $text will be HTML-escaped
 * @return string The formatted DIV element
 */
	function div($class = null, $text = null, $attributes = array(), $escape = false) {
		return $this->Html->div($class, $text, $attributes, $escape);
	}

/**
 * Crea un elemento IMG formateado xhtml.
 *
 * Se encarga de validar que el archivo exista (si no existe, el browser demora buscandolo hasta que salta por timeout).
 * Tambien asegura que siempre una IMG tenga los atributos title y alt, por compatibilidad con ambos navegadores que los interpretan disferente.
 *
 * @param string $path Path a un archivo de image, relativo a el directorio webroot/img/.
 * @param array	$htmlAttributes Array de attributos HTML.
 * @return string
 */
	function image($path, $htmlAttributes = array()) {
		/**
		* Siempre las rutas y los archivos van en minusculas.
		*/
		$path = strtolower($path);

		/**
		* Me aseguro que el archivo de la imagen exista, sino pongo una por defecto.
		*/
		if(!file_exists(WWW_ROOT . IMAGES_URL . $path)) {
			$path = "noimage.gif";
		}

		/**
		* Me aseguro de que siempre tenga los atributos title y alt cuando tenga uno de ellos por lo menos.
		*/
		if (isset($htmlAttributes['alt']) && !isset($htmlAttributes['title'])) {
			$htmlAttributes['title'] = $htmlAttributes['alt'];
		}
		elseif (isset($htmlAttributes['title']) && !isset($htmlAttributes['alt'])) {
			$htmlAttributes['alt'] = $htmlAttributes['title'];
		}

		return $this->output($this->Html->image($path, $htmlAttributes));
    }
    
    
/**
 * Crea una tabla html. Esta tabla tiene multiples opciones.
 *
 * @param array	$datos .
 *
 * Si no paso un array con los encabezados dentro del parametro "encabezado", y dentro de los atributos
 * de la tabla seteo a true el atributo "mostrarEncabezados" los saca de la lista de campos.
 *

 * Ejemplo del array de encabezados encaso de que lo quiera especificar manualmente.
 *
 * <code>
 * $encabezado[] = $paginador->sort('Campo 1', 'campo1', array("model"=>"Model1"));
 * $encabezado[] = $paginador->sort('Campo 2', 'campo2', array("model"=>"Model2"));
 * </code>
 *
 *
 * Ejemplo del array del cuerpo.
 *
 * <code>
 *
 *	$fila = array();
 *	$fila[] 	= array("model"=>"Model1", "field"=>"campo1", "valor"=>"xx1");
 *	$fila[] 	= array("model"=>"Model1", "field"=>"campo1", "valor"=>"xx1", "orden"=>false);
 *	$fila[] 	= array("model"=>"Model2", "field"=>"campo1", "valor"=>"xxx2", "nombreEncabezado"=>"Cod.");
 *	$fila[] 	= array("tipo"=>"idDetail", "model"=>"Model2", "field"=>"campo1", "valor"=>"xxx2", "nombreEncabezado"=>"Cod.");
 *	$fila[] 	= array("tipo"=>"accion", "model"=>"Model2", "field"=>"campo1", "valor"=>"xxx2", "nombreEncabezado"=>"Cod.");
 *	$fila[] 	= array("tipo"=>"valor", "model"=>"Model2", "field"=>"campo1", "valor"=>"xxx2", "nombreEncabezado"=>"Cod.");
 *	$cuerpo[] = $fila;
 *  Los tipos pueden ser:
 *			- valor: el valor que tiene para pintarse.
 *			- accion: codigo HTML que se pintara (ejemplo, un link que dispare un accion X).
 *			- idDetail: un array (urls) con las urls de las acciones.
 *			- desglose: datos para generar un desglose.
 *
 * Puedo tambien querer enviar opciones a la fila, en cuyo caso queda asi:
 *  $cuerpo[] = array("contenido"=>$fila, "opciones"=>array("class"=>"fila_resaltada", "seleccionMultiple"=>false, "eliminar"=>false, "modificar"=>false, "permisos"=>false));
 *
 * Ejemplo de uso de la funcion tabla.
 *
 * <code>
 * $tabla = $formulario->tabla(array(	"tabla"		=> array(	"class"				=>"grilla",
 *																"eliminar"			=>true,
 *																"seleccionLov"		=>false,
 *																"modificar"			=>true,
 *																"seleccionMultiple"	=>true,
 * 																"permisos"			=>true,
 *																"mostrarEncabezados"=>true,
 *																"ordenEnEncabezados"=>true,
 * 																"zebra"				=>true,
 * 																"simple"			=>false,
 *																"omitirMensajeVacio"=>false,
 *																"mostrarIds"		=>false),
 *									"encabezado" 	=> $encabezado,
 *									"cuerpo"		=> $cuerpo,
 * 									"pie"			=> $pie));
 * </code>
 * @return string
 */
	function tabla($datos = array()) {
		$tabla = "";
		/**
		* Especifico las opciones por defecto.
		*/
		$opciones = array(	'seleccionLov'		=> false,
							'seleccionMultiple'	=> true,
							'permisos'			=> true,
							'eliminar'			=> true,
							'modificar'			=> true,
							'mostrarEncabezados'=> true,
							'ordenEnEncabezados'=> true,
							'mostrarIds'		=> false,
							'omitirMensajeVacio'=> false,
							'zebra'				=> true,
							'simple'			=> false);

		if(!empty($datos['tabla'])) {
			$opciones = am($opciones, $datos['tabla']);
		}

		if(isset($this->params['named']['seleccionMultiple']) && $this->params['named']['seleccionMultiple'] == 0) {
			$opciones['seleccionMultiple'] = false;
		}
		
		$opcionesHtmlValidas = array('class', 'colspan', 'id');
		$opcionesHtml = array();
		foreach($opcionesHtmlValidas as $v) {
			if(isset($opciones[$v])) {
				$opcionesHtml[$v] = $opciones[$v];
			}
		}


		/**
		* Si es una tabla simple, no le pongo inteligencia, es decir,
		* pinto lo que me venga.
		*/
		if($opciones['simple']){
			foreach($datos['cuerpo'] as $f) {
				$cells = $headers = array();
				foreach($f as $columna) {
					if(empty($columna['type'])) {
						$columna['type'] == "cell";
					}
					if(empty($columna['opciones'])) {
						$columna['opciones'] = array();
					}
					
					if($columna['type'] == "header") {
						$headers[] = array($columna['valor'], $columna['opciones']);
					}
					elseif($columna['type'] == "cell") {
						$cells[] = array($columna['valor'], $columna['opciones']);
					}
				}
				
				if(!empty($headers)) {
					$filaHeaders[] = $headers;
				}
				
				if(!empty($cells)) {
					$filaCells[] = $cells;
				}
			}
			
			/**
			* La funcion tableHeaders del framework no maneja multiples rows de header, por eso uso la funcion
			* tableCells y reemplazo los tds por ths.
			*/
			$out[] = str_replace("td", "th", $this->Html->tableCells($filaHeaders));
			$out[] = $this->Html->tableCells($filaCells);
			return $this->output("\n\n<table " . $this->_parseAttributes($opcionesHtml, null, '', '') . ">" . $this->output(join("\n", $out)) . "\n</table>\n\n");
			
		}
		
		$encabezados = array();
		if(!empty($datos['encabezado'])) {
			$encabezados = $datos['encabezado'];
		}
		
		if(isset($datos['cuerpo']) && !empty($datos['cuerpo'])) {
			$cuerpo = array();

			if($opciones['permisos']) {
				/**
				* Lo agrego al array del cuerpo de la tabla.
				*/
				foreach($datos['cuerpo'] as $kk=>$vv) {
					/**
					* El contenido de la fila puede venir como un array puro o dentro del elemento contenido.
					*/
					$opcionesFila = array();
					if(!empty($vv['opciones'])) {
						$opcionesFila = $vv['opciones'];
					}

					/**
					* Agrego el desglose de los permisos.
					*/
					if(!(isset($opcionesFila['permisos']) && $opcionesFila['permisos'] === false)) {
						$contenido = false;
						if(!empty($vv['contenido'])) {
							$vv = $vv['contenido'];
							$contenido = true;
						}
						foreach($vv as $kk1=>$vv1) {
							if(isset($vv1['field']) && $vv1['field'] == "id" && !empty($vv1['valor'])) {
								$registroPermisos = array(
									"tipo"=>"desglose",
									"id"=>$vv1['valor'],
									"update"=>"desglose_permisos_" . $vv1['model'],
									"imagen"=>array("nombre"=>"permisos.gif",
													"alt"=>"Permisos"),
									"url"=>"../" . strtolower(inflector::pluralize(inflector::underscore($vv1['model']))) . "/permisos");
								if($contenido === true) {
									array_unshift($datos['cuerpo'][$kk]['contenido'], $registroPermisos);
								}
								else {
									array_unshift($datos['cuerpo'][$kk], $registroPermisos);
								}
							}
						}
					}
				}
			}
			foreach($datos['cuerpo'] as $k=>$v) {
				/**
				* El contenido de la fila puede venir como un array puro o dentro del elemento contenido.
				*/
				$opcionesFila = array();
				if(!empty($v['opciones'])) {
					$opcionesFila = $v['opciones'];
					/**
					* Si por parametros me dice que no se permite la seleccion multiple, prevalece a la especificacion
					* de la tabla y de la fila.
					*/
					if(isset($this->params['named']['seleccionMultiple']) && $this->params['named']['seleccionMultiple'] == 0) {
						$opcionesFila['opciones']['seleccionMultiple'] = false;
					}
					
				}
				if(!empty($v['contenido'])) {
					$v = $v['contenido'];
				}
				
				$cellsOut = array();
				$outDesgloses = array();

				$acciones = null;
				foreach($v as $campo) {
					$valor = "&nbsp;";
					$atributosCelda = null;

					if(isset($campo['valor'])) {
						$valor = $campo['valor'];
					}
					
					if(!isset($campo['tipo']) || $campo['tipo'] == "datos") {
						$tipoCelda = "datos";
						if(isset($campo['model'])) {
							$modelKey = $campo['model'];
						}
						if(isset($campo['field'])) {
							$nombreCampo = $campo['field'];
						}
					}
					else {
						$tipoCelda = $campo['tipo'];
					}

					if($tipoCelda == "accion") {
						$acciones[] = $valor;
						continue;
					}
					elseif($tipoCelda == "idDetail"){
						$detailUrls = $campo['urls'];
						continue;
					}
					elseif($tipoCelda == "desglose") {
						$sId = $campo['id'];
						$image = $campo['url'];
						if(isset($campo['imagen']['nombre'])) {
							$nombre = $campo['imagen']['nombre'];
							unset($campo['imagen']['nombre']);
							$image = $this->image($nombre, $campo['imagen']);
						}
						
						$classController = inflector::pluralize(inflector::classify($this->params['controller']));

						/**
						* Puede que la url venga de la forma ../controller/action
						* En este caso, solo me interesa la action.
						*/
						$action = $campo['url'];
						if(strstr($campo['url'], "/")) {
							$action = array_pop(explode("/", $campo['url']));
						}

						$acciones[] = $this->link($image, $campo['url'] . "/" . $sId,
							array(	"tipo"=>"ajax",
									"class"=>$classController . "-" . $action . "-" . $sId,
									"update"=>$campo['update'] . "_" . $sId,
									"onclick"=>"mostrarOcultarDivYTr(this, '" . $campo['update'] . "_" . $sId . "', 'tr_" . $campo['update'] . "_" . $sId . "', '" . router::url("/") . $this->params['controller'] . "');"));
									
						/**
						* Si esta seteada la session de este desglose, pinto este desglose.
						*/
						if($this->Session->check("desgloses")) {
							$desgloses = $this->Session->read("desgloses");
						 	if(isset($desgloses[$classController . "-" . $action . "-" . $sId])) {
								$jsDesglose[] = "jQuery('." . $classController . "-" . $action . "-" . $sId . "').trigger('click');";
							}
						}
						
						//$contenido = $this->Html->div(null, null, array("id"=>$campo['update'] . "_" . $sId, "class"=>"div_desglose"));
						$contenido = $this->tag("div", "", array("id"=>$campo['update'] . "_" . $sId, "class"=>"desglose"));
						$atributosFila = array("id"=>"tr_" . $campo['update'] . "_" . $sId, "class"=>"desglose");
						$outDesgloses[] = array($contenido, array("colspan"=>10), $atributosFila);
						continue;
					}
					
					$atributos = array();
					foreach($opcionesHtmlValidas as $opcionHtml) {
						if(isset($campo[$opcionHtml])) {
							$atributos[$opcionHtml] = $campo[$opcionHtml];
						}
					}
					if(!empty($modelKey)) {
						if($k==0 && $opciones['mostrarEncabezados'] && empty($datos['encabezado'])) {
							/**
							* El parametro, en caso de ser una lov, viene o por url o en $this->data
							*/
							if(isset($this->params['pass']['retornarA']) && !empty($this->params['pass']['retornarA'])) {
								$params['url'] = array("retornarA"=>$opciones['seleccionLov']['retornarA'], "layout"=>"lov");
							}
							elseif(isset($this->data['Formulario']['retornarA']) && !empty($this->data['Formulario']['retornarA'])) {
								$params['url'] = array("retornarA"=>$this->data['Formulario']['retornarA'], "layout"=>"lov");
							}
							$params['model'] = $modelKey;

							if(isset($campo['nombreEncabezado'])) {
								$nombre = inflector::humanize($campo['nombreEncabezado']);
							}
							else {
								$nombre = inflector::humanize($nombreCampo);
							}
							
							if(!($nombreCampo == "id" && !$opciones['mostrarIds'])) {
								if(isset($campo['orden']) && $campo['orden'] === false) {
									$encabezados[] = $nombre;
								}
								else {
									if($opciones['ordenEnEncabezados']) {
										$encabezados[] = $this->Paginador->sort($nombre, $nombreCampo, $params);
									}
									else {
										$encabezados[] = $nombre;
									}
								}
							}
						}

						$model =& ClassRegistry::getObject($modelKey);
						if(!array_key_exists("class", $atributos) && is_object($model)) {
							$columnType = $model->getColumnType($nombreCampo);
							if(substr($columnType, 0, 5) == "enum(") {
								$columnType = "enum";
							}
						}
						if(!empty($campo['tipoDato'])) {
							$columnType = $campo['tipoDato'];
						}
						if(!empty($columnType)) {
							switch($columnType) {
								case "moneda":
									$clase = "derecha";
									$valor = $this->Formato->format($valor, array("type"=>"moneda"));
									break;
								case "enum":
									$clase = "centro";
									break;
								case "integer":
									$clase = "derecha";
									break;
								case "float":
								case "decimal":
									$clase = "derecha";
									break;
								case "date":
									$clase = "centro";
									$valor = $this->Formato->format($valor, "date");
									break;
								case "datetime":
									$clase = "centro";
									$valor = $this->Formato->format($valor, "dateTime");
									break;
								case "string":
								case "text":
								default:
									$clase = "izquierda";
									break;
							}
							if(empty($campo['class'])) {
								$atributos = array("class"=>$clase);
							}
							else {
								$atributos = array("class"=>$campo['class']);
							}
						}
						
						if($nombreCampo == "id") {
							$id = $valor;

							$controller = "";
							if(isset($this->params['url']['url'])) {
								$parse = router::parse($this->params['url']['url']);
								$esController = strtolower(inflector::pluralize(inflector::underscore($campo['model'])));
								if($parse['controller'] != $esController) {
									$controller = "../" . $esController . "/";
								}
							}
							
							if($opciones['seleccionLov']) {
								if(isset($opciones['seleccionLov']['camposRetorno']) && !empty($opciones['seleccionLov']['camposRetorno'])) {
									$valoresRetorno = array();
									foreach(explode("|", $opciones['seleccionLov']['camposRetorno']) as $campoRetorno) {
										list($mRetorno, $cRetorno) = explode(".", $campoRetorno);
										foreach($v as $kk=>$vv){
											if(isset($vv['model']) && isset($vv['field']) && $vv['model'] == $mRetorno && $vv['field'] == $cRetorno) {
												$valoresRetorno[] = $vv['valor'];
												break;
											}
										}
									}
									if(isset($opciones['seleccionLov']['separadorRetorno'])) {
										$retono = implode($opciones['seleccionLov']['separadorRetorno'], $valoresRetorno);
									}
									else {
										$retono = implode(" - ", $valoresRetorno);
									}
								}
								else {
									$retono = $id;
								}
								if($this->traerPreferencia("lov_apertura") == "popup") {
									$padre = "opener";
								}
								else {
									$padre = "";
								}
								array_unshift($acciones, $this->link($this->image("seleccionar.gif", array("alt"=>"Selecciona este registro")), null, array("class"=>"seleccionar jqmClose", "id"=>"xsxs", "title"=>$retono, "onclick"=>"retornoLov('" . $opciones['seleccionLov']['retornarA'] . "','" . $id . "', '" . str_replace("'", "\'", $retono) . "', '" . $padre . "');")));
								/**
								* Cuando se trata de una lov que viene de una busqueda y solo retorna un registro, se lo autoselecciono y cierro la lov.
								*/
								if(!empty($this->params['named']['layout']) && $this->params['named']['layout'] === "lov" && !empty($this->params['named']['accion']) && $this->params['named']['accion'] === "buscar" && count($datos['cuerpo']) === 1) {
									$view =& ClassRegistry::getObject('view');
									$view->jsCode[] = "jQuery('.jqmClose').trigger('click');";
								}
							}

							/**
							* Lo utilizo para saber cual fue el origen (desde donde viene) este request,
							* asi evito agregarlo al history.
							$origen = "";
							if($this->params['isAjax'] == "1") {
								$origen = "/origenIsAjax:1";
							}
							*/
							
							if($opciones['eliminar'] && (!(isset($opcionesFila['eliminar']) && $opcionesFila['eliminar'] === false))) {
								if($campo['delete']) {
									$urlLink = $controller . "delete/" . $id;
									if(isset($detailUrls['delete'])) {
										$urlLink = $detailUrls['delete'];
									}
									array_unshift($acciones, $this->link($this->image("delete.gif", array("alt"=>"Elimina este registro")), $urlLink, array(), "Esta seguro que desea eliminar el registro?", false));
								}
								else {
									array_unshift($acciones, $this->image("delete_disable.gif", array("alt"=>"Elimina este registro")));
								}
							}
							
							if($opciones['modificar'] && (!(isset($opcionesFila['modificar']) && $opcionesFila['modificar'] === false))) {
								if($campo['write']) {
									//$urlLink = $controller . "edit/" . $id . $origen;
									$urlLink = $controller . "edit/" . $id;
									if(isset($detailUrls['edit'])) {
										$urlLink = $detailUrls['edit'];
									}
									array_unshift($acciones, $this->link($this->image("edit.gif", array("alt"=>"Modifica este registro")), $urlLink));
								}
								else {
									array_unshift($acciones, $this->image("edit_disable.gif", array("alt"=>"Modifica este registro")));
								}
							}
							
							/**
							* Puede que la tabla indique seleccion multiple, pero esta fila particular no.
							*/
							if($opciones['seleccionMultiple'] && (!(isset($opcionesFila['seleccionMultiple']) && $opcionesFila['seleccionMultiple'] === false))) {
								/**
								* Si debo agregarlo, lo agrego al principio de todas las acciones.
								*/
								array_unshift($acciones, $this->input("seleccionMultiple.id_" . $id, array("type"=>"checkbox", "label"=>false, "div"=>false)));
							}
							
							/**
							* Si debo mostrar los Ids y tengo alguna accion debo agregar una columna
							*/
							if($opciones['mostrarIds'] && !empty($acciones)) {
								if(empty($atributos['class'])) {
									$atributos['class'] = "derecha";
								}
								$cellsOut[] = array($id, $atributos);
							}
							else {
								$valor = "NO PINTAR";
							}
						}
					}
					
					/**
					* Fuerzo $valor a string, porque si $valor = 0, no evaluara.
					*/
					if($valor . "" != "NO PINTAR") {
						$cellsOut[] = array($valor, $atributos);
					}
				}

				if(!empty($acciones)) {
					array_unshift($cellsOut, array(implode("", $acciones), array("class"=>"acciones")));
				}
				
				/**
				* Utilizo el atributo charoff de html para guardar el id del registro.
				* Lo uso porque este atributo es valido por la w3c (si pusiera un atributo id, por ejemplo,
				* se veria igual, aunque w3c no validaria) y si no se usa align char, no cambia nada.
				* ver: http://www.w3schools.com/tags/tag_tr.asp
				*/
				if(!empty($id)) {
					$atributosFila = array("class"=>"fila_datos", "charoff"=>$id);
				}
				else {
					$atributosFila = array("class"=>"fila_datos");
				}
				$atributosFila = am($atributosFila, $opcionesFila);
				$rowsOut[] = $this->_fila($cellsOut, $atributosFila);

				/**
				* Si tengo desgloses, los agrego.
				*/
				if(!empty($outDesgloses)) {
					foreach($outDesgloses as $outDesglose) {
						if(!empty($outDesglose['2'])) {
							$atributosFila = $outDesglose['2'];
							$atributosFila['tipo'] = "desglose";
							unset($outDesglose['2']);
						}
						$rowsOut[] = $this->_fila(array($outDesglose), $atributosFila);
					}
				}
			}
		}
		
		if(!empty($encabezados) && $opciones['mostrarEncabezados']) {
			if($opciones['eliminar'] || $opciones['modificar'] || $opciones['seleccionMultiple'] || $opciones['seleccionLov'] || !empty($acciones)) {
			
				if($opciones['seleccionMultiple']) {
					$seleccion[] = $this->link("T", null, array("class"=>"seleccionarTodos")) . " ";
					$seleccion[] = $this->link("N", null, array("class"=>"deseleccionarTodos")) . " ";
					$seleccion[] = $this->link("I", null, array("class"=>"invertir"));
					$accionesString = $this->tag("div", implode("", $seleccion) . $this->tag("span", "Acciones"), array("class"=>"acciones"));
				}
				else {
					$accionesString = "Acciones";
				}
				array_unshift($encabezados, $accionesString);
			}
			$tabla .= "\n<thead>\n" . $this->Html->tableHeaders($encabezados) . "\n</thead>";
		}

		if(!empty($rowsOut)) {
			$tabla .= $this->tag("tbody", implode("\n", $rowsOut));
		}
		elseif(!empty($encabezados)){
			$tabla .= $this->output("\n<tbody></tbody>");
		}

		/**
		* Si la tabla tiene un pie, lo agrego.
		*/
		if(!empty($datos['pie'])) {
			$out = array();
			$cellsOut = array();
			if(!empty($datos['cuerpo'][0])) {
				if(!empty($datos['cuerpo'][0]['contenido'])) {
					$v = $datos['cuerpo'][0]['contenido'];
				}
				else {
					$v = $datos['cuerpo'][0];
				}
				foreach($v as $columna) {
					if(isset($columna['model'])) {
						foreach($datos['pie'] as $k=>$pies) {
							foreach($pies as $pie) {
								if($columna['model'] == $pie['model'] && $columna['field'] == $pie['field']) {
									$cellsOut[] = $pie['valor'];
								}
							}
						}
					}
				}
			}
			if(!empty($cellsOut)) {
				$out[] = $this->Html->tableHeaders($cellsOut);
				$tabla .= "\n<tfoot>\n" . implode("", $out) . "\n</tfoot>";
			}
		}
		
		if(!empty($tabla)) {
			//if(!isset($opcionesHtml['class'])) {
			//	$opcionesHtml['class'] = "tabla";
			//}
			//$tabla = $this->output("\n\n<table " . $this->_parseAttributes($opcionesHtml, null, '', '') . ">" . $tabla . "\n</table>\n\n");
			$tabla = $this->tag("table", $tabla, $opcionesHtml);
			//$tabla = $this->output("\n\n<table " . $this->_parseAttributes($opcionesHtml, null, '', '') . ">" . $tabla . "\n</table>\n\n");
		}


		/**
		* Agrego codigo JS.
		*/

		/**
		* Escribo el codigo js (jquery) que me ayudara con las funciones de selecciona multiple.
		*/
		$jsSeleccionMultiple = '
			jQuery("table .seleccionarTodos").click(
				function() {
					jQuery(".tabla input[@type=\'checkbox\']").checkbox("seleccionar");
					return false;
				}
			);
			jQuery("table .deseleccionarTodos").click(
				function() {
					jQuery(".tabla input[@type=\'checkbox\']").checkbox("deseleccionar");
					return false;
				}
			);
			jQuery("table .invertir").click(
				function() {
					jQuery(".tabla input[@type=\'checkbox\']").checkbox("invertir");
					return false;
				}
			);

			
			jQuery("#modificar").click(
				function() {
					var c = jQuery(".tabla input[@type=\'checkbox\']").checkbox("contar");
					if (c>0) {
						var action = "' . $this->Html->url("/") . $this->params['controller'] . '/edit";
						jQuery("#form")[0].action = action;
						jQuery("#form")[0].submit();
					}
					else {
						alert("Debe seleccionar al menos un registro.");
					}
					return false;
				}
			);
			
			jQuery("#eliminar").click(
				function() {
					var c = jQuery(".tabla input[@type=\'checkbox\']").checkbox("contar");
					if (c>0) {
						var mensaje = "Esta seguro que desea eliminar " + c;
						if(c==1) {
							mensaje = mensaje + " registro?";
						}
						else {
							mensaje = mensaje + " registros?";
						}
						if(confirm(mensaje)) {
							var action = "' . $this->Html->url("/") . $this->params['controller'] . '/deleteMultiple";
							jQuery("#form")[0].action = action;
							jQuery("#form")[0].submit();
						}
					}
					else {
						alert("Debe seleccionar al menos un registro.");
					}
					return false;
				}
			);
		';
		
		/**
		* Escribo el codigo js (jquery) que hara el efecto zebra (alterativo en cada fila par).
		*/
		$jsZebra = '
			jQuery(".grilla tr").click(
				function() {
					//jQuery(this).toggleClass("seleccionado");
				});
			jQuery(".fila_datos").mouseover(
				function() {
					jQuery(this).addClass("over");
				});
			jQuery(".fila_datos").mouseout(
				function() {
					jQuery(this).removeClass("over");
				});
			jQuery(".fila_datos:even").addClass("alternativo");
		';

		$jsZebra = "";
		$jsTabla = "";
		if(($opciones['zebra'] || $opciones['seleccionMultiple'] || !empty($jsDesglose)) && !empty($tabla)) {
			if(!empty($jsDesglose)) {
				$jsTabla .= implode("\n", $jsDesglose);
			}
			if($opciones['zebra']) {
				$jsTabla .= $jsZebra;
			}
			if($opciones['seleccionMultiple']) {
				$jsTabla .= $jsSeleccionMultiple;
			}
			if(!empty($opciones['seleccionLov'])) {
				if(isset($padre) && $padre == "opener") {
					$hidden = "opener.document.getElementById('" . $opciones['seleccionLov']['retornarA'] . "')";
				}
				else {
					$hidden = "document.getElementById('" . $opciones['seleccionLov']['retornarA'] . "')";
				}
				
				$jsSeleccionLov = '
					var seleccionMultipleId = ' . $hidden . ';
					var ids = new Array();
					if(seleccionMultipleId != null) {
						if(seleccionMultipleId.value.indexOf("**||**") > 0) {
							ids = seleccionMultipleId.value.split("**||**");
						}
						if(seleccionMultipleId.value.length > 0 && ids.length == 0) {
							ids.push(seleccionMultipleId.value);
						}
						for (var i=0; i< ids.length; i++ ) {
							jQuery("#seleccionMultipleId" + ids[i]).attr("checked", "true");
						}
					}
				';
				$jsTabla .= $jsSeleccionLov;
			}
			$this->addScript($jsTabla, "ready");
		}

		if(empty($tabla)) {
			if($opciones['omitirMensajeVacio'] === false) {
				$tabla = $this->tag("span", "No existen datos cargados o los criterios de su busqueda no arrojan resultados.", array("class"=>"color_rojo"));
			}
			else {
				/**
				* Creo una tabla vacia.
				*/
				$tableEstructura = "<thead></thead>";
				$tableEstructura .= "<tbody></tbody>";
				$tableEstructura .= "<tfoot></tfoot>";
				$tabla = $this->output("\n<table " . $this->_parseAttributes($opcionesHtml, null, '', '') . ">" . $tableEstructura . "\n</table>\n\n");
			}
		}

		return $tabla;
	}

	function _fila($celdas, $trOptions = null) {
		static $count = 0;
		$out = null;
		foreach ($celdas as $celda) {
			if(is_string($celda)) {
				$out[] = $this->tag("td", $celda);
			}
			elseif(isset($celda[0]) && isset($celda[1])) {
				$out[] = $this->tag("td", $celda[0], $celda[1]);
			}
			elseif(isset($celda[0]) && !isset($celda[1])) {
				$out[] = $this->tag("td", $celda[0]);
			}
			elseif(!isset($celda[0])) {
				$out[] = $this->tag("td", $celda);
			}
		}
		if($count % 2) {
			if(!empty($trOptions['class'])) {
				$trOptions['class'] .= " alternativo";
			}
			else {
				$trOptions['class'] = "alternativo";
			}
		}

		/**
		* Las filas de desglose, no las cuento para la zebra.
		*/
		if(!(!empty($trOptions['tipo']) && $trOptions['tipo'] == "desglose")) {
			$count++;
		}
		unset($trOptions['tipo']);
		return $this->tag("tr", $out, $trOptions);
	}


	function link($title, $href = null, $options = array(), $confirm = null, $escapeTitle = true) {

		//return $this->output(sprintf($this->tags['link'], $url, $this->_parseAttributes($htmlAttributes), $title));
		//return "<a>XXX</a>";
		/**
		* Si viene nulo, asumo que solo esta puesto el link para ejecutar codigo JS, entonces, hago que se quede en el lugar.
		*/
		if(empty($href)) {
			$href = "javascript:void(0);";
		}
		
		/**
		* Detecto si viene una imagen, hay que escarparlo.
		*/
		if(strstr($title, '<img src="')) {
			$escapeTitle = false;
		}
		
		if(isset($options['tipo']) && $options['tipo'] == "ajax") {
			unset($options['tipo']);
			return $this->output($this->Ajax->link($title, $href, $options, $confirm, $escapeTitle));
		}
		else {
			if(is_null($confirm)) {
				$confirmMessage = false;
			}
			else {
				$confirmMessage = $confirm;
			}
			return $this->output($this->Html->link($title, $href, $options, $confirmMessage, $escapeTitle));
		}
	}


	function bloque($contenido=null, $opciones=array()) {
		//trigger_error("bloque esta deprecado.");
		if(is_array($contenido)) {
			$codigo_html = implode("\n", $contenido);
		}
		else {
			$codigo_html = "\n" . $contenido;
		}

		if(isset($opciones['fieldset'])) {
			$imagen = "";
			$legend = "";
			if (!empty($opciones['fieldset']['imagen'])) {
				$imagen = $this->image($opciones['fieldset']['imagen'], array("class"=>"legend"));
				unset($opciones['fieldset']['imagen']);
			}
			if (!empty($opciones['fieldset']['legend']) && is_string($opciones['fieldset']['legend'])) {
				$legend = sprintf($this->Html->tags['legend'], $imagen . $opciones['fieldset']['legend']);
				unset($opciones['fieldset']['legend']);
			}
			$attr = "";
			if(!empty($opciones['fieldset'])) {
				$attr = " " . $this->_parseAttributes($opciones['fieldset'], null, '', '');
			}
			$fieldset = sprintf($this->Html->tags['fieldset'], $attr, $legend . $codigo_html);
			$codigo_html = $fieldset;
		}
		
		if(!empty($opciones['div'])) {
			if(!isset($opciones['fieldset'])) {
				$codigo_html = "\n\n" . $this->Html->div(null,"\n" . $codigo_html . "\n", $opciones['div'], false);
			}
			else {
				$codigo_html = "\n" . $this->Html->div(null, $fieldset, $opciones['div'], false);
			}
		}

		if(isset($opciones['caja_redondeada'])) {
			if(isset($opciones['caja_redondeada']["clase"])){
				$clase = $opciones['caja_redondeada']["clase"];
			}
			else {
				$clase = "caja_redondeada_contenido";
			}

			//$html_inicio = '<!-- start roundcorners --><div class="top-left"></div><div class="top-right"></div><div class="inside">';
			//$html_fin = '<!-- finish roundcorners --></div><div class="bottom-left"></div><div class="bottom-right"></div>';

			$html_inicio = '<div class="t"><div class="b"><div class="l"><div class="r"><div class="bl"><div class="br"><div class="tl"><div class="tr">';
			$html_fin = '</div></div></div></div></div></div></div></div>';
			
			$codigo_html = "\n" . $html_inicio . "\n" . $codigo_html . "\n" . $html_fin;
			
		}
		return $this->output($codigo_html);
	}
    
	
/**
 * Crea un tag form de html con contenido html dentro.
 *
 * @param mixed $contenido Puede ser contenido html (string) o un array de contenido html.
 * @param array $opctiones.
 * @return string Un formulario html biien formado con contenido dentro.
 * @access public
*/
	function form($contenido=null, $opciones=array()) {

		/**
		* Cuando data tiene mas de dos dimensiones, es porque es un update multiple.
		*/
		if(!isset($opciones['action']) && isset($this->data) && $this->action == "edit") {
			$opciones['action'] = "saveMultiple";
		}
		
		if(!isset($opciones['id'])) {
			$opciones['id'] = "form";
		}
		
		$form = "\n" . $this->create(null, $opciones);
		if(is_array($contenido)) {
			$form .= implode("\n", $contenido);
		}
		elseif(is_string($contenido)) {
			$form .= $contenido;
		}
		$form .= "\n" . $this->end();
		return $this->output($form);
	}

	
	/**
	 * Returns an HTML FORM element.
	 *
	 * @access public
	 * @param string $model The model object which the form is being defined for
	 * @param array  $options
	 * @return string An formatted opening FORM tag.
	 */
	function create($model = null, $options = array()) {
		return $this->Form->create($model, $options);
	}


/**
 * Generates a form input element complete with label and wrapper div
 *
 * @param string $tagName This should be "Modelname.fieldname", "Modelname/fieldname" is deprecated
 * @param array $options
 * Las opciones para el caso de lov son:
 * array("lov"=>array("controller"		=> "nombreController",
 * 					"separadorRetorno"	=> " - ",
 * 					"seleccionMultiple"	=>	true,
 * 					"camposRetorno"		=> array("Convenio.numero",
 *												"Convenio.nombre")));
 *
 *		$options['verificarRequerido']
 *				- true  	=> Opcion por defecto. Indica que si un campo es requerido, se lo marcara como tal.
 *				- false 	=> No se marcara un campo como requerido aunque lo sea.
 *				- forzado 	=> Se marcara el campo como requerido aunque no lo sea.
 * @return string
 */
	function input($tagName, $options = array()) {
		/**
		* Pongo un valor por defecto para el after, ya que lo uso para la lov, la fecha, etc...
		*/
		if(empty($options['after'])) {
			$options['after'] = "";
		}
		$requerido = "";
		if(!isset($options['verificarRequerido'])) {
			$verificarRequerido = true;
		}
		else {
			$verificarRequerido = $options['verificarRequerido'];
			unset($options['verificarRequerido']);
		}
		
		/**
		* En caso de ser un campo de condiciones (los filtros),
		* si no me cargo el valor de label para el campo, lo saco del nombre del campo.
		*/
		if(preg_match("/^Condicion./", $tagName) && !isset($options['label'])) {
			/**
			* A las condiciones no las marco como requeridas. No me interesa esto.
			*/
			$verificarRequerido = false;
			
			$tmpName = $tagName;
			$tmpName = preg_replace("/^Condicion./", "", $tmpName);
			list($model, $field) = explode("-", $tmpName);
			
			if(substr($field, strlen($field) - 7) == "__desde") {
				$field = str_replace("__desde", "", $field);
			}
			elseif(substr($field, strlen($field) - 7) == "__hasta") {
				$field = str_replace("__hasta", "", $field);
			}

			$tmpName = str_replace("-", ".", $tmpName);
			if (strpos($tmpName, '/') !== false || strpos($tmpName, '.') !== false) {
				list( , $texto) = preg_split('/[\/\.]+/', $tmpName);
			} else {
				$texto = $tmpName;
			}
			$texto = str_replace("_id", "", str_replace("__hasta", "", str_replace("__desde", "", $texto)));
			$options['label'] = inflector::humanize($texto);

			if(empty($options['value']) && !empty($this->data['Condicion'][$model . "-" . $field])) {
				$options['value'] = $this->data['Condicion'][$model . "-" . $field];
			}
		}

		if(!isset($model) && !isset($field)) {
			list($model, $field) = explode(".", $tagName);
		}
		
		/**
		* Busco que tipo de campo es.
		*/
		if(isset($options['type'])){
			$tipoCampo = $options['type'];
		}
		/**
		* Si viene la opcion lov no vacia y no seteo el tipo, especifico el tipo a lov.
		*/
		elseif(!empty($options['lov']) && is_array($options['lov'])) {
			$tipoCampo = "lov";
		}
		
		if (ClassRegistry::isKeySet($model) &&
			!(!empty($options['options']) && is_string($options['options']) && $options['options'] === "listable")) {
			$modelClass =& ClassRegistry::getObject($model);
			$tableInfo = $modelClass->schema();
			if(empty($options['options']) && !empty($modelClass->opciones[$field])) {
				$options['options'] = $modelClass->opciones[$field];
			}
			
			/**
			* Determino si es un campo requerido para marcarlo con el (*).
			* Esto lo hago si es que no viene dado ningun "after" en las opciones.
			*/
			if($verificarRequerido === true) {
				if(isset($modelClass->validate[$field])) {
					foreach($modelClass->validate[$field] as $regla) {
						if(isset($regla["rule"]) && $regla["rule"] == "/.+/") {
							$requerido = $this->tag("span", "(*)", array("class"=>"color_rojo"));
						}
					}
				}
			}

			/**
			* Si es un nuevo registro agrego el valor por defecto en caso de que este exista.
			*/
			if($this->action =="add" && !isset($this->data[$model][$field]) &&!empty($tableInfo[$field]['default']) && !isset($options['value']) && $tableInfo[$field]['default'] != "0000-00-00") {
				$options['value'] = $tableInfo[$field]['default'];
			}

			if(!empty($tableInfo[$field]['type'])) {
				if(substr($tableInfo[$field]['type'], 0, 5) == "enum(") {
					/**
					* De los tipo de campo enum, busco las opciones.
					* Si ya me viene especificado las options, respeto lo que viene,
					* sino, cargo con los valores de la DB.
					*/
					if(empty($options['options'])) {
						$valores = str_replace("'", "", str_replace(")", "", substr($tableInfo[$field]['type'], 5)));
						$values = explode(",", $valores);
						foreach($values as $v) {
							$options['options'][$v] = $v;
						}
					}
					$tipo = "enum";
				}
				else {
					$tipo = $tableInfo[$field]['type'];
				}
				
				if(empty($tipoCampo)) {
					$mapeoTipos['string'] = "text";
					$mapeoTipos['enum'] = "radio";
					
					if(isset($mapeoTipos[$tipo])) {
						$tipoCampo = $mapeoTipos[$tipo];
					}
					else {
						$tipoCampo = $tipo;
					}
				}
			}


			/**
			* Verifico el largo del campo para setear el maxLength
			*/
			if(!empty($tableInfo[$field]['length']) && !isset($options['maxlength']) && $tipoCampo != "float") {
				$options['maxlength'] = $tableInfo[$field]['length'];
			}
		}
		elseif(!empty($options['options']) && is_string($options['options']) && $options['options'] === "listable") {
			$opcionesValidas = array("displayField", "groupField", "conditions", "fields", "order", "limit", "recursive", "group", "contain", "model");
			$opcionesValidasArray = array("displayField", "groupField", "conditions", "fields", "order", "contain");
			foreach($opcionesValidas as $opcionValida) {
				if(!empty($options[$opcionValida])) {
					if(!is_array($options[$opcionValida]) && in_array($opcionValida, $opcionesValidasArray)) {
						$condiciones[$opcionValida] = $opcionValida . ":" . serialize(array($options[$opcionValida]));
					}
					elseif(in_array($opcionValida, $opcionesValidasArray)) {
						$condiciones[$opcionValida] = $opcionValida . ":" . serialize($options[$opcionValida]);
					}
					else {
						$condiciones[$opcionValida] = $opcionValida . ":" . $options[$opcionValida];
					}
					unset($options[$opcionValida]);
				}
			}

			/**
			* Armo la url y voy al controller a buscar los valores.
			*/
			$options['options'] = $this->requestAction("/" . $this->params['controller'] . "/" . $options['options'] . "/" . implode("/", $condiciones));
		}

		if ($verificarRequerido === "forzado") {
			$requerido = $this->tag("span", "(*)", array("class"=>"color_rojo"));
		}

		if(isset($tipoCampo)) {

			if(isset($this->data[$model][$field])) {
				$valorCampo = $this->data[$model][$field];
			}
			elseif (isset($options['value'])) {
				$valorCampo = $options['value'];
			}
			else {
				$valorCampo = null;
			}

			/**
			* Manejo los tipos de datos date para que me arme el control seleccion de fechas.
			*/
			if($tipoCampo === "date") {
				/**
				* Cuando el campo ya tiene un valor y este es una fecha valida, no lo vuelvo a formatear.
				* si lo mando al helper, me lo formatear para mysql. Esto puede darse durante un add al volver a insertar
				* o cuando un edit no valida.
				*/
				if(preg_match(VALID_DATE, $valorCampo)) {
					$options['value'] = $valorCampo;
				}
				else {
					$options['value'] = $this->Formato->format($valorCampo, array("default"=>false, "type"=>"date"));
				}
				$options['type'] = "text";
				$options['class'] = "fecha";
				$options['after'] = $this->inputFecha($tagName, $options) . $options['after'];
			}

			/**
			* Manejo los tipos de datos datetime para que me arme el control seleccion de fechas con hora.
			*/
			elseif($tipoCampo === "datetime") {
				if(preg_match(VALID_DATE, $valorCampo)) {
					$options['value'] = $valorCampo;
				}
				else {
					$options['value'] = $this->Formato->format($valorCampo, array("default"=>false, "type"=>"datetime"));
				}
				$options['type'] = "text";
				$options['class'] = "fecha";
				$options['after'] = $this->inputFecha($tagName, $options, true) . $options['after'];
			}

			/**
			* Agrega el link para poder descargar en caso de que sea un edit.
			*/
			elseif($tipoCampo === "file") {
				if(!empty($options['descargar']) && $options['descargar'] === true && $this->action == "edit") {
					if($this->params['action'] == "edit" && !empty($this->params['pass'][0])) {
						$options['aclaracion'] = "Puede descargar el archivo y ver su contenido desde aca " . $this->link($this->image("archivo.gif", array("alt"=>"Descargar")), "descargar/" . $this->params['pass'][0]);
					}
				}
				if(!empty($options['mostrar']) && $options['mostrar'] === true && $this->action == "edit" && isset($this->params['pass'][0])) {
					$options['after'] = str_replace(">", " />" ,$this->tag("img", null, array("alt"=>"", "class"=>"imagen_mostrar", "src"=>Router::url("/") . $this->params['controller'] . "/descargar/" . $this->params['pass'][0] . "/mostrar:true")));
				}
				unset($options['descargar']);
				unset($options['mostrar']);
			}
			
			/**
			* Manejo los campos autocomplete.
			* autocompleteBuscar: El metodo de la clase controlador que se encargara de realizar la busqueda.
			* funcionOnItemSelect: El metodo de la clase controlador que se encargara de hacer alguna accion
			* (en caso de ser necesario) una vez que el usuario haya seleccionado una valor.
			*
			* Si en la vista defino la funcion agregarParametrosAdicionalesRedefinido js, esta debera retornar un string
			* de la forma /param1:xxx/param2:yyy, seran agregados al request ajax y pasados al controller.
			*
			*
			* En el controller la funcion deberia ser algo como esto:
			*
			*	function autocompleteBuscar() {
			*		$acciones = $this->RecibosConcepto->Concepto->find("all", array("conditions"=>array("Concepto.nombre like"=>str_replace("[EXPANSOR]", "%", $this->params['named']['partialText'])), "order"=>array("Concepto.nombre")));
			*		$acciones = $this->Util->combine($acciones, '{n}.Concepto.id', '{n}.Concepto.nombre');
			*		$this->set("data", $this->Util->generarAutocomplete($acciones));
			*		$this->render("../elements/autocomplete");
			*	}
			*
			*/
			elseif($tipoCampo === "autocomplete") {
				$rnd = intval(rand());
				$options['id'] = $rnd;
				$options['type'] = "text";
				$options['after'] .= $this->image("contenga.gif", array("class"=>"busqueda_tipo", "id"=>"contenga_" . $rnd, "style"=>"cursor: pointer;", "alt"=>"Busqueda que contenga el texto ingresado"));
				$options['after'] .= $this->image("empiece.gif", array("class"=>"busqueda_tipo", "id"=>"empiece_" . $rnd, "style"=>"cursor: pointer;", "alt"=>"Busqueda que empiece con el texto ingresado"));
			
				$opcionesDefaultAutocomplete = array(	"class"					=>"autocomplete",
														"funcionBusqueda"		=>"autocompleteBuscar",
														"onItemSelect"			=>false);

				$options = am($opcionesDefaultAutocomplete, $options);

				/**
				* Si no ha especificado una url ni un div para hacer update, asumo un input autocomplete comun.
				*/
				if($options['onItemSelect'] === false) {
					$urlAutocomplete = Router::url(array("controller"=>$this->params["controller"], "action"=>$options['funcionBusqueda']));
					$jsAutocomplete = $this->codeBlock('
						jQuery("#' . $options['id'] . '").autocomplete("' . $urlAutocomplete . '");
					');
				}
				else {
					$options['after'] .= $this->image("auto_off.gif", array("class"=>"busqueda_autoincremental", "id"=>"autoincremental_off_" . $rnd, "style"=>"cursor: pointer;", "alt"=>"Busca al presionar la tecla enter (menos veloz)"));
					$options['after'] .= $this->image("auto_on.gif", array("class"=>"busqueda_autoincremental", "id"=>"autoincremental_on_" . $rnd, "style"=>"cursor: pointer;", "alt"=>"Busca al presionar cada tecla (mas veloz)"));
					$jsAutocomplete = $this->codeBlock('
						jQuery("#' . $options['id'] . '").blur(
							function(){
								jQuery("input[type=submit]").attr("disabled", false);
							});
							
						jQuery("#' . $options['id'] . '").focus(
							function(){
								jQuery("input[type=submit]").attr("disabled", true);
							});
							
						jQuery("#' . $options['id'] . '").keypress(
							function(e) {
								/**
								* Si es una busqueda autoincremental, solo posteo cuando presiona enter.
								*/
							
								if(jQuery("#autoincremental_off_" + this.id).css("display") == "inline" && e.which != 13) {
									return;
								}
								
								/**
								* [a-z, A-Z, 0-9, -, _]
								*/
								if (	e.which == 32 || e.which == 8 || e.which == 13
										|| (e.which >= 97 && e.which <= 122)
										|| (e.which >= 65 && e.which <= 90)) {

									/**
									* backspace: Quito el ultimo caracter, porque el val aun lo tiene.
									*/
									if(e.which == 8) { 
										var textoBuscar = jQuery(this).val();
										textoBuscar = textoBuscar.substring(0, textoBuscar.length-1);
									}
									/**
									* space: Se lo agrego como texto, porque cuando postea, lo elimina.
									*/
									else if(e.which == 32) {
										var textoBuscar = jQuery(this).val() + "[SPACE]";
									}
									/**
									* Es una letra minuscula o mayuscula.
									*/
									else {
										var textoBuscar = jQuery(this).val() + String.fromCharCode(e.which);
									}

									/**
									* Determino si se trata de una busque que empiece/contenga.
									*/
									if($("#empiece_' . $options['id'] . '").css("display") == "inline") {
										textoBuscar += "[EXPANSOR]";
									}
									else {
										textoBuscar = "[EXPANSOR]" + textoBuscar + "[EXPANSOR]";
									}

									function agregarParametrosAdicionales() {
										if(typeof agregarParametrosAdicionalesRedefinido == "function") {
											return agregarParametrosAdicionalesRedefinido();
										}
										else {
											return "";
										}
									}
									
									jQuery.ajax({
										type: "GET",
										async: false,
										url: "' . Router::url(array("controller"=>$this->params["controller"], "action"=>$options['onItemSelect']["url"])) . '/partialText:" + textoBuscar + agregarParametrosAdicionales(),
										success: function(html){
											jQuery("#' . $options['onItemSelect']["update"] . '").html(html);
										}
									});
								}
							}
						);
					');
				}
				
				$options['after'] .= $jsAutocomplete;
				unset($options['funcionBusqueda']);
				unset($options['onItemSelect']);
				unset($options['funcionOnItemSelect']);
			}
			
			/**
			* Manejo los campos periodo.
			*/
			elseif($tipoCampo === "periodo") {
				$rnd = intval(rand());
				$options['type'] = "text";
				$options['class'] = "periodo";
				$options['id'] = $rnd;
				$after = "";
				$q1 = $this->link($this->image("1q.gif", array("class"=>"periodo")), null, array("title"=>"Primera Quincena", "onclick"=>"jQuery('#" . $rnd . "').attr('value', '" . $this->Formato->format(null, array("type" => "1QAnterior")) . "');"));
				$q2 = $this->link($this->image("2q.gif", array("class"=>"periodo")), null, array("title"=>"Segunda Quincena", "onclick"=>"jQuery('#" . $rnd . "').attr('value', '" . $this->Formato->format(null, array("type" => "2QAnterior")) . "');"));
				$m = $this->link($this->image("m.gif", array("class"=>"periodo")), null, array("title"=>"Mensual", "onclick"=>"jQuery('#" . $rnd . "').attr('value', '" . $this->Formato->format(null, array("type" => "mensualAnterior")) . "');"));
				if(empty($options['periodo'])) {
					$after .= $q1 . $q2 . $m;
				}
				else {
					foreach($options['periodo'] as $v) {
						switch($v) {
							case "1Q":
								$after .= $q1;
								break;
							case "2Q":
								$after .= $q2;
								break;
							case "M":
								$after .= $m;
								break;
							case "soloAAAAMM":
								$after .= $this->link($this->image("m.gif", array("class"=>"periodo")), null, array("title"=>"Mensual", "onclick"=>"jQuery('#" . $rnd . "').attr('value', '" . substr($this->Formato->format(null, array("type" => "mensualAnterior")), 0, 6) . "');"));
								break;
						}
					}
				}
				$options['after'] = $after . $options['after'];
			}
			
			elseif($tipoCampo === "radio") {
				$options['type'] = "radio";
				$options['legend'] = false;
				if(!isset($options['label'])) {

					if(isset($tmpName)) {
						$options['before'] = $this->label($tmpName);
					}
					else {
						$options['before'] = $this->label($tagName);
					}
				}
				else {
					$options['before'] = $this->label(null, $options['label']);
				}
				/**
				* Pongo todas las opciones dentro de un div para poder asignarles estilos.
				*/
				$options['before'] .= "<div class='radio_opciones'>";
				$options['after'] = "</div>" . $options['after'];
				$options['label'] = false;
				$options['class'] = "radio";

				/**
				* Cuando esta vacio, cakePHP agrega un hidden para postear el vacio.
				* Yo agrego el hidden a mano, por lo cual le pongo siempre una valor para que cake no lo creen al hidden.
				*/
				if(empty($options['value'])) {
					$options['value'] = "/**VACIO**/";
					if(!empty($options['name'])) {
						$options['before'] .= $this->Form->hidden($tagName, array("name"=>$options['name'], "value"=>""));
					}
					else {
						$options['before'] .= $this->Form->hidden($tagName, array("value"=>""));
					}
				}
			}

			/**
			* El array parametros posteara (si los encuentra) via params->named los valores de los controles especificados.
			* $formulario->input('Banco.id', array("label"=>"Cuenta", "type"=>"relacionado", "valor"=>"Banco.id", "relacion"=>"Soporte.modo", "parametros"=>array("Soporte.empleador_id", "Soporte.grupo_id"), "url"=>"pagos/cuentas_relacionado"));
			*/
			elseif($tipoCampo === "relacionado") {

				$tmp = explode(".", $tagName);
				$id = Inflector::camelize($tmp[0]) . Inflector::camelize($tmp[1]);
				$tmp = explode(".", $options['relacion']);
				$idHiddenRelacionado = Inflector::camelize($tmp[0]) . Inflector::camelize($tmp[1]);
				$idHiddenRelacionadoTmp = $idHiddenRelacionado . "Tmp" . intval(rand());

				$value[0] = "Seleccione su opcion";
				/**
				* Busco el valor cuando sea un edit, o cuando es un add que no ha validado.
				*/
				if(!empty($options['valor']) && ($this->action != "add" ||
					($this->action == "add" && !empty($this->validationErrors) && !empty($this->data)))) {
					$valueId = $this->value($tagName);
					list($mRetorno, $cRetorno) = explode(".", $options['valor']);
					if(isset($mRetorno) && isset($cRetorno) && isset($this->data[$mRetorno][$cRetorno])) {
						$value[$valueId] = $this->data[$mRetorno][$cRetorno];
					}
					/**
					* No le pongo model ni campo, ya que es temporal, nunca debere guardar estos valores.
					*/
					$options['after'] .= $this->input("Bar.foo", array("value"=>$id . "|" . $valueId, "id"=>$idHiddenRelacionadoTmp, "type"=>"hidden"));
				}
				
				$jsParametros = "";
				if(!empty($options['parametros'])) {
					foreach($options['parametros'] as $parametro) {
						list($modelParametro, $fieldParametro) = explode(".", $parametro);
						$parametroRelacionado = $modelParametro . inflector::camelize($fieldParametro);
						$jsParametrosArray[] =
						$parametroRelacionado . ": jQuery('#" . $parametroRelacionado . "').val()";
					}
					$jsParametros ="parametros = {" . implode(", ", $jsParametrosArray) . "};";
				}
				
				$requestAjax = '
					jQuery("#' . $id . '").bind("click", function () {
						var valor = jQuery("#' . $idHiddenRelacionado . '").val();
						var parametros = {}; ' .
						$jsParametros . '
						var parametrosAdicionales = "";
						jQuery.each(parametros, function(key, value) {
   							parametrosAdicionales = parametrosAdicionales + key + ":" + value + "/";
 						});
 						
						var reg = new RegExp("^[0-9]+$");
						if(!reg.test(valor)) {
							alert("Antes de continuar debe seleccionar un valor para ' . str_replace(" Id", "", Inflector::humanize($tmp[1])) . '");
						}
						else {
							var elHidden = document.getElementById("' . $idHiddenRelacionadoTmp . '");

							if(elHidden != null) {
								var tmp = elHidden.value.split("|");
								/**
								* Si ya existe el hidden, comparo que no haya cambiado el valor.
								*/
								if(tmp[1] == valor) {
									return;
								}

								/**
								* Si cambio, lo asigno nuevamente.
								*/
								jQuery("#' . $idHiddenRelacionadoTmp . '").val(tmp[0] + "|" + valor);
							}
							else {
								/**
								* Si el hidden aun no esta creado, lo creo.
								*/
								jQuery("#form").append("<input id=\'' . $idHiddenRelacionadoTmp . '\' type=\'hidden\' value=\'" + jQuery(this).attr("id") + "|" + valor + "\' >");
							}
						

							/**
							* Hago el request via jSon.
							*/
							jQuery.getJSON("' . Router::url("/") . $options['url'] . '/" + valor + "/" + parametrosAdicionales,
								function(datos){
									var options = "";
									for (var i = 0; i < datos.length; i++) {
										options += "<option value=\"" + datos[i].optionValue + "\">" + datos[i].optionDisplay + "</option>";
									}
									jQuery("#' . $id . '").html(options);
								}
							);
						}
					})';
				
				$this->addScript($requestAjax);
				$options = am($options, array("type"=>"select"), array("options"=>$value), array("maxlength"=>false));
				unset($options['url']);
				unset($options['relacion']);
				unset($options['valor']);
				return $this->input($tagName, $options);
			}

			/**
			* Manejo los tipos de datos numericos.
			*/
			elseif($tipoCampo === "float" || $tipoCampo === "integer") {
				$options['class'] = "derecha";
			}

			elseif($tipoCampo === "checkboxMultiple") {
				return $this->checkboxMultiple($tagName, $options);
			}
		
			elseif($tipoCampo === "lov"
				&& isset($options['lov']['controller'])
					&& !empty($options['lov']['controller'])
						&& is_string($options['lov']['controller'])) {

				$rnd = intval(rand());
				$id = $this->domId($tagName);
				
				/**
				* Cargo nuevamente los valores.
				*/
				$value = array();
				if(true || $this->action == "edit") {
					foreach($options['lov']['camposRetorno'] as $campoRetorno) {
						list($mRetorno, $cRetorno) = explode(".", $campoRetorno);
						if(isset($this->data[$mRetorno][$cRetorno])) {
							$value[] = $this->data[$mRetorno][$cRetorno];
						}
						else {
							/**
							* Si aun no lo encontre, puede que este en recursive = 2.
							* Trato de buscarlo mas adentro en el array.
							*/
							$modelParent = Inflector::classify($options['lov']['controller']);
							if(isset($this->data[$modelParent][$mRetorno][$cRetorno])) {
								$value[] = $this->data[$modelParent][$mRetorno][$cRetorno];
							}
						}
					}
				}
				
				$opcionesLov = array(	"action"			=> "index",
										"layout"			=> "lov",
										"separadorRetorno" 	=> "-",
										"retornarA"			=> $id,
										"targetId" 			=> "target_" . $rnd);
										
				$options['lov'] = am($opcionesLov, $options['lov']);
				$options['lov']['camposRetorno'] = implode("|", $options['lov']['camposRetorno']);
				$url = strstr(router::url($options['lov']), router::url("/"));


				/**
				* Si permite seleccion multiple, pongo un textarea, sino un text comun.
				*/
				if(isset($options['lov']['seleccionMultiple']) && $options['lov']['seleccionMultiple'] == 0) {
					$type = "text";
				}
				else {
					$type = "textarea";
				}

				$lupa = $this->image("buscar.gif", array(	"alt" 	=>"Seleccione una opcion",
															"class" =>"lupa_lov",
															"id"	=>"lupa_" . $rnd));

				/**
				* El control lov se abre en un popup o en un div, de acuerdo a las preferencias.
				*/
				if($this->traerPreferencia("lov_apertura") == "popup") {
					$options['after'] = $this->link($lupa, null, array('onclick' => "abrirVentana('" . $rnd . "', '" . $url . "')")) . $options['after'];
				}
				else {
					$idDiv = "div_" . $this->domId($tagName);
					$cerrar = $this->link("", null, array("title"=>"Cerrar", "class"=>"jqmCloseEstilo jqmClose"));
					$target = "target_" . $rnd;
					$targetDiv = $this->bloque($this->image("cargando.gif", array("alt"=>"Cargando...")) . "<h1>Aguarde por favor...</h1>", array("div"=>array("id"=>$target)));
					$divLov = $this->bloque($cerrar . $targetDiv, array("div"=>array("class"=>"jqmWindow", "id"=>$idDiv)));

					
					$divLov .= $this->codeBlock('
						jQuery("#lupa_' . $rnd . '").bind("click",
							function () {
								jQuery("#' . $idDiv . '").jqm(
															{
																modal: 	true,
																target: "#' . $target . '",
																ajax: 	"' . $url . '"
															}
														).jqmShow();
							}
						);
					');
					$options['after'] = $this->link($lupa, null) . $divLov . $options['after'];
				}


				unset($options['type']);
				unset($options['lov']);
				unset($options['maxlength']);

				/**
				* Creo la hidden que sera quien en definitiva, contenga el valor correto a actualizar, lo que
				* muestra la lov es solo una "pantalla" linda al usuario, esta input hidden tiene el valor que se actualizara.
				*/
				$options['after'] .= $this->input($tagName, am($options, array("id"=>$id, "type"=>"hidden")));

				/**
				* Busco el valor "descriptivo" para mostrarle al usuario.
				*/
				if(!empty($value)) {
					foreach($value as $k=>$v) {
						if(preg_match(VALID_DATE_MYSQL, $v)) {
							$value[$k] = $this->Formato->format($v, array("type"=>"date"));
						}
					}
					if(isset($options['lov']['separadorRetorno']) && !empty($options['lov']['separadorRetorno'])) {
						$options['value'] = implode($options['lov']['separadorRetorno'], $value);
					}
					else {
						$options['value'] = implode(" - ", $value);
					}
					$options['title'] = $options['value'];
				}
				
				/**
				* Busco una etiqueta que vera el usuario.
				*/
				if(!isset($options['label'])) {
					if(isset($tmpName)) {
						$options['label'] = Inflector::humanize(array_pop(explode(".", str_replace("_id", "", $tmpName))));
					}
					else {
						$options['label'] = Inflector::humanize(array_pop(explode(".", str_replace("_id", "", $tagName))));
					}
				}
				
				$options = am($options, array(	'id'		=> $id . "__",
												'readonly'	=> true,
												'type'		=> $type,
												'class'		=> 'izquierda'));

				list($model, $field) = explode(".", $tagName);
				if(!empty($this->data[$model][$field . "__"])) {
					$options['value'] = $this->data[$model][$field . "__"];
				}
				return $this->input($tagName . "__", $options);
			}
		}
		$aclaracion = "";
		if(!empty($options['aclaracion'])) {
			$aclaracion = $this->tag("span", $options['aclaracion'], array("class"=>"aclaracion"));
			unset($options['aclaracion']);
		}

		$options['after'] .= $aclaracion . $requerido;
		if(isset($options['maxlength']) && $options['maxlength'] === false) {
			unset($options['maxlength']);
		}
		return $this->Form->input($tagName, $options);
	}

/**
 * Returns a formatted LABEL element for HTML FORMs.
 *
 * @param string $fieldName This should be "Modelname.fieldname", "Modelname/fieldname" is deprecated
 * @param string $text Text that will appear in the label field.
 * @return string The formatted LABEL element
 */
	function label($fieldName = null, $text = null, $attributes = array()) {
		return $this->Form->label($fieldName, $text, $attributes);
	}
/**
 * Creates a submit button element.
 *
 * @param  string  $caption  The label appearing on the button
 * @param  array   $options
 * @return string A HTML submit button
 */
	function submit($caption = 'Submit', $options = array()) {
		return $this->Form->submit($caption, $options);
	}

/**
 * Creates a button tag.
 *
 * @param  mixed  $params  Array of params [content, type, options] or the
 *						   content of the button.
 * @param  string $type	   Type of the button (button, submit or reset).
 * @param  array  $options Array of options.
 * @return string A HTML button tag.
 * @access public
 */
	function button($caption = '', $options = array()) {
		$return = '<div class="submit">';
		$return .= '<input type="button" value="' . $caption . '" ' . $this->_parseAttributes($options, null, '', '') . ' />';
		$return .= '</div>';
		return $return;
	}

 /**
 * Returns a formatted SELECT element.
 *
 * @param string $fieldName Name attribute of the SELECT
 * @param array $options Array of the OPTION elements (as 'value'=>'Text' pairs) to be used in the SELECT element
 * @param mixed $selected The option selected by default.  If null, the default value
 *                        from POST data will be used when available.
 * @param array $attributes  The HTML attributes of the select element.  If
 *                           'showParents' is included in the array and set to true,
 *                           an additional option element will be added for the parent
 *                           of each option group.
 * @param mixed $showEmpty If true, the empty select option is shown.  If a string,
 *                         that string is displayed as the empty element.
 * @return string Formatted SELECT element
 */
	function select($fieldName, $options = array(), $selected = null, $attributes = array(), $showEmpty = '') {
		return $this->Form->select($fieldName, $options, $selected, $attributes, $showEmpty);
	}

/**
 * Closes an HTML form.
 *
 * @access public
 * @return string A closing FORM tag.
 */
	function end($model = null) {
		return $this->Form->end($model);
	}

/**
 * Creates a password input widget.
 *
 * @param  string  $fieldName Name of a field, like this "Modelname.fieldname", "Modelname/fieldname" is deprecated
 * @param  array	$options Array of HTML attributes.
 * @return string
 */
	function password($fieldName, $options = array()) {
	return $this->Form->password($fieldName, $options);
	}

/**
 * Creates a button tag.
 *
 * @param  mixed  $params  Array of params [content, type, options] or the
 *                         content of the button.
 * @param  string $type    Type of the button (button, submit or reset).
 * @param  array  $options Array of options.
 * @return string A HTML button tag.
 * @access public
 	function button($params, $type = 'button', $options = array()) {

		$this->_parseAttributes($options$model
		//<button type="button" name="segundoboton">Bot&oacute;n Button</button>
	}
*/


/**
 * Returns a set of SELECT elements for a full datetime setup: day, month and year, and then time.
 *
 * @param string $tagName Prefix name for the SELECT element
 * @param string $dateFormat DMY, MDY, YMD or NONE.
 * @param string $timeFormat 12, 24, NONE
 * @param string $selected Option which is selected.
 * @return string The HTML formatted OPTION element
 */
	function dateTime($tagName, $dateFormat = 'D/M/Y', $timeFormat = '24', $selected = null, $attributes = array(), $showEmpty = true) {
		return $this->Form->dateTime($tagName, $dateFormat, $timeFormat, $selected, $attributes, $showEmpty);
	}

/**
 */
	function rangoFechas($tagName, $options = array(), $separador="</td><td>", $seleccionarHora=false) {
		$options_desde = $options;
		$options_hasta = $options;
		if(isset($options['label']['text']) && !empty($options['label']['text']))
		{
			$options_desde['label']['text'] = $options['label']['text'] . " desde";
			$options_hasta['label']['text'] = $options['label']['text'] . " hasta";
			$options_desde['div'] = false;
			$options_hasta['div'] = false;
		}

		$tagNameDesde = str_replace(".", "", Inflector::camelize($tagName . "__Desde"));
		$tagNameHasta = str_replace(".", "", Inflector::camelize($tagName . "__Hasta"));

		$return = "";
		$return .= $this->inputFecha($tagName . "__desde", $options_desde, $seleccionarHora);
		$return .= $separador;
		$return .= $this->inputFecha($tagName . "__hasta", $options_hasta, $seleccionarHora);
		return $return;
	}

	function inputFecha($tagName, $options = array(), $seleccionarHora=false) {
		$this->setEntity($tagName);
		$id = $this->domId(implode('.', array_filter(array($this->model(), $this->field()))));
		$codigo_html = $this->image("calendario.gif", array("class"	=>"fecha", "alt"=>"Seleccione una fecha"));


		if($seleccionarHora) {
			$codigo_html = $this->link($codigo_html, "javascript:NewCal('".$id."','dd/mm/yyyy', true, 24, 'dropdown', true)");
			$codigo_html .= $this->codeBlock('
				jQuery("#' . $id . '").mask("99/99/9999 99:99");
			');
		}
		else {
			$codigo_html = $this->link($codigo_html, "javascript:NewCal('".$id."','dd/mm/yyyy')", array("id"=>$id . "Fecha"));
			//$codigo_html .= $this->codeBlock('
			//	jQuery("#' . $id . '").mask("99/99/9999");
			//');
		}
		return $codigo_html;
	}

	function inputFechaHora($tagName, $options = array()) {
		return $this->inputFecha($tagName, $options, true);
	}

	function inputHora($tagName, $options = array()) {
		return $this->dateTime($tagName, 'D/M/Y', '24', null, array("class"=>"select_hora"));
	}


	function checkboxMultiple($tagName, $options) {

		list($model, $field) = explode(".", $tagName);
		$opciones['elementosHtmlAttributes'] = array("class" => "checkboxMultiple");
		$opciones['contenedorHtmlAttributes'] = array("class" => "checkboxMultiple");
		unset($options['type']);
		$options = am($opciones, $options);
        
        foreach($options['options'] as $id=>$valor) {
	        $elementosHtmlAttributes = $options['elementosHtmlAttributes'];
			$elementosHtmlAttributes['id'] = $model . Inflector::camelize($field) . $id;
            $elementosHtmlAttributes['value'] = $id;
			if(!empty($this->data[$model][$field])) {
				$seleccionados = $this->data[$model][$field];
				if($id & $seleccionados) {
					$checked['checked'] = 'checked';
					$checkbox[] = "<li>" . sprintf($this->tags['checkboxmultiple'], $model, $field, $this->Html->_parseAttributes(am($elementosHtmlAttributes, $checked))) . $this->Form->label($elementosHtmlAttributes['id'], $valor) . "</li>\n";
				}
				else {
					$checkbox[] = "<li>" . sprintf($this->tags['checkboxmultiple'], $model, $field, $this->Html->_parseAttributes($elementosHtmlAttributes)) . $this->Form->label($elementosHtmlAttributes['id'], $valor) . "</li>\n";
				}
			}
			else {
				$checkbox[] = "<li>" . sprintf($this->tags['checkboxmultiple'], $model, $field, $this->Html->_parseAttributes($elementosHtmlAttributes)) . $this->Form->label($elementosHtmlAttributes['id'], $valor) . "</li>\n";
			}
        }
		
		$id = mt_rand();
		$seleccion[] = $this->link("T", "", array("onclick"=>'jQuery("#' . $id . ' input[@type=\'checkbox\']").checkbox("seleccionar");return false;')) . " / ";
		$seleccion[] = $this->link("N", "", array("onclick"=>'jQuery("#' . $id . ' input[@type=\'checkbox\']").checkbox("deseleccionar");return false;')) . " / ";
		$seleccion[] = $this->link("I", "", array("onclick"=>'jQuery("#' . $id . ' input[@type=\'checkbox\']").checkbox("invertir");return false;'));
		$seleccionString = $this->bloque($seleccion, array("div"=>array("class"=>"seleccion")));
		
        $lista = "\n<ul" . $this->Html->_parseAttributes($options['contenedorHtmlAttributes']).">\n" . implode($checkbox) . "</ul>\n";
        $control = $this->bloque($seleccionString . $lista, array("div"=>array("id"=>$id, "class"=>$options['contenedorHtmlAttributes']['class'])));
        if(!empty($options['label'])) {
        	$label = $this->label($options['label']);
        }
        else {
        	$label = $this->label($tagName);
        }
        return $this->bloque($label . $control, array("div"=>array("class"=>"input")));
    }


/**
 * Returns a formatted error message for given FORM field, NULL if no errors.
 *
 * @param string $field A field name, like "Modelname.fieldname", "Modelname/fieldname" is deprecated
 * @param string $text		Error message
 * @param array $options	Rendering options for <div /> wrapper tag
 * @return string If there are errors this method returns an error message, otherwise null.
 */
	function error($field, $text = null, $options = array()) {
		return $this->Form->error($field, $text, $options);
	}

/**
* Dada una preferencia trae su valor
*/
	function traerPreferencia_deprecated($preferencia) {
		if($this->Session->check("__Usuario")) {
			$usuario = $this->Session->read("__Usuario");
			return $usuario['Usuario']['preferencias'][$preferencia];
		}
	}

}
?>