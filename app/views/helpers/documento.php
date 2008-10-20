<?php
/**
 * Helper para la creacion de documentos.
 *
 * Permite la creacion de documentos de tipo Excel2007, Excel5, Pdf y Html a partir de los mismos metodos.
 * Utilizo la libreria PHPExcel ( http://www.phpexcel.net ).
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.controllers
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * Helper para la creacion de documentos.
 *
 * @package		pragtico
 * @subpackage	views.helpers
 */
class DocumentoHelper extends AppHelper {


/**
 * El objeto de tipo PHPExcel.
 *
 * @var object
 */
	var $doc;

	
/**
 * Constructor de la clase.
 * Instancia un objeto de la clase PHPExcel.
 * @return void.
 */   
    function __construct() {
		/**
		 * Incluye al path los archivos de las clases de PHPExcel.
		 */
		set_include_path(get_include_path() . PATH_SEPARATOR . APP . "vendors" . DS . "PHPExcel" . DS . "Classes");
		App::import('Vendor', "IOFactory", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel"), "IOFactory.php");
        $this->doc = new PHPExcel();
    }


/**
 * Crea todas las propiedades genericas del archivo de una sola vez y lo posiciona en la primera hoja.
 *
 * @param array $options opciones de la orientacion del papel.
 * 				Ej: $documento->create(array("orientation" => "landscape"));
 * @return void.
 */
    function create($options = array()) {
		$this->doc->getProperties()->setCreator("Pragtico");
		$this->doc->getProperties()->setLastModifiedBy("Pragtico");
		$this->doc->getProperties()->setTitle("Planilla para el Ingreso de Novedades - Pragtico");
		$this->doc->getProperties()->setSubject("Planilla para el Ingreso de Novedades - Pragtico");
		$this->doc->getProperties()->setDescription("Planilla para el Ingreso de Novedades. Pragtico permite el ingreso de las novedades al sistema de una manera rapida.");
		$this->doc->getProperties()->setKeywords("novedades pragtico");
		$this->doc->getProperties()->setCategory("novedades");
		$this->doc->setActiveSheetIndex(0);
		$this->doc->getActiveSheet()->setShowGridlines(false);
		$this->doc->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    	if(isset($options['orientation']) && $options['orientation'] === "landscape") {
			$this->doc->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		}
    }
	
	
/**
 * Setea un valor y opcionalmente el formato en una celda o rango.
 * En caso de especificarse un rango, hace un merge de las celdas del rango.
 *
 * @param string $cellName La celda de o el rango de celdas.
 * @param string $value El valor a especificar en la celda o celdas.
 * @param array $options Opciones adicionales.
 *			- style: array con estilos validos a aplicar a la celda o rango de celdas.
 * @return void.
 */
	function setCellValue($cellName, $value, $options = array()) {
		if(preg_match("/^([A-Z]+[0-9]+)\:[A-Z]+[0-9]+$/", $cellName, $matches)) {
			$this->doc->getActiveSheet()->mergeCells($cellName);
			$cellName = $matches[1];
		}
		$this->doc->getActiveSheet()->setCellValue($cellName, $value);
		if(!empty($options['style'])) {
			$this->doc->getActiveSheet()->getStyle($cellName)->applyFromArray($options['style']);
		}
	}


/**
 * Setea una validacion especifica para una celda.
 *
 * @param string $cellName La celda que debo validar.
 * @param string $type El tipo de validacion:
 *			- decimal
 *			- lista
 * @param array $options Opciones adicionales.
 *			- valores: array con los valores a mostrar en la lista.
 * @return void.
 */
	function setDataValidation($cellName, $type, $options = array()) {
		$objValidation = $this->doc->getActiveSheet()->getCell($cellName)->getDataValidation();
		
		if($type === "decimal") {
			$tipo = PHPExcel_Cell_DataValidation::TYPE_DECIMAL;
			$mensaje = "Solo puede ingresar numeros";
		}
		elseif($type === "lista") {
			$tipo = PHPExcel_Cell_DataValidation::TYPE_LIST;
			$mensaje = "Debe seleccionar un valor de la lista";
			$objValidation->setFormula1('"' . implode(",", $options['valores']) . '"');
			$objValidation->setShowDropDown(true);
		}
		$objValidation->setType( $tipo );
		
		/**
		* Cuando se detecta un error, que no lo deje continuar hasta que lo corrija.
		*/
		$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_STOP );
		$objValidation->setAllowBlank(false);
		$objValidation->setShowErrorMessage(true);
		$objValidation->setError($mensaje);
		$this->doc->getActiveSheet()->getCell($cellName)->setDataValidation($objValidation);
	}


/**
 * Genera el documentos y lo envia al browser para la descarga o lo guarda en una ubicacion del servidor.
 *
 * @param string $formato El formato del archivo a crear:
 *			- Excel2007
 *			- Excel5
 *			- PDF
 *			- HTML
 * @param string $archivo La ruta y el nombre del archivo donde crearlo.
 * En caso de ser null, se envia al browser para la descarga
 * @return void.
 */
	function save($formato = "Excel2007", $archivo = null) {
		$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($this->doc, $formato);

		if($formato === "Excel2007") {
			/**
			* Si se trata de Excel 2007, no precalculo por que no tiene sentido, ya que perdere tiempo ahora, y luego,
			* al abrilo, excel, calcula automaticamente las formulas.
			*/
			$objPHPExcelWriter->setPreCalculateFormulas(false);
			$extension = "xlsx";
		}
		elseif($formato === "Excel5") {
			$extension = "xls";
		}
		elseif($formato === "PDF") {
			$extension = "pdf";
		}
		elseif($formato === "HTML") {
			$extension = "html";
		}

		/**
		* Obligo a que me aparezca el dialogo de descarga para guardar el archivo.
		*/
		if(empty($archivo)) {
			$archivo = "php://output";
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment;filename=planilla." . $extension);
			header("Content-Transfer-Encoding: binary");
		}
		$objPHPExcelWriter->save($archivo);
		exit();
	}

}
?>