<?php
//$this->Session->write('Config.language', 'spa');
//$this->Session->write('Config.language', 'eng');

$salir = $formulario->link($formulario->image("salir.gif"), array('controller' => 'usuarios', 'action' => 'logout'), array("title"=>__("Logout", true)));
$limpiar = $formulario->link($formulario->image("limpiar.gif"), null, array("id"=>"bandaLimpiar", "title"=>__("Clean all searches", true)));
$cerrar = $formulario->link($formulario->image("cerrar.gif"), null, array("id"=>"bandaCerrarDesgloses", "title"=>__("Close all breakdowns", true)));

$href = router::url("/") . $this->params['controller'];
$formulario->addScript("var bandaLimpiar = function() {ajaxGet('" . $href . "/limpiar_busquedas');jQuery('#accion').attr('value','limpiar');jQuery('#form').submit();}; jQuery('#bandaLimpiar').bind('click', bandaLimpiar)", "ready");
$formulario->addScript("var bandaCerrarDesgloses = function() {ajaxGet('" . $href . "/cerrar_desgloses');window.location.reload(true);}; jQuery('#bandaCerrarDesgloses').bind('click', bandaCerrarDesgloses)", "ready");

$iconos = $formulario->tag("p", $limpiar . $cerrar . $salir);
$banda_izquierda = $formulario->tag("div", $formulario->tag("p", $formulario->getCrumbs()), array("class"=>"banda_izquierda"));
$usuario = $session->read("__Usuario");
$usuario = $formulario->tag("span", $usuario['Usuario']['nombre_completo']);
echo $formulario->tag("div", $banda_izquierda . $usuario . $iconos, array("class"=>"banda"));

?>