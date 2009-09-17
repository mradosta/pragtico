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
* Si hay algo que mostrar en la session, lo obtengo para mostralo luego.
*/
ob_start();
$session->flash();
$flash = ob_get_clean();

$codigo_html[] = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
$codigo_html[] = "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
$codigo_html[] = "<head>";
$codigo_html[] = "<title>Login</title>";
$codigo_html[] = $html->charset('UTF-8');
$codigo_html[] = "<link rel='icon' href='" . $this->webroot . "favicon.ico' type='image/x-icon'/>";
$codigo_html[] = "<link rel='shortcut icon' href='" . $this->webroot . "favicon.ico' type='image/x-icon'/>";

$appForm->addScript("

    /** Set focus on element */
    if (jQuery('#UsuarioLoginGroup').length > 0) {
        jQuery('#UsuarioLoginGroup').focus();
    } else {
        jQuery('#UsuarioLoginNombre').focus();
    }

    /** Binds enter key to sumbit form */
    jQuery('#UsuarioLoginNombre, #UsuarioLoginClave, #UsuarioLoginGroup').keypress(function (e) {
        if (e.which == 13) {
            jQuery('#form').submit();
        }
    });
");
        
$html->css('aplicacion.default.screen.min', null, array('media' => 'screen'), false);
$js[] = 'jquery/jquery-1.3.2.min';
$js[] = 'jquery/jquery.cookie.min';
$js[] = 'jquery/jquery.accordion.min';
$js[] = 'jquery/jquery.checkbox.min';
$js[] = 'jquery/jquery.simplemodal.min';
$js[] = 'jquery/jquery.form.min';
$js[] = 'jquery/jquery.sprintf.min';
$js[] = 'default.min';
$appForm->addScript($js, 'links');
$codigo_html[] = $asset->scripts_for_layout();
$codigo_html[] = '</head>';

$codigo_html[] = '<body>';
$codigo_html[] = $flash;
$codigo_html[] = $appForm->tag('div', $content_for_layout, array('class' => 'login'));
$links[] = $appForm->link($appForm->image('cake.power.gif', array('alt' => 'CakePhp')), 'http://www.cakephp.org');
$links[] = $appForm->link($appForm->image('firefox.gif', array('alt' => 'Firefox 3')), 'http://www.spreadfirefox.com/node&id=0&t=308');
$codigo_html[] = $appForm->tag('div', $links, array('class' => 'links_externos'));
$codigo_html[] = $cakeDebug;
$codigo_html[] = '</body>';
$codigo_html[] = '</html>';

echo implode("\n", $codigo_html);
?>