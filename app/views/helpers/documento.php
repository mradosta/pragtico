<?php

set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes');

//include 'PHPExcel/Writer/Excel2007.php';
//include 'PHPExcel/Writer/Excel5.php';
//include 'PHPExcel/IOFactory.php';

class DocumentoHelper extends AppHelper {

	var $doc;
    
    function __construct() {
		App::import('Vendor', "IOFactory", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel"), "IOFactory.php");
        $this->doc = new PHPExcel();
    }

/**
* Crea todas las propiedades genericas del archivo de una sola vez y lo posiciona en la primera hoja.
*/
    function create() {
		$this->doc->getProperties()->setCreator("Pragtico");
		$this->doc->getProperties()->setLastModifiedBy("Pragtico");
		$this->doc->getProperties()->setTitle("Planilla para el Ingreso de Novedades - Pragtico");
		$this->doc->getProperties()->setSubject("Planilla para el Ingreso de Novedades - Pragtico");
		$this->doc->getProperties()->setDescription("Planilla para el Ingreso de Novedades. Pragtico permite el ingreso de las novedades al sistema de una manera rapida.");
		$this->doc->getProperties()->setKeywords("novedades pragtico");
		$this->doc->getProperties()->setCategory("novedades");
		//$this->doc->getActiveSheet()->setTitle('Simple');
		$this->doc->setActiveSheetIndex(0);
    }
	

	function setCellValue($cellName, $value) {
		$this->doc->getActiveSheet()->setCellValue($cellName, $value);
	}
	
	function __Xconstruct() {
	//function DocumentoHelper() {
		App::import('Vendor', "IOFactory", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel"), "IOFactory.php");
		$this->objPHPExcel = new PHPExcel();
	}

	function save($file = "php://output", $formato = "Excel2007") {
		$__objPHPExcelWriter = PHPExcel_IOFactory::createWriter($this->doc, $formato);

		//require_once(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel" . DS . "Writer" . DS . "Excel2007.php");
		//$__objPHPExcelWriter = new PHPExcel_Writer_Excel2007($this->doc);

		/**
		* Si se trata de Excel 2007, no precalculo por que no tiene sentido, ya que perdere tiempo ahora, y luego,
		* al abrilo, excel, calcula automaticamente las formulas.
		*/
		if($formato === "Excel2007") {
			$__objPHPExcelWriter->setPreCalculateFormulas(false);
		}
		//$__objPHPExcelWriter->save($file);
/*
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=filename.xlsx");
header("Content-Transfer-Encoding: binary ");
*/		
		$__objPHPExcelWriter->save($file);

		
		/*
			//require_once(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel" . DS . "Writer" . DS . "Excel2007.php");
			//$__objPHPExcelWriter = new PHPExcel_Writer_Excel2007($this->__objPHPExcel);
			$__objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $formato);
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
		else {
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
			$this->doc->save($fu);
		}
		*/
		//$__objPHPExcelWriter->save("php://output");
		//$__objPHPExcelWriter->save($file . "." . $extension);
	}

	function test() {
		App::import('Vendor', "IOFactory", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel"), "IOFactory.php");
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
		$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
		$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
		$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
		$objPHPExcel->getProperties()->setCategory("Test result file");

		$objPHPExcel->setActiveSheetIndex(0);

		for ($i = 2; $i <= 350; $i++) {
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, 'MARTIN');
			
			if ($i % 10 == 0) {
				// Add a page break
				$objPHPExcel->getActiveSheet()->setBreak( 'A' . $i, PHPExcel_Worksheet::BREAK_ROW );
			}
			
		}

		$objDrawing = new PHPExcel_Worksheet_HeaderFooterDrawing();
		$objDrawing->setName('PHPExcel logo');
		$objDrawing->setPath(WWW_ROOT . IMAGES_URL . 'logo.jpg');
		$objDrawing->setHeight(36);
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&G');
		//$objPHPExcel->getActiveSheet()->setBreak( 'A' . $i, PHPExcel_Worksheet::BREAK_ROW );
		
		//$documento->objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
		
		//$objDrawing = new PHPExcel_Worksheet_HeaderFooterDrawing();
		//$objDrawing->setPath(WWW_ROOT . IMAGES_URL . 'logo.jpg');
		//$objDrawing->setCoordinates('A1');
     	//$objDrawing->setResizeProportional(true);
     	//$objDrawing->setWidthAndHeight(160,120);
		//$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

		//$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&C&HPleasetreat this document as confidential!');
		//$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&GENCABEZADO');
		//$objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, 'CF');
		//$objPHPExcel->getActiveSheet()->getHeaderFooter()->setImages($objDrawing);
		//$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader($objDrawing);
		//$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objPHPExcel->getProperties()->getTitle() . '&RPage &P of &N');
		//$x = $objPHPExcel->getActiveSheet()->getHeaderFooter()->getImages();
		//$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&G' . $x['CF']);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
		//$objWriter->save('/tmp/test.xlsx');
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
		// Send the document to the browser
		//Configure::write('debug', 0);
  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Type: application/force-download");
  header("Content-Type: application/octet-stream");
  header("Content-Type: application/download");;
  header("Content-Disposition: attachment;filename=test.xlsx");
  //header("Content-Disposition: attachment;filename=test.xls");
  //header("Content-Disposition: attachment;filename=test.pdf");
  header("Content-Transfer-Encoding: binary ");
  		Configure::write('debug', 0);
		$objWriter->save("php://output");
		//$objWriter->save("/tmp/test.xls");
	}


	function sendToBrowser($formato = "Excel2007") {
		$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, $formato);
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
		//$objWriter->save('/tmp/test.xlsx');
		//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
		// Send the document to the browser
		//Configure::write('debug', 0);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");;
		header("Content-Disposition: attachment;filename=test.xlsx");
		//header("Content-Disposition: attachment;filename=test.xls");
		//header("Content-Disposition: attachment;filename=test.pdf");
		header("Content-Transfer-Encoding: binary ");
  		Configure::write('debug', 0);
		$objWriter->save("php://output");
		//$objWriter->save("/tmp/test.xls");
	}


/*
$documento->objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
$documento->objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
$documento->objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
$documento->objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
$documento->objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
$documento->objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
$documento->objPHPExcel->getProperties()->setCategory("Test result file");
*/
}





?>