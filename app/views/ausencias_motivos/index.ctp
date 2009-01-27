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
$condiciones['Condicion.Banco-nombre'] = array();
$fieldsets[] = array('campos' => $condiciones);
$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('legend' => "Motivos de las Ausencias", 'imagen' => 'ausencias_motivos.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => 'AusenciasMotivo', 'field' => 'id', 'valor' => $v['AusenciasMotivo']['id'], 'write' => $v['AusenciasMotivo']['write'], 'delete' => $v['AusenciasMotivo']['delete']);
	$fila[] = array('model' => 'AusenciasMotivo', 'field' => 'motivo', 'valor' => $v['AusenciasMotivo']['motivo']);
	$fila[] = array('model' => 'Situacion', 'field' => 'nombre', 'valor' => $v['Situacion']['nombre'], "nombreEncabezado" => "Situacion");
	$fila[] = array('model' => 'AusenciasMotivo', 'field' => 'tipo', 'valor' => $v['AusenciasMotivo']['tipo']);
	$cuerpo[] = $fila;
}

echo $this->element('index/index', array('condiciones' => $fieldset, 'cuerpo' => $cuerpo));

?>