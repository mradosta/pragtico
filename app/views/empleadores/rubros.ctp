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
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Rubro'] as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => 'Rubro', 'field' => 'id', 'valor' => $v['id'], 'write' => $v['write'], 'delete' => $v['delete']);
	$fila[] = array('model' => 'Rubro', 'field' => 'nombre', 'valor' => $v['nombre']);
	$cuerpo[] = $fila;
}

$url = array('controller' => "empleadores_rubros", 'action' => 'add', "EmpleadoresRubro.empleador_id"=>$this->data['Empleador']['id']);
echo $this->element('desgloses/agregar', array('url' => $url, 'titulo' => "Rubros", 'cuerpo' => $cuerpo));

?>