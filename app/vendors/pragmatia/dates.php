<?php
/**
 * Este archivo contiene la logica para operaciones con fechas.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		vendors.pragmatia
 * @since			Pragtico v 1.0.0
 * @version			$Revision: 54 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2008-10-23 23:14:28 -0300 (Thu, 23 Oct 2008) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * Clase para el manejo de fechas.
 *
 * @package pragtico
 * @subpackage vendors.pragmatia
 */
class Dates {

/**
 * Constructor de la clase.
 * Me aseguro que las constantes existan (normalmente definidas en bootstrap.php.
 *
 * @return void
 * @access public
 */
	function __construct() {
		if (!defined('VALID_DATE_MYSQL')) {
			define('VALID_DATE_MYSQL', '/(19\d\d|20\d\d)[\-](0[1-9]|1[012])[\-](0[1-9]|[12][0-9]|3[01])|^$/');
		}
		if (!defined('VALID_DATETIME_MYSQL')) {
			define('VALID_DATETIME_MYSQL', '/(19\d\d|20\d\d)[\-](0[1-9]|1[012])[\-](0[1-9]|[12][0-9]|3[01])\s{1}([0-1][0-9]|[2][0-3]):([0-5][0-9]):{0,1}([0-5][0-9]){0,1}|^$/');
		}
	}

	
/**
 * Calcula la diferencia entre dos fechas.
 *
 * Las fechas deben estar en formato mysql (yyyy-mm-dd hh:mm:ss) aunque no completa (@see __getValidDateTime)
 *
 * @param string $fechaDesde La fecha desde la cual se tomara la diferencia.
 * @param string $fechaHasta La fecha hasta la cual se tomara la diferencia. Si no se pasa la fecha hasta,
 * se tomara la fecha actual como segunda fecha.
 *
 * @return mixed 	array con dias, horas, minutos y segundos en caso de que las fechas sean validas.
 * 					False en caso de que las fechas sean invalidas.
 * @access public
 */
	function dateDiff($fechaDesde, $fechaHasta = null) {
		if($fechaDesde = $this->__getValidDateTime($fechaDesde)) {
			$fechaDesde = strtotime($fechaDesde);
		} else {
			return false;
		}
		
		if($fechaHasta = $this->__getValidDateTime($fechaHasta)) {
			$fechaHasta = strtotime($fechaHasta);
		} else {
			return false;
		}
		
		/**
		* Corrijo con un dia, para que desde hoy hasta hoy haya 0 dias.
		*/
		$diff = abs($fechaDesde-$fechaHasta);
		$daysDiff = floor($diff/60/60/24);
		$diff -= $daysDiff*60*60*24;
		$hrsDiff = floor($diff/60/60);
		$diff -= $hrsDiff*60*60;
		$minsDiff = floor($diff/60);
		$diff -= $minsDiff*60;
		$secsDiff = $diff;

		$diferencia=false;
		$diferencia['dias']=$daysDiff;
		$diferencia['horas']=$hrsDiff;
		$diferencia['minutos']=$minsDiff;
		$diferencia['segundos']=$secsDiff;
		return $diferencia;
	}


/**
 * Suma una cantidad de 'intervalo' a una fecha.
 *
 * @param string $fecha La fecha a la cual se le debe sumar el intervalo.
 * @param string $intervalo El intervalo de tiempo.
 * El intervalo puede ser:
 *		y Year
 *		q Quarter
 *		m Month
 * 		w Week
 * 		d Day
 * 		h Hour
 * 		n minute
 * 		s second
 * @param integer $cantidad La cantidad de intervalo a sumar a la fecha.
 * @return mixed La fecha en formato yyyy-mm-dd hh:mm:ss con el intervalo agregado, false si no fue posible realizar la operacion.
 * @access public
 */
	function dateAdd($fecha = null, $cantidad = 1, $intervalo = 'd') {
		$validIntervalo = array('y', 'q', 'm', 'w', 'd', 'h', 'n', 's');
		if(!in_array($intervalo, $validIntervalo) || !is_numeric($cantidad)) {
			return false;
		}
		
		if($fecha = $this->__getValidDateTime($fecha)) {
			$fecha = strtotime($fecha);
		}
		else {
			return false;
		}
		$ds = getdate($fecha);

		$h = $ds['hours'];
		$n = $ds['minutes'];
		$s = $ds['seconds'];
		$m = $ds['mon'];
		$d = $ds['mday'];
		$y = $ds['year'];

		switch ($intervalo) {
			case 'y':
				$y += $cantidad;
				break;
			case 'q':
				$m +=($cantidad * 3);
				break;
			case 'm':
				$m += $cantidad;
				break;
			case 'w':
				$d +=($cantidad * 7);
				break;
			case 'd':
				$d += $cantidad;
				break;
			case 'h':
				$h += $cantidad;
				break;
			case 'n':
				$n += $cantidad;
				break;
			case 's':
				$s += $cantidad;
				break;
		}
		
		return date('Y-m-d h:i:s', mktime($h ,$n, $s, $m ,$d, $y));
	}
	
	
/**
 * Dada una fecha en alguno de los formatos admitidos, retorna una fechaHora MySql valida y completa.
 *
 * @param  string $fecha Una fecha.
 * 	Formatos Admitidos de entrada:
 *			yyyy-mm-dd hh:mm:ss
 *			yyyy-mm-dd hh:mm
 *			yyyy-mm-dd
 * @return mixed FechaHora MYSQL valida y completa (yyyy-mm-dd hh:mm:ss) en caso haber ingresado una fecha valida,
 * false en otro caso.
 * @access private
 */
	function __getValidDateTime($fecha) {
		if (empty($fecha)) {
			$fecha = date('Y-m-d H:i:s');
		} else {
			$fecha = trim($fecha);
		}
		if(preg_match(VALID_DATETIME_MYSQL, $fecha, $matches) || preg_match(VALID_DATE_MYSQL, $fecha, $matches)) {
			if(!isset($matches[4])) {
				$matches[4] = '00';
			}
			if(!isset($matches[5])) {
				$matches[5] = '00';
			}
			if(!isset($matches[6])) {
				$matches[6] = '00';
			}
			return $matches[1] . '-' . $matches[2] . '-' . $matches[3] . ' ' . $matches[4] . ':' . $matches[5] . ':' . $matches[6];
		}
		else {
			return false;
		}
	}
}

?>