<?php 
$codigo_html = "";
if(empty($content_for_layout)) {
	$content_for_layout = "No tiene privilegios para realizar esta accion.";
}

$mensaje = "<p class='session_titulo_error'>" . $formulario->image("permisos.gif") . " " . $content_for_layout . "</p>";

if(!empty($errores)) {
	$mensaje .= "<p>" . $errores['errorDescripcion'] . "</p>";
}
$mensaje .= "<p class='session_aceptar'>" . $formulario->link("Aceptar", "#", array("onclick"=>"return ocultarSessionFlash();", "class"=>"link_boton", "title"=>"Aceptar")) . "</p>";
$mensaje .= $formulario->bloque("", array("div"=>array("class"=>"clear")));
$codigo_html .= $formulario->bloque($mensaje, array("caja_redondeada"=>true));
$codigo_html = $formulario->bloque($codigo_html, array("div"=>array("id"=>"session_flash", "class"=>"session_flash", "style"=>"display:none;")));
$codigo_html .= $javascript->codeBlock("centrarYMostrarSessionFlash();
function centrarYMostrarSessionFlash(){

	var elDivDelFlash = document.getElementById('session_flash');
	if (elDivDelFlash) {
		var top=((screen.availHeight-elDivDelFlash.offsetHeight)/2)-150;
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