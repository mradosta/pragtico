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
 

	/**
	* Creo la planilla.
	*/
	$excelWriter->send("planilla-" . date("d_m_Y") . ".xls");
	$worksheet =& $excelWriter->addWorksheet();

	$fila=0;
	$col=0;


	/**
	* Creo los formatos.
	*/
	$formato_titulo =& $excelWriter->addFormat();
	$formato_titulo->setBold();
	$formato_titulo->setColor('black');
	$formato_titulo->setSize(14);
	$formato_titulo->setAlign('center');
	$formato_titulo->setAlign('merge');
	$formato_titulo->setUnderline(1);
	$worksheet->write($fila,$col+2,"PLANILLA PARA LA CARGA MASIVA DE HORAS",$formato_titulo);

	$formato_datos =& $excelWriter->addFormat();
	$formato_datos->setBold();
	$formato_datos->setSize(10);
	$formato_datos->setColor('white');
	$formato_datos->setFgColor('red');
	$formato_datos->setAlign('center');
	$formato_datos->setAlign('merge');

	$formato_datos_centro =& $excelWriter->addFormat();
	$formato_datos_centro->setAlign('center');
	
	$formato_horas =& $excelWriter->addFormat();
	$formato_horas->setBold();
	$formato_horas->setSize(10);
	$formato_horas->setColor('white');
	$formato_horas->setFgColor('green');
	$formato_horas->setAlign('center');
	$formato_horas->setAlign('merge');
	$formato_horas->setTop(1);
	$formato_horas->setRight(1);
	$formato_horas->setBottom(1);
	$formato_horas->setLeft(1);

	$formato_horas_ajuste =& $excelWriter->addFormat();
	$formato_horas_ajuste->setBold();
	$formato_horas_ajuste->setSize(10);
	$formato_horas_ajuste->setColor('white');
	$formato_horas_ajuste->setFgColor('green');
	$formato_horas_ajuste->setAlign('center');
	$formato_horas_ajuste->setTop(1);
	$formato_horas_ajuste->setRight(1);
	$formato_horas_ajuste->setBottom(1);
	$formato_horas_ajuste->setLeft(1);

	$formato_total=& $excelWriter->addFormat();
	$formato_total->setBold();
	$formato_total->setSize(10);
	$formato_total->setColor('white');
	$formato_total->setFgColor('red');
	$formato_total->setAlign('center');

	$formato_observaciones=& $excelWriter->addFormat();
	$formato_observaciones->setBold();
	$formato_observaciones->setSize(10);
	$formato_observaciones->setColor('white');
	$formato_observaciones->setFgColor('green');
	$formato_observaciones->setAlign('center');

	$fila+=2;
	$worksheet->setColumn($col,$col,60);
	$worksheet->write($fila,$col,"EMPLEADOR",$formato_datos);
	$col++;
	$worksheet->setColumn($col,$col,60);
	$worksheet->write($fila,$col,"TRABAJADOR",$formato_datos);
	$col++;
	$worksheet->setColumn($col,$col,20);
	$worksheet->write($fila,$col,"INGRESO",$formato_datos);
	$col++;
	$worksheet->setColumn($col,$col,30);
	$worksheet->write($fila,$col,"CATEGORIA",$formato_datos);
	$col++;
	$worksheet->setColumn($col,$col,20);
	$worksheet->write($fila,$col,"BAJA",$formato_datos);
	$col++;
	$worksheet->setColumn($col,$col,20);
	$worksheet->write($fila,$col,"PERIODO",$formato_horas);
	$col++;
	$worksheet->write($fila,$col,"HORAS",$formato_horas);
	$worksheet->mergeCells($fila, $col, $fila, ($col+2));
	$col+=3;
	$worksheet->write($fila,$col,"HORAS AJUSTE",$formato_horas_ajuste);
	$worksheet->mergeCells($fila, $col, $fila, ($col+2));
	$col+=3;
	$worksheet->setColumn($col,$col,16);
	$worksheet->write($fila,$col,"TOTAL",$formato_total);
	$col++;
	$worksheet->setColumn($col,$col,120);
	$worksheet->write($fila,$col,"OBSERVACIONES",$formato_observaciones);


	$col=0;
	$fila++;
	$worksheet->setColumn($col,$col,60);
	$worksheet->write($fila,$col,"",$formato_datos);
	$col++;
	$worksheet->setColumn($col,$col,60);
	$worksheet->write($fila,$col,"",$formato_datos);
	$col++;
	$worksheet->setColumn($col,$col,20);
	$worksheet->write($fila,$col,"",$formato_datos);
	$col++;
	$worksheet->setColumn($col,$col,30);
	$worksheet->write($fila,$col,"",$formato_datos);
	$col++;
	$worksheet->setColumn($col,$col,20);
	$worksheet->write($fila,$col,"",$formato_datos);
	$col++;
	$worksheet->setColumn($col,$col,20);
	$worksheet->write($fila,$col,"",$formato_horas);
	$col++;
	$worksheet->setColumn($col,$col,16);
	$worksheet->write($fila,$col,"NORMALES",$formato_horas);
	$col++;
	$worksheet->setColumn($col,$col,16);
	$worksheet->write($fila,$col,"Al 50%",$formato_horas);
	$col++;
	$worksheet->setColumn($col,$col,16);
	$worksheet->write($fila,$col,"Al 100%",$formato_horas);
	$col++;
	$worksheet->setColumn($col,$col,16);
	$worksheet->write($fila,$col,"NORMALES",$formato_horas_ajuste);
	$col++;
	$worksheet->setColumn($col,$col,16);
	$worksheet->write($fila,$col,"Al 50%",$formato_horas_ajuste);
	$col++;
	$worksheet->setColumn($col,$col,16);
	$worksheet->write($fila,$col,"Al 100%",$formato_horas_ajuste);
	$col++;
	$worksheet->setColumn($col,$col,16);
	$worksheet->write($fila,$col,"",$formato_total);
	$col++;
	$worksheet->setColumn($col,$col,16);
	$worksheet->write($fila,$col,"",$formato_observaciones);

	foreach($datos as $dato) {
		$fila++;
		$col=0;
		$worksheet->setColumn($col, $col, 60);
		$worksheet->write($fila,$col,$dato['Relacion']['id'] . " || " . $dato['Empleador']['cuit']." - ".$dato['Empleador']['nombre']);
		$col++;
		$worksheet->setColumn($col, $col, 60);
		$worksheet->write($fila,$col,$dato['Trabajador']['cuil'] . " - " . $dato['Trabajador']['apellido'] . " " . $dato['Trabajador']['nombre']);
		$col++;
		$worksheet->setColumn($col, $col, 20);
		$worksheet->write($fila,$col,$formato->format($dato['Relacion']['ingreso'], "db2helper"), $formato_datos_centro);
		$col++;
		$worksheet->setColumn($col, $col, 30);
		$worksheet->write($fila,$col,$dato['ConveniosCategoria']['nombre']);
		$col++;
		$worksheet->setColumn($col, $col, 20);
		$worksheet->write($fila,$col,str_replace("&nbsp;", "", $formato->format($dato['Relacion']['egreso'], "db2helper")), $formato_datos_centro);
		$col+=8;
		/**
		* Agrego la formula que me da el total.
		*/
		$worksheet->writeFormula($fila,$col,"=SUM(B".($fila+1).":L".($fila+1).")");
	}

	/**
	* Seteo la primer hoja como activa y la mando al browser.
	*/
	$worksheet->activate();
	$excelWriter->close();

?>