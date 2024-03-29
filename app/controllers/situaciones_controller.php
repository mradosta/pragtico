<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las situaciones (SICOSS).
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
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
 * La clase encapsula la logica de negocio asociada a las situaciones (SICOSS).
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class SituacionesController extends AppController {

    var $paginate = array(
        'order' => array(
            'Situacion.nombre' => 'asc'
        )
    );
    
/**
 * ausencias_motivos.
 * Muestra via desglose los motivos de ausencias relacionados a esta situacion.
 */
	function ausencias_motivos($id) {
		$this->Situacion->contain('AusenciasMotivo');
		$this->data = $this->Situacion->read(null, $id);
	}
	
}
?>