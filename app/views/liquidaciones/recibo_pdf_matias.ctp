<?
define("SIZE_LETRA_TITULOS",  5);
define("SIZE_LETRA_DATOS",  7);
define("ALTURA_CELDA_TITULOS",  15);

$datos[] = $this->data;
//d(APP . "vendors" . DS . "pdf-php" . DS . "fonts");
$pdf->selectFont(APP . "vendors" . DS . "pdf-php" . DS . "fonts" . DS . "Helvetica.afm");
//$pdf->ezText('Hello World!',50);

/**
* Bandera que indica si se pintan solo datos (papel preimpreso) o se pinta la estrcutura y los datos.
*/
$preimpr=1;

/**
Se crea una instancia de la clase ezpdf.
*/
$pdf->Cezpdf("a4", "landscape");

/**
* Se evalua el valor de la bandera para ver si se tiene que pintar el dise�o y datos � solo datos
*/
if ($preimpr==1){
/**
* Se crea un objeto a fin de que se repita lo mismo en todas las hojas del documento pdf.
*/
$id=$pdf->openObject();

/**
* Se pinta el dise�o de los recibos de sueldo por hoja del documento (el duplicado y el original).
*/

/**
* Se setean las coordenadas con los valores iniciales.
*/
$x0=10;
$y0=10;
$xf=10;
$yf=590;

for ($i=0;$i<2;$i++){
		/**
		* Dibuja el cuerpo del recibo.
		*/
		$pdf->line($x0, $y0, $xf, $yf);//linea vertical (inicio del recibo de sueldo desde la izquierda)
		$pdf->line($x0+408, $y0, $xf+408, $yf);//linea vertical (fin del recibo de sueldo desde la izquierda)
		$pdf->line($x0, $yf, $xf+408, $yf);//linea horizontal de arriba del recibo de sueldo.
		$pdf->line($x0, $y0, $xf+408, $y0);//linea horizontal de abajo del recibo de sueldo.

		/**
		* Dibuja el encabezado del recibo.
		*/
		$pdf->line($x0, $y0+520, $xf+408, $y0+520);//linea horizontal (delimita el encabezado del recibo)
		$pdf->addText($x0+240, $y0+555, 10, "25 de Mayo 125 1� Piso");//Direcci�n en encabezado del recibo.
		$pdf->addText($x0+240, $y0+545, 10, "5000 - C�rdoba");//C�digo Postal en encabezado del recibo.
		$pdf->addText($x0+240, $y0+535, 10, "Tel/Fax: (0351) 4231575/ 4250365");//Telefono en encabezado del recibo.
		$pdf->addText($x0+240, $y0+525, 10, "C.U.I.T.: 30-59083010-7");//CUIT en encabezado del recibo.

		$pdf->line($x0, $y0+495, $xf+408, $y0+495);//linea horizontal (delimita el usuario)
		/**
		* Dibuja la estructura de Periodo Abonado,  Fecha de pago... Legajo. 
		*/	
		$pdf->line($x0, $y0+480, $xf+408, $y0+480);//linea horizontal (Fila del Periodo abonado, ...,  legajo)
		$pdf->addText($x0+5,  $y0+485,  SIZE_LETRA_TITULOS,  "Periodo Abonado");
		$pdf->addText($x0+110,  $y0+485,  SIZE_LETRA_TITULOS,  "Fecha de Pago");
		$pdf->addText($x0+200,  $y0+485,  SIZE_LETRA_TITULOS,  "Apellido y Nombre");
		$pdf->addText($x0+300,  $y0+485,  SIZE_LETRA_TITULOS,  "D.N.I");
		$pdf->addText($x0+370,  $y0+485,  SIZE_LETRA_TITULOS,  "Legajo");
		$pdf->line($x0+85, $y0+495, $xf+85, $yf-115);//linea vertical separadora de periodo y fecha de pago.
		$pdf->line($x0+165, $y0+495, $xf+165, $yf-115);//linea vertical separadora de fecha de pago y apellido.
		$pdf->line($x0+290, $y0+495, $xf+290, $yf-115);//linea vertical separadora de apellido y DNI.
		$pdf->line($x0+345, $y0+495, $xf+345, $yf-115);//linea vertical separadora de DNI y legajo.
		$pdf->line($x0, $y0+480-ALTURA_CELDA_TITULOS, $xf+408, $y0+480-ALTURA_CELDA_TITULOS);//linea horizontal (Datos del Periodo abonado, ...,  legajo)
		
		/**
		* Dibuja la estructura de CUIL,  Fecha de Ingreso....,  Banco Deposito.
		*/	
		$pdf->line($x0, $y0+420, $xf+408, $y0+420);//linea horizontal (Fila del CUIL, ...,  Banco deposito)
		$pdf->addText($x0+35, $y0+455, SIZE_LETRA_TITULOS, "C.U.I.L");
		$pdf->addText($x0+105, $y0+455, SIZE_LETRA_TITULOS, "Fecha de Ingreso");
		$pdf->addText($x0+175, $y0+455, SIZE_LETRA_TITULOS, "Antig.");
		$pdf->addText($x0+245, $y0+455, SIZE_LETRA_TITULOS, "Categor�a");
		$pdf->addText($x0+335, $y0+455, SIZE_LETRA_TITULOS, "Banco Dep�sito");
		$pdf->line($x0+90, $y0+465, $xf+90, $yf-145);//linea vertical separadora de CUIL y fecha de ingreso.
		$pdf->line($x0+165, $y0+465, $xf+165, $yf-145);//linea vertical separadora de fecha de ingreso y Antig.
		$pdf->line($x0+200, $y0+465, $xf+200, $yf-145);//linea vertical separadora de Antig y Categoria.
		$pdf->line($x0+300, $y0+465, $xf+300, $yf-145);//linea vertical separadora de Categoria y Banco deposito.
		$pdf->line($x0, $y0+435+ALTURA_CELDA_TITULOS, $xf+408, $y0+435+ALTURA_CELDA_TITULOS);//linea horizontal (Datos del CUIL, ...,  Banco deposito)
		//$pdf->line($x0, $y0+435, $xf+408, $y0+435);//linea horizontal (Datos del CUIL, ...,  Banco deposito)
		
		/**
		* Dibuja la estructura del detalle de recibo.
		*/
		
		//$pdf->line($x0, $y0+435, $xf+408, $y0+435);//linea horizontal (Fila de detalle)
		$pdf->line($x0, $y0+420+ALTURA_CELDA_TITULOS, $xf+408, $y0+420+ALTURA_CELDA_TITULOS);//linea horizontal (Fila de detalle)
		$pdf->addText($x0+15, $y0+425, SIZE_LETRA_TITULOS, "Cantidad / Concepto");
		$pdf->addText($x0+250, $y0+425, SIZE_LETRA_TITULOS, "Valor Unitario");
		$pdf->addText($x0+310, $y0+425, SIZE_LETRA_TITULOS, "Haberes");
		$pdf->addText($x0+370, $y0+425, SIZE_LETRA_TITULOS, "Deducciones");
		$pdf->line($x0+40, $y0+420, $xf+40, $yf-395);//linea vertical separadora de Cantidad/Concepto.
		$pdf->line($x0+235, $y0+435, $xf+235, $yf-395);//linea vertical separadora de Concepto y Valor Unitario.
		$pdf->line($x0+295, $y0+435, $xf+295, $yf-395);//linea vertical separadora de Valor Unitario y Haberes.
		$pdf->line($x0+355, $y0+435, $xf+355, $yf-395);//linea vertical separadora de Haberes y Deducciones.
		$pdf->line($x0, $y0+185, $xf+408, $y0+185);//linea horizontal (Datos de detalle)
		
		/**
		* Dibuja la estructura de No/Sujeto a deducciones,  Totales.
		*/	
		$pdf->line($x0, $y0+170, $xf+408, $y0+170);//LINEA HORIZONTAL (deducciones).
		$pdf->addText($x0+175, $y0+175, SIZE_LETRA_TITULOS, "No suj. a deducci�n");
		$pdf->addText($x0+240, $y0+175, SIZE_LETRA_TITULOS, "Sujeto a deducci�n");
		$pdf->addText($x0+310, $y0+175, SIZE_LETRA_TITULOS, "Total Haberes");
		$pdf->addText($x0+360, $y0+175, SIZE_LETRA_TITULOS, "Total Deducciones");
		$pdf->line($x0+355, $y0+155, $xf+355, $yf-395);//linea vertical separadora de Total haberes/total deducciones.
		$pdf->line($x0+295, $y0+155, $xf+295, $yf-395);//linea vertical separadora de Sujeto a deduciion y Total haberes.
		$pdf->line($x0+225, $y0+155, $xf+225, $yf-395);//linea vertical separadora de No suj deduc y Sujeto a deduciion.
		$pdf->line($x0+170, $y0+155, $xf+170, $yf-395);//linea vertical separadora de No suj deduccion.
		$pdf->line($x0, $y0+155, $xf+408, $y0+155);//LINEA HORIZONTAL (datos deducciones).
		
		/**
		* Dibuja la estructura del Neto a Cobrar.
		*/
		$pdf->line($x0+295, $y0+140, $xf+408, $y0+140);//LINEA HORIZONTAL (neto a cobrar).
		$pdf->addText($x0+300, $y0+145, SIZE_LETRA_TITULOS, "Neto a Cobrar");
		$pdf->line($x0+295, $y0+140, $xf+295, $yf-425);//linea vertical separadora separadora de Neto a Cobrar.
		$pdf->line($x0+355, $y0+140, $xf+355, $yf-395);//linea vertical separadora de Neto a Cobrar y el valor.
		
		/**
		* Dibuja la estructura donde se muestra el neto a cobrar en letras.
		*/
		$pdf->addText($x0+5, $y0+115, SIZE_LETRA_TITULOS, "Son:");//Muestra la cantidad a cobrar en letras.
		
		/**
		* Dibuja la estructura del Ultimo Deposito Aporte Jubilatorio.
		*/
		$pdf->addText($x0+260, $y0+115, SIZE_LETRA_TITULOS, "Ultimo Dep�sito Aporte Jubilatorio");//Titulo: Aporte Jubilatorio
		
		$pdf->line($x0, $y0+112, $xf+408, $y0+112);//LINEA HORIZONTAL, inicio de fila de Periodo,  fecha,  banco.
		$pdf->addText($x0+5, $y0+102, SIZE_LETRA_TITULOS, "Antiguedad Reconocida");
		$pdf->addText($x0+250, $y0+102, SIZE_LETRA_TITULOS, "Periodo");
		$pdf->addText($x0+310, $y0+102, SIZE_LETRA_TITULOS, "Fecha");
		$pdf->addText($x0+360, $y0+102, SIZE_LETRA_TITULOS, "Banco");
		$pdf->line($x0+240, $y0+80, $xf+240, $yf-468);//linea vertical separadora de Antig. Reconocida y Periodo.
		$pdf->line($x0+300, $y0+80, $xf+300, $yf-468);//linea vertical separadora de Periodo y Fecha.
		$pdf->line($x0+345, $y0+80, $xf+345, $yf-468);//linea vertical separadora de Fecha y Banco.
		$pdf->line($x0, $y0+100, $xf+408, $y0+100);//LINEA HORIZONTAL, fin de fila de Periodo,  fecha,  banco.

		/**
		* Dibuja el lugar de Pago y la Fecha.
		*/
		$pdf->line($x0, $y0+80, $xf+408, $y0+80);//LINEA HORIZONTAL (inicio delimita Lugar y Fecha).
		$pdf->line($x0+260, $y0+40, $xf+408, $y0+40);//LINEA HORIZONTAL (fin delimita Lugar y Fecha).
		$pdf->line($x0+260, $y0+40, $xf+260, $yf-500);//linea vertical (limita lugar y Fecha).
		$pdf->addText($x0+230, $y0+70, SIZE_LETRA_TITULOS, "Lugar:");
		$pdf->addText($x0+230, $y0+50, SIZE_LETRA_TITULOS, "Fecha:");

		//$pdf->setLineStyle(1, '', '', array(22));
		$pdf->line($x0+10, $y0+20, $xf+130, $y0+20);//LINEA HORIZONTAL (Firma/Empleado).
		//$pdf->setLineStyle(1);

		/**
		* Se fija si corresponde al recibo duplicado (firma el empleado) � al original (firma el empleador).
		*/
		if ($i!=1){
			$firma="Firma/Empleado";
		}
		else{
			$firma="Firma/Empleador";
		}
		$pdf->addText($x0+30, $y0+10, 10, $firma);//Muestra el campo de Firma

		/**
		* Se actualiza el valor de las coordenadas.
		*/
		$x0=10+412;
		$y0=10;
		$xf=10+412;
		$yf=590;
}

$pdf->closeObject ();

$pdf->addObject ($id, "all");
}


/**
* Se pintan los datos correspondientes a los recibos de sueldo.
*/

/**
* Se setean las coordenadas con los valores iniciales.
*/
$x=20;
$y=590;
/**
* Por cada recibo de sueldo muestro sus datos correspondientes,  creo una nueva p�gina y reseteo los valores de coordenadas.
*/
$cant_recibos=count ($datos);
for ($j=0;$j<$cant_recibos;$j++){

	for ($i=0;$i<2;$i++){
		$pdf->addText($x, 515, 10, "USUARIO: ".$datos[$j]["Liquidacion"]["user_id"]." - ".$datos[$j]["Liquidacion"]["empleador_nombre"]."");//Muestar el codido de usuario y su nombre.
		
		/**
		* Muestra los datos de: Periodo abonado,  Fecha de pago,  Apellido y Nombre,  DNI,  Legajo.
		*/
		$pdf->addText($x-5, 480, SIZE_LETRA_DATOS, $datos[$j]["Liquidacion"]["periodo"]);//Muestra el Periodo Abonado.
		$pdf->addText($x+105, 480, SIZE_LETRA_DATOS, $datos[$j]["Liquidacion"]["fecha"]);//Muestra la Fecha de pago.
		$pdf->addText($x+175, 480, SIZE_LETRA_DATOS, $datos[$j]["Liquidacion"]["trabajador_apellido"]." ".$datos[$j]["Liquidacion"]["trabajador_nombre"]);//Muestra el Nombre y Apellido.
		$pdf->addText($x+290, 480, SIZE_LETRA_DATOS, "Falta Dato");//Muestra el DNI del trabajador.
		$pdf->addText($x+350, 480, SIZE_LETRA_DATOS, "Falta Dato");//Muestra el legajo del trabajador.

		/**
		* Muestra los datos de: CUIL,  Fecha de Ingreso,  Antig,  Categoria,  Banco Deposito.
		*/
		$pdf->addText($x, 450, SIZE_LETRA_DATOS, $datos[$j]["Liquidacion"]["trabajador_cuil"]);//Muestra el CUIL del trabajador.
		$pdf->addText($x+95, 450, SIZE_LETRA_DATOS, $datos[$j]["Liquidacion"]["relacion_ingreso"]);//Muestra la fecha de ingreso del trabajador.
		$pdf->addText($x+170, 450, SIZE_LETRA_DATOS, "FD");//Muestra la Antig del trabajador.
		$pdf->addText($x+195, 450, SIZE_LETRA_DATOS, $datos[$j]["Liquidacion"]["convenio_categoria_nombre"]);//Muestra la Categoria del trabajador.
		$pdf->addText($x+300, 450, SIZE_LETRA_DATOS, "Falta de Dato");//Muestra el bco de dep�sito.

		/**
		* Muestra los datos de Deduccion y Totales y el Neto a Cobrar.
		*/
		$pdf->addText($x+355, 155, SIZE_LETRA_DATOS, "Falta de Dato");// Muestra el Neto a Cobrar.
		$pdf->addText($x+355, 170, SIZE_LETRA_DATOS, "Falta de Dato");//Muestra el Total de Deducciones.
		$pdf->addText($x+300, 170, SIZE_LETRA_DATOS, "Falta de Dato");//Muestra el Total de Haberes.
		$pdf->addText($x+220, 170, SIZE_LETRA_DATOS, "Falta de Dato");//Muestra el valor Sujeto a Deduccion.
		$pdf->addText($x+165, 170, SIZE_LETRA_DATOS, "Falta de Dato");//Muestra el valor no sujeto a deduccion.

		/**
		* Muestar en letra el neto a cobrar.
		*/
		$pdf->addText($x+40, 125, SIZE_LETRA_DATOS, "Falta de Dato");//Muestra la cantidad a cobrar en letras.

		/**
		* Muestra los datos del ultimo aporte jubilatorio.
		*/
		$pdf->addText($x+235, 98, SIZE_LETRA_DATOS, "Falta de Dato");//Muestra el Periodo.
		$pdf->addText($x+295, 98, SIZE_LETRA_DATOS, "fd/fd/fdfd");//Muestra la Fecha.
		$pdf->addText($x+340, 98, SIZE_LETRA_DATOS, "Falta de Dato");//Muestra el Banco.

		/**
		* Muestra el lugar y la fecha de pago.
		*/
		$pdf->ezSetDy(-82);
		//$pdf->ezSetDx($x+300);
		$pdf->ezText("<b>FD</b>");
		$pdf->ezSetDy(-80);
		$pdf->ezText("<b>10/10/2008</b>");
		//$pdf->addText($x+300, 80, SIZE_LETRA_DATOS, "Falta de Dato");//Muestra el Lugar.
		//$pdf->addText($x+300, 60, SIZE_LETRA_DATOS, "fd/fd/fdfd");//Muestra la Fecha.

		/**
		* Muestra el Detalle del recibo.
		*/
		$y=420;
		$cant_detalles=count ($datos[0]["LiquidacionesDetalle"]);
		for ($k=0;$k<$cant_detalles;$k++){
			//addTextWrap(x,y,width,size,text,[justification='left'][,angle=0])
			$pdf->addText($x, $y, SIZE_LETRA_DATOS, $datos[0]["LiquidacionesDetalle"][$k]["valor_cantidad"]);//Muestra la cantidad del concepto.
			$pdf->addText($x+60, $y, SIZE_LETRA_DATOS, $datos[0]["LiquidacionesDetalle"][$k]["concepto_nombre"]);//Muestra el nombre del concepto.
			$pdf->addText($x+240, $y, SIZE_LETRA_DATOS, $datos[0]["LiquidacionesDetalle"][$k]["valor"]);//Muestra el valor unitario.
			$pdf->addText($x+300, $y, SIZE_LETRA_DATOS, "Falta Dato");//Muestra los haberes.
			$pdf->addText($x+355, $y, SIZE_LETRA_DATOS, "Falta Dato");//Muestra las deducciones.
			$y=$y-10;
		}

		$x=$x+412;
		//$y=;
	}
	//if ($j!=0)$pdf->ezNewPage();
	$x=20;
	//$y=590;
}

/**
Muestra el documento pdf.
*/
$pdf->ezStream();


//d($pdf->messages);
?>