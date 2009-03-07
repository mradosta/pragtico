<?php
/**
 * Resolv formulas.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.vendors.pragmatia
 * @since           Pragtico v 1.0.0
 * @version         $Revision: 267 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2009-02-16 13:24:25 -0200 (lun, 16 feb 2009) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
 
 set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes');
 
/**
 * Resolv formulas Class.
 *
 * @package     pragtico
 * @subpackage  app.vendors.pragmatia
 */
class Formulas {

	
/**
 * PHPExcel object.
 *
 * @var object
 * @access private
 */
	private $__objPHPExcel = null;
	
	
/**
 * Used cells.
 *
 * @var array
 * @access private
 */	
	private $__cellId = 0;


/**
 * Instance PHPExcel's object.
 *
 * @return void
 * @access public
 */
    function __construct() {
		/** PHPExcel_Calculation */
		if (!class_exists('PHPExcel_Calculation')) {
			App::import('Vendor', 'Calculation', true, array(APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel'), 'Calculation.php');
		}
		$this->__objPHPExcel = new PHPExcel();
	}


/**
 * __cleanUp the formula.
 *
 * @param string $formula. The formula to resolv.
 * @return string The ready to use formula.
 * @access private
 */
	function __cleanUp($formula) {
		/** Replace spaces in formulas to unify criterias.*/
		$formula = preg_replace('/\s*([=|,|\(|\)])\s*/', '$1', $formula);
		$formula = preg_replace('/(\d)\s{0,}\/\s{0,}(\d)/', '$1/$2', $formula);
		$formula = preg_replace('/[^[:print:]]/', '', $formula);
		if (substr($formula, 0, 1) !== '=') {
			$formula = '=' . $formula;
		}
		return $formula;
	}

	
	function __cleanUpDate($strings, $formula) {
		foreach (array_unique($strings[0]) as $k => $string) {
			if ($strings[1][$k] + $strings[2][$k] + $strings[3][$k] === 0) {
				$formula = str_replace($string, '', $formula);
			} else {
				$formula = str_replace($string, sprintf('date(%d, %d, %d)', $strings[1][$k], $strings[2][$k], $strings[3][$k]), $formula);
			}
		}
		return $formula;
	}
	

/**
 * Resolv the formula.
 *
 * @param string $formula. The formula to resolv.
 * @return mixed Value for the resolved formula. N/A when errors in formula.
 * @access public
 */
	function resolver($formula) {
		$cellId = 0;

		$formula = $this->__cleanUp($formula);

		/** Partial solution to avoid PHPExcel unnecesary formula part evaluation */
		// http://phpexcel.codeplex.com/WorkItem/View.aspx?WorkItemId=9447
		$parts = explode(',', $formula);
		if (!empty($parts)) {
			foreach($parts as $k => $part) {
				$parts[$k] = preg_replace('/([\d\.]+\/0)/', '0', $part);
			}
			$formula = implode(',', $parts);
		}
		
		/** Replace Mysql dates to PHPExcel dates */
		if (preg_match_all("/date\('(\d\d\d\d)-(\d\d)-(\d\d)'\)/", $formula, $strings)) {
			$formula = $this->__cleanUpDate($strings, $formula);
		}
		if (preg_match_all("/'(\d\d\d\d)-(\d\d)-(\d\d)'/", $formula, $strings)) {
			$formula = $this->__cleanUpDate($strings, $formula);
		}
		if (preg_match_all("/(\d\d\d\d)-(\d\d)-(\d\d)/", $formula, $strings)) {
			$formula = $this->__cleanUpDate($strings, $formula);
		}


		/** Convert group functions arguments to values in columns */
		if (preg_match_all("/(.*)([min|max|sum|average]+)\(([[0-9]\,]+)\)/Ui", $formula, $partes)) {
			if (!empty($partes[3])) {
				$formulaParcialRecontruida = null;
				foreach ($partes[3] as $k => $valores) {
					$tmpValores = explode(',', $valores);
					$rangoInferior = 'A' . ($cellId + 1);
					foreach ($tmpValores as $valor) {
						$cellId++;
						$this->__objPHPExcel->getActiveSheet()->setCellValue('A' . $cellId, (int)$valor);
					}
					$rangoSuperior = 'A' . $cellId;
					$tmpPartes[] = $partes[1][$k] . $partes[2][$k] . '(' . $rangoInferior . ':' . $rangoSuperior . ')';
				}
				$formula = str_replace(implode('', $partes[0]), implode('', $tmpPartes), $formula);
				$formula = str_replace('/' . implode('', $partes[0]) . '/', implode('', $tmpPartes), $formula);
			}
		}
		
		$this->__cellId++;
		$formula = str_replace('\'', '"', $formula);
		$this->__objPHPExcel->getActiveSheet()->setCellValue('ZZ' . $this->__cellId, $formula);
		return $this->__objPHPExcel->getActiveSheet()->getCell('ZZ' . $this->__cellId)->getCalculatedValue();
	}

}