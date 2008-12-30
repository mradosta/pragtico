<?php
/**
 * Helper que me facilita el formateo de string, numeros, etc.
 *
 * Dado un nombre predefinido, formateo cualquier cosa.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pragmatia de RPB S.A.
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.views.helpers
 * @since           Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * Clase que contiene el helper para el formateo.
 * Esta clase es muy importante, ya que si bien es un helper, el behavior Util y el component Util, utilizan metodos de esta clase.
 *
 * @package     pragtico
 * @subpackage  app.views.helpers
 */
class FormatoHelper extends AppHelper {

/**
 * Los helpers que utilizare.
 *
 * @var arraya
 * @access public.
 */
	var $helpers = array("Number", "Time");

	
/**
 * Busca patrones dentro de un texto y los reemplaza por su reemplazo.
 *
 * @param array $patrones Los patrones que busco.
 * @param array $reemplazos Con los que tengo que reemplazar.
 * @param string $texto El texto donde realizar los remplazos.
 * Los partones, siempre deberan ser de la forma Model.[SubModel.]campo[.1,2,n]
 * Pueden venir indicados por numeros, pero debe existir el reemplazo al numero de la forma:
 *		1:Model.[SubModel.]campo[.1,2,n]
 *		2:Model.[SubModel.]campo[.1,2,n]
 * La seri 1,2,n significa que debe iterar por el patron (ej: el detalle de un recibo).
 *
 * @return string El texto con los remplazos realizados.
 * @access public.
 */
	function reemplazarEnTexto($patrones, $reemplazos, $texto) {

		/**
		* Busco primero los reemplazos que estan expresados en numeros.
		*/
		foreach ($patrones as $patron) {
			preg_match("/^([0-1]+)\:(.+)/", $patron, $matches);
			if (!empty($matches)) {
				$numericos[$matches[1]] = $matches[2];
			}
		}
		
		foreach ($patrones as $patron) {
			$key = $patron;
			if (!preg_match("/^[0-1]+\:.+/", $patron)) {
				/**
				* Si es un numero, significa que es un patron que me vino expresado como numero, entonces, lo deberia
				* tener en el array de numericos.
				*/
				if (is_numeric($patron)) {
					$patron = $numericos[$patron];
				}
				
				$tmp = explode("|", $patron);
				$formato = null;
				if (!empty($tmp[1])) {
					$tmpFormatos = explode(";", $tmp[1]);
					foreach ($tmpFormatos as $tmpFormato) {
						list($key, $value) = explode(":", $tmpFormato);
						$formato[$key] = $value;
					}
				}
				$tmp = explode(".", $tmp[0]);
				$cantidad = count($tmp);
				/**
				* Si el ultimo elemento es un numero me esta indicando loop.
				*/
				if ($cantidad == 2) {
					$aReemplazar["#*" . $key . "*#"] = $reemplazos[$tmp[0]][$tmp[1]];
				}
				elseif ($cantidad == 3) {
					$aReemplazar["#*" . $key . "*#"] = $reemplazos[$tmp[0]][$tmp[1]][$tmp[2]];
				}
				elseif ($cantidad == 4) {
					$aReemplazar["#*" . $key . "*#"] = $reemplazos[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]];
				}
				elseif ($cantidad == 5) {
					$aReemplazar["#*" . $key . "*#"] = $reemplazos[$tmp[0]][$tmp[1]][$tmp[2]][$tmp[3]][$tmp[4]];
				}
				else {
					$aReemplazar["#*" . $key . "*#"] = "";
				}
				
				if (!empty($formato)) {
					$aReemplazar["#*" . $key . "*#"] = $this->format($aReemplazar["#*" . $patron . "*#"], $formato);
				}
			}
			else {
				$aReemplazar["#*" . $key . "*#"] = "";
			}
		}
		return str_replace(array_keys($aReemplazar), $aReemplazar, $texto);
	}


/**
 * Formatea un valor de acuerdo a un formato.
 *
 * @param string $valor Un valor a formatear.
 * @param mixed 	array $options Opciones que contiene el tipo de formato y/o sus opciones.
 *					string El tipo de formato (sin opciones) que se desea.
 * @return mixed 	Un string o un array con el/los valor/es formateado/s de acuerdo a lo especificado.
 * @access public.
 */
	function format($valor, $options = array()) {
		if (is_string($options)) {
			$tmp = $options;
			$options = array();
			$options['type'] = $tmp;
		}

		$return = $valor;
		$options = array_merge(array('type'=>"numero"), $options);
		$type = $options['type'];
		unset($options['type']);
		
		switch($type) {
			case "periodo":
				if (!empty($valor) && (preg_match(VALID_PERIODO, strtoupper($valor), $matches) || preg_match('/^(20\d\d)(0[1-9]|1[012])$/', $valor, $matches))) {
					$tmp = null;
					$tmp['periodoCompleto'] = $matches[0];
					$tmp['ano'] = $matches[1];
					$tmp['mes'] = $matches[2];
					$tmp['periodo'] = (!empty($matches[3]))?$matches[3]:'M';
					$value = array(	"mes"	=> $tmp['mes'],
									"ano"	=> $tmp['ano']);

					if ($tmp['periodo'] === "1Q") {
						$value = array_merge($value, array("dia" => "01"));
						$fechaDesde = $this->format($value, array("type" => "date", "format" => "Y-m-d"));
						$value = array_merge($value, array("dia" => "15"));
						$fechaHasta = $this->format($value, array("type" => "date", "format" => "Y-m-d"));
					}
					elseif ($tmp['periodo'] === "2Q") {
						$value = array_merge($value, array("dia" => "16"));
						$fechaDesde = $this->format($value, array("type" => "date", "format" => "Y-m-d"));
						$value = array_merge($value, array("dia"=>$this->format($value, array("type" => "ultimoDiaDelMes"))));
						$fechaHasta = $this->format($value, array("type" => "date", "format" => "Y-m-d"));
					}
					elseif ($tmp['periodo'] === "M") {
						$value = array_merge($value, array("dia" => "01"));
						$fechaDesde = $this->format($value, array("type" => "date", "format" => "Y-m-d"));
						$value = array_merge($value, array("dia"=>$this->format($value, array("type" => "ultimoDiaDelMes"))));
						$fechaHasta = $this->format($value, array("type" => "date", "format" => "Y-m-d"));
					}
					$tmp['desde'] = $fechaDesde;
					$tmp['hasta'] = $fechaHasta;
					$return = $tmp;
				}
				else {
					$return = false;
				}
				break;		
			case "date":
				if (is_array($valor) && !empty($valor['dia']) && !empty($valor['mes']) && !empty($valor['ano']) && is_numeric($valor['dia']) && is_numeric($valor['mes']) && is_numeric($valor['ano'])) {
					$tmp = null;
					$tmp = $valor['ano'] . "-" . str_pad($valor['mes'], 2, "0", STR_PAD_LEFT) . "-" . str_pad($valor['dia'], 2, "0", STR_PAD_LEFT);
					$valor = null;
					$valor = $tmp;
				}
				$options = array_merge(array("default"=>true), $options);
				$fecha = trim(substr($valor, 0, 10));
				if (empty($fecha) && $options['default'] === true) {
					if (!isset($options['format'])) {
						$options['format'] = "Y-m-d";
					}
					$fecha = date("Y-m-d");
				}

				if (!empty($fecha)) {
					if (preg_match(VALID_DATE, $fecha, $matches)) {
						$options['format'] = "Y-m-d";
						$return = $matches[3] . "-" . $matches[2] . "-" . $matches[1];
					}
					elseif (preg_match(VALID_DATE_MYSQL, $fecha, $matches)) {
						if (!isset($options['format'])) {
							$options['format'] = "d/m/Y";
						}
						$return = $this->Time->format($options['format'], $fecha);
					}
					elseif ($fecha == "0000-00-00") {
						$return = "";
					}
				}
				break;
			case "dateTime":
			case "datetime":
				$fecha = substr($valor, 0, 10);
				$optionsTmp = $options;
				unset($optionsTmp['format']);
				$return = $this->format($fecha, array_merge($optionsTmp, array("type" => "date")));
				if (!isset($options['format'])) {
					$options['format'] = "H:i:s";
				}
				$hora = substr($valor, 10);
				if (empty($hora) && empty($return) && $options['default'] === false) {
					$return = "";
				}
				else {
					if (empty($hora)) {
						$hora = "00:00:00";
					}
					$return .= " " . $this->Time->format($options['format'], $hora);
				}
			break;
			case "numero":
			case "number":
				$options = array_merge(array("before" => "", "thousands" => "", "decimals" => ","), $options);
				$return = $this->Number->format($valor, $options);
				break;
			case "moneda":
				$options['type'] = "number";
				$return = $this->format($valor, array_merge(array("before" => "$ "), $options));
				break;
			case "ano":
			case "mes":
			case "dia":
				$valor = $this->format($valor, array_merge(array("type" => "date", "format" => "Y-m-d"), $options));
				if (empty($valor)) {
					$return = $valor;
				}
				else {
					if ($type === "dia") {
						$return = $this->Time->format("d", $valor);
					}
					elseif ($type === "mes") {
						$return = $this->Time->format("m", $valor);
					}
					elseif ($type === "ano") {
						$return = $this->Time->format("Y", $valor);
					}
				}
				break;
			case "ultimoDiaDelMes":
				$return = $this->Time->format("d", mktime(0, 0, 0, ($this->format($valor, array("type" => "mes")) + 1), 0, $this->format($valor, array("type" => "ano"))));
				break;
			case "diaAnterior":
				$return = $this->Time->format("d", mktime(0, 0, 0, $this->format($valor, array("type" => "mes")), ($this->format($valor, array("type" => "dia")) - 1), $this->format($valor, array("type" => "ano"))));
				break;
			case "mesAnterior":
				$return = $this->Time->format("m", mktime(0, 0, 0, $this->format($valor, array("type" => "mes")), 0, $this->format($valor, array("type" => "ano"))));
				break;
			case "anoAnterior":
				$return = $this->format($valor, array("type" => "ano")) - 1;
				break;
			case "1QAnterior":
				if ($this->format($valor, array("type" => "dia")) <= 15) {
					$mes = $this->format($valor, array("type" => "mesAnterior"));
					if ($mes == 12) {
						$ano = $this->format($valor, array("type" => "anoAnterior"));
					}
					else {
						$ano = $this->format($valor, array("type" => "ano"));
					}
				}
				else {
					$mes = $this->format($valor, array("type" => "mes"));
					$ano = $this->format($valor, array("type" => "ano"));
				}
				$return = $ano . $mes . "1Q";
				break;
			case "2QAnterior":
				$mes = $this->format($valor, array("type" => "mesAnterior"));
				if ($mes == 12) {
					$ano = $this->format($valor, array("type" => "anoAnterior"));
				}
				else {
					$ano = $this->format($valor, array("type" => "ano"));
				}
				$return = $ano . $mes . "2Q";
				break;
			case "mensualAnterior":
				$mes = $this->format($valor, array("type" => "mesAnterior"));
				if ($mes == 12) {
					$ano = $this->format($valor, array("type" => "anoAnterior"));
				}
				else {
					$ano = $this->format($valor, array("type" => "ano"));
				}
				$return = $ano . $mes . "M";
				break;
			case "periodoEnLetras":
				if (preg_match(VALID_PERIODO, $valor, $matches)) {
					$before = "";
					if (substr($matches[3], 0, 1) == "1") {
						$before = "Primera quincena de ";
					}
					elseif (substr($matches[3], 0, 1) == "2") {
						$before = "Segunda quincena de ";
					}
					$mes = $matches[2];
					$ano = $matches[1];
				}
				elseif (strlen($valor) === 6 || strlen($valor) === 5) {
					$options = array_merge(array("case" => "lower"), $options);
					$before = "";
					$ano = substr($valor, 0, 4);
				}
				$mes = $this->__getMeses((int)substr($valor, 4, 2));
				$return = $before . $mes . " de " . $ano;
				$return = $this->__case($return, $options['case']);
				break;
			case "mesEnLetras":
				$options = array_merge(array("case" => "lower"), $options);
				$meses = $this->__getMeses();
				if (strtolower($valor) === "all") {
					$tmp = null;
					foreach ($meses as $k=>$mes) {
						$tmp[$k] = $this->__case($mes, $options['case']);
					}
					$return = $tmp;
				}
				else {
					$mes = (int)$this->format($valor, array("type" => "mes"));
					$return = $this->__case($meses[$mes], $options['case']);
				}
				break;
			case "numeroEnLetras":
				$options = array_merge(array("places"=>2, "case" => "lower", "decimals" => ".", "option" => "palabras", "ceroCents"=>false), $options);
				unset($options['type']);
				$valor = $this->format($valor, $options);

				set_include_path(get_include_path() . PATH_SEPARATOR . APP . "vendors");
				App::import('Vendor', "Words", true, array(APP . "vendors" . DS . "Numbers"), "Words.php");
				$nw = new Numbers_Words();
				if ($options['option'] == "moneda") {
					$return = $nw->toCurrency($valor, "es_AR");
				}
				else if ($options['option'] == "palabras") {
					$return = $nw->toWords($valor, "es_AR");
				}
				if ($options['ceroCents'] === false) {
					$return = str_replace(" con cero centavos", "", $return);
					$return = str_replace(" con cero", "", $return);
				}
				/**
				* Corrijo errores de la clase
				*/
				$return = str_replace(" con veintiuno centavos", " con veintiun centavos", $return);
				$return = $this->__case($return, $options['case']);
				break;
		}
		return $return;
	}

	
/**
 * Convierte en texto en mayusculas, minusculas o titulo (ucfirst).
 *
 * @param $data mixed Array unidimensional o string con el texto a convertir.
 * @param $case string Especifica como convertir. Las posibilidades son:
 *				- ucfirst (default)
 *				- upper
 *				- lower
 * @return mixed Array convertido cuando el input haya sido un array, sino, un string.
 * @access private.
 */
	function __case($data, $case = "ucfirst") {
		$esString = false;
		if (!is_array($data) && is_string($data)) {
			$data = array($data);
			$esString = true;
		}
		if ($case == "upper") {
			foreach ($data as $k=>$v) {
				$data[$k] = strtoupper($v);
			}
		}
		elseif ($case == "lower") {
			foreach ($data as $k=>$v) {
				$data[$k] = strtolower($v);
			}
		}
		elseif ($case == "ucfirst") {
			foreach ($data as $k=>$v) {
				$data[$k] = ucfirst($v);
			}
		}
	
		if ($esString) {
			return $data[0];
		}
		return $data;
	}
	

/**
 * Genera un array (key=>value) con los meses.
 *
 * @param $mes Integer Opcional que indica el numero mes que se pretende retorne.
 * @return mixed 	array (key=>value) La key contine el numero del mes y el value el nombre del mes.
 *					string El nombre del mes solicitado.
 * @access private.
 */
	function __getMeses($mes = null) {
		$meses['1'] = "enero";
		$meses['2'] = "febrero";
		$meses['3'] = "marzo";
		$meses['4'] = "abril";
		$meses['5'] = "mayo";
		$meses['6'] = "junio";
		$meses['7'] = "julio";
		$meses['8'] = "agosto";
		$meses['9'] = "setiembre";
		$meses['10'] = "octubre";
		$meses['11'] = "noviembre";
		$meses['12'] = "diciembre";
		if (is_numeric($mes)) {
			if (isset($meses[$mes])) {
				return $meses[$mes];
			}
			else {
				return "";
			}
		}
		return $meses;
	}
}
?>