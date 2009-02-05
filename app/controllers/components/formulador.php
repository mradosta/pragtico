<?php
/**
 * Formulador Component.
 * Se encarga resolver las formulas.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.controllers.components
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
 
 set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes');
 
/**
 * La clase encapsula la logica necesaria para resolver una formula.
 * Parsea la formula proveniente del sistema, y la deja de la forma en que PHPExcel la necesita para funcionar correctamente.
 *
 * @package     pragtico
 * @subpackage  app.controllers.components
 */
class FormuladorComponent extends Object {

	
/**
 * La instancia de PHPExcel que utilizare para resolver las formulas.
 *
 * @var object
 * @access private
 */
	private $__objPHPExcel = null;
	
	
/**
 * Lleva el numero de celdas ya utilizado.
 *
 * @var array
 * @access private
 */	
	private $__cellId = 0;

	
/**
 * Inicializa el Component para usar en el controller.
 *
 * @param object $controller Una referencia al controller que esta instanciando el component.
 * @return void
 * @access public
 */
    function startup(&$controller) {
		/**
		* PHPExcel_Calculation
		*/
		App::import('Vendor', "Calculation", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel"), "Calculation.php");
		$this->__objPHPExcel = new PHPExcel();
	}


/**
 * Resuelve una formula.
 *
 * @return mixed Un string o un valor numerico. N/A en caso de error en la formula.
 * @access public
 */
	function resolver($formula) {
		$cellId = 0;
		
		/**
		* reemplazo los espacios entre las comas o los iguales, para unificar criterios.
		*/
		$formula = preg_replace('/\s*=\s*/', '=', $formula);
		$formula = preg_replace('/\s*,\s*/', ',', $formula);
		$formula = str_replace("if (", "if(", $formula);
		
		
		/**
		* En el formulador, si hay una comparacion de strings se equivoca.
		* Lo verifico en php, y pongo en en la celda un valor booleano.
		*/
		if (preg_match_all("/\((\'[\w\s\/]+\'=\'[\w\s\/]+\')/", $formula, $strings)) {
			foreach ($strings[1] as $k=>$string) {
				$cellId++;
				$partes = explode("=", str_replace(" ", "", str_replace("'", "", $string)));
				if ($partes[0] == $partes[1]) {
					$this->__objPHPExcel->getActiveSheet()->setCellValue("A" . $cellId, true);
				}
				else {
					$this->__objPHPExcel->getActiveSheet()->setCellValue("A" . $cellId, false);
				}
				
				/**
				* Debo escapar en caso de tener una barra invertida antes de reemplazar.
				*/
				$string = str_replace("/", "\/", $string);
				$formula = preg_replace("/" . $string . "/", "A" . $cellId, $formula, 1);
			}
		}
		
		
		/**
		* Puede que los valores del camino verdadero y el falso de un if sean string, entonces debo colocarlos en celdas separadas.
		*/
		if (preg_match_all("/\([A-Z]+\d\,\'([\w\s]+)\'\,\'([\w\s]+)\'\)/", $formula, $strings)) {
			$cellId++;
			$this->__objPHPExcel->getActiveSheet()->setCellValue("A" . $cellId, $strings[1][0]);
			$formula = preg_replace("/\'" . $strings[1][0] . "\'/", "A" . $cellId, $formula, 1);
			$cellId++;
			$this->__objPHPExcel->getActiveSheet()->setCellValue("A" . $cellId, $strings[2][0]);
			$formula = preg_replace("/\'" . $strings[2][0] . "\'/", "A" . $cellId, $formula, 1);
		}

		
		/**
		* Las funciones de grupo, me van a venir como una lista de valores, y el formulador de excel, las necesita
		* cargadas en celdas y la formula ser expresada como rango, entonces, lo convierto.
		*/
		if (preg_match_all("/(.*)([min|max|sum|average]+)\(([[0-9]\,]+)\)/Ui", $formula, $partes)) {
			if (!empty($partes[3])) {
				$formulaParcialRecontruida = null;
				foreach ($partes[3] as $k=>$valores) {
					$tmpValores = explode(",", $valores);
					$rangoInferior = "A" . ($cellId + 1);
					foreach ($tmpValores as $valor) {
						$cellId++;
						$this->__objPHPExcel->getActiveSheet()->setCellValue("A" . $cellId, (int)$valor);
					}
					$rangoSuperior = "A" . $cellId;
					$tmpPartes[] = $partes[1][$k] . $partes[2][$k] . "(" . $rangoInferior . ":" . $rangoSuperior . ")";
				}
				$formula = str_replace(implode("", $partes[0]), implode("", $tmpPartes), $formula);
				$formula = str_replace("/" . implode("", $partes[0]) . "/", implode("", $tmpPartes), $formula);
			}
		}
		
		$this->__cellId++;
		$this->__objPHPExcel->getActiveSheet()->setCellValue("Z" . $this->__cellId, $formula);
		return $this->__objPHPExcel->getActiveSheet()->getCell("Z" . $this->__cellId)->getCalculatedValue();
	}

}    