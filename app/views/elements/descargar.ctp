<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.views
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
	/**
	 * Realiza un download generico de cualquier archivo.
	 */
    //Configure::write('debug', 0);

    if(!empty($reemplazarTexto)) {
		//d($reemplazarTexto);
		//$result = $formato->replace(null, $liquidacion, $patterns);

		if ($archivo['type'] === 'application/vnd.ms-excel') {
			$data = $formato->replace(null, $reemplazarTexto['reemplazos'], $reemplazarTexto['texto']);
			set_include_path(get_include_path() . PATH_SEPARATOR . APP . "vendors" . DS . "PHPExcel" . DS . "Classes");
			App::import('Vendor', "IOFactory", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel"), "IOFactory.php");
			$objReader = PHPExcel_IOFactory::createReader('Excel5');
			$objPHPExcel = $objReader->load(WWW_ROOT . 'files' . DS . 'modelo_recibo_naty.xls');
			$worksheet = $objPHPExcel->getActiveSheet();

			foreach ($data as $cell => $value) {
				$worksheet->setCellValue($cell, $value);
			}
			
			$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment;filename=" . $archivo['name']);
			header("Content-Transfer-Encoding: binary");
			$objPHPExcelWriter->save('php://output');
			die;
		} else {

		$archivo['data'] = $formato->replace(null, $reemplazarTexto['reemplazos'], file_get_contents(WWW_ROOT . 'files' . DS . 'documents' . DS . $archivo['name']));
    	/**
    	* Si el texto esta en UTF-8, se debe usar latin1...
    	* http://ar2.php.net/mb_strlen (ver comentario de Peter Albertsson)
    	*/
    	$archivo['size'] = mb_strlen($archivo['data'], 'latin1');
		}
    }
    
    header('Content-type: ' . $archivo['type']);
    if(!isset($mostrar)) {
    	header('Content-length: ' . $archivo['size']);
    	header('Content-Disposition: attachment; filename="' . $archivo['name'] . '"');
    }
    echo $archivo['data'];
    exit();
?>