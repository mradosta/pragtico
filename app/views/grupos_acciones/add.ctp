<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['GruposAccion.id'] = array();
$campos['GruposAccion.grupo_id'] = array("options"=>$grupos, "type"=>"checkboxMultiple");
$fieldsets[] = array("campos"=>$campos);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"relacion entre accion y grupo", "imagen"=>"acciones.gif")));


$cuerpoT1 = $cuerpoT2 = null;
foreach($datosIzquierda as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Bar", "field"=>"id", "valor"=>$k);
	$fila[] = array("model"=>"Bar", "field"=>"foo", "valor"=>$v);
	$cuerpoT1[] = $fila;
}

foreach($datosDerecha as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Bar", "field"=>"id", "valor"=>$k);
	$fila[] = array("model"=>"Bar", "field"=>"foo", "valor"=>$v);
	$cuerpoT2[] = $fila;
}

$t1 = $this->renderElement("tablas_from_to/tabla", array("cuerpo"=>$cuerpoT1, "encabezados"=>array("Nombre"), "opciones"=>array("class"=>"izquierda")));
$t2 = $this->renderElement("tablas_from_to/tabla", array("cuerpo"=>$cuerpoT2, "encabezados"=>array("Nombre"), "opciones"=>array("class"=>"derecha")));

$tablas = $formulario->bloque(am($t1, $t2), array("div"=>array("id"=>"tablasFromTo")));

$botones[] = $formulario->input("Form.accion", array("type"=>"hidden", "id"=>"accion", "value"=>"grabar"));
$botones[] = $formulario->button("Cancelar", array("class"=>"cancelar", "onClick"=>"document.getElementById('accion').value='cancelar';form.submit();"));
$botones[] = $formulario->submit("Grabar", array("onclick"=>"document.getElementById('accion').value='grabar';"));

$buscar = $formulario->input('Accion.nombre', array("type"=>"autocomplete", "verificarRequerido"=>false, "onItemSelect"=>array("url"=>"actualizarTablaIzquierda", "update"=>"tablaFromToIzquierda"), "div"=>false));
$contenido = $formulario->bloque("", array("div"=>array("class"=>"clear")));
$contenido .= $formulario->bloque($buscar . $tablas, array("fieldset"=>array("imagen"=>"acciones.gif", "legend"=>"Asignar Acciones permitidas al/los Grupo/s seleccionado/s")));
$contenido .= $formulario->bloque("", array("div"=>array("class"=>"clear")));
$acciones = $formulario->bloque($botones, array("div"=>array("id"=>"botones", "class"=>"botones")));


/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $formulario->form($fieldset . $contenido . $acciones);


echo $ajax->jsPredefinido(array("tipo"=>"tablasFromTo"));
echo $formulario->codeBlock("inicializar();");
?>