<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los conceptos
 * propios de las relaciones laborales existentes entre trabajadores y empleadores.
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
 * La clase encapsula la logica de acceso a datos asociada a los conceptos propios de las
 * relaciones laborales que hay entre en un trabajador y un empleador.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class RelacionesConcepto extends AppModel {

    var $validate = array(
        'relacion_id' => array(
            array(
                'rule'      => VALID_NOT_EMPTY,
                'message'   => 'Debe seleccionar la relacion laboral.')
        ),
        'concepto_id' => array(
            array(
                'rule'      => VALID_NOT_EMPTY,
                'message'   => 'Debe seleccionar el concepto.')
        ),
        'formula' => array(
            array(
                'rule'      => 'validFormulaParenthesis',
                'message'   => 'La formula no abre y cierra la misma cantidad de parentesis.'),
            array(
                'rule'      => 'validFormulaBrackets',
                'message'   => 'La formula no abre y cierra la misma cantidad de corchetes.')
        )
    );

	var $modificadores = array(	'index'	=>
			array('contain'	=> array('Relacion'	=> array('Empleador', 'Trabajador', 'ConveniosCategoria'), 'Concepto')),
								'edit'	=>
			array('contain'	=> array('Relacion'	=> array('Empleador', 'Trabajador', 'ConveniosCategoria'), 'Concepto')));
	
	var $belongsTo = array('Relacion', 'Concepto');


	function afterFind($results, $primary = false) {
		if (!isset($results[0][0]) && $primary === true) {
			foreach ($results as $k => $result) {
				if (isset($result['Relacion']['ConveniosCategoria'])) {
					$options = null;
					$options['relacion'] = $result;
					$options['relacion']['ConveniosCategoria'] = $result['Relacion']['ConveniosCategoria'];
					$options['codigoConcepto'] = $result['Concepto']['codigo'];
					$tmp = $this->Concepto->findConceptos('Relacion', $options);
					$results[$k]['RelacionesConcepto']['jerarquia'] = $tmp[$result['Concepto']['codigo']]['jerarquia'];
					$results[$k]['RelacionesConcepto']['formula_aplicara'] = $tmp[$result['Concepto']['codigo']]['formula'];
				}
			}
		}
		return parent::afterFind($results, $primary);
	}
}
?>