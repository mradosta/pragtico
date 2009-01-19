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
$usuario = $appForm->input("Usuario.loginNombre", array("label"=>"Nombre de Usuario"));
$campos[] = $appForm->bloque($usuario, array("div"=>array("class"=>"izquierda")));
$clave = $appForm->input("Usuario.loginClave", array("type"=>"password", "label"=>"Clave"));
$campos[] = $appForm->bloque($clave, array("div"=>array("class"=>"izquierda")));
$ingresar = $appForm->submit("Ingresar");
$campos[] = $appForm->bloque($ingresar, array("div"=>array("class"=>"derecha")));
$campos[] = $appForm->bloque("", array("div"=>array("class"=>"clear")));

$bloques[] = $appForm->bloque($appForm->image('login.gif'), array("div"=>array("class"=>"centro")));
$bloques[] = $appForm->bloque("&nbsp;", array("div"=>array("class"=>"clear")));
$bloques[] = $appForm->bloque($campos);
 */

$usuario = $appForm->input("Usuario.loginNombre", array("label"=>"Usuario", "tabindex"=>"1"));
$clave = $appForm->input("Usuario.loginClave", array("type"=>"password", "label"=>"Clave"));
$ingresar = $appForm->submit("Ingresar");

$bloques = $usuario . $clave . $ingresar;


/**
 * creo el formulario
 */
$form = $appForm->form($bloques, array("action"=>"login"));

echo $appForm->tag("div", $form, array("class"=>"ingreso"));

?>