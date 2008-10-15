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
 * indica si se deben imprimir lineas, titulos, etc.
 * 1: se imprimen formatos
 * 0: no se imprimen formatos
 */ 
$imprimir_formato=1;

$datos[]=$this->data;

$datos[0]['Liquidacion']['trabajador_dni']="26513716";
$datos[0]['Liquidacion']['trabajador_legajo']="26513716";
$datos[0]['Liquidacion']['antiguedad']="FD";
$datos[0]['Liquidacion']['banco_deposito']="RIO-208384838";
$datos[0]['Liquidacion']['no_sujeto_deduccion']="103,28";
$datos[0]['Liquidacion']['sujeto_deduccion']="271,05";
$datos[0]['Liquidacion']['total_haberes']="374,33";
$datos[0]['Liquidacion']['total_deducciones']="58.22";
$datos[0]['Liquidacion']['neto']="316,00";
$datos[0]['Liquidacion']['fecha_sus']=date("Y-m-d");
$datos[0]['Liquidacion']['periodo_sus']="Feb 08";
$datos[0]['Liquidacion']['banco']="Cordoba";
$datos[0]['Liquidacion']['antiguedad_reconocida']="";


//d($datos);

foreach($datos[0]['LiquidacionesDetalle'] as $k=>$v){
	$datos[0]['LiquidacionesDetalle'][$k]['haberes']="271,05";
	$datos[0]['LiquidacionesDetalle'][$k]['deducciones']="";
}

$pdf->Cezpdf("a4", "landscape");
$pdf->ezSetMargins(MARGEN_HOJA_SUPERIOR,MARGEN_HOJA_INFERIOR,MARGEN_HOJA_IZQUIERDO,MARGEN_HOJA_DERECHO);
$pdf->selectFont(APP . "vendors" . DS . "pdf-php" . DS . "fonts" . DS . "Helvetica.afm");


	/**
	 * recorro el vector de datos
	 */
	foreach($datos as $k=>$dato){

		/**
		 * armo la tabla periodo abonado, fecha de pago....
		 */

		$porcentajes_columnas= array(
										'periodo_abonado'	=> 15,
										'fecha_pago'		=> 15,
										'apellido_nombre'	=> 40,
										'dni'				=> 15,
										'legajo'			=> 15
									);

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
															'justification'	=> 'center',
															'width'			=> ($porcentajes_columnas['periodo_abonado']*ANCHO_RECIBO)/100),
								'fecha_pago'		=> array(
															'justification'	=> 'center',
															'width'			=> ($porcentajes_columnas['fecha_pago']*ANCHO_RECIBO)/100),
								'apellido_y_nombre'	=> array(
															'justification'	=> 'left',
															'width'			=> ($porcentajes_columnas['apellido_nombre']*ANCHO_RECIBO)/100),
								'dni'				=> array(
															'justification'	=> 'center',
															'width'			=> ($porcentajes_columnas['dni']*ANCHO_RECIBO)/100),
								'legajo'			=> array(
															'justification'	=> 'center',
															'widht'			=> ($porcentajes_columnas['legajo']*ANCHO_RECIBO)/100));
		$data['opciones']=array	(
										'showHeadings'		=> $imprimir_formato,
										'headerFontSize'	=> SIZE_LETRA_TITULOS,
										'fontSize'			=> SIZE_LETRA_DATOS,
										'shaded'			=> 0,
										'width'				=> ANCHO_RECIBO,
										'xOrientation'		=> 'right',
										'showLines'			=> 1,
										'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO
										'cols'				=> $opciones_columnas
									);
		$recibos[$k][] = $data;
		$data = null;
		$opciones_columnas=false;
		$porcentajes_columnas=false;

		/**
		 * armo la tabla cuil, fecha de ingreso....
		 */

		$porcentajes_columnas= array(
										'cuil'			=> 16.4,
										'fecha_ingreso'	=> 13.7,
										'antiguedad'	=> 6.9,
										'categoria'		=> 33,
										'banco_deposito'			=> 30
									);

		$data['datos'][]=array	(
											'cuil'			=> $dato['Liquidacion']['trabajador_cuil'],
											'fecha_ingreso'	=> $formato->format($dato['Liquidacion']['relacion_ingreso'],array("type"=>"db2helper")),
											'antiguedad'	=> $dato['Liquidacion']['antiguedad'],
											'categoria'		=> $dato['Liquidacion']['convenio_categoria_nombre'],
											'banco_deposito'			=> $dato['Liquidacion']['banco_deposito']
											);
		$data['columnas']=array	(
											'cuil'			=> "C.U.I.L.",
											'fecha_ingreso'	=> "Fec. de Ingreso",
											'antiguedad'	=> "Antig.",
											'categoria'		=> "Categoria",
											'banco_deposito'			=> "Banco Deposito"
											);
		$opciones_columnas=array(
								'cuil'	=> array(
															'justification'	=> 'center',
															'width'			=> ($porcentajes_columnas['cuil']*ANCHO_RECIBO)/100
															),
								'fecha_ingreso'		=> array(
															'justification'	=> 'center',
															'width'			=> ($porcentajes_columnas['fecha_ingreso']*ANCHO_RECIBO)/100),
								'antiguedad'	=> array(
															'justification'	=> 'right',
															'width'			=> ($porcentajes_columnas['antiguedad']*ANCHO_RECIBO)/100),
								'categoria'				=> array(
															'justification'	=> 'left',
															'width'			=> ($porcentajes_columnas['categoria']*ANCHO_RECIBO)/100),
								'banco_deposito'			=> array(
															'justification'	=> 'left',
															'widht'			=> ($porcentajes_columnas['banco_deposito']*ANCHO_RECIBO)/100
															)
								);
		$data['opciones']=array	(
										'showHeadings'		=> 1,
										'headerFontSize'	=> SIZE_LETRA_TITULOS,
										'fontSize'			=> SIZE_LETRA_DATOS,
										'shaded'			=> 0,
										'width'				=> ANCHO_RECIBO,
										'xOrientation'		=> 'right',
										'showLines'			=> 1,
										'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO
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
			$data['datos'][]=array	(
									'cantidad'		=> $detalle['valor_cantidad'],
									'concepto'		=> $detalle['concepto_nombre'],
									'valor_unitario'=> $valor,
									'haberes'		=> $detalle['haberes'],
									'deducciones'	=> $detalle['deducciones']
									);
		}

		$porcentajes_columnas= array(
									'cantidad'		=> 20,
									'concepto'		=> 35,
									'valor_unitario'=> 15,
									'haberes'		=> 15,
									'deducciones'	=> 20
									);
		$data['columnas']=array	(
								'cantidad'			=> "Cantidad",
								'concepto'			=> "Concepto",
								'valor_unitario'	=> "Valor Unitario",
								'haberes'			=> "Haberes",
								'deducciones'		=> "Deducciones"
								);
		$opciones_columnas=array(
								'cantidad'			=> array(
															'justification'	=> 'right',
															'width'			=> ($porcentajes_columnas['cantidad']*ANCHO_RECIBO)/100
															),
								'concepto'			=> array(
															'justification'	=> 'left',
															'width'			=> ($porcentajes_columnas['concepto']*ANCHO_RECIBO)/100),
								'valor_unitario'	=> array(
															'justification'	=> 'right',
															'width'			=> ($porcentajes_columnas['valor_unitario']*ANCHO_RECIBO)/100),
								'haberes'			=> array(
															'justification'	=> 'right',
															'width'			=> ($porcentajes_columnas['haberes']*ANCHO_RECIBO)/100),
								'deducciones'		=> array(
															'justification'	=> 'right',
															'widht'			=> ($porcentajes_columnas['deducciones']*ANCHO_RECIBO)/100
															)
								);
		$data['opciones']=array	(
									'showHeadings'		=> 1,
									'headerFontSize'	=> SIZE_LETRA_TITULOS,
									'fontSize'			=> SIZE_LETRA_DATOS,
									'shaded'			=> 0,
									'width'				=> ANCHO_RECIBO,
									'xOrientation'		=> 'right',
									'showLines'			=> 1,
									'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO
									'cols'				=> $opciones_columnas,
									'showLinesExtended'	=> array('top'=>0)
								);
		$recibos[$k][] = $data;
		$data = null;

		/**
		 * armo la tabla no sujeto a deduccion, sujeto a deduccion, etc.
		 */

		$porcentajes_columnas= array(
									'no_sujeto_deduccion'	=> 50,
									'sujeto_deduccion'		=> 20,
									'total_haberes'			=> 15,
									'total_deducciones'		=> 15
									);

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
															'justification'	=> 'right',
															'width'			=> ($porcentajes_columnas['no_sujeto_deduccion']*ANCHO_RECIBO)/100
															),
								'sujeto_deduccion'		=> array(
															'justification'	=> 'right',
															'width'			=> ($porcentajes_columnas['sujeto_deduccion']*ANCHO_RECIBO)/100),
								'total_haberes'			=> array(
															'justification'	=> 'right',
															'width'			=> ($porcentajes_columnas['total_haberes']*ANCHO_RECIBO)/100),
								'total_deducciones'		=> array(
															'justification'	=> 'right',
															'width'			=> ($porcentajes_columnas['total_deducciones']*ANCHO_RECIBO)/100)
								);
		$data['opciones']=array	(
								'showHeadings'		=> 1,
								'headerFontSize'	=> SIZE_LETRA_TITULOS,
								'fontSize'			=> SIZE_LETRA_DATOS,
								'shaded'			=> 0,
								'width'				=> ANCHO_RECIBO,
								'xOrientation'		=> 'right',
								'showLines'			=> 1,
								'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO
								'cols'				=> $opciones_columnas,
								'showLinesExtended'	=> array('top'=>0)
								);
		$recibos[$k][] = $data;
		$data = null;

		/**
		 * armo la tabla neto a cobrar
		 */
		//hago este calculo para que la tabla ocupe el mismo espacio que las columnas de total haberes y total deducciones
		$ancho_tabla = (($porcentajes_columnas['total_haberes']+$porcentajes_columnas['total_deducciones'])*ANCHO_RECIBO)/100;

		$porcentajes_columnas= array(
									'neto_cobrar'			=> 50,
									'neto_cobrar_valor'		=> 50
									);

		$data['datos'][]=array	(	'neto_cobrar'		=> "Neto a Cobrar",
									'neto_cobrar_valor'	=> $dato['Liquidacion']['neto']
								);
		$data['columnas']=array	(
								'neto_cobrar'		=> "Neto a Cobrar",
								'neto_cobrar_valor'	=> "Valor"
								);
		$opciones_columnas=array(
								'neto_cobrar'		=> array(
															'justification'	=> 'left',
															'width'			=> ($porcentajes_columnas['neto_cobrar']*ANCHO_RECIBO)/100
															),
								'neto_cobrar_valor'	=> array(
															'justification'	=> 'right',
															'width'			=> ($porcentajes_columnas['neto_cobrar_valor']*ANCHO_RECIBO)/100
															)
								);
		$data['opciones']=array	(
								'showHeadings'		=> 1,
								'headerFontSize'	=> SIZE_LETRA_TITULOS,
								'fontSize'			=> SIZE_LETRA_DATOS,
								'shaded'			=> 0,
								'width'				=> ANCHO_RECIBO,
								'xOrientation'		=> 'right',
								'showLines'			=> 1,
								'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO,
								'cols'				=> $opciones_columnas,
								'showLinesExtended'	=> array('top'=>0)
								);
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
																'justification'	=> 'left',
																'width'			=> ($porcentajes_columnas['antiguedad_reconocida']*ANCHO_RECIBO)/100
																),
								'periodo_sus'		=> array(
															'justification'	=> 'right',
															'width'			=> ($porcentajes_columnas['periodo_sus']*ANCHO_RECIBO)/100),
								'fecha_sus'			=> array(
															'justification'	=> 'center',
															'width'			=> ($porcentajes_columnas['fecha_sus']*ANCHO_RECIBO)/100),
								'banco'		=> array(
															'justification'	=> 'center',
															'width'			=> ($porcentajes_columnas['banco']*ANCHO_RECIBO)/100)
								);
		$data['opciones']=array	(
								'showHeadings'		=> 1,
								'headerFontSize'	=> SIZE_LETRA_TITULOS,
								'fontSize'			=> SIZE_LETRA_DATOS,
								'shaded'			=> 0,
								'width'				=> ANCHO_RECIBO,
								'xOrientation'		=> 'right',
								'showLines'			=> 1,
								'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO,
								'cols'				=> $opciones_columnas,
								'titleFontSize'		=> SIZE_LETRA_DATOS
								);
		$data['titulo']="Son:                       TRESCIENTOS DIECISEIS                      ultimo Deposito aporte jubilatorio";
		$data['Dy']=-10;
		$recibos[$k][] = $data;
		$data = null;

		/**
		 * armo la tabla lugar y fecha
		 */

		$porcentajes_columnas= array(
									'datos_0'	=> 50,
									'datos_1'	=> 50
									);

		$data['datos'][]=array	(	'datos_0'	=> "Lugar",
									'datos_1'	=> "Córdoba"
								);
		$data['datos'][]=array	(	'datos_0'	=> "Fecha",
									'datos_1'	=> date("d/m/Y")
								);
		$data['columnas']=array	(
								'datos_0'	=> "lugar",
								'datos_1'	=> "fecha"
								);
		$opciones_columnas=array(
								'datos_0'		=> array(
													'justification'	=> 'left',
													'width'			=> ($porcentajes_columnas['datos_0']*ANCHO_RECIBO)/100
													),
								'datos_1'	=> array(
												'justification'	=> 'center',
												'width'			=> ($porcentajes_columnas['datos_1']*ANCHO_RECIBO)/100
												)
								);
		$data['opciones']=array	(
								'showHeadings'		=> 0,
								'headerFontSize'	=> SIZE_LETRA_TITULOS,
								'fontSize'			=> SIZE_LETRA_DATOS,
								'shaded'			=> 0,
								'width'				=> ANCHO_RECIBO,
								'xOrientation'		=> 'right',
								'showLines'			=> 0,
								'xPos'				=> 0+MARGEN_HOJA_IZQUIERDO,
								'cols'				=> $opciones_columnas,
								'showLinesExtended'	=> array('top'=>0)
								);
		$data['Dy']=-2;
		$recibos[$k][] = $data;
		$data = null;

	}
	//debug($recibos);
	foreach($recibos as $recibo){
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

				$pdf->ezTable($tabla['datos'], $tabla['columnas'], $tabla['titulo'], $tabla['opciones']);
			}
		}
	}

$pdf->ezStream();
?>