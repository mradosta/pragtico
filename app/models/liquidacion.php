<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las liquidaciones.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
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
 * La clase encapsula la logica de acceso a datos asociada a las liquidaciones.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Liquidacion extends AppModel {

	/**
	* Seteo los tipos posibles de liquidaciones que podre realizar.
	*/
	var $opciones = array('tipo' => array(
						  		'normal'			=> 'Normal',
			   					'descuentos'		=> 'Descuentos',
			   					'sac'				=> 'Sac',
		   						'vacaciones'		=> 'Vacaciones',
		   						'liquidacion_final'	=> 'Liquidacion Final',
		   						'especial'			=> 'Especial'));
	
	var $hasMany = array(	'LiquidacionesDetalle' =>
                        array('className'   => 'LiquidacionesDetalle',
                              'foreignKey' 	=> 'liquidacion_id',
                              'order'		=> 'LiquidacionesDetalle.concepto_orden',
                              'dependent'	=> true),
                            'LiquidacionesError' =>
                        array('className'   => 'LiquidacionesError',
                              'foreignKey' 	=> 'liquidacion_id',
                              'dependent'	=> true),
                            'LiquidacionesAuxiliar' =>
                        array('className'   => 'LiquidacionesAuxiliar',
                              'foreignKey' 	=> 'liquidacion_id',
                              'dependent'	=> true),
							'Pago' =>
                        array('className'   => 'Pago',
                              'foreignKey' 	=> 'liquidacion_id',
                              'dependent'	=> true));

	var $belongsTo = array(	'Trabajador' =>
                        array('className'    => 'Trabajador',
                              'foreignKey'   => 'trabajador_id'),
							'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'),
							'Empleador' =>
                        array('className'    => 'Empleador',
                              'foreignKey'   => 'empleador_id'),
							'Factura' =>
                        array('className'    => 'Factura',
                              'foreignKey'   => 'factura_id')                              );
                              

/**
 * I must overwrite default cakePHP deleteAll method because it's not performant when there're many 
 * relations and many records.
 * I also add transaccional behavior and a better error check.
 * TODO:
 * 		when the relation has a dependant relation, this method will not delete that relation.
 */	
	function deleteAll($conditions, $cascade = true, $callbacks = false) {
		$ids = Set::extract(
			$this->find('all', array_merge(array('fields' => '{$this->alias}.{$this->primaryKey}', 'recursive' => 0), compact('conditions'))),
			'{n}.{$this->alias}.{$this->primaryKey}'
		);
		
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$c = 0;
		$db->begin($this);
		foreach ($this->hasMany as $assoc => $data) {
			$table = $db->name(Inflector::tableize($assoc));
			$conditions = array($data['foreignKey'] => $ids);
			$sql = sprintf('DELETE FROM %s %s', $table, $db->conditions($conditions));
			$this->query($sql);

			//$this->__buscarError();
			if (empty($this->dbError)) {
				$c++;
			}
		}
		
		if (count($this->hasMany) === $c) {
			$sql = sprintf('DELETE FROM %s %s', $db->name($this->useTable), $db->conditions(array($this->primaryKey => $ids)));
			$this->query($sql);
			//$this->__buscarError();
			if (empty($this->dbError)) {
				$db->commit($this);
				return true;
			}
			else {
				$db->rollback($this);
				return false;
			}
		}
		else {
			$db->rollback($this);
			return false;
		}
	}
	

/**
 * Generates a receipt.
 *
 * @param string $type. The type of recipt you want to generate.
 *      - normal
 *      - sac
 *      - bla
 *      - bla
 * @param array $options.
 *      - period: 1=first_half, 2=second_half.
 *      - year: The year where to calcula SAC.
 *      - january to december: Sum of remuneratives total by month.
 * @return array. A receipt ready to be saved.
 * @access public
 */
    function getReceipt($type = 'normal', $relationships = null, $options = array()) {
        $relationshipsIds = Set::extract('/Relacion/id', $relationships);
        if ($type === 'sac') {

            $defaults['january'] = 0;
            $defaults['february'] = 0;
            $defaults['march'] = 0;
            $defaults['april'] = 0;
            $defaults['may'] = 0;
            $defaults['june'] = 0;
            $defaults['july'] = 0;
            $defaults['august'] = 0;
            $defaults['september'] = 0;
            $defaults['october'] = 0;
            $defaults['november'] = 0;
            $defaults['december'] = 0;
            $options = array_merge($defaults, $options);
            
            $condtions['Liquidacion.relacion_id'] = $relationshipsIds;
            $condtions['Liquidacion.ano'] = $options['year'];
            if ($options['period'] == '1') {
                $condtions['mes'] = array('AND' => array(
                        'Liquidacion.mes >=' => 1,
                        'Liquidacion.mes <=' => 6));
            } elseif ($options['period'] == '2') {
                $condtions['mes'] = array('AND' => array(
                        'Liquidacion.mes >=' => 6,
                        'Liquidacion.mes <=' => 12));
            } else {
                return array('error' => sprintf('Wrong period (%s). Only "1" for the first_half or "2" for the second_half allowed for type %s.', $options['period'], $type));
            }

            foreach ($relationships as $relationship) {
                $options['relation'] = array_pop(Set::combine(array($relationship), '{n}.Relacion.id', array('{2}, {1} ({0})', '{n}.Empleador.nombre', '{n}.Trabajador.nombre', '{n}.Trabajador.apellido')));
                $options['start'] = strtotime($relationship['Relacion']['ingreso'] . ' 00:00:00 UTC');
                $options['end'] = strtotime($relationship['Relacion']['egreso'] . ' 00:00:00 UTC');
            }

            /** Use PHPExcel to get complex calculations done */
            set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes');
            App::import('Vendor', 'IOFactory', true, array(APP . 'vendors' . DS . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel'), 'IOFactory.php');
            $objPHPExcelReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objPHPExcelReader->load(WWW_ROOT . 'files' . DS . 'base' . DS . 'sac.xlsx');
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcelSheet = $objPHPExcel->getActiveSheet();

            foreach ($options as $cellName => $data) {
                $objPHPExcelSheet->setCellValue(ucfirst($cellName), $data);
            }

            $objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objPHPExcelWriter->save('/tmp/sac-generated.xlsx');
            return sprintf('%01.2f', $objPHPExcelSheet->getCell('TOTAL_PRAGTICO')->getCalculatedValue());
            
            //$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            //$objPHPExcelWriter->save('/tmp/sac-generated.xls');
            $objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objPHPExcelWriter->save('/tmp/sac-generated2.xlsx');
            
            $fields = array('Liquidacion.relacion_id', 'SUM(remunerativo) AS total_remunerativo');
            $groupBy = array('Liquidacion.relacion_id', 'Liquidacion.mes');

            $r = $this->find('all', array(
                    'recursive' => -1,
                    'fields'    => $fields,
                    'condtions' => $condtions,
                    'group'     => $groupBy));
            //d($r);


            
        }
    }
    
var $hasOne = array (
  'Dinertotal' => array (
  'className' => 'Liquidacion',
  'fields' => array (
  '(SELECT MAX(count) FROM `liquidaciones` WHERE diner_id =
`Diner`.`id`) as `totalcount`'
  ),
  'foreignKey' => 'id',
  ),
);
    
function afterFind($results, $primary = false) {
    //array_walk($results, create_function(’&$v’, ‘$v['Photo']['rownum'] = $v[0]['rownum']; unset($v[0]);’));
    if ($primary == true) {
        if (Set::check($results, '0.0')) {
            $fieldName = key($results[0][0]);
            foreach ($results as $key=>$value) {
                $results[$key][$this->alias][$fieldName] = $value[0][$fieldName];
                unset($results[$key][0]);
            }
        }
    }
    return $results;
}

	function addEditDetalle($opciones) {
		/**
		* Se refiere a los conceptos que deben tratarse de forma especial, ya que modifican data en table, u otra cosa.
		*/
		$this->recursive = -1;
		$liquidacion = $this->findById($opciones['liquidacionId']);
		$conceptosHora = array('horas_extra_50', 'horas_extra_100');
		if (in_array($opciones['conceptoCodigo'], $conceptosHora)) {
			$this->LiquidacionesDetalle->Concepto->recursive = -1;
			$concepto = $this->LiquidacionesDetalle->Concepto->findByCodigo($opciones['conceptoCodigo']);

			$save['relacion_id'] = $liquidacion['Liquidacion']['relacion_id'];
			$save['liquidacion_id'] = $liquidacion['Liquidacion']['id'];
			$save['tipo'] = str_replace('Horas ', '', preg_replace('/0$/', '0 %', Inflector::humanize($opciones['conceptoCodigo'])));
			$save['periodo'] = $liquidacion['Liquidacion']['ano'] . str_pad('0', 2, $liquidacion['Liquidacion']['mes'], STR_PAD_RIGHT) . $liquidacion['Liquidacion']['periodo'];
			$save['estado'] = 'Pendiente';
			$save['cantidad'] = $opciones['valor'];
			$save['observacion'] = 'Ingresado desde la modificacion de una Liquidacion';
			$horaModel = new Hora();
			//$horaModel->begin();
			$horaModel->save(array('Hora'=>$save));
			
			//$horaModel->rollBack();
		}
	}

}
?>