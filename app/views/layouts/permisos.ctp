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
 

$mensaje[] = $formulario->tag("span", $formulario->link("Cerrar", null, array("class"=>"link_boton", "title"=>"Cerrar")));
$mensaje[] = $formulario->image("permisos.gif");
$mensaje[] = $formulario->tag("span", "Usted no tiene permisos suficientes para realizar esta operacion.", array("class"=>"contenido"));
$erroresTmp[] = $formulario->tag("span", "Detalles (el registro no se ha modificado)", array("class"=>"titulos"));
echo $formulario->tag("div", $mensaje, array("class"=>"session_flash session_flash_error"));

$js = "
	jQuery('.session_flash .link_boton').bind('click', vOcultar);
";

$formulario->addScript($js);

?>