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
 * @copyright       Copyright 2005-2008, Pragmatia de RPB S.A.
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.views.helpers
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
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


    private $__currentRow = 1;
    
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
 *              Ej: $documento->create(array("orientation" => "landscape"));
 *              Ej: $documento->create(array("password" => "MyPass"));
 *              Ej: $documento->create(array("password" => false));    -> no pass
 *              Ej: $documento->create(array("password" => ""));        -> generara un password
 * @return void.
 * @access public.  
 */
    function create($options = array()) {

        $__defaults = array('password' => false, 'header' => true, 'orientation' => 'portrait', 'title' => '');
        $options = array_merge($__defaults, $options);

        $this->doc->getProperties()->setCreator('Pragtico')->setLastModifiedBy('Pragtico')->setTitle('Pragtico')->setSubject('Pragtico')->setDescription('Pragtico')->setKeywords('Pragtico')->setCategory('Pragtico');
        $this->doc->setActiveSheetIndex(0);
        $this->setActiveSheet();
        $this->activeSheet->setShowGridlines(true);
        $this->activeSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        if ($options['orientation'] === 'portrait') {
            $this->activeSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        } else {
            $this->activeSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        }

        /** Protejo la hoja para que no me la modifiquen, excepto lo que realmente necesito que modifique que lo desbloqueo luego */
        if ($options['password'] !== false) {
            if (is_string($options['password'])) {
                $this->activeSheet->getProtection()->setPassword(substr($options['password'], 0, 10));
            } else {
                $options['password'] = substr(Configure::read('Security.salt'), 0, 10);
            }
            $this->activeSheet->getProtection()->setPassword($options['password']);
            $this->activeSheet->getProtection()->setSheet(true);
        }


        $this->activeSheet->getDefaultStyle()->getFont()->setName('Courier New');
        $this->activeSheet->getDefaultStyle()->getFont()->setSize(6);
        $this->activeSheet->getDefaultRowDimension()->setRowHeight(10);


        if ($options['header'] !== false) {
            $header = '';
            if (is_numeric($options['header'])) {
                $groupParams = User::getGroupParams($options['header']);
            } elseif (is_string($options['header'])) {
                $header = $options['header'];
            } else {
                $groupParams = User::getGroupParams();
            }
            
            if (!empty($groupParams)) {
                $header = sprintf("&L%s\n%s - %s\nCP: %s - %s - %s\nCUIT: %s&R%s\nPagina &P de &N",
                    $groupParams['nombre_fantasia'],
                    $groupParams['direccion'],
                    $groupParams['barrio'],
                    $groupParams['codigo_postal'],
                    $groupParams['ciudad'],
                    $groupParams['pais'],
                    $groupParams['cuit'],
                    date('Y-m-d'));
            }
            $this->activeSheet->getHeaderFooter()->setOddHeader($header);
        }

        if (!empty($options['title'])) {
            $this->setCellValue('A1:E2', $options['title'], array(
                'style' => array(
                    'font' => array('bold' => true, 'size' => 12))));
            $this->moveCurrentRow(7, false);
        }
    }
    

    function setActiveSheet($activeSheetName = '') {
        $this->activeSheet = $this->doc->getActiveSheet();
    }


    function moveCurrentRow($step = 1, $relative = true) {
        if ($relative === true) {
            $this->__currentRow += $step;
        } else {
            $this->__currentRow = $step;
        }
        return $this->__currentRow;
    }
    
    function setCurrentRow($row) {
        $this->__currentRow = $row;
    }

    function getCurrentRow() {
        return $this->__currentRow;
    }
    

/**
 * Forma un nombre de celda standar.
 *
 * @param string $cellName La celda
 *          - A         Retornara A__currentRow.
 *          - A5        Retornara A5.
 *          - null      Retornara el valor el la proxima columna no ocupada y el la proxima fila no ocupada.
 *          - 4,3       Retornara el valor en la columna 4 y fila 3 (La A es la columna 1, La primer fila es la 1).
 *          - 4         Retornara el valor en la columna 4 y __currentRow.
 * @return string Una celda de la forma "B3". String vacio en caso de error.
 * @access private. 
 */
    function __getCellName($cellName = null) {
        
        if (preg_match('/^[A-Z]+$/', $cellName)) {
            return $cellName . $this->getCurrentRow();
        } elseif (is_numeric($cellName)) {
            return PHPExcel_Cell::stringFromColumnIndex($cellName) . $this->getCurrentRow();
        } elseif (preg_match('/^[A-Z]+[0-9]+$/', $cellName)) {
            return $cellName;
        } elseif (preg_match('/^([0-9]+)\,([0-9]+)$/', $cellName, $matches)) {
            return PHPExcel_Cell::stringFromColumnIndex($matches[1]) . $matches[2];
        } elseif (is_null($cellName)) {
            /**
            * Busco la proxima columna y fila libre.
            */
            return $this->activeSheet->getHighestColumn() . $this->doc->getActiveSheet()->getHighestRow();
        } else {
            return '';
        }
    }

/**
 * Creates a formatted totals table.
 *
 * @param array() key => value pair for label in keys.
 * <code>
 * $t = array();
 * $t['Label A'] = array('value' => array('bold', 'right'));
 * $t['Label B'] = 'value';
 * $documento->setTotals($t);
 * </code>
 *
 */
    function setTotals($totals = array()) {
        if (!empty($totals)) {
            $this->moveCurrentRow(3);
            $this->setCellValue('A' . $this->getCurrentRow() . ':D' . $this->getCurrentRow(), 'TOTALES', 'title');
            foreach ($totals as $label => $total) {
                $this->moveCurrentRow();
                $this->setCellValue('B', $label. ':', array('bold', 'right'));
                if (is_array($total)) {
                    $this->setCellValue('C', key($total), $total[key($total)]);
                } else {
                    $this->setCellValue('C', $total, 'total');
                }
            }
        }
    }
    

    function setWidth($cellName, $value) {
        if (is_numeric($cellName)) {
            return $this->activeSheet->getColumnDimensionByColumn($cellName)->setWidth($value);
        } elseif (preg_match('/^([A-Z]+)\:([A-Z]+)$/', $cellName, $matches)) {
            for ($i = PHPExcel_Cell::columnIndexFromString($matches['1']); $i <= PHPExcel_Cell::columnIndexFromString($matches['2']); $i++) {
                $this->activeSheet->getColumnDimensionByColumn($i-1)->setWidth($value);
            }
        } else {
            return $this->activeSheet->getColumnDimension($cellName)->setWidth($value);
        }
    }

/**
            array(  '2,' . $fila =>
                array('value' => '=SUM(C' . $beginRow . ':C' . ($fila - 1) . ')', 'options' => 'total'),
                    '4,' . $fila =>
                array('value' => '=SUM(E' . $beginRow . ':E' . ($fila - 1) . ')', 'options' => 'total');
 * @return void.
 * @access public.  
 */
    function setCellValueFromArray($data) {
        $defaults = array('value' => '', 'options' => array());
        $this->moveCurrentRow();
        foreach ($data as $cell => $value) {
            if (is_array($value)) {
                $value = array_merge($defaults, (array)$value);
                $this->setCellValue($cell, $value['value'], $value['options']);
            } else {
                $this->setCellValue($cell, $value);
            }
        }
    }


/**
 * Setea un valor y opcionalmente el formato en una celda o rango.
 * En caso de especificarse un rango, hace un merge de las celdas del rango.
 *
 * @param string $cellName La celda de o el rango de celdas.
 *          - A5        Seteara el valor en la celda A5.
 *          - A5:C6     Hara un merge entre las celdas y seteara el valor.
 *          - null      Seteara el valor el la proxima columna no ocupada y el la proxima fila no ocupada.
 *          - 4,3       Seteara el valor en la columna 4 y fila 3 (La A es la columna 1, La primer fila es la 1).
 *          - 4,3:5,8   Hara un merge entre las celdas y seteara el valor.
 * @param string $value El valor a especificar en la celda o celdas.
 * @param array $options Opciones adicionales.
 *          - style: array con estilos validos a aplicar a la celda o rango de celdas.
 *          - merge: una celda especificado de la forma 4,3.
 * @return void.
 * @access public.  
 */
    function setCellValue($cellName, $value, $options = null) {

        /** Check for range */
        $tmp = explode(':', $cellName);
        if (count($tmp) === 2) {
            $cellName = $this->__getCellName($tmp[0]);
            $this->activeSheet->mergeCells($cellName . ":" . $this->__getCellName($tmp[1]));
        } else {
            $cellName = $this->__getCellName($cellName);
        }
        
        if (Set::check($options, 'merge')) {
            $this->doc->getActiveSheet()->mergeCells($cellName . ":"  .  $this->__getCellName($options['merge']));
            unset($options['merge']);
        }
        
        if (is_string($options)) {
            if ($options === 'total') {
                $options = array('right', 'currency', 'bold');
            } else {
                $options = array($options);
            }
        }

        if (!empty($options)) {
            if (Set::check($options, 'style')) {
                $styles = $options['style'];
            } else {
                $styles = array();
                $style = null;
                foreach ($options as $k => $v) {
                    $option = null;
                    if (is_numeric($k)) {
                        $option = $v;
                        $v = null;
                    } else {
                        $option = $k;
                    }
                    switch ($option) {
                        case 'right':
                            $style = array(
                                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
                        break;
                        case 'center':
                            $style = array(
                                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
                        break;
                        case 'bold':
                            $style = array(
                                'font'      => array('bold' => true));
                        break;
                        case 'title':
                            $style = array(
                                'font'      => array('bold' => true),
                                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                'borders'   => array('bottom'     => array(
                                    'style' => PHPExcel_Style_Border::BORDER_DOTTED)));
                            if (!empty($v)) {
                                $this->setWidth(str_replace(array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), '', $cellName), $v);
                            } else {
                                $this->activeSheet->getColumnDimension(str_replace(array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), '', $cellName))->setAutoSize(true);
                            }
                        break;
                        case 'decimal':
                            $this->activeSheet->getStyle($cellName)->getNumberFormat()->setFormatCode('0.00');
                            $style = array('alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
                        break;
                        case 'currency':
                            $this->activeSheet->getStyle($cellName)->getNumberFormat()->setFormatCode('"$ "0.00;"$ -"0.00');
                            $style = array('alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
                        break;
                    }
                    
                    if (!empty($style)) {
                        $options = null;
                        $styles = array_merge($styles, $style);
                    }
                }
            }

            if (!empty($styles)) {
                $this->activeSheet->getStyle($cellName)->applyFromArray($styles);
            }
        }
        $this->activeSheet->setCellValue($cellName, $value);
    }


/**
 * Setea una validacion especifica para una celda.
 *
 * @param string $cellName La celda que debo validar.
 * @param string $type El tipo de validacion:
 *          - decimal
 *          - lista
 * @param array $options Opciones adicionales.
 *          - valores: array con los valores a mostrar en la lista.
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
 *          - Excel2007
 *          - Excel5
 *          - PDF
 *          - HTML
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