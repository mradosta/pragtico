<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a las vacaciones.
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
 * La clase encapsula la logica de acceso a datos asociada a las vacaciones.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Vacacion extends AppModel {

	var $recursive = 2;
	var $validate = array( 
        'desde' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar la fecha de inicio de las vacaciones.'),
			array(
				'rule'	=> VALID_DATE, 
				'message'	=>'Debe especificar una fecha valida.')
				
        ),
        'hasta' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY, 
				'message'	=>'Debe especificar la fecha de fin de las vacaciones.'),
			array(
				'rule'	=> VALID_DATE, 
				'message'	=>'Debe especificar una fecha valida.')
				
        ),
        'relacion_id__' => array(
			array(
				'rule'	=> VALID_NOT_EMPTY,
				'message'	=>'Debe especificar la relacion laboral que toma las vacaciones.')
        )        
	);
	
	var $belongsTo = array(	'Relacion' =>
                        array('className'    => 'Relacion',
                              'foreignKey'   => 'relacion_id'));



/**
 * buscarDiasVacaciones
 * Dada un ralacion y un periodo retorna los dias de vacaciones.
 * @return array vacio si no hay dias de vacaciones.
 */
	function buscarDiasVacaciones($opciones, $relacion) {
	
		$sql = "
			select		v.desde,
						v.hasta
			from		vacaciones v
			where		1=1
			and			v.relacion_id = '" . $relacion['Relacion']['id'] . "'
			and			v.desde >= '" . $opciones['desde'] . "'
			and			v.hasta <= '" . $opciones['hasta'] . "'
		";

		$r = $this->query($sql);
		d($r);
		$horas['#horas'] = $horas['#horas_extra_50'] = $horas['#horas_extra_100'] = $horas['#horas_enfermedad'] = 0;
		$conceptos = array();
		if (!empty($r)) {
			$modelConcepto = new Concepto();
			foreach ($r as $hora) {
				if ($relacion['ConveniosCategoria']['jornada'] == "Mensual" && ($hora['h']['tipo'] == "Normal" || $hora['h']['tipo'] == "Enfermedad")) {
					continue;
				}
				
				switch($hora['h']['tipo']) {
					case "Normal":
						$tipo = "#horas";
						break;
					case "Extra 50%":
						$tipo = "#horas_extra_50";
						break;
					case "Extra 100%":
						$tipo = "#horas_extra_100";
						break;
					case "Enfermedad":
						$tipo = "#horas_enfermedad";
						break;
				}
				$horas[$tipo] = $hora[0]['total'];

				/**
				* Busco el concepto.
				*/
				$tipo = str_replace("#", "", $tipo);
				$conceptos = am($conceptos, $modelConcepto->findConceptos("ConceptoPuntual", $relacion, $tipo));
			}
		}
		
		return array("conceptos"=>$conceptos, "variables"=>$horas);
	}
}
?>