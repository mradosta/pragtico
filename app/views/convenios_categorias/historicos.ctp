<?php
/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
//d($this->data);
foreach ($this->data['ConveniosCategoriasHistorico'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"ConveniosCategoriasHistorico", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Convenio", "field"=>"nombre", "valor"=>$this->data['Convenio']['nombre']);
	$fila[] = array("model"=>"ConveniosCategoria", "field"=>"nombre", "valor"=>$this->data['ConveniosCategoria']['nombre']);
	$fila[] = array("model"=>"ConveniosCategoriasHistorico", "field"=>"desde", "valor"=>$v['desde']);
	$fila[] = array("model"=>"ConveniosCategoriasHistorico", "field"=>"hasta", "valor"=>$v['hasta']);
	$fila[] = array("model"=>"ConveniosCategoriasHistorico", "field"=>"costo", "valor"=>$v['costo'], "tipoDato"=>"moneda");
	$cuerpo[] = $fila;
}

$url = array("controller"=>"convenios_categorias_historicos", "action"=>"add", "ConveniosCategoriasHistorico.convenios_categoria_id"=>$this->data['ConveniosCategoria']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Historico de Categorias", "cuerpo"=>$cuerpo));

?>