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
$out[] = $formulario->tag("span", "Detalles", array("class"=>"titulo"));
$out2[] = "<br />";
foreach ($data as $k=>$v) {
	$out2[] = $k . ": ". $v . "<br />";
}
$out[] = $formulario->tag("div", $out2, array("class"=>"tabla"));
echo $formulario->tag("div", $out, array("class"=>"unica"));

?>