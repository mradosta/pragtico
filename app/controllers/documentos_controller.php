<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los documentos modelo del sistema.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pragmatia de RPB S.A.
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.controllers
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a los documentos modelo del sistema.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class DocumentosController extends AppController {


    function patterns($id) {
        $this->Documento->contain("DocumentosPatron");
        $this->data = $this->Documento->read(null, $id);
    }
    
    
/**
 * Permite la generacion de un documento a partir de una plantilla rtf.
 */
	function generar() {
		if (empty($this->data['Documento']['id'])) {
			/**
			* Me aseguro de cargar el model que utilizare, y lo llevo a recursive level 2, de modo de contar con la
			* mayor cantidad de datos posible.
			*/
			App::import('model', $this->params['named']['model']);
			
			$model = new $this->params['named']['model']();
			if (!empty($this->params['named']['contain'])) {
				$this->set('contain', $this->params['named']['contain']);
				$model->contain(unserialize(str_replace('**', '\'', $this->params['named']['contain'])));
			}
			$data = $model->findById($this->params['named']['id']);
			ob_start();
			debug($this->__removerFrameworkData($data), true, false);
			$out = ob_get_clean();
			$out = str_replace('Array', '', $out);
			$documentos = $this->Documento->find('list', array('conditions'=>array('Documento.model'=>$this->params['named']['model']), 'fields'=>array('Documento.id', 'Documento.nombre')));
			$this->set('documentos', $documentos);
			$this->set('data', $out);
			$this->set('model', $this->params['named']['model']);
			$this->set('id', $this->params['named']['id']);
		}
		else {
			/**
			* Me aseguro de cargar el model que utilizare, y lo llevo a recursive level 2, de modo de contar con la
			* mayor cantidad de datos posible.
			*/
			App::import('model', $this->data['Extra']['model']);
			$model = new $this->data['Extra']['model']();
			if (!empty($this->data['Extra']['contain'])) {
				$model->contain(unserialize(str_replace('**', '\'', $this->data['Extra']['contain'])));
			}
			$data = $model->findById($this->data['Extra']['id']);
			$documento = $this->Documento->findById($this->data['Documento']['id']);
			$reemplazarTexto['patrones'] = unserialize($documento['Documento']['patrones']);
			$reemplazarTexto['reemplazos'] = $data;
			
			$archivo['data'] = $documento['Documento']['file_data'];
			$archivo['type'] = $documento['Documento']['file_type'];
			$archivo['name'] = $this->Util->getFileName($documento['Documento']['nombre'], $documento['Documento']['file_type']);

			$this->set('reemplazarTexto', $reemplazarTexto);
			$this->set('archivo', $archivo);
			$this->render('../elements/descargar', 'descargar');
		}
	}


/**
 * Quita los campos propios del framework, ya que el usuario no tiene porque verlos, lo confunde.
 */
	function __removerFrameworkData($array) {
		if (!is_array($array)) {
			$array;
		}

		$removeKey = array('user_id', 'role_id', 'group_id', 'permissions', 'write', 'delete', 'created', 'modified', 'file_data', 'file_size', 'file_type');
		foreach ($array as $k=>$v) {
			if (is_array($v)) {
				foreach ($v as $k1=>$v1) {
					if (is_array($v1)) {
						foreach ($v1 as $k2=>$v2) {
							if (is_array($v2)) {
								foreach ($v2 as $k3=>$v3) {
									if (in_array($k3, $removeKey)) {
										unset($array[$k][$k1][$k2][$k3]);
									}
								}
							}
							elseif (in_array($k2, $removeKey)) {
								unset($array[$k][$k1][$k2]);
							}
						}
					}
					elseif (in_array($k1, $removeKey)) {
						unset($array[$k][$k1]);
					}
				}
			}
			elseif (in_array($k, $removeKey)) {
				unset($array[$k]);
			}
		}
		return $array;
	}


/**
 * Si lo subio correctamente, lo graba en la session para luego poder hacer un preview.
 */
	function __getFilex() {

		
		if (!empty($this->data['Documento']['archivo']) && is_uploaded_file($this->data['Documento']['archivo']['tmp_name'])) {
			if (isset($this->data['Documento']['archivo']['error']) && $this->data['Documento']['archivo']['error'] === 0) {
				$contenido = fread(fopen($this->data['Documento']['archivo']['tmp_name'], 'r'), $this->data['Documento']['archivo']['size']);
				$archivo['file_size'] = $this->data['Documento']['archivo']['size'];
				$archivo['file_type'] = $this->data['Documento']['archivo']['type'];
				$archivo['file_data'] = $contenido;
				//d($archivo);
				
				set_include_path(get_include_path() . PATH_SEPARATOR . APP . "vendors" . DS . "PHPExcel" . DS . "Classes");
				App::import('Vendor', "IOFactory", true, array(APP . "vendors" . DS . "PHPExcel" . DS . "Classes" . DS . "PHPExcel"), "IOFactory.php");
				
				/*
				if (preg_match("/.*\.xls$/", $this->data['Novedad']['planilla']['name'])) {
					$objReader = PHPExcel_IOFactory::createReader('Excel5');
				}
				elseif (preg_match("/.*\.xlsx$/", $this->data['Novedad']['planilla']['name'])) {
					$objReader = PHPExcel_IOFactory::createReader('Excel2007');
				}
				*/
				$objReader = PHPExcel_IOFactory::createReader('Excel5');
				//d($this->data['Documento']['archivo']['tmp_name']);
				$objPHPExcel = $objReader->load($this->data['Documento']['archivo']['tmp_name']);
				$worksheet = $objPHPExcel->getActiveSheet();
				//d($worksheet->getBreaks());
			//$worksheet->setCellValue($col . ($row+50), $x[$col . $row]);
			//$worksheet->duplicateStyle( $worksheet->getStyle($col . $row), $col . ($row+50) );
				
App::import('Helper', array('formato', 'time', 'number'));
$formato = new FormatoHelper();
$formato->Number =& new NumberHelper();
$formato->Time =& new TimeHelper();

$Liquidacion = ClassRegistry::init('Liquidacion');				
$liquidacion = $Liquidacion->findById(5);				
//d($liquidacion);
				
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
				$patterns[$cell] = $mathes[1];
			}
			//$texto .= $tmp;
		}
	}
}
//d($texto);
//unset($patterns['C5']);

$result = $formato->replace(null, $liquidacion, $patterns);
/*
d($result);

d($patters);
$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objPHPExcelWriter->save('/tmp/test_dup.xls');
d($x);

				
				$objPHPExcel->getActiveSheet()->setCellValue('G9', 'MARTIN');
				for($i=50; $i<20000; $i++) {
					$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, 'MARTIN' . $i);
				}
				$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objPHPExcelWriter->save('/tmp/test_large.xls');
				d("X");
*/				
				
				
				$this->Session->write('tmpArchivo', $archivo);
				return true;
			}
			else {
				$this->Documento->dbError['errorDescripcion'] = 'El archivo no se subio correctamente. Intentelo nuevamente.';
				return false;
			}
		}
	}


/**
 * Si lo subio correctamente, lo graba en la session para luego poder hacer un preview.
 */
    function __getFile() {

        
        if (!empty($this->data['Documento']['archivo']) && is_uploaded_file($this->data['Documento']['archivo']['tmp_name'])) {
            if (isset($this->data['Documento']['archivo']['error']) && $this->data['Documento']['archivo']['error'] === 0) {
                $fileName = basename($this->data['Documento']['archivo']['tmp_name']);
                copy($this->data['Documento']['archivo']['tmp_name'], TMP . $fileName);
                return $fileName;
            } else {
                $this->Documento->dbError['errorDescripcion'] = 'El archivo no se subio correctamente. Intentelo nuevamente.';
                return false;
            }
        }
    }
    
/**
 * El metodo add debe primero buscar los patrones dentro del documento que proporciono el usuario, luego
 * presentara un preview de los patrones encontrados, y si el usuario lo confirma, se graba.
 */
	function save() {
        if (isset($this->data['Form']['confirmar'])) {
            parent::save();
        } elseif ($fileName = $this->__getFile()) {
            $content = file_get_contents(TMP . $fileName);
			if (preg_match_all('/#\*(.+)\*#/U', $content, $matches)) {
				$this->data['Documento']['patrones'] = $matches[1];
                $this->data['Documento']['file_name'] = $fileName;
                $this->data['Documento']['file_type'] = $this->data['Documento']['archivo']['type'];
                $this->data['Documento']['file_size'] = $this->data['Documento']['archivo']['size'];
			} else {
				$this->Session->setFlash('No se encontraron patrones en el archivo origen. Verifiquelo y reintentelo nuevamente.', 'error');
			}
		}
		$this->set('models', $this->__getModels());
		$this->render('add');
	}

	
	function __getModels() {
		$modelsTmp = Configure::listObjects('model');
		sort($modelsTmp);
		foreach ($modelsTmp as $v) {
			$models[$v] = $v;
		}
		return $models;
	}
	
	
	function add() {
		if (empty($this->data)) {
			$this->set('models', $this->__getModels());
		}
		return parent::add();
	}	

/**
 * Permite descargar el archivo del documento.
 */
	function descargar($id) {
		$documento = $this->Documento->findById($id);
		$archivo['data'] = $documento['Documento']['file_data'];
		$archivo['size'] = $documento['Documento']['file_size'];
		$archivo['type'] = $documento['Documento']['file_type'];
		$archivo['name'] = $this->Util->getFileName($documento['Documento']['nombre'], $documento['Documento']['file_type']);
		$this->set('archivo', $archivo);
		$this->render('../elements/descargar', 'descargar');
	}


    
}
?>