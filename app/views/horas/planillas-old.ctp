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
	$excelWriter->send("planilla-" . $tipoPlanilla . "-" . date("d_m_Y") . ".xls");
	$worksheet =& $excelWriter->addWorksheet();
	
	$fila=0;
	$col=0;


	/**
	* Creo los formatos.
	*/
	$format_title =& $excelWriter->addFormat();
	$format_title->setBold();
	$format_title->setColor('black');
	$format_title->setSize(14);
	$format_title->setAlign('center');
	$format_title->setAlign('merge');
	$format_title->setUnderline(1);
	$worksheet->write($fila,$col+2,"PLANILLA PARA LA CARGA MASIVA DE HORAS",$format_title);

	$format_titulo =& $excelWriter->addFormat();
	$format_titulo->setBold();
	$format_titulo->setSize(10);
	$format_titulo->setColor('white');
	$format_titulo->setFgColor('blue');
	$format_titulo->setAlign('center');
	$format_titulo->setAlign('merge');

	$format_titulo_abajo =& $excelWriter->addFormat();
	$format_titulo_abajo->setBold();
	$format_titulo_abajo->setSize(8);
	$format_titulo_abajo->setColor('white');
	$format_titulo_abajo->setFgColor('blue');
	$format_titulo_abajo->setAlign('center');

	$fila=$fila+2;
	$worksheet->setColumn($col,$col,40);
	$worksheet->write($fila,$col,"EMPLEADOR",$format_titulo);
	$col++;
	$worksheet->setColumn($col,$col,40);
	$worksheet->write($fila,$col,"TRABAJADOR",$format_titulo);
	if ($tipoPlanilla=="resumida") {
		$col++;
		$worksheet->write($fila,$col,"HORAS",$format_titulo);
		$worksheet->mergeCells($fila, 2, $fila, 7);
	}

	$col=0;
	$fila++;
	$worksheet->setColumn($col,$col,40);
	$worksheet->write($fila,$col,"",$format_titulo);
	$col++;
	$worksheet->setColumn($col,$col,40);
	$worksheet->write($fila,$col,"",$format_titulo);

	/**
	* Dependiendo del tipo de planilla, seran los datos que pongo.
	*/
	if ($tipoPlanilla=="extendida") {

		$col++;

		for ($i=1;$i<=31;$i++) {
			$worksheet->write(($fila-1),$col++,"DIA " . $i,$format_titulo);
			$worksheet->write(($fila-1),$col++,"",$format_titulo);
			$worksheet->write(($fila-1),$col++,"",$format_titulo);
			$worksheet->write(($fila-1),$col++,"",$format_titulo);
			$worksheet->write($fila,$col-4,"NOR.",$format_titulo_abajo);
			$worksheet->write($fila,$col-3,"50%",$format_titulo_abajo);
			$worksheet->write($fila,$col-2,"100%",$format_titulo_abajo);
			$worksheet->write($fila,$col-1,"ENF.",$format_titulo_abajo);
		}
		$worksheet->write(($fila-1),$col++,"TOTAL",$format_titulo);
		$worksheet->write($fila,($col-1),"",$format_titulo);
		$columnaTotales = $col - 1;
	}
	elseif ($tipoPlanilla=="resumida") {
		$col++;
		$worksheet->setColumn($col,$col,16);
		$worksheet->write($fila,$col,"NORMALES",$format_titulo);
		$col++;	
		$worksheet->setColumn($col,$col,16);
		$worksheet->write($fila,$col,"Al 50%",$format_titulo);
		$col++;	
		$worksheet->setColumn($col,$col,16);
		$worksheet->write($fila,$col,"Al 100%",$format_titulo);
		$col++;	
		$worksheet->setColumn($col,$col,16);
		$worksheet->write($fila,$col,"ENFERMEDAD",$format_titulo);
		$col++;	
		$worksheet->setColumn($col,$col,16);
		$worksheet->write($fila,$col,"PRESENTISMO",$format_titulo);
		$col++;	
		$worksheet->setColumn($col,$col,16);
		$worksheet->write($fila,$col,"TOTAL / PER",$format_titulo);
		$columnaTotales = $col;
		
	}

	foreach($datos as $dato) {
		$fila++;
		$col=0;
		$worksheet->setColumn($col, $col, 40);
		$worksheet->write($fila,$col,$dato['Relacion']['id'] . " || " . $dato['Empleador']['cuit']." - ".$dato['Empleador']['nombre']);
		$col++;
		$worksheet->setColumn($col, $col, 40);
		$worksheet->write($fila,$col,$dato['Trabajador']['cuil'] . " - " . $dato['Trabajador']['apellido'] . " " . $dato['Trabajador']['nombre']);

		if ($tipoPlanilla=="extendida") {
			$worksheet->writeFormula($fila,$columnaTotales,"=SUM(B".($fila+1).":DV".($fila+1).")");
		}
		elseif ($tipoPlanilla=="resumida") {
		$worksheet->writeFormula($fila,7,"=SUM(B".($fila+1).":F".($fila+1).")");
		}
	}

	/**
	* Seteo la primer hoja como activa y la mando al browser.
	*/
	$worksheet->activate();
	$excelWriter->close();

?>