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
 * Creo los campos de ingreso de datos.
$usuario = $formulario->input("Usuario.loginNombre", array("label"=>"Nombre de Usuario"));
$campos[] = $formulario->bloque($usuario, array("div"=>array("class"=>"izquierda")));
$clave = $formulario->input("Usuario.loginClave", array("type"=>"password", "label"=>"Clave"));
$campos[] = $formulario->bloque($clave, array("div"=>array("class"=>"izquierda")));
$ingresar = $formulario->submit("Ingresar");
$campos[] = $formulario->bloque($ingresar, array("div"=>array("class"=>"derecha")));
$campos[] = $formulario->bloque("", array("div"=>array("class"=>"clear")));

$bloques[] = $formulario->bloque($formulario->image("login.gif"), array("div"=>array("class"=>"centro")));
$bloques[] = $formulario->bloque("&nbsp;", array("div"=>array("class"=>"clear")));
$bloques[] = $formulario->bloque($campos);
 */

$usuario = $formulario->input("Usuario.loginNombre", array("label"=>"Usuario", "tabindex"=>"1"));
$clave = $formulario->input("Usuario.loginClave", array("type"=>"password", "label"=>"Clave"));
$ingresar = $formulario->submit("Ingresar");

$bloques = $usuario . $clave . $ingresar;


/**
 * creo el formulario
 */
$form = $formulario->form($bloques, array("action"=>"login"));

echo $formulario->tag("div", $form, array("class"=>"ingreso"));

?>