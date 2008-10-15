<?php
/**
 * Defino constantes de formatos
 */

/**
 * ancho y alto del recibo
 */
define("ANCHO_RECIBO", 400);
define("ALTO_RECIBO", 600);

/**
 * tamaños de letras
 */
define("SIZE_LETRA_TITULOS", 6);
define("SIZE_LETRA_ENCABEZADOS", 10);
define("SIZE_LETRA_DATOS", 8);

/**
 * margenes de la hoja
 */
define("MARGEN_HOJA_SUPERIOR", "15");
define("MARGEN_HOJA_INFERIOR", "0");
define("MARGEN_HOJA_IZQUIERDO", "15");
define("MARGEN_HOJA_DERECHO", "15");

/**
 * Indica el desplazamiento en x de un recibo al otro en la hoja apaizada.
 */
define("DESPLAZAMIENTO_X",  419);

/**
 * define la cantidad de detalles maximos que puede tener un detalle.
 */
define("MAX_CANTIDAD_DETALLES",  24);

/**
 * define el espacio que se deja despues de la fecha para imprimir la linea de la firma y la firma.
 */
define("ESPACIO_FIRMA",  10);


/**
 * indica si se imprimen formatos
 * 1: se imprimen formatos
 * 0: no se imprimen formatos
 */
$imprimir_formato=0;

$datos[]=$this->data;
//para probar la paginacion
$datos[]=$datos[0];

//relleno el vector
foreach($datos as $k=>$dato){
	$datos[$k]['Liquidacion']['trabajador_dni']="26513716";
	$datos[$k]['Liquidacion']['trabajador_legajo']="26513716";
	$datos[$k]['Liquidacion']['antiguedad']="FD";
	$datos[$k]['Liquidacion']['banco_deposito']="RIO-208384838";
	$datos[$k]['Liquidacion']['no_sujeto_deduccion']="103,28";
	$datos[$k]['Liquidacion']['sujeto_deduccion']="271,05";
	$datos[$k]['Liquidacion']['total_haberes']="374,33";
	$datos[$k]['Liquidacion']['total_deducciones']="58.22";
	$datos[$k]['Liquidacion']['neto']="316,00";
	$datos[$k]['Liquidacion']['fecha_sus']=date("Y-m-d");
	$datos[$k]['Liquidacion']['periodo_sus']="Feb 08";
	$datos[$k]['Liquidacion']['banco']="Cordoba";
	$datos[$k]['Liquidacion']['antiguedad_reconocida']="";
	foreach($datos[$k]['LiquidacionesDetalle'] as $v=>$w){
		$datos[$k]['LiquidacionesDetalle'][$v]['haberes']="271,05";
		$datos[$k]['LiquidacionesDetalle'][$v]['deducciones']="";
	}
}

$datos[1]['Liquidacion']['trabajador_dni']="25858942";
//d($datos);


$pdf->Cezpdf("a4", "landscape");
$pdf->ezSetMargins(MARGEN_HOJA_SUPERIOR,MARGEN_HOJA_INFERIOR,MARGEN_HOJA_IZQUIERDO,MARGEN_HOJA_DERECHO);
$pdf->selectFont(APP . "vendors" . DS . "pdf-php" . DS . "fonts" . DS . "Helvetica.afm");


/**
 * recorro el vector de datos
 */
foreach($datos as $k=>$dato){

	/**
	 * armo la tabla del encabezado
	 */
	if($imprimir_formato){
		$data['datos'][]=array	(	'encabezado'		=> "25 de mayo 125 1º Piso",
									'datos_0'			=> "");
		$data['datos'][]=array	(	'encabezado'		=> "5000 - Córdoba",
									'datos_0'			=> "");
		$data['datos'][]=array	(	'encabezado'		=> "Tel/Fax: 0351 4231575 / 425-0365",
									'datos_0'			=> "");
		$data['datos'][]=array	(	'encabezado'		=> "C.U.I.T. 30-59083010-7",
									'datos_0'			=> "");
	}		
	else{
		$data['datos'][]=array	(	'encabezado'		=> "",
									'datos_0'			=> "");
		$data['datos'][]=array	(	'encabezado'		=> "",
									'datos_0'			=> "",
									'datos_0'			=> "");
		$data['datos'][]=array	(	'encabezado'		=> "",
									'datos_0'			=> "");
		$data['datos'][]=array	(	'encabezado'		=> "",
									'datos_0'			=> "");
	}		
	$data['columnas']=array	(
							'datos_0'		=> "Datos",
							'encabezado'	=> "Encabezado"
							);
	$opciones_columnas=array(
							'datos_0'		=> array(
													'justification'	=> "right",
													'width'			=> "60%"
													),
							'encabezado'	=> array(
														'justification'	=> "left",
														'width'			=> "40%"
														)
							);
	$data['opciones']=array	(
							'showHeadings'		=> 0,
							'headerFontSize'	=> SIZE_LETRA_TITULOS,
							'fontSize'			=> SIZE_LETRA_DATOS,
							'shaded'			=> 0,
							'width'				=> ANCHO_RECIBO,
							'xOrientation'		=> "right",
							'showLines'			=>$imprimir_formato,
							'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO,
							'cols'				=> $opciones_columnas
							);
	//d($data);
	$recibos[$k][] = $data;
	$data = null;

	
	
	/**
	 * armo la tabla periodo abonado, fecha de pago....
	 */
	$data['datos'][]=array	(
							'periodo_abonado'	=> $dato['Liquidacion']['periodo'],
							'fecha_pago'		=> $formato->format($dato['Liquidacion']['fecha'],array("type"=>"db2helper")),
							'apellido_nombre'	=> $dato['Liquidacion']['trabajador_apellido']." ".$dato['Liquidacion']['trabajador_nombre'],
							'dni'				=> $dato['Liquidacion']['trabajador_dni'],
							'legajo'			=> $dato['Liquidacion']['trabajador_legajo']
							);

	$data['columnas']=array	(
							'periodo_abonado'	=> "Periodo Abonado",
							'fecha_pago'		=> "Fecha de Pago",
							'apellido_nombre'	=> "Apellido y Nombre",
							'dni'				=> "D.N.I.",
							'legajo'			=> "Legajo"
							);
	$opciones_columnas=array(
							'periodo_abonado'	=> array(
														'justification'	=> "center",
														'width'			=> "15%"
														),
							'fecha_pago'		=> array(
														'justification'	=> "center",
														'width'			=> "15%"
														),
							'apellido_nombre'	=> array(
														'justification'	=> "left",
														'width'			=> "40%"
														),
							'dni'				=> array(
														'justification'	=> "center",
														'width'			=> "15%"
														),
							'legajo'			=> array(
														'justification'	=> "center",
														'width'			=> "15%"
														)
							);
	$data['opciones']=array	(
									'showHeadings'		=>$imprimir_formato,
									'headerFontSize'	=> SIZE_LETRA_TITULOS,
									'fontSize'			=> SIZE_LETRA_DATOS,
									'shaded'			=> 0,
									'width'				=> ANCHO_RECIBO,
									'xOrientation'		=> "right",
									'showLines'			=>$imprimir_formato,
									'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO,
									'cols'				=> $opciones_columnas,
									'showLinesExtended'	=> array('top'=>0)
							);
	$recibos[$k][] = $data;
	$data = null;

	/**
	 * armo la tabla cuil, fecha de ingreso....
	 */
	$data['datos'][]=array	(
							'cuil'			=> $dato['Liquidacion']['trabajador_cuil'],
							'fecha_ingreso'	=> $formato->format($dato['Liquidacion']['relacion_ingreso'],array("type"=>"db2helper")),
							'antiguedad'	=> $dato['Liquidacion']['antiguedad'],
							'categoria'		=> $dato['Liquidacion']['convenio_categoria_nombre'],
							'banco_deposito'=> $dato['Liquidacion']['banco_deposito']
							);
	$data['columnas']=array	(
										'cuil'			=> "C.U.I.L.",
										'fecha_ingreso'	=> "Fec. de Ingreso",
										'antiguedad'	=> "Antig.",
										'categoria'		=> "Categoria",
										'banco_deposito'=> "Banco Deposito"
										);
	$opciones_columnas=array(
							'cuil'	=> array(
											'justification'	=> "center",
											'width'			=> "16.4%"
											),
							'fecha_ingreso'	=> array(
													'justification'	=> "center",
													'width'			=> "13.7%"
													),
							'antiguedad'	=> array(
													'justification'	=> "right",
													'width'			=>"6.9%"
													),
							'categoria'		=> array(
													'justification'	=> "left",
													'width'			=> "33%"
													),
							'banco_deposito'	=> array(
														'justification'	=> "left",
														'width'			=> "30%"								)
							);
	$data['opciones']=array	(
									'showHeadings'		=>$imprimir_formato,
									'headerFontSize'	=> SIZE_LETRA_TITULOS,
									'fontSize'			=> SIZE_LETRA_DATOS,
									'shaded'			=> 0,
									'width'				=> ANCHO_RECIBO,
									'xOrientation'		=> "right",
									'showLines'			=>$imprimir_formato,
									'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO,
									'cols'				=> $opciones_columnas,
									'showLinesExtended'	=> array('top'=>0)
								);
	/**
	 * recorro los detalles
	 */
	$recibos[$k][] = $data;
	$data = null;

	foreach($dato['LiquidacionesDetalle'] as $detalle){

		if($detalle['concepto_imprimir'] == "Si") {
			$valor = $detalle['valor']; 
		}
		elseif($detalle['concepto_imprimir'] == "Solo Con Valor" && !empty($detalle['valor'])) {
			$valor = $detalle['valor']; 
		}
		else {
			continue;
		}
		/*
		 * veo cuanto espacio ocupa el texto de concepto, si ocupa mas que el tamaño de la columna,
		 * lo trunco al tamaño máximo de la columna
		 */

		$data['datos'][]=array	(
								'cantidad'		=> $detalle['valor_cantidad'],
								'concepto'		=> $detalle['concepto_nombre'],
								'valor_unitario'=> $valor,
								'haberes'		=> $detalle['haberes'],
								'deducciones'	=> $detalle['deducciones']
								);
	}

	/**
	 * si el vector tiene menos de 20 componentes lo relleno vacio hasta 20
	 */
	if(count($data['datos'])<MAX_CANTIDAD_DETALLES){
		for($i=count($data['datos']);$i<MAX_CANTIDAD_DETALLES;$i++){
			$data['datos'][$i]=array	(
									'cantidad'		=> "",
									'concepto'		=> "",
									'valor_unitario'=> "",
									'haberes'		=> "",
									'deducciones'	=> ""
									);
		}

	} 
	$data['columnas']=array	(
							'cantidad'			=> "Cantidad",
							'concepto'			=> "Concepto",
							'valor_unitario'	=> "Valor Unitario",
							'haberes'			=> "Haberes",
							'deducciones'		=> "Deducciones"
							);
	$opciones_columnas=array(
							'cantidad'			=> array(
														'justification'	=> "right",
														'width'			=> "19%"
														),
							'concepto'			=> array(
														'justification'	=> "left",
														'width'			=> "36%"),
							'valor_unitario'	=> array(
														'justification'	=> "right",
														'width'			=> "15%"),
							'haberes'			=> array(
														'justification'	=> "right",
														'width'			=> "15%"),
							'deducciones'		=> array(
														'justification'	=> "right",
														'width'			=> "15%")
							);
	$data['opciones']=array	(
								'showHeadings'		=>$imprimir_formato,
								'headerFontSize'	=> SIZE_LETRA_TITULOS,
								'fontSize'			=> SIZE_LETRA_DATOS,
								'shaded'			=> 0,
								'width'				=> ANCHO_RECIBO,
								'xOrientation'		=> "right",
								'showLines'			=>$imprimir_formato,
								'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO,
								'cols'				=> $opciones_columnas,
								'showLinesExtended'	=> array('top'=>0)
							);
	

	$data = normalizar_datos_columna("concepto",$data,$pdf);
	//d($data);
	$recibos[$k][] = $data;
	$data = null;

	/**
	 * armo la tabla no sujeto a deduccion, sujeto a deduccion, etc.
	 */
/*
	$porcentajes_columnas= array(
								'no_sujeto_deduccion'	=> 50,
								'sujeto_deduccion'		=> 20,
								'total_haberes'			=> 15,
								'total_deducciones'		=> 15
								);
*/
	$data['datos'][]=array	('no_sujeto_deduccion'	=> $dato['Liquidacion']['no_sujeto_deduccion'],
							'sujeto_deduccion'		=> $dato['Liquidacion']['sujeto_deduccion'],
							'total_haberes'			=> $dato['Liquidacion']['total_haberes'],
							'total_deducciones'		=> $dato['Liquidacion']['total_deducciones']
							);
	$data['columnas']=array	(
							'no_sujeto_deduccion'	=> "No suj. a deduccion",
							'sujeto_deduccion'		=> "Sujeto a deduccion",
							'total_haberes'			=> "Total Haberes",
							'total_deducciones'		=> "Total Deducciones"
							);
	$opciones_columnas=array(
							'no_sujeto_deduccion'	=> array(
															'justification'	=> "right",
															'width'			=> "50%"
															),
							'sujeto_deduccion'		=> array(
															'justification'	=> "right",
															'width'			=> "20%"
															),
							'total_haberes'			=> array(
															'justification'	=> "right",
															'width'			=> "15%"
															),
							'total_deducciones'		=> array(
															'justification'	=> "right",
															'width'			=> "15%"
															)
							);
	$data['opciones']=array	(
							'showHeadings'		=>$imprimir_formato,
							'headerFontSize'	=> SIZE_LETRA_TITULOS,
							'fontSize'			=> SIZE_LETRA_DATOS,
							'shaded'			=> 0,
							'width'				=> ANCHO_RECIBO,
							'xOrientation'		=> "right",
							'showLines'			=>$imprimir_formato,
							'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO,
							'cols'				=> $opciones_columnas,
							'showLinesExtended'	=> array('top'=>0)
							);
	//d($data);
	$recibos[$k][] = $data;
	//hago este calculo para que la tabla ocupe el mismo espacio que las columnas de total haberes y total deducciones
	$ancho = str_replace("%","",$opciones_columnas['total_haberes']['width'])+str_replace("%","",$opciones_columnas['total_deducciones']['width']);
	$ancho = ($ancho * $data['opciones']['width'])/100;
	$ubicacion=(ANCHO_RECIBO-$ancho)+0+MARGEN_HOJA_IZQUIERDO;
	$data = null;

	/**
	 * armo la tabla neto a cobrar
	 */
	if($imprimir_formato){
		$data['datos'][]=array	(	'neto_cobrar'		=> "Neto a Cobrar",
									'neto_cobrar_valor'	=> $dato['Liquidacion']['neto']
								);
	}		
	else{
		$data['datos'][]=array	(	'neto_cobrar'		=> "",
									'neto_cobrar_valor'	=> $dato['Liquidacion']['neto']
								);
	}		
	$data['columnas']=array	(
							'neto_cobrar'		=> "Neto a Cobrar",
							'neto_cobrar_valor'	=> "Valor"
							);
	$opciones_columnas=array(
							'neto_cobrar'		=> array(
														'justification'	=> "left",
														'width'			=> "50.7%"
														),
							'neto_cobrar_valor'	=> array(
														'justification'	=> "right",
														'width'			=> "49.3%"
														)
							);
	$data['opciones']=array	(
							'showHeadings'		=> 0,
							'headerFontSize'	=> SIZE_LETRA_TITULOS,
							'fontSize'			=> SIZE_LETRA_DATOS,
							'shaded'			=> 0,
							'width'				=> $ancho,
							'xOrientation'		=> "right",
							'showLines'			=>$imprimir_formato,
							'xPos'				=> $ubicacion,
							'cols'				=> $opciones_columnas,
							'showLinesExtended'	=> array('top'=>0)
							);
	//d($data);
	$recibos[$k][] = $data;
	$data = null;

	/**
	 * armo la tabla con son, ultimo aporte, antiguedad reconocida, periodo, etc.
	 */
	$porcentajes_columnas= array(
								'antiguedad_reconocida'	=> 40,
								'periodo_sus'			=> 20,
								'fecha_sus'				=> 20,
								'banco'					=> 20
								);

	$data['datos'][]=array	(	
								'antiguedad_reconocida'	=> $dato['Liquidacion']['antiguedad_reconocida'],
								'periodo_sus'			=> $dato['Liquidacion']['periodo_sus'],
								'fecha_sus'				=> $formato->format($dato['Liquidacion']['fecha_sus'],array("type"=>"db2helper")),
								'banco'					=> $dato['Liquidacion']['banco']
							);
	$data['columnas']=array	(
							'antiguedad_reconocida'	=> "Antiguedad Reconocida",
							'periodo_sus'			=> "Periodo",
							'fecha_sus'				=> "Fecha",
							'banco'					=> "Banco"
							);
	$opciones_columnas=array(
							'antiguedad_reconocida'	=> array(
															'justification'	=> "left",
															'width'			=> "40%"
															),
							'periodo_sus'		=> array(
														'justification'	=> "right",
														'width'			=> "20%"
														),
							'fecha_sus'			=> array(
														'justification'	=> "center",
														'width'			=>"20%"),
							'banco'		=> array(
														'justification'	=> "center",
														'width'			=> "20%"
												)
							);
	$data['opciones']=array	(
							'showHeadings'		=>$imprimir_formato,
							'headerFontSize'	=> SIZE_LETRA_TITULOS,
							'fontSize'			=> SIZE_LETRA_DATOS,
							'shaded'			=> 0,
							'width'				=> ANCHO_RECIBO,
							'xOrientation'		=> "right",
							'showLines'			=>$imprimir_formato,
							'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO,
							'cols'				=> $opciones_columnas,
							'titleFontSize'		=> SIZE_LETRA_DATOS
							);
	$data['titulo']="Son:                       TRESCIENTOS DIECISEIS                      ultimo Deposito aporte jubilatorio";
	$data['Dy']=-10;
	$recibos[$k][] = $data;
	//hago este calculo para que la tabla ocupe el mismo espacio que las columnas fecha y banco
	$ancho = str_replace("%","",$opciones_columnas['fecha_sus']['width'])+str_replace("%","",$opciones_columnas['banco']['width']);
	$ancho = ($ancho * $data['opciones']['width'])/100;
	$ubicacion=(ANCHO_RECIBO-$ancho)+0+MARGEN_HOJA_IZQUIERDO;
	$data = null;

	/**
	 * armo la tabla lugar y fecha
	 */
	if($imprimir_formato){
		$data['datos'][]=array	(	'datos_0'	=> "Lugar:",
									'datos_1'	=> "Córdoba"
								);
		$data['datos'][]=array	(	'datos_0'	=> "Fecha:",
									'datos_1'	=> date("d/m/Y")
								);
	}
	else{
		$data['datos'][]=array	(	'datos_0'	=> "",
									'datos_1'	=> "Córdoba"
								);
		$data['datos'][]=array	(	'datos_0'	=> "",
									'datos_1'	=> date("d/m/Y")
								);
	}

	$data['columnas']=array	(
							'datos_0'	=> "lugar",
							'datos_1'	=> "fecha"
							);
	$opciones_columnas=array(
							'datos_0'		=> array(
													'justification'	=> "left",
													'width'			=>"22%"										),
							'datos_1'	=> array(
											'justification'	=> "left",
											'width'			=>"78%"
											)
							);
	$data['opciones']=array	(
							'showHeadings'		=> 0,
							'headerFontSize'	=> SIZE_LETRA_TITULOS,
							'fontSize'			=> SIZE_LETRA_DATOS,
							'shaded'			=> 0,
							'width'				=> $ancho,
							'xOrientation'		=> "right",
							'showLines'			=> 0,
							'xPos'				=> $ubicacion,
							'cols'				=> $opciones_columnas,
							'showLinesExtended'	=> array('top'=>0)
							);
	$data['Dy']=-2;
	$recibos[$k][] = $data;
	$data = null;

	/**
	 * armo la tabla para la firma
	 */
	if($imprimir_formato){
		$data['datos'][]=array(	'datos_firma'	=> "     ----------------------------------------------");
		$data['datos'][]=array(	'datos_firma'	=> "               Firma Empleador");
	}		
	else{
		$data['datos'][]=array(	'datos_firma'	=> "");
		$data['datos'][]=array(	'datos_firma'	=> "");
	}		
	$data['columnas']=array	(
							'datos_firma'		=> "Firma Empleador"
							);
	$opciones_columnas=array(
							'datos_firma'		=> array(
														'justification'	=> "left",
														'width'			=> "100%"
														)
							);
	$data['opciones']=array	(
							'showHeadings'		=> 0,
							'headerFontSize'	=> SIZE_LETRA_TITULOS,
							'fontSize'			=> SIZE_LETRA_DATOS,
							'shaded'			=> 0,
							'width'				=> ANCHO_RECIBO,
							'xOrientation'		=> "right",
							'showLines'			=> 0,
							'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO,
							'cols'				=> $opciones_columnas
							);
	//d($data);
	$recibos[$k][] = $data;
	$data = null;


}
//d($recibos);
foreach($recibos as $k=>$recibo){
	for($i=0;$i<2;$i++) {
		$pdf->ezSetY(ALTO_RECIBO - MARGEN_HOJA_SUPERIOR);
		foreach($recibo as $tabla){
			$tabla['opciones']['xPos'] += $i * DESPLAZAMIENTO_X;
			//
			if(empty($tabla['titulo'])){
				$tabla['titulo']="";
			}
			if(!empty($tabla['Dy'])){
				$pdf->ezSetDy($tabla['Dy']);
			}

			$yf=$pdf->ezTable($tabla['datos'], $tabla['columnas'], $tabla['titulo'], $tabla['opciones']);
		}
		if($i==1 && $k<count($recibos)-1){
			$pdf->ezSetDY(-$yf-1);
		}
	}
}
$pdf->ezStream();

function normalizar_datos_columna($columna,$array_datos,&$pdf){
	if(!empty($array_datos) && array_key_exists($columna,$array_datos['columnas'])){
		/*
		 * saco el ancho que va a ocupar el dato en la columna concepto
		 * le sumo 10 porque cuando pinta la tabla le deja de 5 de cada lado antes de escribir
		*/
		$ancho_columna = str_replace("%","",$array_datos['opciones']['cols'][$columna]['width']);
		$ancho_columna = ($ancho_columna/100)* $array_datos['opciones']['width'];
	}
	foreach($array_datos['datos'] as $k=>$v){
		$ancho_datos = $pdf->getTextWidth(SIZE_LETRA_DATOS,$v[$columna])+10;
		if($ancho_datos>$ancho_columna){
			//trunco el valor para que no ocupe otra fila si es mayor al ancho de la columna
			for($i=strlen($v[$columna])-1;$i>=0;$i--){
				$dato = substr($v[$columna],0,$i);
				$ancho = $pdf->getTextWidth(SIZE_LETRA_DATOS,$dato)+10;
				if($ancho<=$ancho_columna){
					$array_datos['datos'][$k][$columna]=$dato;
					break;
				}
			}
		}
	}
	return($array_datos);
}

?>