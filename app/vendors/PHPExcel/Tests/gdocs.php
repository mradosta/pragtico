<?php
error_reporting(E_ALL);

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../Classes/');

/** PHPExcel_Writer)GoogleDocs */
require_once 'GoogleDocs.php';

/** PHPExcel */
require_once 'PHPExcel.php';

/** PHPExcel_IOFactory */
require_once 'PHPExcel/IOFactory.php';

echo date('H:i:s') . " Load from Excel2007 file\n";
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("05featuredemo.xlsx");

echo date('H:i:s') . " Write to GoogleDocs format\n";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'GoogleDocs');
$objWriter->setCredentials('maarten.balliauw@gmail.com', 'homer0a');
$objWriter->save(str_replace('.php', '.xls', __FILE__));

// Echo memory peak usage
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

// Echo done
echo date('H:i:s') . " Done writing files.\r\n";
