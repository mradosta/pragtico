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
 * @version			$Revision: 248 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2009-02-03 13:42:43 -0200 (Tue, 03 Feb 2009) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.TrabajadoresDocumento-nombre'] = array();
$fieldsets[] = array('campos' => $condiciones);
$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('legend' => "Documentos", 'imagen' => 'buscar.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k => $v) {
	$fila = null;
	$fila[] = array('model' => 'TrabajadoresDocumento', 'field' => 'id', 'valor' => $v['TrabajadoresDocumento']['id'], 'write' => $v['AusenciasSeguimiento']['write'], 'delete' => $v['AusenciasSeguimiento']['delete']);
	$fila[] = array('model' => 'TrabajadoresDocumento', 'field' => 'nombre', 'valor' => $v['TrabajadoresDocumento']['nombre']);
	$fila[] = array('model' => 'TrabajadoresDocumento', 'field' => 'observacion', 'valor' => $v['TrabajadoresDocumento']['observacion']);
	$fila[] = array('model' => 'TrabajadoresDocumento', 'field' => 'created', 'valor' => $v['TrabajadoresDocumento']['created']);
	$cuerpo[] = $fila;
}

echo $this->element('index/index', array('condiciones' => $fieldset, 'cuerpo' => $cuerpo));

?>