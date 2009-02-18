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
		App::import('Vendor', 'Calculation', true, array(APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel'), 'Calculation.php');
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
		if (substr($formula, 0, 1) !== '=') {
			$formula = '=' . $formula;
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
		/** Invalidate division by zero */
		if (preg_match('/.*\/\s*0.*/', $formula)) {
			return '#N/A';
		}
		
		/** PHPExcel mistakes when comparing string, so verify it in PHP and send PHPExcel calculated boolean value.*/
		if (preg_match_all("/\((\'[\w\s\/]+\'=\'[\w\s\/]+\')/", $formula, $strings)) {
			foreach (array_unique($strings[1]) as $k => $string) {
				$cellId++;
				$partes = explode('=', $string);
				if ($partes[0] === $partes[1]) {
					$this->__objPHPExcel->getActiveSheet()->setCellValue('A' . $cellId, true);
				} else {
					$this->__objPHPExcel->getActiveSheet()->setCellValue('A' . $cellId, false);
				}
				
				/** Replace scaped character */
				$string = str_replace('/', '\/', $string);
				$formula = preg_replace('/' . $string . '/', 'A' . $cellId, $formula, 1);
			}
		/** Replace Mysql dates to PHPExcel dates */
		} elseif (preg_match_all("/date\('(\d\d\d\d)-(\d\d)-(\d\d)'\)/", $formula, $strings)) {
			foreach (array_unique($strings[0]) as $k => $string) {
				$formula = str_replace($string, sprintf('date(%s, %s, %d)', $strings[1][$k], $strings[2][$k], $strings[3][$k]), $formula);
			}
		}
		
		
		/** Maybe values for an if statment are string, so must put then in separated cells */
		if (preg_match_all("/\([A-Z]+\d\,\'([\w\s]+)\'\,\'([\w\s]+)\'\)/", $formula, $strings)) {
			$cellId++;
			$this->__objPHPExcel->getActiveSheet()->setCellValue('A' . $cellId, $strings[1][0]);
			$formula = preg_replace("/\'" . $strings[1][0] . "\'/", 'A' . $cellId, $formula, 1);
			$cellId++;
			$this->__objPHPExcel->getActiveSheet()->setCellValue("A" . $cellId, $strings[2][0]);
			$formula = preg_replace("/\'" . $strings[2][0] . "\'/", 'A' . $cellId, $formula, 1);
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
		$this->__objPHPExcel->getActiveSheet()->setCellValue("ZZ" . $this->__cellId, $formula);
		return $this->__objPHPExcel->getActiveSheet()->getCell("ZZ" . $this->__cellId)->getCalculatedValue();
	}

}