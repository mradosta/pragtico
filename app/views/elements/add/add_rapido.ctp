<?php
/**
* Creo el element que se mostrar en caso de errores al guardar (por ejmplo, clave duplicada).
*/
$erroresDb = $this->renderElement('error_base_datos');


$t1 = $this->renderElement("tablas_from_to/tabla", array('cuerpo' => $cuerpoTablaIzquierda, "encabezados"=>$encabezadosTablaIzquierda, "opciones"=>array("class"=>"izquierda")));
$t2 = $this->renderElement("tablas_from_to/tabla", array('cuerpo' => $cuerpoTablaDerecha, "encabezados"=>$encabezadosTablaDerecha, "opciones"=>array("class"=>"derecha")));
$tablas = $formulario->tag("div", am($t1, $t2), array("class"=>"tablasFromTo"));

$autocomplete = "";
if(!empty($busqueda)) {
	$buscar = $formulario->input("Bar.foo", array(	"label"				=> $busqueda['label'],
													"type"				=> "autocomplete",
													"div"				=> false,
													"verificarRequerido"=> false,
			"onItemSelect"=>array(	"url"	=>"actualizarTablaIzquierda",
									"update"=>"tablaFromToIzquierda")));
									
	$autocomplete = $formulario->bloque($buscar, array("div"=>array("class"=>"autocomplete")));
}


$contenido = $formulario->bloque($autocomplete . $tablas . $extra, array('fieldset' => $fieldset));
if(!empty($extra)) {
	if(is_array($extra)) {
		$contenido .= implode("", $extra);
	}
	else {
		$contenido .= $extra;
	}
}


/**
* Agrego un hiden que me indicara desde donde viene.
*/
$contenido .= $formulario->input("Form.tipo", array("type"=>"hidden", "value"=>"addRapido"));

$acciones = $formulario->tag("div", $this->renderElement("add/acciones"), array("class"=>"botones_tablas_from_to"));
$add = $formulario->tag("div", $formulario->form($contenido . $acciones), array("class"=>"unica"));
echo $formulario->tag("div", $add, array("class"=>"add"));

$ajax->jsPredefinido(array("tipo"=>"tablasFromTo"));
$ajax->jsPredefinido(array("tipo"=>"busqueda"));

?>