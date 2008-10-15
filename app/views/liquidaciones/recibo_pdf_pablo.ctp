<?
/*
********************************************************************************************************
defino la estructura a nivel formato, margenes de celdas, margenes de titulos, tamaño de letras, etc.
********************************************************************************************************
*/
/*
 * Indica el desplazamiento en y desde el y inicial hasta el encabezado.
 */
//define("ENCABEZADO", 520);

/**
 * Indica el desplazamiento en x de un recibo al otro en la hoja apaizada.
 */
define("DESPLAZAMIENTO_X",  414);
//define("DESPLAZAMIENTO_X",  425);

/*
* defino constantes de formatos
*/

define("SIZE_LETRA_TITULOS", 6);
define("SIZE_LETRA_ENCABEZADOS", 10);
define("SIZE_LETRA_DATOS", 8);

define("LETRA_TITULOS", "tahoma");
define("LETRA_ENCABEZADOS", "tahoma");
define("LETRA_DATOS", "tahoma");

/*
* margenes de la hoja
*/
define("MARGEN_HOJA_SUPERIOR", "10");
define("MARGEN_HOJA_INFERIOR", "10");
define("MARGEN_HOJA_IZQUIERDO", "10");
define("MARGEN_HOJA_DERECHO", "2");

/*
* margenes del encabezado
*/
define("MARGEN_ENCABEZADO_SUPERIOR", "20");
define("MARGEN_ENCABEZADO_INFERIOR", "10");

/*
* margenes de los titulos
*/
define("MARGEN_TITULOS_SUPERIOR", "10");
define("MARGEN_TITULOS_INFERIOR", "5");
define("MARGEN_TITULOS_IZQUIERDO", "5");
define("MARGEN_TITULOS_DERECHO", "5");

/*
* margen para los datos
*/
define("MARGEN_DATOS_IZQUIERDO", "5");
define("MARGEN_DATOS_SUPERIOR", "10");
define("MARGEN_DATOS_INFERIOR", "5");
define("MARGEN_DATOS_DERECHO", "5");

/*
define("ALTURA_CELDA_ENCABEZADO", 40);
define("ALTURA_CELDA_TITULOS", 8);
define("ALTURA_CELDA_DATOS", 10);
*/

define("INTERLINEADO", 10);
define("ANCHO_RECIBO", 415);
define("ALTO_RECIBO", 600);
define("ALTO_CONTENIDO", 240);
define("ESPACIO_SON", 10);


$datos[] = $this->data;
//d(APP . "vendors" . DS . "pdf-php" . DS . "fonts");
$pdf->selectFont(APP . "vendors" . DS . "pdf-php" . DS . "fonts" . DS . "Tahoma.afm");

/**
* Bandera que indica si se pintan solo datos (papel preimpreso) o se pinta la estructura y los datos.
*/
$pre_impreso=0;

/**
Se crea una instancia de la clase ezpdf.
*/
$pdf->Cezpdf("a4", "landscape");
$pdf->selectFont(APP . "vendors" . DS . "pdf-php" . DS . "fonts" . DS . "Helvetica.afm");


/**
* Se setean las coordenadas con los valores iniciales.
*/

$x0=0+MARGEN_HOJA_IZQUIERDO;
$y0=ALTO_RECIBO-MARGEN_HOJA_SUPERIOR;
$xf=ANCHO_RECIBO-MARGEN_HOJA_DERECHO;
$yf=0+MARGEN_HOJA_INFERIOR;
$xd=0;//el desplazamiento en x
foreach($datos as $dato){
	while($xd<=DESPLAZAMIENTO_X){

		$x0=$x0+$xd; // le sumo el desplazamiento en x a $x0
		$xf=$xf+$xd;
		$pdf->line($x0, $y0, $x0, $yf);//linea vertical (inicio del recibo de sueldo desde la izquierda)
		$pdf->line($xf, $y0, $xf, $yf);//linea vertical (inicio del recibo de sueldo desde la derecha)

		$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (inicio del recibo de sueldo desde arriba)
		$pdf->line($x0, $yf, $xf, $yf);//linea horizontal (fin del recibo de sueldo desde abajo)

		/**
		* Dibuja el encabezado del recibo.
		*/
		$altura_letra=$pdf->getFontHeight(SIZE_LETRA_ENCABEZADOS);//saco la altura de la letra de los encabezados
		$ancho=$pdf->getTextWidth(SIZE_LETRA_ENCABEZADOS,"Tel/Fax: (0351) 4231575/ 4250365")+10;//saco el ancho que ocupa tel/fax para alinear todos igual
		$y0=$y0-MARGEN_ENCABEZADO_SUPERIOR;
		if (!$pre_impreso){
			$pdf->addText($xf-$ancho, $y0, SIZE_LETRA_ENCABEZADOS, "25 de Mayo 125 1� Piso");
		}
		$y0=$y0-$altura_letra;
		if(!$pre_impreso){
			$pdf->addText($xf-$ancho, $y0, SIZE_LETRA_ENCABEZADOS, "5000 - C�rdoba");//C�digo Postal en encabezado del recibo.
		}
		$y0=$y0-$altura_letra;
		if(!$pre_impreso){
			$pdf->addText($xf-$ancho, $y0, SIZE_LETRA_ENCABEZADOS, "Tel/Fax: (0351) 4231575/ 4250365");//Telefono en encabezado del recibo.
		}
		$y0=$y0-$altura_letra;
		if(!$pre_impreso){
			$pdf->addText($xf-$ancho, $y0, SIZE_LETRA_ENCABEZADOS, "C.U.I.T.: 30-59083010-7");//CUIT en encabezado del recibo.
		}

		$y0=$y0-MARGEN_ENCABEZADO_INFERIOR; // marco la proxima posicion y teniendo en cuenta el margen
		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (delimita el encabezado del recibo)
		}
		$y0=$y0-MARGEN_DATOS_SUPERIOR;
		$pdf->addText($x0+MARGEN_DATOS_IZQUIERDO, $y0, SIZE_LETRA_DATOS, "Usuario: 781201 - DADA SA");//USUARIO.

		$altura_letra=$pdf->getFontHeight(SIZE_LETRA_TITULOS);//saco la altura de la letra de los titulos
		$y0=$y0-MARGEN_DATOS_INFERIOR;

		/*
		* saco las coordenadas para dibujar las lineas verticales de los primeros titulos
		*/
		$y0_linea_titulos=$y0; 
		$y1_linea_titulos=$y0-MARGEN_TITULOS_SUPERIOR-MARGEN_TITULOS_INFERIOR-MARGEN_DATOS_SUPERIOR-MARGEN_DATOS_INFERIOR;
		$y_datos=$y0-MARGEN_TITULOS_SUPERIOR-MARGEN_TITULOS_INFERIOR-MARGEN_DATOS_SUPERIOR; // saco la coordenada de y de los datos para poder pintarlos justo despues de las lineas verticales que dividen las columnas
		$x_datos=$x0;

		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (titulos)
		}
		$y0=$y0-MARGEN_TITULOS_SUPERIOR;

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Periodo Abonado");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Periodo Abonado");//periodo abonado.
		}
		$pdf->addText($x0, $y_datos, SIZE_LETRA_DATOS, $dato['Liquidacion']['periodo']);//datos periodo abonado.

		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO;
		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical (periodo abonado)
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Fecha de Pago");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Fecha de Pago");//fecha de pago.
		}
		$pdf->addText($x0+5, $y_datos, SIZE_LETRA_DATOS, $dato['Liquidacion']['fecha']);//datos fecha de pago.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO+14;
		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical (fecha de pago)
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Apellido y Nombre");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Apellido y Nombre");//apellido y nombre.
		}
		$pdf->addText($x0, $y_datos, SIZE_LETRA_DATOS, $dato["Liquidacion"]["trabajador_apellido"]." ". $dato["Liquidacion"]["trabajador_nombre"]);//datos apellido y nombre.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO+80;

		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical (apellido)
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"D.N.I.");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "D.N.I.");//dni.
		}
		$pdf->addText($x0, $y_datos, SIZE_LETRA_DATOS, "Falta Dato");//datos dni.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO+30;
		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical (dni)
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Legajo");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Legajo");//legajo.
		}
		$pdf->addText($x0, $y_datos, SIZE_LETRA_DATOS, "Falta Dato");//datos dni.

		$x0=0+$xd+MARGEN_HOJA_IZQUIERDO; // reinicializo x
		$y0=$y0-MARGEN_TITULOS_INFERIOR;

		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (cierre titulos)
		}
		$y0=$y1_linea_titulos;

		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (datos)
		}

		/*
		* titulos y datos cuil, fecha de ingreso....
		*/

		/*
		* saco las coordenadas para dibujar las lineas verticales de los primeros titulos
		*/
		$y0_linea_titulos=$y0; 
		$y1_linea_titulos=$y0-MARGEN_TITULOS_SUPERIOR-MARGEN_TITULOS_INFERIOR-MARGEN_DATOS_SUPERIOR-MARGEN_DATOS_INFERIOR;
		$y_datos=$y0-MARGEN_TITULOS_SUPERIOR-MARGEN_TITULOS_INFERIOR-MARGEN_DATOS_SUPERIOR; // saco la coordenada de y de los datos para poder pintarlos justo despues de las lineas verticales que dividen las columnas

		$y0=$y0-MARGEN_TITULOS_SUPERIOR;

		$ancho=$pdf->getTextWidth(SIZE_LETRA_DATOS,$dato['Liquidacion']['trabajador_cuil']);//saco el ancho que ocupa el dato cuil
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "C.U.I.L.");//titulo cuil.
		}
		$pdf->addText($x0, $y_datos, SIZE_LETRA_DATOS, $dato['Liquidacion']['trabajador_cuil']);//datos cuil.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO;
		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical (cuil)
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Fecha de Ingreso");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Fecha de Ingreso");//fecha de pago.
		}
		$pdf->addText($x0+5, $y_datos, SIZE_LETRA_DATOS, $dato['Liquidacion']['relacion_ingreso']);//datos fecha de pago.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO;
		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical (fecha de pago)
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Antig.");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Antig.");//antiguedad.
		}
		$pdf->addText($x0+5, $y_datos, SIZE_LETRA_DATOS, "FD");//datos antiguedad.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO;

		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical (antiguedad)
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,$dato["Liquidacion"]["convenio_categoria_nombre"]);//saco el ancho que ocupa el dato
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Categoria");//titulo categoria.
		}
		$pdf->addText($x0, $y_datos, SIZE_LETRA_DATOS, $dato["Liquidacion"]["convenio_categoria_nombre"]);//datos categoria del trabajador.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO+85;
		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical (dni)
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Banco Deposito");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Banco Deposito");//legajo.
		}
		$pdf->addText($x0, $y_datos, SIZE_LETRA_DATOS, "Falta Dato");//datos dni.

		$x0=0+$xd+MARGEN_HOJA_IZQUIERDO;//reinicializo x
		$y0=$y0-MARGEN_TITULOS_INFERIOR;
		//$y0=$y1_linea_titulos;

		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (datos)
		}

		$y0=$y1_linea_titulos;

		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (datos)
		}


		/*
		* saco las coordenadas para dibujar las lineas verticales de los primeros titulos
		*/
		$y0_linea_titulos=$y0; 
		$y1_linea_titulos=$y0-MARGEN_TITULOS_SUPERIOR;
		//$y_datos=$y0-MARGEN_TITULOS_SUPERIOR-MARGEN_TITULOS_INFERIOR-MARGEN_DATOS_SUPERIOR; // saco la coordenada de y de los datos para poder pintarlos justo despues de las lineas verticales que dividen las columnas

		$y0=$y0-MARGEN_TITULOS_SUPERIOR;

		$ancho=$pdf->getTextWidth(SIZE_LETRA_DATOS,"Cantidad");//saco el ancho que ocupa el dato cuil
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Cantidad");//titulo cuil.
		}
		$altura_letra=$pdf->getFontHeight(SIZE_LETRA_DATOS);//saco la altura de la letra de los datos para saber saber el rebglon siguiente
		$y1_linea_titulos=$y1_linea_titulos-MARGEN_TITULOS_INFERIOR-MARGEN_DATOS_SUPERIOR;

		/*
		* esto iria dentro de un ciclo
		*/
		for($i=0;$i<=2;$i++){

			$pdf->addText($x0+30, $y1_linea_titulos, SIZE_LETRA_DATOS, "1.00");//datos cuil.
			$ancho=$pdf->getTextWidth(SIZE_LETRA_DATOS,"1.00");//saco el ancho que ocupa el dato cuil
			//$x0=$x0+$ancho+10;
			$y1_linea_titulos=$y1_linea_titulos-$altura_letra;
		}
		
		$y1_linea_titulos=$y1_linea_titulos-MARGEN_DATOS_INFERIOR-ALTO_CONTENIDO;
		$x0=0+$xd+MARGEN_HOJA_IZQUIERDO;//reinicializo x
		if(!$pre_impreso){
			$pdf->line($x0, $y1_linea_titulos, $xf, $y1_linea_titulos);//linea horizontal (datos)
		}

		$y0=$y1_linea_titulos;
		$y0_linea_titulos=$y0;
		$y1_linea_titulos=$y0-MARGEN_TITULOS_SUPERIOR-MARGEN_TITULOS_INFERIOR-MARGEN_DATOS_SUPERIOR-MARGEN_DATOS_INFERIOR;
		$y_datos=$y0-MARGEN_TITULOS_SUPERIOR-MARGEN_TITULOS_INFERIOR-MARGEN_DATOS_SUPERIOR; // 

		$y0=$y0-MARGEN_TITULOS_SUPERIOR;

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"No Suj. a deduccion");//saco el ancho que ocupa el titulo

		$x0=$x0+MARGEN_TITULOS_IZQUIERDO+130;

		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical antes de no sujeto a deduccion
		}
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;

		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "No Suj. a deduccion");//fecha de pago.
		}
		$pdf->addText($x0+5, $y_datos, SIZE_LETRA_DATOS, "103.28");//datos fecha de pago.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO;
		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical no sujeto a deduccion
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Sujeto a deduccion");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Sujeto a deduccion");//fecha de pago.
		}
		$pdf->addText($x0+5, $y_datos, SIZE_LETRA_DATOS, "271.05");//datos fecha de pago.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO;
		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos-MARGEN_TITULOS_SUPERIOR-MARGEN_TITULOS_INFERIOR);//linea vertical no sujeto a deduccion
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Total Haberes");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Total Haberes");//fecha de pago.
			$pdf->addText($x0, $y1_linea_titulos-MARGEN_DATOS_SUPERIOR, SIZE_LETRA_TITULOS, "Neto a Cobrar");//titulo neto a cobrar.
		}
		$pdf->addText($x0+5, $y_datos, SIZE_LETRA_DATOS, "374.33");//datos total haberes.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO+15;

		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos-MARGEN_DATOS_SUPERIOR-MARGEN_DATOS_INFERIOR);//linea vertical no sujeto a deduccion
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Total Deducciones");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Total Deducciones");//fecha de pago.
		}
		$pdf->addText($x0+30, $y_datos, SIZE_LETRA_DATOS, "58.22");//datos total deducciones.
		$pdf->addText($x0+30, $y1_linea_titulos-MARGEN_DATOS_SUPERIOR, SIZE_LETRA_DATOS, "316.00");//datos total deducciones.

		$x0=0+$xd+MARGEN_HOJA_IZQUIERDO; // reinicializo x
		$y0=$y0-MARGEN_TITULOS_INFERIOR;

		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (cierre titulos)
		}
		$y0=$y1_linea_titulos;

		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (datos)
		}

		$y0=$y0-MARGEN_DATOS_SUPERIOR-MARGEN_DATOS_INFERIOR;
		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea vertical (neto a cobrar)
		}
		$y0=$y0-MARGEN_TITULOS_SUPERIOR-ESPACIO_SON;
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Son:");//saco el ancho que ocupa el titulo
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Son:");//titulo importe en letra.
		}
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO+MARGEN_DATOS_IZQUIERDO+30;
		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"TRESCIENTOS DIECISEIS:");//saco el ancho que ocupa el titulo

		$pdf->addText($x0, $y0, SIZE_LETRA_DATOS, "TRESCIENTOS DIECISEIS");//importe en letra.
		$x0=$x0+$ancho+MARGEN_DATOS_DERECHO+80;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Ultimo Deposito Aporte Jubilatorio");//importe en letra.
		}

		$x0=0+$xd+MARGEN_HOJA_IZQUIERDO; // reinicializo x
		$y0=$y0-MARGEN_TITULOS_INFERIOR;
		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (debajo de son)
		}
		
		$y0_linea_titulos=$y0;
		$y1_linea_titulos=$y0-MARGEN_TITULOS_SUPERIOR-MARGEN_TITULOS_INFERIOR-MARGEN_DATOS_SUPERIOR-MARGEN_DATOS_INFERIOR;
		$y_datos=$y0-MARGEN_TITULOS_SUPERIOR-MARGEN_TITULOS_INFERIOR-MARGEN_DATOS_SUPERIOR; // 

		$y0=$y0-MARGEN_TITULOS_SUPERIOR;
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Antiguedad Reconocida");//saco el ancho que ocupa el titulo
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Antiguedad Reconocida");//fecha de pago.
		}
		//$pdf->addText($x0+5, $y_datos, SIZE_LETRA_DATOS, "103.28");//datos fecha de pago.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO+100;
		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical no sujeto a deduccion
		}
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Periodo");//saco el ancho que ocupa el titulo
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Periodo");//fecha de pago.
		}
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO;
		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical no sujeto a deduccion
		}


		/*
		$pdf->ezSetY($y1_linea_titulos);
		if($xd==0){
			$cols=array("No Suj. a deduccion","Sujeto a deduccion","Total Haberes","Total Deducciones");
			$data=array('103.28','271.05','374.33','58.22');
			$options=array(	'width'=>$xf,
							'xPos'=>224,
							'shaded'=>0,
							'xOrientation'=>'centre',
							'titleFontSize'=>'0',
							'fontSize'=>SIZE_LETRA_DATOS,
							'cols'=>array('No Suj. a deduccion'=>array('fontSize'=>80)));
			$pdf->ezTable($data,$cols,'',$options);
		}
		*/
		/*

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Antig.");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Antig.");//antiguedad.
		}
		$pdf->addText($x0+5, $y_datos, SIZE_LETRA_DATOS, "FD");//datos antiguedad.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO;

		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical (antiguedad)
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,$dato["Liquidacion"]["convenio_categoria_nombre"]);//saco el ancho que ocupa el dato
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Categoria");//titulo categoria.
		}
		$pdf->addText($x0, $y_datos, SIZE_LETRA_DATOS, $dato["Liquidacion"]["convenio_categoria_nombre"]);//datos categoria del trabajador.
		$x0=$x0+$ancho+MARGEN_TITULOS_DERECHO+85;
		if(!$pre_impreso){
			$pdf->line($x0, $y0_linea_titulos, $x0, $y1_linea_titulos);//linea vertical (dni)
		}

		$ancho=$pdf->getTextWidth(SIZE_LETRA_TITULOS,"Banco Deposito");//saco el ancho que ocupa el titulo
		$x0=$x0+MARGEN_TITULOS_IZQUIERDO;
		if(!$pre_impreso){
			$pdf->addText($x0, $y0, SIZE_LETRA_TITULOS, "Banco Deposito");//legajo.
		}
		$pdf->addText($x0, $y_datos, SIZE_LETRA_DATOS, "Falta Dato");//datos dni.

		$x0=0+MARGEN_HOJA_IZQUIERDO;
		$y0=$y0-MARGEN_TITULOS_INFERIOR;
		//$y0=$y1_linea_titulos;

		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (datos)
		}

		$y0=$y1_linea_titulos;

		if(!$pre_impreso){
			$pdf->line($x0, $y0, $xf, $y0);//linea horizontal (datos)
		}
*/


		$xd=$xd+DESPLAZAMIENTO_X;
		//reinicializo las coordenadas iniciales
		$y0=ALTO_RECIBO-MARGEN_HOJA_SUPERIOR;
		$x0=0+MARGEN_HOJA_IZQUIERDO;
	}
}

$pdf->ezStream();
/**
* Dibuja el cuerpo del recibo.
*/
//$pdf->line($x0, $y0, $xf, $yf);//linea vertical (inicio del recibo de sueldo desde la izquierda)
?>