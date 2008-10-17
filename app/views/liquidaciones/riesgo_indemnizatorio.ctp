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
 



$excel->load(WWW_ROOT . "files" . DS . "calculo_indemnizatorio.xlsx");
$excel->setCellValue("test1", 40);
$excel->setCellValue("test2", 20);

//$excel->setCellValue("fecha", date("Y-m-d"));
$excel->setCellValue("Relacion.ingreso", date("d/m/Y"));
$excel->setCellValue("Relacion.egreso", "26/10/2008");
$excel->setCellValue("Empleador.nombre", "XXXXX EMPLEADOR X");
//$excel->setFormatCode("Relacion.ingreso", PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
//$excel->setFormatCode("Relacion.egreso", PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
//$excel->save(TMP . "calculado", "html");
$excel->save(TMP . "calculado", "excel2007");




?>