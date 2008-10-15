<?php

/**
 * tamaños de letras
 */
define("SIZE_LETRA_TITULOS", 14);
define("SIZE_LETRA_ENCABEZADOS", 20);
define("SIZE_LETRA_DATOS", 12);

/**
 * margenes de la hoja
 */
define("MARGEN_HOJA_SUPERIOR", 30);
define("MARGEN_HOJA_INFERIOR", 30);
define("MARGEN_HOJA_IZQUIERDO", 30);
define("MARGEN_HOJA_DERECHO", 30);

/**
 * tamaños de hoja
 */
define("ANCHO_HOJA", 595.28);
define("ALTO_HOJA", 841.89);

$ruta = APP . "vendors" . DS . "pdf-php" . DS . "fonts" . DS;
$datos = $this->data;
/**
 * coordenadas para A4
 * x:595.28
 * y:841.89
 */

$pdf->Cezpdf("a4", "portrait");
$pdf->ezSetMargins(MARGEN_HOJA_SUPERIOR,MARGEN_HOJA_INFERIOR,MARGEN_HOJA_IZQUIERDO,MARGEN_HOJA_DERECHO);
$pdf->selectFont($ruta . 'Helvetica.afm');

/**
 * dibujo el marco del vale
 */ 

$pdf->line(MARGEN_HOJA_IZQUIERDO,ALTO_HOJA - MARGEN_HOJA_SUPERIOR,ANCHO_HOJA-MARGEN_HOJA_DERECHO,ALTO_HOJA - MARGEN_HOJA_SUPERIOR);

$ancho = $pdf->getTextWidth(SIZE_LETRA_TITULOS,$datos['Ropa']['fecha']);
$posicion_fecha = ANCHO_HOJA - MARGEN_HOJA_DERECHO -  $ancho - 10;
$opciones=array('aleft'=>$posicion_fecha);

$pdf->ezText($formato->format($datos['Ropa']['fecha'], "db2helper"), SIZE_LETRA_TITULOS,$opciones);

$opciones=array('justification'=>"center");

$pdf->ezSetDY(-10);

$pdf->ezText("<b>Vale Por Entrega de Ropa</b>", SIZE_LETRA_ENCABEZADOS,$opciones);
$pdf->ezSetDY(-20);

$opciones=array('aleft'=>MARGEN_HOJA_IZQUIERDO + 10);
$pdf->ezText("<b>Orden de Ropa Nº: </b>".$datos['Ropa']['id'], SIZE_LETRA_TITULOS,$opciones);
$pdf->ezSetDY(-5);
$pdf->ezText("<b>Legajo: </b>".$datos['Relacion']['Trabajador']['legajo'], SIZE_LETRA_TITULOS,$opciones);
$pdf->ezSetDY(-5);
$pdf->ezText("<b>Empleado: </b>".$datos['Relacion']['Trabajador']['apellido']." ".$datos['Relacion']['Trabajador']['nombre'], SIZE_LETRA_TITULOS,$opciones);
$pdf->ezSetDY(-5);
$y = $pdf->ezText("<b>Emisión: </b>".date("d/m/Y"), SIZE_LETRA_TITULOS,$opciones);

$pdf->line(MARGEN_HOJA_IZQUIERDO,$y - 10,ANCHO_HOJA-MARGEN_HOJA_DERECHO,$y - 10);

$pdf->ezSetDY(-10);

//$pdf->ezText("<b><i>Listado de Prendas</i></b>", SIZE_LETRA_TITULOS);
//$pdf->ezSetDY(-10);


/**
 * junto los datos para armar la tabla
 */

$data['columnas']=array	(
						'prenda'=> "<b>Prenda</b>",
						'tipo'	=> "<b>Tipo</b>",
						'color'	=> "<b>Color</b>",
						'modelo'=> "<b>Modelo</b>",
						'tamano'=> "<b>Tamaño</b>"
						);
$opciones_columnas=array(
						'prenda'=> array(
										'justification'	=> "left",
										'width'			=> "30%"
										),
						'tipo'	=> array(
										'justification'	=> "left",
										'width'			=> "15%"),
						'color'	=> array(
										'justification'	=> "left",
										'width'			=> "20%"),
						'modelo'=> array(
										'justification'	=> "left",
										'width'			=> "20%"),
						'tamano'=> array(
										'justification'	=> "right",
										'width'			=> "15%"
										)
						);
$data['opciones']=array	(
							'showHeadings'		=>1,
							'headerFontSize'	=> SIZE_LETRA_TITULOS,
							'fontSize'			=> SIZE_LETRA_DATOS,
							'shaded'			=> 0,
							'showLines'			=> 1,
							'cols'				=> $opciones_columnas,
							'width'				=> ANCHO_HOJA - MARGEN_HOJA_DERECHO - MARGEN_HOJA_IZQUIERDO - 10,
							'xOrientation'		=> "right",
							'xPos'				=> MARGEN_HOJA_IZQUIERDO + 10,
							'titleFontSize'		=> SIZE_LETRA_TITULOS,
							'showLinesExtended' => array('left'=>0,'right'=>0)
						);


foreach($datos['RopasDetalle'] as $prenda){
	$data['datos'][]=array	(
						'prenda'	=> $prenda['prenda'],
						'tipo'		=> $prenda['tipo'],
						'color'		=> $prenda['color'],
						'modelo'	=> $prenda['modelo'],
						'tamano'	=> $prenda['tamano']
						);

}

$y = $pdf->ezTable($data['datos'], $data['columnas'], "<b>Listado de Prendas</b>", $data['opciones']);
/**
 * dibujo el resto de las lineas del marco
 */
$y = $y - 10;

$pdf->line(MARGEN_HOJA_IZQUIERDO,$y,ANCHO_HOJA-MARGEN_HOJA_DERECHO,$y);
$pdf->line(MARGEN_HOJA_IZQUIERDO,ALTO_HOJA - MARGEN_HOJA_SUPERIOR,MARGEN_HOJA_IZQUIERDO,$y);
$pdf->line(ANCHO_HOJA - MARGEN_HOJA_DERECHO,ALTO_HOJA - MARGEN_HOJA_SUPERIOR,ANCHO_HOJA - MARGEN_HOJA_DERECHO,$y);
$pdf->ezStream();
?>