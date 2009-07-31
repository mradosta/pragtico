<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a la relacion que existe
 * entre los convenios colectivos y los conceptos.
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
 * La clase encapsula la logica de acceso a datos asociada a la relacion que existe
 * entre los convenios colectivos y los conceptos.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class ConveniosConcepto extends AppModel {

    protected $__permissions = '508';

    var $breadCrumb = array('format'    => '%s de %s',
                            'fields'    => array('Concepto.nombre', 'Convenio.nombre'));
    
    var $validate = array(
        'convenio_id' => array(
            array(
                'rule'      => VALID_NOT_EMPTY,
                'message'   => 'Debe seleccionar el convenio colectivo.')
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
	
	var $belongsTo = array('Convenio', 'Concepto');

}
?>
