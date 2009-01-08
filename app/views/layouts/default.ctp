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
 
//header("Content-Type: text/html");
//$headers = apache_request_headers();
//d($headers);
//header('Cache-Control: private');
//header("Cache-Control: private, max-age=3600");
//header('Pragma: private');
//header("Cache-Control: no-store, no-cache, must-revalidate");
//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

/**
* Si hay algo que mostrar en la session, lo obtengo para mostralo luego.
*/
ob_start();
$session->flash();
$flash = ob_get_clean();

/**
* Hago el render del menu primero que nada, asi me carga los js y css que pudiese tener.
*/
//$menu = $this->element("layout" . DS . "menu", array('cache'=>'+1 day'));
$menu = $this->element("layout" . DS . "menu");
//$encabezado = $this->element("layout" . DS . "encabezado", array('cache'=>'+1 day'));
$encabezado = $this->element("layout" . DS . "encabezado");
$barra = $this->element("layout" . DS . "barra");

$codigo_html[] = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
$codigo_html[] = "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
$codigo_html[] = "<head>";
$codigo_html[] = "\t<title>" . $title_for_layout . "</title>";
$codigo_html[] = "\t" . $html->charset('UTF-8');
$codigo_html[] = "\t<link rel='icon' href='" . $this->webroot . "favicon.ico' type='image/x-icon'/>";
$codigo_html[] = "\t<link rel='shortcut icon' href='" . $this->webroot . "favicon.ico' type='image/x-icon'/>";


/*
$codigo_html .= '\n
        <!-- Additional IE/Win specific style sheet (Conditional Comments) -->
        <!--[if lte IE 7]>
        <link rel="stylesheet" href="jquery.tabs-ie.css" type="text/css" media="screen">
        <![endif]-->';
*/
//$codigo_html .= $html->css(array(	"aplicacion.default.screen", "jquery.jqmodal", "liquidcorners/liquidcorners"),
//$html->css(array(	"aplicacion.default.screen",
//					"jquery.jqmodal"), null, array("media"=>"screen"), false);

/**
$css[] = "aplicacion.default.screen";
$css[] = "jquery.autocomplete";
*/
$css[] = "aplicacion.default.screen";

//if($formulario->traerPreferencia("lov_apertura") != "popup") {
//	$css[] = "jquery.jqmodal";
//}
$html->css($css, null, array("media"=>"screen"), false);

$formulario->addScript("default", "links");
$formulario->addScript("datetimepicker", "links");
$formulario->addScript("jquery", "links");
$formulario->addScript("jquery.autocomplete", "links");
$formulario->addScript("jquery.jqmodal", "links");
$formulario->addScript("jquery.jeditable", "links");
$formulario->addScript("jquery.form", "links");
$formulario->addScript("jquery.flydom", "links");
$formulario->addScript("jquery.maskedinput", "links");
$formulario->addScript("jquery.accordion", "links");
$formulario->addScript("jquery.checkbox", "links");
/*
$javascript->link(array(	"default",
							"datetimepicker",
							"jquery",
							"jquery.autocomplete",
							"jquery.jqmodal",
							"jquery.jeditable",
							"jquery.form",
							"jquery.flydom",
							"jquery.maskedinput",
							"jquery.accordion",
							"jquery.checkbox"), false);
*/
$codigo_html[] = $asset->scripts_for_layout();
$codigo_html[]= "</head>";

$codigo_html[] = "<body>";
$codigo_html[] = $flash;
$codigo_html[] = $encabezado;
$codigo_html[] = $barra;
$contenido = $formulario->tag("div", $content_for_layout, array("class"=>"cuerpo"));
$codigo_html[] = $formulario->tag("div", $menu . $contenido, array("class"=>"contenido"));
$codigo_html[] = $cakeDebug;
$codigo_html[] = "</body>";
$codigo_html[] = "</html>";

echo implode("\n", $codigo_html);
?>