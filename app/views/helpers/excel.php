<?php
/**
 * Este es un helper CakePHP que sirve para crear archivos excel.
 *
 * @author MRadosta <mradosta AT pragmatia.com>
 * @from 11/07/2006
 */
 
set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes');



class ExcelHelper extends AppHelper {

	private $__objPHPExcel = null;


/**
 * Cargo el archivo xlsx.
 */
	function load($xlsxFile) {
		//App::import('Vendor', "Reader", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel" . DS . "Reader"), "Excel2007.php");
		require_once(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel" . DS . "Reader" . DS . "Excel2007.php");
		$__objPHPExcelReader = new PHPExcel_Reader_Excel2007();
		$this->__objPHPExcel = $__objPHPExcelReader->load($xlsxFile);
		$this->__objPHPExcel->setActiveSheetIndex(0);

		$cell = $this->__objPHPExcel->getActiveSheet()->getCell('comentario');
		d($cell);
		d($this->__objPHPExcel->getActiveSheet()->getComment($cell->getCoordinate())->getText()->getPlainText());
		d($this->__objPHPExcel->getActiveSheet()->getComment('A9')->getPlainText());


	}

	function setCellValue($cellName, $value) {
		$this->__objPHPExcel->getActiveSheet()->setCellValue($cellName, $value);
	}

	function setFormatCode($cellName, $value) {
		//$this->__objPHPExcel->getActiveSheet()->getCell($cellName)->setFormatCode($value);
		
		$this->__objPHPExcel->getActiveSheet()->getStyle('B6')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
		
	}

	function getCalculatedValue($cellName) {
		return $this->__objPHPExcel->getActiveSheet()->getCell($cellName)->getCalculatedValue();
	}

	function save($file, $formato = "excel2007") {
		if($formato === "excel2007") {
			require_once(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel" . DS . "Writer" . DS . "Excel2007.php");
			$__objPHPExcelWriter = new PHPExcel_Writer_Excel2007($this->__objPHPExcel);
			$__objPHPExcelWriter->setPreCalculateFormulas(false);
			$extension = "xlsx";
		}
		elseif($formato === "html") {
			require_once(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel" . DS . "Writer" . DS . "HTML.php");
			$__objPHPExcelWriter = new PHPExcel_Writer_HTML($this->__objPHPExcel);
			$extension = "html";
		}
		elseif($formato === "pdf") {
			require_once(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel" . DS . "Writer" . DS . "PDF.php");
			$__objPHPExcelWriter = new PHPExcel_Writer_PDF($this->__objPHPExcel);
			$extension = "pdf";
		}
		
		//$__objPHPExcelWriter->save("php://output");
		$__objPHPExcelWriter->save($file . "." . $extension);
	}



	function xsave($file) {
		require_once(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel" . DS . "Reader" . DS . "Excel2007.php");
		require_once(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel" . DS . "Writer" . DS . "Excel2007.php");
		$objPHPExcelReader = new PHPExcel_Reader_Excel2007();
		$objPHPExcel = $objPHPExcelReader->load(WWW_ROOT . "files" . DS . "calculo_indemnizatorio.xlsx");
		
		$objPHPExcelWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objPHPExcelWriter->setPreCalculateFormulas(false);
		//$objPHPExcelWriter->save(WWW_ROOT . "files" . DS . "calculox.xlsx");
		$objPHPExcelWriter->save("/tmp/calculox.xlsx");
	}
















	
	/*
	function command() {
		foreach (func_get_args()as $a) {
			
		}
	}
	*/

}