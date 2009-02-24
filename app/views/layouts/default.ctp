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
//$css[] = "basic";
$css[] = "aplicacion.default.screen";
//$css[] = "theme/ui.all";
//$css[] = "theme/ui.base";
$css[] = "theme/ui.core";
$css[] = "theme/ui.theme";
$css[] = "theme/ui.dialog";
//if($appForm->traerPreferencia("lov_apertura") != "popup") {
//	$css[] = "jquery.jqmodal";
//}
$html->css($css, null, array("media"=>"screen"), false);

$appForm->addScript("default", "links");
$appForm->addScript("datetimepicker", "links");
$appForm->addScript("jquery", "links");
$appForm->addScript("jquery.autocomplete", "links");
//$appForm->addScript("jquery.jqmodal", "links");
//$appForm->addScript("jquery.simplemodal", "links");
$appForm->addScript("jquery-ui-personalized-1.5.3", "links");
//$appForm->addScript("basic", "links");
//$appForm->addScript("jquery.jeditable", "links");
$appForm->addScript("jquery.form", "links");
$appForm->addScript("jquery.flydom", "links");
$appForm->addScript("jquery.maskedinput", "links");
$appForm->addScript("jquery.accordion", "links");
$appForm->addScript("jquery.checkbox", "links");
$appForm->addScript("jquery.cookie", "links");

//d(Router::url());
$appForm->addScript('
		
	/**
	 * Rebuild table tbody adding breakDowns rows.
	 */
	var buildTable = function(clickedRowId, url, table) {
		var breakDownRowId = "breakdown_row" + url.replace(/\//g, "_");
		var newTbody = jQuery("<tbody/>");
		
		if (table == undefined) {
			table = "table.index";
			jQuery(table + " > tbody > tr").each(

				function() {
					newTbody.append(this);
					
					if (clickedRowId == jQuery(this).attr("charoff")) {
						var td = jQuery("<td/>").attr("colspan", "10");
						td.append(jQuery("<div/>").attr("class", "desglose").load(url, 
							function() {
								jQuery("img.breakdown_icon", this).bind("click", breakdown);
							}
						));
						var tr = jQuery("<tr/>").addClass(breakDownRowId).addClass("breakdown_row").append(td);
						newTbody.append(tr);
					}
				}
			);
			jQuery(table + " > tbody").remove();
			jQuery(table).append(newTbody);
		} else {
	
			table.parent().find("table:first > tbody > tr").each(
				function() {
					newTbody.append(this);
					
					if (clickedRowId == jQuery(this).attr("charoff")) {
						var td = jQuery("<td/>").attr("colspan", "10");
						td.append(jQuery("<div/>").attr("class", "desglose").load(url, 
							function() {
								jQuery("img.breakdown_icon", this).bind("click", breakdown);
							}
						));
						var tr = jQuery("<tr/>").addClass(breakDownRowId).addClass("breakdown_row").append(td);
						newTbody.append(tr);
					}
				}
			);
			table.find("tbody").remove();
			table.append(newTbody);
		}
		
		return false;
	}
	
	
	/**
	 * Delete cookies and hide all breakdown rows.
	 */
	var closeAllBreakdowns = function() {
		jQuery.cookie("breakDowns", null);
		jQuery(".breakdown_row").hide();
		return false;
	}
	jQuery("#closeAllBreakdowns").click(closeAllBreakdowns);
	
	
	/**
	 * If in cookie, must re-open breakdown.
	 */
	var breakDownsCookie = jQuery.cookie("breakDowns");
	if (breakDownsCookie != null) {
		breakDowns = breakDownsCookie.split("|");
		jQuery("img.breakdown_icon").each(
			function() {
				if (jQuery.inArray(this.getAttribute("longdesc"), breakDowns) >= 0) {
					clickedRowId = this.getAttribute("longdesc").split("/").pop();
					buildTable(clickedRowId, this.getAttribute("longdesc"));
				}
			}
		);
	}	
	
	
	/**
	 * Binds click event to breakdown icons.
	 */
 	var breakdown = function() {

		jQuery("div.banda_izquierda > p").text(jQuery("div.banda_izquierda > p").text() + " >> " + jQuery(this).attr("alt"));

		var clickedRowId = jQuery(this).parent().parent().attr("charoff");
		var url = this.getAttribute("longdesc");
		
		var breakDownsCookie = jQuery.cookie("breakDowns");
		if (breakDownsCookie != null) {
			breakDowns = breakDownsCookie.split("|");
		} else {
			breakDowns = Array();
		}
		
		var breakDownRowId = "breakdown_row" + url.replace(/\//g, "_");
		if (jQuery("." + breakDownRowId).length) {
			jQuery("." + breakDownRowId).toggle();
			if (!jQuery("." + breakDownRowId).is(":visible")) {
				delete breakDowns[jQuery.inArray(url, breakDowns)];
				jQuery.cookie("breakDowns", breakDowns.join("|"));
			}
		} else {
			breakDowns.push(url);
			jQuery.cookie("breakDowns", breakDowns.join("|"));
			var table = jQuery(this).parent().parent().parent().parent();
			buildTable(clickedRowId, url, table);
		}
	}
	jQuery("img.breakdown_icon").bind("click", breakdown);
	
	

');
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
$contenido = $appForm->tag("div", $content_for_layout, array("class"=>"cuerpo"));
$codigo_html[] = $appForm->tag("div", $menu . $contenido, array("class"=>"contenido"));
$codigo_html[] = $cakeDebug;
$codigo_html[] = "</body>";
$codigo_html[] = "</html>";

echo implode("\n", $codigo_html);
?>