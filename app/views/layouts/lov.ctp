<?php
$codigo_html = "\n<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
$codigo_html .= "\n<html xmlns=\"http://www.w3.org/1999/xhtml\">";

$codigo_html .= "\n<head>";
$codigo_html .= $html->charset('UTF-8');
$codigo_html .= "\n<title>";
$codigo_html .= $title_for_layout;
$codigo_html .= "\n</title>";

$codigo_html .= $html->css(array(	"aplicacion.default.screen", "jquery.jqmodal"),
									null,
									array("media"=>"screen"));

$codigo_html .= $javascript->link(array("default",
										"datetimepicker",
										"jquery",
										"jquery.jqmodal",
										"jquery.form",
										"jquery.flydom",
										"jquery.scrollTo",
										"jquery.maskedinput",
										"jquery.accordion",
										"jquery.checkbox"));


$codigo_html .= "\n</head>";
$codigo_html .= "\n<body>";
$codigo_html .= $javascript->codeBlock("jQuery.noConflict();");

$codigo_html .= $content_for_layout;

if(!empty($this->jsCode)) {
	$codigo_html .= $javascript->codeBlock(implode("", $this->jsCode));
}	

$codigo_html .= "\n</body>";


echo $codigo_html;

?>