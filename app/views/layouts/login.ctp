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
 
header("Content-Type: text/html");

/**
* Si hay algo que mostrar en la session, lo obtengo para mostralo luego.
*/
ob_start();
$session->flash();
$flash = ob_get_clean();

$codigo_html[] = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
$codigo_html[] = "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
$codigo_html[] = "<head>";
$codigo_html[] = "\t<title>" . $title_for_layout . "</title>";
$codigo_html[] = "\t" . $html->charset('UTF-8');
$codigo_html[] = "\t<link rel='icon' href='" . $this->webroot . "favicon.ico' type='image/x-icon'/>";
$codigo_html[] = "\t<link rel='shortcut icon' href='" . $this->webroot . "favicon.ico' type='image/x-icon'/>";

$html->css("aplicacion.default.screen", null, array("media"=>"screen"), false);
$formulario->addScript("default", "links");
$formulario->addScript("jquery", "links");
$codigo_html[] = $asset->scripts_for_layout();
$codigo_html[] = "</head>";

$codigo_html[] = "\n<body>";
$codigo_html[] = $flash;
$codigo_html[] = $formulario->tag("div", $content_for_layout, array("class"=>"login"));
$links[] = $formulario->link($formulario->image("cake.power.gif"), "http://www.cakephp.org", array("alt"=>"CakePhp"));
$links[] = $formulario->link($formulario->image("firefox.gif"), "http://www.spreadfirefox.com/node&id=0&t=308", array("alt"=>"Firefox 3"));
$codigo_html[] = $formulario->tag("div", $links, array("class"=>"links_externos"));
$codigo_html[] = $cakeDebug;
$codigo_html[] = "</body>";
$codigo_html[] = "</html>";

echo implode("\n", $codigo_html);
?>