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
 
if(!empty($registros)) {
	$documento->create();
	$fila = $filaInicio = 8;

	/**
	* Oculto la columna donde tengo los identificadores de la relacion.
	*/
	$documento->doc->getActiveSheet()->getColumnDimension('A')->setVisible(false);

	/**
	* Pongo el titulo de la planilla.
	*/
	$documento->setCellValue("E1:M3", "Novedades - " . date("d/m/Y"),
		array("style"=>array("font"		=> array("bold" => true, "size" => 14),
							"alignment" => array("horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))
			));
	
	/**
	* Agrego el logo de Pragtico.
	*/
	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setName('Pragtico');
	$objDrawing->setDescription('Pragtico');
	$objDrawing->setPath(WWW_ROOT . "img/logo_pragtico.jpg");
	$objDrawing->setCoordinates('B1');
	$objDrawing->setHeight(130);
	$objDrawing->setWidth(260);
	$objDrawing->getShadow()->setVisible(true);
	$objDrawing->setWorksheet($documento->doc->getActiveSheet());
	
	/**
	* Pongo las columnas en auto ajuste del ancho.
	*/
	$documento->doc->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$documento->doc->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$documento->doc->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	
	/**
	* Pongo los titulos de las columnas.
	*/
	$estiloTituloColumna =
		array(
			'font'    => array(
				'bold'      => true
			),
			'alignment' => array(
				'vertical' 	 => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'fill' => array(
				'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation'   => 90,
				'startcolor' => array(
					'argb' => 'FFA0A0A0'
				),
				'endcolor'   => array(
					'argb' => 'FFFFFFFF'
				)
			)
		);

	$documento->setCellValue("A" . $fila . ":A" . ($fila+1), "Relacion");
	$documento->setCellValue("B" . $fila . ":B" . ($fila+1), "Empleador", array("style"=>$estiloTituloColumna));
	$documento->setCellValue("C" . $fila . ":C" . ($fila+1), "Trabajador", array("style"=>$estiloTituloColumna));
	$documento->setCellValue("D" . $fila . ":D" . ($fila+1), "Categoria", array("style"=>$estiloTituloColumna));

	$documento->setCellValue("E" . $fila . ":G" . $fila, "Horas", array("style"=>$estiloTituloColumna));
	$documento->doc->getActiveSheet()->getColumnDimension('E')->setWidth(6);
	$documento->setCellValue("E" . ($fila+1), "Normal", array("style"=>$estiloTituloColumna));
	$documento->doc->getActiveSheet()->getColumnDimension('F')->setWidth(6);
	$documento->setCellValue("F" . ($fila+1), "50%", array("style"=>$estiloTituloColumna));
	$documento->doc->getActiveSheet()->getColumnDimension('G')->setWidth(6);
	$documento->setCellValue("G" . ($fila+1), "100%", array("style"=>$estiloTituloColumna));
	
	$documento->setCellValue("H" . $fila . ":J" . $fila, "Horas Ajuste", array("style"=>$estiloTituloColumna));
	$documento->doc->getActiveSheet()->getColumnDimension('H')->setWidth(6);
	$documento->setCellValue("H" . ($fila+1), "Normal", array("style"=>$estiloTituloColumna));
	$documento->doc->getActiveSheet()->getColumnDimension('I')->setWidth(6);
	$documento->setCellValue("I" . ($fila+1), "50%", array("style"=>$estiloTituloColumna));
	$documento->doc->getActiveSheet()->getColumnDimension('J')->setWidth(6);
	$documento->setCellValue("J" . ($fila+1), "100%", array("style"=>$estiloTituloColumna));
	
	$documento->setCellValue("K" . $fila . ":L" . $fila, "Ausencias", array("style"=>$estiloTituloColumna));
	$documento->doc->getActiveSheet()->getColumnDimension('K')->setWidth(15);
	$documento->setCellValue("K" . ($fila+1), "Motivo", array("style"=>$estiloTituloColumna));
	$documento->doc->getActiveSheet()->getColumnDimension('L')->setWidth(6);
	$documento->setCellValue("L" . ($fila+1), "Dias", array("style"=>$estiloTituloColumna));

	$documento->setCellValue("M" . $fila . ":M" . $fila, "Vales", array("style"=>$estiloTituloColumna));
	$documento->setCellValue("M" . ($fila+1) . ":M" . ($fila+1), "$", array("style"=>$estiloTituloColumna));
	$documento->doc->getActiveSheet()->getColumnDimension('M')->setWidth(9);

	/**
	* Protejo la hoja para que no me la modifiquen, excepto lo que realmente necesito que modifique que lo desbloqueo luego.
	*/
	$documento->doc->getActiveSheet()->getProtection()->setPassword(substr(Configure::read('Security.salt'), 0, 10));
	$documento->doc->getActiveSheet()->getProtection()->setSheet(true);

	$fila++;
	foreach($registros as $registro) {
		$fila++;
		$documento->setCellValue("A" . $fila, $registro['Relacion']['id']);
		$documento->setCellValue("B" . $fila, $registro['Empleador']['nombre']);
		$documento->setCellValue("C" . $fila, $registro['Trabajador']['nombre']);
		$documento->setCellValue("D" . $fila, $registro['ConveniosCategoria']['nombre']);


		foreach(str_split("EFGHIJKLM") as $col) {
			$documento->setDataValidation($col . $fila, "decimal");
			
			/**
			* Debo especificamente desbloquear las celdas que le permitire introducir al usuario.
			*/
			$documento->doc->getActiveSheet()->getStyle($col . $fila)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
		}
		
		/**
		* El combo con los posibles motivos.
		*/
		$documento->setDataValidation("K" . $fila, "lista", array("valores"=>$motivos));
		
		$objValidation = $documento->doc->getActiveSheet()->getCell("K" . $fila)->getDataValidation();
		$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
		$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
		$objValidation->setAllowBlank(false);
		$objValidation->setShowInputMessage(true);
		$objValidation->setShowErrorMessage(true);
		$objValidation->setShowDropDown(true);
		$objValidation->setError("Debe seleccionar un valor de la lista");
		$objValidation->setFormula1('"' . implode(",", $motivos) . '"');
		$documento->doc->getActiveSheet()->getCell("K" . $fila)->setDataValidation($objValidation);
	}
	$documento->save($formatoDocumento);
}
else {
	/**
	* Especifico los campos para ingresar las condiciones.
	*/
	$condiciones['Condicion.Relacion-trabajador_id'] = array(	"lov"=>array("controller"		=>	"trabajadores",
																			"separadorRetorno"	=>	" ",
																			"camposRetorno"		=>array("Trabajador.apellido",
																										"Trabajador.nombre")));

	$condiciones['Condicion.Relacion-empleador_id'] = array(	"lov"=>array("controller"	=> "empleadores",
																			"camposRetorno"	=> array("Empleador.nombre")));

	$condiciones['Condicion.Relacion-id'] = array(	"label"	=> "Relacion",
													"lov"	=> array(	"controller"	=> "relaciones",
																		"camposRetorno"	=> array(	"Empleador.nombre",
																									"Trabajador.apellido")));
	$condiciones['Condicion.Novedad-tipo'] = array("type"=>"checkboxMultiple");
	$condiciones['Condicion.Novedad-formato'] = array("type"=>"radio");
	$fieldsets[] = array("campos"=>$condiciones);
	
	$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Novedades", "imagen"=>"novedades.gif")));
	$opcionesTabla['tabla']['omitirMensajeVacio'] = true;
	$accionesExtra['opciones'] = array("acciones"=>array($formulario->link("Generar", null, array("class"=>"link_boton", "id"=>"confirmar", "title"=>"Confirma las liquidaciones seleccionadas"))));
	$botonesExtra['opciones'] = array("botones"=>array("limpiar", $formulario->submit("Generar", array("title"=>"Genera la planilla base para importar novedades"))));
	echo $this->renderElement("index/index", array("botonesExtra"=>$botonesExtra, "condiciones"=>$fieldset, "opcionesForm"=>array("action"=>"generar_planilla"), "opcionesTabla"=>$opcionesTabla));
}

?>