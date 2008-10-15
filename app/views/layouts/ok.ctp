<?php

$mensaje[0] = $formulario->tag("span", $formulario->link("Cerrar", null, array("class"=>"link_boton", "title"=>"Cerrar")));
$mensaje[1] = $formulario->image("ok_icono_verde.gif");
$mensaje[2] = $formulario->tag("span", $content_for_layout, array("class"=>"contenido"));

if(!empty($warnings)) {
	$mensaje[1] = $formulario->image("ok_icono_amarillo.gif");
	foreach($warnings as $k=>$warning) {
		$textoWarnings = null;
		foreach($warning as $w) {
			$textoWarnings[] = $w['warningDescripcion'];
		}
		if(count($warnings) > 1) {
			array_unshift($textoWarnings, "Registro " . $k . ":");
		}
		$mensajeWarning[] = $formulario->tag("span", implode("<br />", $textoWarnings), array("class"=>"detalle"));
	}
	echo $formulario->tag("div", $mensaje ,array("class"=>"session_flash session_flash_warning"));
	array_unshift($mensajeWarning, $formulario->tag("span", "Detalles (No necesariamente significan errores, el registro se ha modificado)", array("class"=>"titulos")));
	echo $formulario->tag("div", $mensajeWarning, array("class"=>"session_flash session_flash_warning_detalle"));
}
else {
	echo $formulario->tag("div", $mensaje ,array("class"=>"session_flash session_flash_ok"));
}


/**
* Si no hay warning, hago que se desaparezca solo el cartel ed aviso, sino, debe hacerlo el usuario para
* asegurarse de que leyo el mensaje de warning.
*/
if(empty($warnings)) {
	$js = "setTimeout(vOcultar, 6000);";
}
else {
	$js = "
		jQuery('.session_flash img').attr('style', 'cursor:pointer');
		jQuery('.session_flash img').attr('alt', 'Ver Detalle');
		jQuery('.session_flash img').attr('title', 'Ver Detalle');
		jQuery('.session_flash img').bind('click', function() {
			jQuery('.session_flash_warning_detalle').fadeIn('slow');
		});
	";
}
$js .= "jQuery('.session_flash .link_boton').bind('click', vOcultar);";

$formulario->addScript($js);
?>