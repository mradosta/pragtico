<?php
/**
* TODO
* Que no se habra dentro de una popup cuando esta en una lov.
*/
$acciones = array();
if(!empty($url) && !isset($url[0])) {
	$tmp = $url;
	$url = false;
	$url[] = $tmp;
}

if(!empty($url)) {
	foreach($url as $v) {
		if(!isset($v['texto']) && $v['action'] === "add") {
			$texto = "Nuevo";
		}
		else {
			$texto = $v['texto'];
			unset($v['texto']);
		}
		$acciones[] = $formulario->link($texto, $v, array("class"=>"link_boton"));
	}
}

/**
* Creo la tabla con las opciones por default para un desglose.
*/
$opcionesTablaDefault =  array("tabla"=>
							array(	"eliminar"			=>true,
									"ordenEnEncabezados"=>false,
									"modificar"			=>true,
									"seleccionMultiple"	=>false,
									"mostrarEncabezados"=>true,
									"zebra"				=>false,
									"mostrarIds"		=>false));
if(!empty($opcionesTabla)) {
	$opcionesTabla = array_merge($opcionesTablaDefault, $opcionesTabla);
}
else {
	$opcionesTabla = $opcionesTablaDefault;
}

if(empty($texto)) {
	$texto = "";
}

$codigoHtml = $formulario->tag("span", $titulo, array("class"=>"titulo"));
if(isset($acciones)) {
	$codigoHtml .= $formulario->tag("span", $acciones, array("class"=>"acciones"));
}
$codigoHtml = $formulario->tag("div", $codigoHtml, array("class"=>"cabecera"));
echo $formulario->tag("div", $codigoHtml . $formulario->tag("div", $formulario->tabla(array_merge(array('cuerpo' => $cuerpo), $opcionesTabla)), array("class"=>"tabla")), array("class"=>"unica"));
?>