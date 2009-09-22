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
        
$codigo_html[] = $html->css('aplicacion.default.screen.min', 'stylesheet', array('media' => 'all'));
$js[] = 'jquery/jquery-1.3.2.min';
$js[] = 'jquery/jquery.cookie.min';
$js[] = 'jquery/jquery.accordion.min';
$js[] = 'jquery/jquery.checkbox.min';
$js[] = 'jquery/jquery.simplemodal.min';
$js[] = 'jquery/jquery.form.min';
$js[] = 'jquery/jquery.sprintf.min';
$js[] = 'default.min';
$codigo_html[] = $javascript->link($js);

$View = ClassRegistry::getObject('view');
if (!empty($View->__jsCodeForReady)) {
    $codigo_html[] = $javascript->codeBlock(sprintf('jQuery(document).ready(function($) {%s});', implode("\n", $View->__jsCodeForReady)));
}
$codigo_html[] = '</head>';

if (!empty($View->__jsCodeForHeader)) {
    $codigo_html[] = $javascript->codeBlock(sprintf('jQuery(document).ready(function($) {%s});', implode("\n", $View->__jsCodeForHeader)));
}

$codigo_html[] = '<body>';
$codigo_html[] = $flash;
$links = null;
$links[] = $appForm->link('Acerca de Pragtico', 'http://www.pragtico.com.ar/wiki', array('tabindex' => '50'));
$links[] = $appForm->link('Manual', 'http://www.pragtico.com.ar/wiki/index.php/Manual_de_Usuario', array('tabindex' => '51'));
$links[] = $appForm->link('Preguntas Frecuentes', 'http://www.pragtico.com.ar/wiki/index.php/FAQ', array('tabindex' => '52'));
$links[] = $appForm->link('Contactenos', 'http://www.pragtico.com.ar/wiki/index.php/Especial:Contactar', array('tabindex' => '53'));
$codigo_html[] = $appForm->tag('div', $appForm->tag('div', $links, array('class' => 'tabs')) . $content_for_layout, array('class' => 'login'));
$links = null;
$links[] = $appForm->link($appForm->image('logo_pragmatia.jpg', array('alt' => 'Pragmatia')), 'http://www.pragmatia.com');
$links[] = $appForm->link($appForm->image('cake.power.gif', array('alt' => 'CakePhp')), 'http://www.cakephp.org');
$links[] = $appForm->link($appForm->image('firefox.png', array('alt' => 'Descargar Firefox 3.5')), 'http://www.spreadfirefox.com/node&id=0&t=308');
$codigo_html[] = $appForm->tag('div', $links, array('class' => 'links_externos'));
$codigo_html[] = $cakeDebug;
$codigo_html[] = '</body>';
$codigo_html[] = '</html>';

echo implode("\n", $codigo_html);
?>