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
 
//d($this->data);
//include ('class.ezpdf.php');
$pdf =& new Cezpdf();
$pdf->selectFont(APP . "vendors" . DS . "pdf-php" . DS . "fonts" . DS . "Helvetica.afm");

$data[]	= array(
		'periodo_abonado'	=> "1Q Ene 08",
		'fecha_pago'		=> "21/01/2008",
		'apellido_y_nombre'	=> "Carron Diego Alej",
		'dni'				=> "13891",
		'legajo'			=> "367");

$colsPref	= array(
		'periodo_abonado'	=> array('justification'=>'center', 'width'=>0),
		'fecha_pago'		=> array('justification'=>'center'),
		'apellido_y_nombre'	=> array('justification'=>'left'),
		'dni'				=> array('justification'=>'center'),
		'legajo'			=> array('justification'=>'center'));
		
$cols		= array(
		'periodo_abonado'	=> "<b>Periodo Abonado</b>",
		'fecha_pago'		=> "<b>Fecha de Pago</b>",
		'apellido_y_nombre'	=> "<b>Apellido y Nombre</b>",
		'dni'				=> "<b>DNI</b>",
		'legajo'			=> "<b>Legajo</b>");
				

//$o = array('showHeadings'=>0,'shaded'=>0,'showLines'=>1);
$o = array('headerFontSize'=>5, 'shaded'=>0, 'width'=>500, 'xOrientation' => 'center', 'fontSize'=>8, 'cols'=>$colsPref);
$pdf->ezTable($data, $cols, null, $o);




$data = null;
$data[]	= array(
		'cuil'				=> "20-13891367-9",
		'fecha_ingreso'		=> "05/09/2007",
		'antiguedad'		=> "00",
		'categoria'			=> "Peon Practi Produc",
		'banco'				=> "Nacion");
$data[]	= array(
		'cuil'				=> "20-13891367-9",
		'fecha_ingreso'		=> "05/09/2007",
		'antiguedad'		=> "00",
		'categoria'			=> "Peon Practi Produc",
		'banco'				=> "Nacion");		

$colsPref	= array(
		'cuil'				=> array('justification'=>'center'),
		'fecha_ingreso'		=> array('justification'=>'center'),
		'antiguedad'		=> array('justification'=>'left'),
		'categoria'			=> array('justification'=>'center'),
		'banco'				=> array('justification'=>'center'));
		
$cols		= array(
		'cuil'				=> "<b>xx</b>",
		'fecha_ingreso'		=> "<b>eeeeeeeee</b>",
		'antiguedad'		=> "<b>kkkkkkkk</b>",
		'categoria'			=> "<b>324233</b>",
		'banco'				=> "<b>dsfdssd</b>");
								

//$o = array('showHeadings'=>0,'shaded'=>0,'showLines'=>1);
$o = array('headerFontSize'=>5, 'showLinesExtended'=>array('top'=>0), 'shaded'=>0, 'width'=>500, 'xOrientation' => 'center', 'fontSize'=>8, 'cols'=>$colsPref);
$pdf->ezTable($data, $cols, null, $o);
$o = array('headerFontSize'=>5, 'showLinesExtended'=>array('top'=>0), 'shaded'=>0, 'width'=>500, 'xOrientation' => 'center', 'fontSize'=>8, 'cols'=>$colsPref);
$pdf->ezTable($data, $cols, null, $o);

$data = null;
$data[]	= array(
		'periodo_abonado'	=> "1Q Ene 08",
		'fecha_pago'		=> "21/01/2008",
		'apellido_y_nombre'	=> "Carron Diego Alej",
		'dni'				=> "13891",
		'legajo'			=> "367");

$colsPref	= array(
		'periodo_abonado'	=> array('justification'=>'center', 'width'=>0),
		'fecha_pago'		=> array('justification'=>'center'),
		'apellido_y_nombre'	=> array('justification'=>'left'),
		'dni'				=> array('justification'=>'center'),
		'legajo'			=> array('justification'=>'center'));
		
$cols		= array(
		'periodo_abonado'	=> "<b>Periodo Abonado</b>",
		'fecha_pago'		=> "<b>Fecha de Pago</b>",
		'apellido_y_nombre'	=> "<b>Apellido y Nombre</b>",
		'dni'				=> "<b>DNI</b>",
		'legajo'			=> "<b>Legajo</b>");
				

//$o = array('showHeadings'=>0,'shaded'=>0,'showLines'=>1);
$o = array('headerFontSize'=>5, 'showLinesExtended'=>array('top'=>0), 'shaded'=>0, 'width'=>500, 'xOrientation' => 'center', 'fontSize'=>8, 'cols'=>$colsPref);
$pdf->ezTable($data, $cols, null, $o);


//$pdf->ezTable($data);
$pdf->ezStream();
?>