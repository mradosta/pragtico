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
  
$codigo_html = "";
$mensaje = "<p class='session_titulo_warning'>" . $appForm->image('warnings.gif') . " " . $content_for_layout . "</p>";

$mensaje .= "<p class='session_aceptar'>" . $appForm->link("Aceptar", "#", array("onclick"=>"return ocultarSessionFlash();", "class"=>"link_boton", "title"=>"Aceptar")) . "</p>";
$mensaje .= $appForm->bloque("", array("div"=>array("class"=>"clear")));
$codigo_html .= $appForm->bloque($mensaje, array("caja_redondeada"=>true));
$codigo_html = $appForm->bloque($codigo_html, array("div"=>array("id"=>"session_flash", "class"=>"session_flash", "style"=>"display:none;")));
$codigo_html .= $javascript->codeBlock("centrarYMostrarSessionFlash();
function centrarYMostrarSessionFlash(){

	var elDivDelFlash = document.getElementById('session_flash');
	if (elDivDelFlash) {
		var top=((screen.availHeight-elDivDelFlash.offsetHeight)/2)-200;
		var left=((screen.availWidth-elDivDelFlash.offsetWidth)/2)-200;

		elDivDelFlash.style.display = 'block';
		elDivDelFlash.style.left = left;
		elDivDelFlash.style.top = top;
	}

	setTimeout('ocultarSessionFlash()',6000);
	return false;
}	
");

echo $codigo_html;
?>