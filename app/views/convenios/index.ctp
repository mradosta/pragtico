<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Convenio-numero'] = array();
$condiciones['Condicion.Convenio-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Convenio Colectivo", "imagen"=>"convenios.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$id = $v['Convenio']['id'];
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose1", "imagen"=>array("nombre"=>"categorias.gif", "alt"=>"Categorias"), "url"=>'categorias');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose2", "imagen"=>array("nombre"=>"conceptos.gif", "alt"=>"Conceptos"), "url"=>'conceptos');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose3", "imagen"=>array("nombre"=>"informaciones.gif", "alt"=>"Informaciones"), "url"=>'informaciones');
	$fila[] = array("tipo"=>"accion", "valor"=>$formulario->link($formulario->image("archivo.gif", array("alt"=>"Descargar")), "descargar/" . $id));
	$fila[] = array("model"=>"Convenio", "field"=>"id", "valor"=>$v['Convenio']['id'], "write"=>$v['Convenio']['write'], "delete"=>$v['Convenio']['delete']);
	$fila[] = array("model"=>"Convenio", "field"=>"numero", "valor"=>$v['Convenio']['numero']);
	$fila[] = array("model"=>"Convenio", "field"=>"nombre", "valor"=>$v['Convenio']['nombre']);
	$fila[] = array("model"=>"Convenio", "field"=>"actualizacion", "valor"=>$v['Convenio']['actualizacion'], "nombreEncabezado"=>"Ult. Actualizacion");
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>