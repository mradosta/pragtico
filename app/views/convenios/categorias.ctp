<?php
/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['ConveniosCategoria'] as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"desglose", "id"=>$v['id'], "update"=>"desglose_1", "imagen"=>array("nombre"=>"historicos.gif", "alt"=>"Historicos"), "url"=>"../convenios_categorias/historicos/");
	$fila[] = array("model"=>"ConveniosCategoria", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"ConveniosCategoria", "field"=>"nombre", "valor"=>$v['nombre'], "nombreEncabezado"=>"Categoria");
	$fila[] = array("model"=>"ConveniosCategoria", "field"=>"costo", "valor"=>$v['costo'], "tipoDato"=>"moneda");
	$fila[] = array("model"=>"ConveniosCategoria", "field"=>"jornada", "valor"=>$v['jornada']);
	$fila[] = array("model"=>"ConveniosCategoria", "field"=>"observacion", "valor"=>$v['observacion']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"convenios_categorias", "action"=>"add", "ConveniosCategoria.convenio_id"=>$this->data['Convenio']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Categorias", "cuerpo"=>$cuerpo));


?>