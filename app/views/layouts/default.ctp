<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.views
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */

/**
* Si hay algo que mostrar en la session, lo obtengo para mostralo luego.
*/
ob_start();
$session->flash();
$flash = ob_get_clean();
$barra = $this->element('layout' . DS . 'barra');

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
//$codigo_html .= $html->css(array( "aplicacion.default.screen", "jquery.jqmodal", "liquidcorners/liquidcorners"),
//$html->css(array( "aplicacion.default.screen",
//                  "jquery.jqmodal"), null, array("media"=>"screen"), false);

/**
$css[] = "aplicacion.default.screen";
$css[] = "jquery.autocomplete";
*/
//$css[] = "basic";
$css = null;
$css[] = 'aplicacion.default.screen';
//$css[] = "theme/ui.all";
//$css[] = "theme/ui.base";
//$css[] = "theme/ui.core";
//$css[] = "theme/ui.theme";
//$css[] = "theme/ui.dialog";
//if($appForm->traerPreferencia("lov_apertura") != "popup") {
//  $css[] = "jquery.jqmodal";
//}

$html->css($css, null, array('media' => 'screen'), false);
$js = null;
$js[] = 'jquery/jquery-1.3.2.min';
$js[] = 'jquery/jquery.cookie';
$js[] = 'jquery/jquery.accordion';
$js[] = 'jquery/jquery.checkbox';
$js[] = 'jquery/jquery.simplemodal';
$js[] = 'jquery/jquery.form';
$js[] = 'jquery/jquery.sprintf';
$js[] = 'default';
$js[] = 'datetimepicker';
$js[] = 'jquery.flydom'; // lo usa en carga drapida de conceptos desde convenios
$appForm->addScript($js, 'links');


//$appForm->addScript("jquery.autocomplete", "links");

        
//$appForm->addScript("jquery.jqmodal", "links");
//$appForm->addScript("jquery.simplemodal", "links");
//$appForm->addScript("jquery-ui-personalized-1.5.3", "links");
//$appForm->addScript("jquery/jquery-ui-1.7.custom", "links");

//$appForm->addScript("jquery/ui.core", "links");
//$appForm->addScript("jquery/ui.accordion", "links");
//$appForm->addScript("jquery/jquery.accordion", "links");

//$appForm->addScript("jquery/jquery.accordion", "links");
//$appForm->addScript("basic", "links");
//$appForm->addScript("jquery.jeditable", "links");

//$appForm->addScript("jquery.form", "links");



//$appForm->addScript("jquery.flydom", "links");
//$session->read('__actualMenu')
$appForm->addScript('

    jQuery.extend({
        bindMultipleCheckBoxManipulation: function(scope) {

    
    
    //jQuery.prototype.bindMultipleCheckBoxManipulation = function(scope) {
        if (scope == undefined) {
            scope = "#index";
        }
        //console.log(scope);
        
        jQuery(scope + " table .seleccionarTodos").click(
            function() {
                jQuery(".tabla :checkbox").checkbox("seleccionar");
                return false;
            }
        );
        jQuery(scope + " table .deseleccionarTodos").click(
            function() {
                jQuery(".tabla :checkbox").checkbox("deseleccionar");
                return false;
            }
        );
        jQuery(scope + " table .invertir").click(
            function() {
                jQuery(".tabla :checkbox").checkbox("invertir");
                return false;
            }
        );
    //}
        }
    });
    jQuery.bindMultipleCheckBoxManipulation();
    
        

        jQuery(".expand_text_area").click(function() {
            var textarea = "#" + jQuery("textarea", jQuery(this).parent()).attr("id");
            if (jQuery(this).hasClass("colapse_text_area")) {
                jQuery(textarea).parent().css("width", "365px");
                jQuery(textarea).css("width", "196px").css("background-image", "url(" + jQuery.url("css/img/textarea.gif") + ")");
                jQuery(this).removeClass("colapse_text_area");
                jQuery(this).addClass("expand_text_area");
            } else {
                jQuery(textarea).parent().css("width", "720px");
                jQuery(textarea).css("width", "565px").css("background-image", "url(" + jQuery.url("css/img/wide_textarea.gif") + ")");
                jQuery(this).addClass("colapse_text_area");
                jQuery(this).removeClass("expand_text_area");
            }
        });
        

    /** Useful function to avoid using Router::url everywere */
    jQuery.url = function(url) {
        return jQuery("#base_url").val() + url;
    }


    /** Creates the menu */
    jQuery(".menu").accordion({
        header: "a.header",
        active: parseInt(jQuery.cookie("menu_cookie"))
    });


    /** Show / Hide conditions */
    jQuery("#hideConditions").bind("click",
        function() {
            jQuery(".conditions_frame").toggle();
            if (jQuery(".conditions_frame").is(":visible")) {
                jQuery.cookie("conditionsFrameCookie", "true");
                jQuery("#hideConditions > img").attr("src", jQuery.url("img/") + "pinchado.gif");
            } else {
                jQuery.cookie("conditionsFrameCookie", "false");
                jQuery("#hideConditions > img").attr("src", jQuery.url("img/") + "sin_pinchar.gif");
            }
        }
    );

    if (jQuery.cookie("conditionsFrameCookie") == "false") {
        jQuery(".conditions_frame").hide();
        jQuery("#hideConditions > img").attr("src", jQuery.url("img/") + "sin_pinchar.gif");
    }


    /** Cretes an object (key => value) from a string
        The form of the string should be:
        str = "paramNameA: aaaaa; paramNameB: cccc";
    */
    jQuery.makeObject = function(str, separator) {
        if (separator == undefined) {
            separator = ";";
        }

        var items = {};
        jQuery.each(str.split(separator),
            function() {
                var tmp = this.split(":");
                //items[tmp[0]] = tmp[1].trim();
                items[tmp[0]] = tmp[1];
            }
        );
        return items;
    }


        
    //jQuery("#opened_lov_options").val("");
    jQuery(".seleccionar").hide();

    /** Binds event to every lov caller */
    jQuery(".lupa_lov").click(
        function() {
    
            jQuery("#opened_lov_options").val(jQuery(this).attr("longdesc"));
            var params = jQuery.makeObject(jQuery("#opened_lov_options").val());

            jQuery("#lov").load(
                jQuery.url(params["controller"] + "/" + params["action"])).modal({
                    containerCss: {
                        height: 450,
                        width: 850,
                        position: "absolute",
                        paddingLeft: 4
                    }
                });
        }
    );
');

$codigo_html[] = $asset->scripts_for_layout();
$codigo_html[] = '</head>';
$codigo_html[] = '<body>';
$codigo_html[] = '<input id="base_url" type="hidden" value="' . Router::url('/') . '" />';
$codigo_html[] = '<div id="lov" class="index"></div>';
/** When opening a Lov Control, all necessary options are temporaly saved in this hidden text field */
$codigo_html[] = '<input id="opened_lov_options" type="hidden" />';

//$menu = $this->element('layout' . DS . 'menu', array('cache' => '+1 day'));
$menu = $this->element('layout' . DS . 'menu');

$codigo_html[] = $flash;
$codigo_html[] = $this->element('layout' . DS . 'encabezado');
$codigo_html[] = $barra;
//$contenido = $appForm->tag('div', '', array('id' => 'lov', 'class' => 'index'));
$contenido = $appForm->tag('div', $content_for_layout, array('class' => 'cuerpo'));
$codigo_html[] = $appForm->tag('div', $menu . $contenido, array('class' => 'contenido'));
$codigo_html[] = $cakeDebug;
$codigo_html[] = '</body>';
$codigo_html[] = '</html>';

echo implode('', $codigo_html);
?>