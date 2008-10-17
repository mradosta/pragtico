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
$mensaje[] = $formulario->image("error_icono_naranja.gif");
$mensaje[] = $formulario->tag("span", $content_for_layout, array("class"=>"contenido"));

if(!empty($errores['errorDescripcion'])) {
	$erroresTmp[] = $formulario->tag("span", "Detalles (el registro no se ha modificado)", array("class"=>"titulos"));
	$erroresTmp[] = $formulario->tag("span", $errores['errorDescripcion'], array("class"=>"detalle"));
	if(!empty($dbError['errorDescripcionAdicional'])) {
		$erroresTmp[] = $formulario->tag("span", $errores['errorDescripcionAdicional'], array("class"=>"detalle"));
	}
	echo $formulario->tag("div", $erroresTmp, array("class"=>"session_flash session_flash_error_detalle"));
}
echo $formulario->tag("div", $mensaje, array("class"=>"session_flash session_flash_error"));

$js = "
	jQuery('.session_flash img').attr('style', 'cursor:pointer');
	jQuery('.session_flash img').attr('alt', 'Ver Detalle');
	jQuery('.session_flash img').attr('title', 'Ver Detalle');
	jQuery('.session_flash img').bind('click', function() {
		jQuery('.session_flash_error_detalle').fadeIn('slow');
	});
	jQuery('.session_flash .link_boton').bind('click', vOcultar);
";

//$this->addScript($js);
$formulario->addScript($js);

?>