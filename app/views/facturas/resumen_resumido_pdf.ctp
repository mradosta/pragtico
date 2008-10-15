<?php
$usuario = $session->read("__Usuario");
$pdf->ezInicio(array("usuario"=>$usuario['Usuario']['nombre']));

if(!empty($registros)) {
	$columnas		= array(
		"Concepto" 		=> array('justification'=>'left'),
		"Cantidad" 		=> array('justification'=>'right'),
		"Total Abonado" => array('justification'=>'right'),
		"Coeficiente" 		=> array('justification'=>'right'),
		"Total" 		=> array('justification'=>'left'));
		
	$opciones = array(	'cols'				=>$columnas,
						'headerFontSize'	=>7,
						'shaded'			=>0,
						'width'				=>550,
						'separadorHeader'	=>true,
						'xOrientation'		=>'center',
						'showLines'			=>0,
						'fontSize'			=>6);

	$tabla = null;
	$totalAFacturar = $totalAbonado = 0;
	foreach($registros as $k=>$v) {
		if(!empty($tabla)) {
			$pdf->ezText("Periodo Liquidado: <b>" . $condiciones['Liquidacion.ano'] . " " . $condiciones['Liquidacion.mes'] . " " . $condiciones['Liquidacion.periodo'] . "</b>", 6);
			$pdf->ezText("Empleador Liquidado: <b>" . $registros[$k-1]['cuit'] . " " . $registros[$k-1]['nombre'] . "</b>", 6);
			$pdf->ezText("");
			$y = $pdf->ezTable($tabla, null, null, $opciones) + 10;
			$pdf->line(20, $y, 578, $y);
			$pdf->ezNewPage();
			$tabla = null;
			$totalAFacturar = $totalAbonado = 0;
		}
		foreach($v['Concepto'] as $k1=>$v1) {
			$subTotal = $v1['coeficiente'] * $v1['total'];
			$totalAbonado += $v1['total'];
			$totalAFacturar += $subTotal;
			$tabla[] = array(
				"Concepto" 		=> $v1['nombre'],
				"Cantidad" 		=> $formato->format($v1['cantidad'], array("before"=>"", "places"=>2)),
				"Total Abonado" => $formato->format($v1['total'], array("before"=>"$ ", "places"=>2)),
				"Coeficiente" 	=> $formato->format($v1['coeficiente'], array("before"=>"", "places"=>2)),
				"Total" 		=> $formato->format($subTotal, array("before"=>"$ ", "places"=>2)));
		}
		$tabla[] = array(
			"Concepto" 		=> "<b>Totales</b>",
			"Cantidad" 		=> "",
			"Total Abonado" => "<b>" . $formato->format($totalAbonado, array("before"=>"$ ", "places"=>2)) . "</b>",
			"Coeficiente" 	=> "",
			"Total" 		=> "<b>" . $formato->format($totalAFacturar, array("before"=>"$ ", "places"=>2)) . "</b>");
	}
	
	$pdf->ezText("Periodo Liquidado: <b>" . $condiciones['Liquidacion.ano'] . " " . $condiciones['Liquidacion.mes'] . " " . $condiciones['Liquidacion.periodo'] . "</b>", 6);
	$pdf->ezText("Empleador Liquidado: <b>" . $registros[$k]['cuit'] . " " . $registros[$k]['nombre'] . "</b>", 6);
	$y = $pdf->ezTable($tabla, null, null, $opciones) + 10;
	$pdf->line(20, $y, 578, $y);
}
else {
	$pdf->ezText("No se han encontrado datos con los criterios especificados. Verifique.");
}
$pdf->ezStream();

?>