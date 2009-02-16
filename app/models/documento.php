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
    

    function beforeSave($options = array()) {
        
        App::import("Vendor", "files", "pragmatia");
        $File = new Files();
        
        unset($this->data[$this->name]['archivo']);
        $this->data[$this->name]['file_data'] = file_get_contents(TMP . $this->data[$this->name]['file_name']);
        switch ($type = $File->getType($this->data[$this->name]['file_type'])) {
            case 'xls':
            case 'xlsx':
                set_include_path(get_include_path() . PATH_SEPARATOR . APP . "vendors" . DS . "PHPExcel" . DS . "Classes");
                App::import('Vendor', "IOFactory", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel"), "IOFactory.php");
                
                if ($type === 'xls') {
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
                } else {
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                }
                $objPHPExcel = $objReader->load(TMP . $this->data[$this->name]['file_name']);
                $worksheet = $objPHPExcel->getActiveSheet();
                $lastRow = $worksheet->getHighestRow();
                $lastCol = $worksheet->getHighestColumn();
                $cells = $worksheet->getCellCollection();
                $texto = "";
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
                $this->data['DocumentosPatron'] = $documentosPatron;
                return true;
                
                break;
        }
        return parent::beforeSave();
    }
    
}
?>