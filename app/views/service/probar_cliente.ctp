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
 

echo $formulario->input("Prueba.wsdl", array("style"=>"width:100%; height:400px;", "type"=>"textarea", "value"=>$pruebas['wsdl']));
echo $formulario->bloque("", array("div"=>array("class"=>"clear")));
echo $formulario->input("Prueba.retorno", array("style"=>"width:100%; height:400px;", "type"=>"textarea", "value"=>$pruebas['retorno']));
echo $formulario->bloque("", array("div"=>array("class"=>"clear")));













?>