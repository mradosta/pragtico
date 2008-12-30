<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.views
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Siniestrado-codigo'] = array();
$condiciones['Condicion.Siniestrado-nombre'] = array();
$fieldsets[] = array('campos' => $condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array('fieldset' => array('imagen' => 'siniestrados.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => "Siniestrado", 'field' => "id", 'valor' => $v['Siniestrado']['id'], "write"=>$v['Siniestrado']['write'], "delete"=>$v['Siniestrado']['delete']);
	$fila[] = array('model' => "Siniestrado", 'field' => "codigo", 'valor' => $v['Siniestrado']['codigo']);
	$fila[] = array('model' => "Siniestrado", 'field' => "nombre", 'valor' => $v['Siniestrado']['nombre']);
	$cuerpo[] = $fila;
}

echo $this->element('index/index', array('condiciones' => $fieldset, 'cuerpo' => $cuerpo));

?>