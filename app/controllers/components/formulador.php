<?php

 set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes');


class FormuladorComponent extends Object {

	private $__objPHPExcel = null;
	private $__cellId = 0;

    function startup() {
		/**
		* PHPExcel_Calculation
		*/
		App::import('Vendor', "Calculation", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel"), "Calculation.php");
		$this->__objPHPExcel = new PHPExcel();
	}


	function resolver($formula) {
		$cellId = 0;
		
		/**
		* En el formulador, si hay una comparacion de strings se equivoca, entonces, lo reemplazo por su
		* equivalente en ascci y comparo numeros que se que lo hace correctamente.
		*/
		preg_match_all("/\((\'\w+\'=\'\w+\')/", $formula, $strings);
		foreach($strings[1] as $k=>$string) {
			$cellId++; 
			$partes = explode("=", str_replace(" ", "", str_replace("'", "", $string)));
			if($partes[0] == $partes[1]) {
				$this->__objPHPExcel->getActiveSheet()->setCellValue("A" . $cellId, true);
			}
			else {
				$this->__objPHPExcel->getActiveSheet()->setCellValue("A" . $cellId, false);
			}
			$formula = preg_replace("/" . $string . "/", "A" . $cellId, $formula, 1);
		}

		/**
		* Las funciones de grupo, me van a venir como una lista de valores, y el formulador de excel, las necesita
		* cargadas en celdas y la formula ser expresada como rango, entonces, lo convierto.
		*/
		preg_match_all("/(.*)([min|max|sum|average]+)\(([\d\,]+)\)/Ui", $formula, $partes);
		if(!empty($partes[3])) {
			$formulaParcialRecontruida = null;
			foreach($partes[3] as $k=>$valores) {
				$tmpValores = explode(",", $valores);
				$rangoInferior = "A" . ($cellId + 1);
				foreach($tmpValores as $valor) {
					$cellId++;
					$this->__objPHPExcel->getActiveSheet()->setCellValue("A" . $cellId, (int)$valor);
				}
				$rangoSuperior = "A" . $cellId;
				$tmpPartes[] = $partes[1][$k] . $partes[2][$k] . "(" . $rangoInferior . ":" . $rangoSuperior . ")";
			}
			$formula = str_replace(implode("", $partes[0]), implode("", $tmpPartes), $formula);
			$formula = str_replace("/" . implode("", $partes[0]) . "/", implode("", $tmpPartes), $formula);
		}

		$this->__cellId++;
		$this->__objPHPExcel->getActiveSheet()->setCellValue("Z" . $this->__cellId, $formula);
		return $this->__objPHPExcel->getActiveSheet()->getCell("Z" . $this->__cellId)->getCalculatedValue();
	}

}    