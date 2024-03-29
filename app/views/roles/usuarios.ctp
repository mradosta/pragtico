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
foreach ($this->data['Usuario'] as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => 'RolesUsuario', 'field' => 'id', 'valor' => $v['RolesUsuario']['id'], 'write' => $v['RolesUsuario']['write'], 'delete' => $v['RolesUsuario']['delete']);
	$fila[] = array('model' => 'Usuario', 'field' => 'nombre', 'valor' => $v['nombre']);
	$fila[] = array('model' => 'Usuario', 'field' => 'nombre_completo', 'valor' => $v['nombre_completo']);
 	$fila[] = array('model' => 'Usuario', 'field' => 'ultimo_ingreso', 'valor' => $v['ultimo_ingreso']);
 	$fila[] = array('model' => 'Usuario', 'field' => 'estado', 'valor' => $v['estado']);
	$cuerpo[] = $fila;
}

$url = array('controller' => "roles_usuarios", 'action' => 'add', "RolesUsuario.rol_id"=>$this->data['Rol']['id']);
echo $this->element('desgloses/agregar', array('url' => $url, 'titulo' => "Usuarios", 'cuerpo' => $cuerpo));

?>