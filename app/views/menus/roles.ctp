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
foreach ($this->data['Grupo'] as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => 'GruposMenu', 'field' => 'id', 'valor' => $v['GruposMenu']['id'], 'write' => $v['GruposMenu']['write'], 'delete' => $v['GruposMenu']['delete']);
	$fila[] = array('model' => 'Grupo', 'field' => 'nombre', 'valor' => $v['nombre']);
	$fila[] = array('model' => 'Grupo', 'field' => 'estado', 'valor' => $v['estado']);
	$cuerpo[] = $fila;
}

$url = array('controller' => "grupos_menus", 'action' => 'add', "GruposMenu.menu_id"=>$this->data['Menu']['id']);
echo $this->element('desgloses/agregar', array('url' => $url, 'titulo' => "Grupos", 'cuerpo' => $cuerpo));

?>