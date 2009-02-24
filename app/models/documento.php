<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los documentos modelo del sistema.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pragmatia de RPB S.A.
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.models
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a los documentos modelo del sistema.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Documento extends AppModel {


	var $order = array('Documento.nombre' => 'asc');
	
	var $validate = array(
        'nombre' => array(
			array(
				'rule'		=> VALID_NOT_EMPTY,
				'message'	=> 'Debe especificar el nombre del documento modelo.')
        )
	);

    var $hasMany = array('DocumentosPatron' => array('dependent' => true));
    

/**
 * After save, moves the uploaded file to documents directory.
 */	
	function afterSave($created) {
		copy(TMP . $this->data['Documento']['file_name'], WWW_ROOT . 'files' . DS . 'documents' . DS . $this->id . '-' . Inflector::classify(strtolower(str_replace(' ', '_', $this->data['Documento']['nombre']))) . '.' . $this->data['Documento']['file_extension']);
		@unlink(TMP . $this->data['Documento']['file_name']);
	}
	

/**
 * Extract patters from file based on it's mime type.
 *
 * @param $file String File name.
 * @param $extension String file extension.
 * @return array Array of patters found in file.
 * @access private
 */
    function getPatternsFromFile($file, $extension) {

        switch ($extension) {
			case 'rtf':
			case 'txt':
				return $this->getPatterns(file_get_contents($file));
				break;	
            case 'xls':
            case 'xlsx':
                set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes');
                App::import('Vendor', 'IOFactory', true, array(APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel'), 'IOFactory.php');
                
                if ($type === 'xls') {
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
                } else {
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                }
                $objPHPExcel = $objReader->load($file);
                $worksheet = $objPHPExcel->getActiveSheet();
                $lastRow = $worksheet->getHighestRow();
                $lastCol = $worksheet->getHighestColumn();
                $cells = $worksheet->getCellCollection();
                $texto = '';
                for ($row = 1; $row <= $lastRow; $row++){
                    for ($col = 'A'; $col <= $lastCol; $col++){
                        $cell = $col . $row;
                        if (isset($cells[$cell])) {
                            $tmp = $cells[$cell]->getValue();
                            if (preg_match('/(#\*.+\*#)/', $tmp, $mathes)) {
                                $documentosPatron[] = array('identificador' => $cell, 'patron' => $mathes[1]);
                            }
                        }
                    }
                }
                return $documentosPatron;
        }
		return false;
    }
	
}
?>