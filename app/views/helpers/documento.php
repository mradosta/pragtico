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
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.views.helpers
 * @since           Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * Helper para la creacion de documentos.
 *
 * @package     pragtico
 * @subpackage  app.views.helpers
 */
class DocumentoHelper extends AppHelper {


/**
 * El objeto de tipo PHPExcel.
 *
 * @var object
 * @access public.
 */
	var $doc;


/**
 * The active sheet.
 *
 * @var object
 * @access public.
 */
	var $activeSheet;
	
	
/**
 * Constructor de la clase.
 * Instancia un objeto de la clase PHPExcel.
 * @return void.
 * @access private.
 */   
    function __construct() {
		/** Include PHPExcel classes. */
		set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes');
		App::import('Vendor', 'IOFactory', true, array(APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel'), 'IOFactory.php');
        $this->doc = new PHPExcel();
    }


/**
 * Crea todas las propiedades genericas del archivo de una sola vez y lo posiciona en la primera hoja.
 *
 * @param array $options opciones de la orientacion del papel.
 * 				Ej: $documento->create(array("orientation" => "landscape"));
 * 				Ej: $documento->create(array("password" => "MyPass"));
 * 				Ej: $documento->create(array("password" => ""));		-> generara un password
 * @return void.
 * @access public.	
 */
    function create($options = array()) {
		$this->doc->getProperties()->setCreator('Pragtico');
		$this->doc->getProperties()->setLastModifiedBy('Pragtico');
		$this->doc->getProperties()->setTitle('Pragtico');
		$this->doc->getProperties()->setSubject('Pragtico');
		$this->doc->getProperties()->setDescription('Pragtico');
		$this->doc->getProperties()->setKeywords('Pragtico');
		$this->doc->getProperties()->setCategory('Pragtico');
		$this->doc->setActiveSheetIndex(0);
        $this->setActiveSheet(0);
		$this->doc->getActiveSheet()->setShowGridlines(true);
		$this->doc->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    	if (isset($options['orientation']) && $options['orientation'] === 'landscape') {
			$this->doc->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		}
		
		/**
		* Protejo la hoja para que no me la modifiquen, excepto lo que realmente necesito que modifique que lo desbloqueo luego.
		*/
		if (isset($options['password'])) {
			if (empty($options['password'])) {
				$options['password'] = substr(Configure::read('Security.salt'), 0, 10);
			} else {
				$options['password'] = substr($options['password'], 0, 10);
			}
			$this->doc->getActiveSheet()->getProtection()->setPassword($options['password']);
			$this->doc->getActiveSheet()->getProtection()->setSheet(true);
		}
    }
	

	function setActiveSheet($activeSheetName = '') {
		$this->activeSheet = $this->doc->getActiveSheet();
	}


/**
 * Forma un nombre de celda standar.
 *
 * @param string $cellName La celda
 * 			- A5 		Retornara A5.
 *			- null		Retornara el valor el la proxima columna no ocupada y el la proxima fila no ocupada.
 *			- 4,3		Retornara el valor en la columna 4 y fila 3 (La A es la columna 1, La primer fila es la 1).
 * @return string Una celda de la forma "B3". String vacio en caso de error.
 * @access private.	
 */
	function __getCellName($cellName = null) {
		
		/** Search for numbered named coll (zero indexed).*/
		if (preg_match("/^([0-9]+)\,([0-9]+)$/", $cellName, $matches)) {
			return PHPExcel_Cell::stringFromColumnIndex($matches[1]) . $matches[2];
		} elseif (preg_match("/^[A-Z]+[0-9]+$/", $cellName)) {
			return $cellName;
		} elseif (is_null($cellName)) {
			/**
			* Busco la proxima columna y fila libre.
			*/
			return $this->doc->getActiveSheet()->getHighestColumn() . $this->doc->getActiveSheet()->getHighestRow();
		} else {
			return '';
		}
	}


    function getStyle($style) {

        /** Alignments */
        $styles['left'] =
            array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT));
        $styles['center'] =
            array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $styles['right'] =
            array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));

        /** Fonts */
        $styles['bold'] = array('font' => array('bold' => true));

        /** Borders */
        $styles['left'] =
            array('left' => array('style' => PHPExcel_Style_Border::BORDER_THIN));
        $styles['top'] =
            array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN));
        $styles['right'] =
            array('right' => array('style' => PHPExcel_Style_Border::BORDER_THIN));
        $styles['bottom'] =
            array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN));



    //array('bold', 'center', 'bottom')

        
        switch($style) {
            case 'boldCenter':
                $return = array('style' => array(
                    'font'      => array('bold' => true),
                    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));
            break;
            case 'boldRight':
                $return = array('style' => array(
                'font'     => array('bold' => true),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)));
            break;
            case 'boldLeft':
                $return = array('style' => array(
                    'font' => array('bold' => true)));
            break;
            case 'center':
                $return = array('style' => array(
                    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));
            break;
            case 'right':
                $return = array('style' => array(
                    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)));
            break;
            case 'borderBottom':
                $return = array('style' => array(
                    'borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_DASHDOT))));
            break;
        }
    }

    
	function setWidth($cellName, $value) {
		if (is_numeric($cellName)) {
			return $this->doc->getActiveSheet()->getColumnDimensionByColumn($cellName)->setWidth($value);
		} elseif (preg_match('/^([A-Z]+)\:([A-Z]+)$/', $cellName, $matches)) {
			for ($i = PHPExcel_Cell::columnIndexFromString($matches['1']); $i <= PHPExcel_Cell::columnIndexFromString($matches['2']); $i++) {
				$this->doc->getActiveSheet()->getColumnDimensionByColumn($i-1)->setWidth($value);
			}
		} else {
			return $this->doc->getActiveSheet()->getColumnDimension($cellName)->setWidth($value);
		}
	}


/**
 * Setea un valor y opcionalmente el formato en una celda o rango.
 * En caso de especificarse un rango, hace un merge de las celdas del rango.
 *
 * @param string $cellName La celda de o el rango de celdas.
 * 			- A5 		Seteara el valor en la celda A5.
 *			- A5:C6		Hara un merge entre las celdas y seteara el valor.
 *			- null		Seteara el valor el la proxima columna no ocupada y el la proxima fila no ocupada.
 *			- 4,3		Seteara el valor en la columna 4 y fila 3 (La A es la columna 1, La primer fila es la 1).
 *			- 4,3:5,8	Hara un merge entre las celdas y seteara el valor.
 * @param string $value El valor a especificar en la celda o celdas.
 * @param array $options Opciones adicionales.
 *			- style: array con estilos validos a aplicar a la celda o rango de celdas.
 * 			- merge: una celda especificado de la forma 4,3.
 * @return void.
 * @access public.	
 */
	function setCellValue($cellName, $value, $options = array()) {
		
		/**
		* Verifico si tengo un rango.
		*/
		$tmp = explode(':', $cellName);
		if (count($tmp) === 2) {
			$cellName = $this->__getCellName($tmp[0]);
			$this->doc->getActiveSheet()->mergeCells($cellName . ":" . $this->__getCellName($tmp[1]));
			unset($options['merge']);
		} else {
			$cellName = $this->__getCellName($cellName);
		}
		
		if (!empty($options['merge'])) {
			$this->doc->getActiveSheet()->mergeCells($cellName . ":"  .  $this->__getCellName($options['merge']));
		}
		
		$this->doc->getActiveSheet()->setCellValue($cellName, $value);
		if (!empty($options['style'])) {
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
 * @access public.	
 */
	function setDataValidation($cellName, $type, $options = array()) {
		$cellName = $this->__getCellName($cellName);
		
		/**
		* Si estoy validando un dato, es porque el usuario debe introducirlo, entonces, 
		* si el documento esta bloqueado, le desbloqueo la celda.
		*/
		if ($this->doc->getActiveSheet()->getProtection()->isProtectionEnabled()) {
			$this->doc->getActiveSheet()->getStyle($cellName)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
		}
		
		$objValidation = $this->doc->getActiveSheet()->getCell($cellName)->getDataValidation();
		
		if ($type === 'decimal') {
			$tipo = PHPExcel_Cell_DataValidation::TYPE_DECIMAL;
			$mensaje = 'Solo puede ingresar numeros';
		} elseif ($type === 'date') {
			$tipo = PHPExcel_Cell_DataValidation::TYPE_DATE;
			$mensaje = 'Solo puede ingresar fechas';
		} elseif ($type === 'list') {
			/** Creo una lista que luego la oculto, con esto valido. */
			preg_match("/^([A-Z]+)([0-9]+)$/", $cellName, $matches);
			$colPosition = PHPExcel_Cell::columnIndexFromString($matches[1]) + 100;
			$ff = 0;
			foreach ($options['valores'] as $valores) {
				$ff++;
				$this->doc->getActiveSheet()->setCellValueByColumnAndRow($colPosition, $ff, $valores);
			}
			$namedColPosition = PHPExcel_Cell::stringFromColumnIndex($colPosition);
			$range = $namedColPosition . "1:" . $namedColPosition . $ff;
			$name = "ValueList" . intval(rand());
			$this->doc->addNamedRange(new PHPExcel_NamedRange($name, $this->doc->getActiveSheet(), $range));
			$tipo = PHPExcel_Cell_DataValidation::TYPE_LIST;
			$mensaje = "Debe seleccionar un valor de la lista";
			$objValidation->setFormula1($name);
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
 * @access public.	
 */
	function save($formato = 'Excel2007', $archivo = null) {
		$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($this->doc, $formato);

		if ($formato === 'Excel2007') {
			/**
			* Si se trata de Excel 2007, no precalculo por que no tiene sentido, ya que perdere tiempo ahora, y luego,
			* al abrilo, excel, calcula automaticamente las formulas.
			*/
			$objPHPExcelWriter->setPreCalculateFormulas(false);
			$extension = 'xlsx';
		}
		elseif ($formato === 'Excel5') {
			$extension = 'xls';
		}
		elseif ($formato === 'PDF') {
			$extension = 'pdf';
		}
		elseif ($formato === 'HTML') {
			$extension = 'html';
		}

		/**
		* Obligo a que me aparezca el dialogo de descarga para guardar el archivo.
		*/
		if (empty($archivo)) {
			$archivo = 'php://output';
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Type: application/force-download');
			header('Content-Type: application/octet-stream');
			header('Content-Type: application/download');
			header('Content-Disposition: attachment;filename=file.' . $extension);
			header('Content-Transfer-Encoding: binary');
		} else {
			$archivo .= '.' . $extension;
		}
		Configure::write('debug', 0);
		$objPHPExcelWriter->save($archivo);
		exit();
	}

}
?>