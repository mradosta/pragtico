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
	var $opciones = array("tipo" => array(	
						  		"normal"			=> "Normal",
			   					"descuentos"		=> "Descuentos",
			   					"sac"				=> "Sac",
		   						"vacaciones"		=> "Vacaciones",
		   						"liquidacion_final"	=> "Liquidacion Final",
		   						"especial"			=> "Especial"));
	
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
			$this->find('all', array_merge(array('fields' => "{$this->alias}.{$this->primaryKey}", 'recursive' => 0), compact('conditions'))),
			"{n}.{$this->alias}.{$this->primaryKey}"
		);
		
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$c = 0;
		$db->begin($this);
		foreach ($this->hasMany as $assoc => $data) {
			$table = $db->name(Inflector::tableize($assoc));
			$conditions = array($data['foreignKey'] => $ids);
			$sql = sprintf("DELETE FROM %s %s", $table, $db->conditions($conditions));
			$this->query($sql);
			$this->__buscarError();
			if (empty($this->dbError)) {
				$c++;
			}
		}
		
		if (count($this->hasMany) === $c) {
			$sql = sprintf("DELETE FROM %s %s", $db->name($this->useTable), $db->conditions(array($this->primaryKey => $ids)));
			$this->query($sql);
			$this->__buscarError();
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
	

	function addEditDetalle($opciones) {
		/**
		* Se refiere a los conceptos que deben tratarse de forma especial, ya que modifican data en table, u otra cosa.
		*/
		$this->recursive = -1;
		$liquidacion = $this->findById($opciones['liquidacionId']);
		$conceptosHora = array("horas_extra_50", "horas_extra_100");
		if (in_array($opciones['conceptoCodigo'], $conceptosHora)) {
			$this->LiquidacionesDetalle->Concepto->recursive = -1;
			$concepto = $this->LiquidacionesDetalle->Concepto->findByCodigo($opciones['conceptoCodigo']);

			$save['relacion_id'] = $liquidacion['Liquidacion']['relacion_id'];
			$save['liquidacion_id'] = $liquidacion['Liquidacion']['id'];
			$save['tipo'] = str_replace("Horas ", "", preg_replace("/0$/", "0 %", Inflector::humanize($opciones['conceptoCodigo'])));
			$save['periodo'] = $liquidacion['Liquidacion']['ano'] . str_pad("0", 2, $liquidacion['Liquidacion']['mes'], STR_PAD_RIGHT) . $liquidacion['Liquidacion']['periodo'];
			$save['estado'] = "Pendiente";
			$save['cantidad'] = $opciones['valor'];
			$save['observacion'] = "Ingresado desde la modificacion de una Liquidacion";
			$horaModel = new Hora();
			//$horaModel->begin();
			$horaModel->save(array("Hora"=>$save));
			
			//$horaModel->rollBack();
		}
	}

}
?>