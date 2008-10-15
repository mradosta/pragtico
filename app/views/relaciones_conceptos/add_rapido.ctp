<?php
$cuerpoT1 = $cuerpoT2 = null;
foreach($datosIzquierda as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Concepto", "field"=>"id", "valor"=>$v['Concepto']['id']);
	$fila[] = array("model"=>"Concepto", "field"=>"codigo", "class"=>"oculto", "valor"=>$v['Concepto']['codigo']);
	$fila[] = array("model"=>"Concepto", "field"=>"nombre", "valor"=>$v['Concepto']['nombre']);
	$cuerpoT1[] = $fila;
}

foreach($datosDerecha as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Concepto", "field"=>"id", "valor"=>$v['Concepto']['id']);
	$fila[] = array("model"=>"Concepto", "field"=>"codigo", "class"=>"oculto", "valor"=>$v['Concepto']['codigo']);
	$fila[] = array("model"=>"Concepto", "field"=>"nombre", "valor"=>$v['Concepto']['nombre']);
	$cuerpoT2[] = $fila;
}

$extra = $formulario->input("RelacionesConcepto.relacion_id", array("type"=>"hidden", "value"=>$relacion['Relacion']['id']));

echo $this->renderElement("add/add_rapido", array(
				"cuerpoTablaIzquierda"		=> $cuerpoT1,
				"cuerpoTablaDerecha"		=> $cuerpoT2,
				"extra"						=> $extra,
				"encabezadosTablaIzquierda"	=> array("Nombre"),
				"encabezadosTablaDerecha"	=> array("Nombre"),
				"busqueda"					=> array("label"=>"Concepto"),
				"fieldset"					=> array(	"imagen"=>	"conceptos.gif",
														"legend"=>	"Asignar conceptos a la Relacion (" . $relacion['Empleador']['nombre'] . " - " . $relacion['Trabajador']['apellido'] . " " . $relacion['Trabajador']['nombre'] . ")")
				));
?>