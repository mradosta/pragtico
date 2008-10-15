<?php



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