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
	$id = $v['id'];
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose_1", "imagen"=>array("nombre"=>"usuarios.gif", "alt"=>"Usuarios"), "url"=>'../grupos/usuarios');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose_2", "imagen"=>array("nombre"=>"menus.gif", "alt"=>"Menus"), "url"=>'../grupos/menus');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose_3", "imagen"=>array("nombre"=>"parametros.gif", "alt"=>"Parametros"), "url"=>'../grupos/parametros');
	$fila[] = array("model"=>"GruposUsuario", "field"=>"id", "valor"=>$v['GruposUsuario']['id'], "write"=>$v['GruposUsuario']['write'], "delete"=>$v['GruposUsuario']['delete']);
	$fila[] = array("model"=>"Grupo", "field"=>"nombre", "valor"=>$v['nombre']);
 	$fila[] = array("model"=>"GruposUsuario", "field"=>"estado", "valor"=>$v['estado']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"grupos_usuarios", "action"=>"add", "GruposUsuario.usuario_id"=>$this->data['Usuario']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Grupos", "cuerpo"=>$cuerpo));

?>