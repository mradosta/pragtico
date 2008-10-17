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
 
$usuario = $session->read("__Usuario");
$pdf->ezInicio(array("usuario"=>$usuario['Usuario']['nombre']));

if(!empty($registros)) {
	$columnasDetalle	= array(
		"Concepto" 		=> array('justification'=>'left'),
		"Cantidad" 		=> array('justification'=>'right'),
		"Total Abonado" => array('justification'=>'right'),
		"Coeficiente" 	=> array('justification'=>'right'),
		"Total" 		=> array('justification'=>'left'));
	
	$columnasResumen	= array(
		"Empleador" 			=> array('justification'=>'left'),
		"Area" 					=> array('justification'=>'left'),
		"Cantidad Trabajadores" => array('justification'=>'right'),
		"Total Abonado" 		=> array('justification'=>'right'),
		"Total a Facturar" 		=> array('justification'=>'right'));
		
	$opciones = array(	'headerFontSize'	=> 7,
						'shaded'			=> 0,
						'width'				=> 550,
						'separadorHeader'	=> true,
						'xOrientation'		=> 'center',
						'showLines'			=> 0,
						'fontSize'			=> 6);

	$opcionesDetalle = $opcionesResumen = $opciones;
	$opcionesDetalle['cols'] = $columnasDetalle;
	$opcionesResumen['cols'] = $columnasResumen;
	
	$tabla = null;
	$totalAFacturar = $totalAbonado = 0;
	foreach($registros as $k=>$v) {
		foreach($v['Trabajador'] as $k1=>$v1) {
			$resumen[$v['cuit']]['cuit'] = $v['cuit'];
			$resumen[$v['cuit']]['Empleador'] = $v['nombre'];
			if(!isset($resumen[$v['cuit']]['areas'][$v1['area']]['cantidad'])) {
				$resumen[$v['cuit']]['areas'][$v1['area']]['cantidad'] = 1;
			}
			else {
				$resumen[$v['cuit']]['areas'][$v1['area']]['cantidad']++;
			}
			
			$tabla = null;
			$totalAFacturar = $totalAbonado = 0;
			foreach($v1['Concepto'] as $k2=>$v2) {
				$subTotal = $v2['coeficiente'] * $v2['total'];
				$totalAbonado += $v2['total'];
				$totalAFacturar += $subTotal;
				$tabla[] = array(
					"Concepto" 		=> $v2['nombre'],
					"Cantidad" 		=> $formato->format($v2['cantidad'], array("before"=>"", "places"=>2)),
					"Total Abonado" => $formato->format($v2['total'], array("before"=>"$ ", "places"=>2)),
					"Coeficiente" 	=> $formato->format($v2['coeficiente'], array("before"=>"", "places"=>2)),
					"Total" 		=> $formato->format($subTotal, array("before"=>"$ ", "places"=>2)));
			}
			$resumen[$v['cuit']]['areas'][$v1['area']]['totalAbonado'] = $totalAbonado;
			$resumen[$v['cuit']]['areas'][$v1['area']]['totalAFacturar'] = $totalAFacturar;
			
			$pdf->ezText("Periodo Liquidado: <b>" . $condiciones['Liquidacion.ano'] . " " . $condiciones['Liquidacion.mes'] . " " . $condiciones['Liquidacion.periodo'] . "</b>", 6);
			$pdf->ezText("Empleador: <b>" . $v['cuit'] . " " . $v['nombre'] . "</b>", 6);
			$pdf->ezText("Trabajador: <b>" . $v1['apellido'] . " " . $v1['nombre'] . "</b>", 6);
			$pdf->ezText("Legajo: <b>" . $v1['legajo'] . "</b>", 6);
			$pdf->ezText("Cuil: <b>" . $v1['cuil'] . "</b>", 6);
			$pdf->ezText("Area: <b>" . $v1['area'] . "</b>", 6);
			$pdf->ezText("", 6);
			$tabla[] = array(
				"Concepto" 		=> "<b>Totales</b>",
				"Cantidad" 		=> "",
				"Total Abonado" => "<b>" . $formato->format($totalAbonado, array("before"=>"$ ", "places"=>2)) . "</b>",
				"Coeficiente" 	=> "",
				"Total" 		=> "<b>" . $formato->format($totalAFacturar, array("before"=>"$ ", "places"=>2)) . "</b>");
			$y = $pdf->ezTable($tabla, null, null, $opcionesDetalle) + 10;
			$pdf->line(20, $y, 578, $y);
			$pdf->ezNewPage();
		}
	}

	$tabla = null;
	$kAnterior = null;
	foreach($resumen as $k=>$v) {
		$cantidad = $totalAbonado = $totalAFacturar = 0;
		foreach($v['areas'] as $k1=>$v1) {
			$cantidad += $v1['cantidad'];
			$totalAbonado += $v1['totalAbonado'];
			$totalAFacturar += $v1['totalAFacturar'];
			if($k != $kAnterior) {
				$kAnterior = $k;
				$tabla[] = array(
					"Empleador" 			=> "<b>" . $k . " " . $v['Empleador'] . "</b>",
					"Area" 					=> $k1,
					"Cantidad Trabajadores" => $v1['cantidad'],
					"Total Abonado"			=> $v1['totalAbonado'],
					"Total a Facturar" 		=> $v1['totalAFacturar']);
			}
			else {
				$tabla[] = array(
					"Empleador" 			=> "",
					"Area" 					=> $k1,
					"Cantidad Trabajadores" => $v1['cantidad'],
					"Total Abonado"			=> $v1['totalAbonado'],
					"Total a Facturar" 		=> $v1['totalAFacturar']);
			}
		}
		$tabla[] = array(
			"Empleador" 			=> "",
			"Area" 					=> "<b>Totales</b>",
			"Cantidad Trabajadores" => "<b>" . $cantidad . "</b>",
			"Total Abonado"			=> "<b>" . $totalAbonado . "</b>",
			"Total a Facturar" 		=> "<b>" . $totalAFacturar . "</b>");
	}
	$pdf->ezText("Cuadro Resumen (" . $condiciones['Liquidacion.ano'] . " " . $condiciones['Liquidacion.mes'] . " " . $condiciones['Liquidacion.periodo'] . ")", 8, array("justification"=>"center"));
	$pdf->ezText("========================", 8, array("justification"=>"center"));
	$pdf->ezText("", 8, array("justification"=>"center"));
	$pdf->ezTable($tabla, null, null, $opcionesResumen);
}
else {
	$pdf->ezText("No se han encontrado datos con los criterios especificados. Verifique.");
}
$pdf->ezStream();
?>