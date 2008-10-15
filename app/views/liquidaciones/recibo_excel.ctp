<?php
	/**
	* Creo la planilla.
	*/
	$excelWriter->send("planilla-" . $tipoPlanilla . "-" . date("d_m_Y") . ".xls");
//	d("X");

	$worksheet =& $excelWriter->addWorksheet();
	

	/**
	* Inicializo las variables que controlan las filas y columnas.
	*/
	$fila=0;
	$col=0;


	$datos = $this->data;
	//d($datos);
	
	/**
	* Creo los formatos.
	*/

	$formato1 =& $excelWriter->addFormat(); // agrega un formato al libro.
	$formato1->setColor('black');	//color de letra.
	$formato1->setSize(10);	//tama�o de letra
	$formato1->setAlign('right');	//alineaci�n dentro de la celda
	$formato1->setLeft(1);	
	$formato1->setTop(1);
	$formato1->setRight(1);
	$formato1->setBottom(1);
	$formato1->setBorderColor('black');
	$formato1->setFgColor('white');

	$formato2 =& $excelWriter->addFormat(); // agrega un formato al libro.
	$formato2->setColor('black');	//color de letra.
	$formato2->setSize(10);	//tama�o de letra
	$formato2->setAlign('left');	//alineaci�n dentro de la celda
	$formato2->setTop(1);
	$formato2->setRight(1);
	$formato2->setLeft(1);
	$formato2->setBottom(1);

	$formato_monto =& $excelWriter->addFormat(); // agrega un formato al libro.
	$formato_monto->setColor('black');	//color de letra.
	$formato_monto->setSize(10);	//tama�o de letra
	$formato_monto->setAlign('left');	//alineaci�n dentro de la celda
	$formato_monto->setTop(1);
	$formato_monto->setRight(1);
	$formato_monto->setBottom(1);

	$formato_peso =& $excelWriter->addFormat(); // agrega un formato al libro.
	$formato_peso->setColor('black');	//color de letra.
	$formato_peso->setSize(10);	//tama�o de letra
	$formato_peso->setAlign('right');	//alineaci�n dentro de la celda
	$formato_peso->setTop(1);
	$formato_peso->setBottom(1);

	$formato_importe_total =& $excelWriter->addFormat(); // agrega un formato al libro.
	$formato_importe_total->setBold();
	$formato_importe_total->setColor('black');	//color de letra.
	$formato_importe_total->setSize(10);	//tama�o de letra
	$formato_importe_total->setAlign('left');	//alineaci�n dentro de la celda
	$formato_importe_total->setTop(1);
	$formato_importe_total->setRight(1);
	$formato_importe_total->setBottom(1);

	$formato_importe =& $excelWriter->addFormat(); // agrega un formato al libro.
	$formato_importe->setColor('black');	//color de letra.
	$formato_importe->setSize(10);	//tama�o de letra
	$formato_importe->setAlign('left');	//alineaci�n dentro de la celda
	$formato_importe->setTop(1);
	$formato_importe->setLeft(1);
	$formato_importe->setBottom(1);

	$formato_peso_total =& $excelWriter->addFormat(); // agrega un formato al libro.
	$formato_peso_total->setColor('black');	//color de letra.
	$formato_peso_total->setSize(10);	//tama�o de letra
	$formato_peso_total->setAlign('right');	//alineaci�n dentro de la celda
	$formato_peso_total->setTop(1);
	$formato_peso_total->setBottom(1);

	$formato3 =& $excelWriter->addFormat(); // agrega un formato al libro.
	$formato3->setColor('black');	//color de letra.
	$formato3->setSize(10);	//tama�o de letra
	$formato3->setAlign('left');	//alineaci�n dentro de la celda

	$datos =& $excelWriter->addFormat(); // agrega un formato al libro.
	$datos->setBold();
	$datos->setColor('black');	//color de letra.
	$datos->setSize(10);	//tama�o de letra
	$datos->setAlign('left');	//alineaci�n dentro de la celda
	$datos->setTop(1);
	$datos->setRight(1);
	$datos->setBottom(1);
	$datos->setLeft(1);

	$formatoblanco=& $excelWriter->addFormat(); // agrega un formato al libro.
	$formatoblanco->setTop(1);
	$formatoblanco->setBottom(1);
	$formatoblanco->setRight(1);
	$formatoblanco->setFgColor('white');

	$formatoblanco2=& $excelWriter->addFormat(); // agrega un formato al libro.
	$formatoblanco2->setLeft(1);	
	$formatoblanco2->setTop(1);
	$formatoblanco2->setRight(1);
	$formatoblanco2->setBottom(1);
	$formatoblanco2->setFgColor('white');

	$formatoblanco3=& $excelWriter->addFormat(); // agrega un formato al libro.
	$formatoblanco3->setLeft(1);	
	$formatoblanco3->setTop(1);
	$formatoblanco3->setBottom(1);
	$formatoblanco3->setFgColor('white');

	$firma =& $excelWriter->addFormat(); // agrega un formato al libro.
	$firma->setColor('black');	//color de letra.
	$firma->setSize(10);	//tama�o de letra
	$firma->setAlign('center');	//alineaci�n dentro de la celda
	$firma->setLeft(1);	
	$firma->setTop(1);
	$firma->setRight(1);
	$firma->setBottom(1);
	$firma->setFgColor('white');


	/**
	* Recorro el vector de datos generando los recibos de sueldo con los datos indicados.
	*/
	
	/**
	* setRow (integer $row, integer $height [, mixed $format=0]).Seteo el height de la primer fila y combino todas las columnas de esa  fila.
	*/
	$worksheet->setRow ($fila,65);

	
	
	/**
	* setColumn (integer $firstcol, integer $lastcol, float $width [, mixed $format=0 [, integer $hidden=0]]). 
	* Seteo el ancho de las columnas.
	*/
	$worksheet->setColumn($col,$col,18);
	$worksheet->setColumn($col+1,$col+1,30);
	$worksheet->setColumn($col+2,$col+2,15);
	$worksheet->setColumn($col+3,$col+3,25);
	$worksheet->setColumn($col+4,$col+4,40);

	/**
	* Escribo las lineas. M�todo write(fila,columna,texto,array_formato).
	* writeBlank (integer $row, integer $col, mixed $format).
	* Escribo las celdas que sean necesarias en blanco.
	* mergeCells (integer $first_row, integer $first_col, integer $last_row, integer $last_col).
	* Combino las celdas que sean necesarias.
	*/

	$worksheet->mergeCells($fila,$col,$fila,$col+3);

	$worksheet->writeBlank ($fila,$col,$formato1);
	$worksheet->writeBlank ($fila,$col+1,$formatoblanco);
	$worksheet->writeBlank ($fila,$col+2,$formatoblanco);
	$worksheet->writeBlank ($fila,$col+3,$formato1);
	
	$fila++;
	$worksheet->write($fila,$col,"Razon Social",$formato1);
	$worksheet->write($fila,$col+1,$datos["Liquidacion"]["empleador_nombre"],$formato2);
	$worksheet->writeBlank ($fila,$col+2,$formatoblanco);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco);
	$worksheet->mergeCells($fila,$col+1,$fila,$col+3);

	$fila++;
	$worksheet->write($fila,$col,"CUIT",$formato1);
	$worksheet->write($fila,$col+1,$datos["Liquidacion"]["empleador_cuit"],$formato2);
	$worksheet->writeBlank ($fila,$col+2,$formatoblanco);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco);
	$worksheet->mergeCells($fila,$col+1,$fila,$col+3);

	$fila++;
	$worksheet->writeBlank ($fila,$col,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco);
	$worksheet->mergeCells($fila,$col,$fila,$col+3);

	$fila++;
	$worksheet->write($fila,$col,"Nombre",$formato1);
	$worksheet->write($fila,$col+1,$datos["Liquidacion"]["trabajador_apellido"]." ".$datos["Liquidacion"]["trabajador_nombre"],$datos);
	$worksheet->write($fila,$col+2,"Periodo de Pago",$formato1);
	$worksheet->write($fila,$col+3,$datos["Liquidacion"]["periodo"]."-".$datos["Liquidacion"]["mes"]."-".$datos["Liquidacion"]["ano"],$datos);
	
	$fila++;
	$worksheet->write($fila,$col,"C.U.I.L",$formato1);
	$worksheet->write($fila,$col+1,$datos["Liquidacion"]["trabajador_cuil"],$formato2);
	$worksheet->writeBlank ($fila,$col+2,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco2);

	$fila++;
	$worksheet->writeBlank ($fila,$col,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco);
	$worksheet->mergeCells($fila,$col,$fila,$col+3);

	/**
	*	Muestro el detalle de importes, mostrandolos y combinando las celdas que sean necesarias.
	*/
	$fila=6;
	$cant_detalle=count ($datos["LiquidacionesDetalle"]);
	for ($i=0;$i<$cant_detalle;$i++){
			$worksheet->mergeCells($fila,$col,$fila,$col+2);
			$worksheet->write($fila,$col,$datos["LiquidacionesDetalle"][$i]["concepto_nombre"],$formato_importe);
			$worksheet->writeBlank ($fila,$col+1,$formatoblanco);
			$worksheet->write($fila,$col+2,"$",$formato_peso);
			$worksheet->write($fila,$col+3,$datos["LiquidacionesDetalle"][$i]["valor"],$formato_monto);
			$fila++;
	}

	/**
	* Escribo las lineas. M�todo write(fila,columna,texto,array_formato).
	* writeBlank (integer $row, integer $col, mixed $format).
	* Escribo las celdas que sean necesarias en blanco.
	* mergeCells (integer $first_row, integer $first_col, integer $last_row, integer $last_col).
	* Combino las celdas que sean necesarias.
	*/
	$worksheet->write($fila,$col,"Total",$datos);
	/**
	* writeFormula (integer $row, integer $col, string $formula [, mixed $format=0]).
	* Escribo la formula que calcula el total de importes.
	*/
	$worksheet->write($fila,$col+2,"$",$formato_peso_total);
	$worksheet->writeFormula ($fila,$col+3,"=SUM(D".($fila-$cant_detalle).":D".($fila).")",$formato_importe_total);
	$worksheet->writeBlank ($fila,$col+1,$formatoblanco3);
	$worksheet->mergeCells($fila,$col,$fila,$col+1);
	$fila++;

	$worksheet->write($fila,$col+3,"Son Pesos: Dos mil cuatrocientos cincuenta con 25 centavos",$formato1);
	$worksheet->writeBlank ($fila,$col,$formatoblanco3);
	$fila++;
	
	$worksheet->write($fila,$col,"Fecha de Pago: ".$datos["Liquidacion"]["fecha"]."",$formato2);
	$worksheet->writeBlank ($fila,$col+1,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+2,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco2);
	$worksheet->mergeCells($fila,$col,$fila+1,$col+1);
	$worksheet->mergeCells($fila,$col+2,$fila+1,$col+3);
	$fila++;

	$worksheet->writeBlank ($fila,$col,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+1,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+2,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco2);
	$fila++;
	
	$worksheet->write($fila,$col+2,"FIRMA",$firma);
	$worksheet->writeBlank ($fila,$col,$formatoblanco3);
	$worksheet->writeBlank ($fila,$col+1,$formatoblanco);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco);
	$worksheet->mergeCells($fila,$col,$fila+4,$col+1);
	$worksheet->mergeCells($fila,$col+2,$fila+4,$col+3);
	$fila++;

	$worksheet->writeBlank ($fila,$col,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+1,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+2,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco2);
	$fila++;

	$worksheet->writeBlank ($fila,$col,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+1,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+2,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco2);
	$fila++;

	$worksheet->writeBlank ($fila,$col,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+1,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+2,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco2);
	$fila++;

	$worksheet->writeBlank ($fila,$col,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+1,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+2,$formatoblanco2);
	$worksheet->writeBlank ($fila,$col+3,$formatoblanco2);

	/**
	* insertBitmap (integer $row, integer $col, string $bitmap [, integer $x=0 [, integer $y=0 [, integer $scale_x=1 [, integer  $scale_y=1]]]]). 
	* Inserto la imagen del encabezado del recibo.
	*/

	//$worksheet->insertBitmap (0,3,"logo.bmp", 0, 0, 1, 1);

	$worksheet->hideGridLines();



	/**
	* Seteo la primer hoja como activa y la mando al browser.
	*/
	$worksheet->activate();
	$excelWriter->close();

?>