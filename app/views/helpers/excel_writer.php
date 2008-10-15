<?php
/**
 * Este es un helper CakePHP que sirve para crear archivos excel.
 *
 * @author MRadosta <mradosta AT pragmatia.com>
 * @from 11/07/2006
 */
 
 App::import('Vendor', "Writer", true, array(APP . "vendors" . DS . "Spreadsheet_Excel_Writer"), "Writer.php");

class ExcelWriterHelper extends Spreadsheet_Excel_Writer {

	var $helpers = array();

	function ExcelWriterHelper(){
		return parent::Spreadsheet_Excel_Writer();;
	}
	
	function test_deprecated() {
		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();

		// sending HTTP headers
		$workbook->send('test.xls');

		// Creating a worksheet
		$worksheet =& $workbook->addWorksheet('My first worksheet');

		// The actual data
		$worksheet->write(0, 0, 'Name');
		$worksheet->write(0, 1, 'Age');
		$worksheet->write(1, 0, 'John Smith');
		$worksheet->write(1, 1, 30);
		$worksheet->write(2, 0, 'Johann Schmidt');
		$worksheet->write(2, 1, 31);
		$worksheet->write(3, 0, 'Juan Herrera');
		$worksheet->write(3, 1, 32);

		// Let's send the file
		$workbook->close();

		//$session->write('nombreArchivo', $archivo);
		//readfile($ruta_archivo);		
	}	

}