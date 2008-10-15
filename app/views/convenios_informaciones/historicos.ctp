<?php
/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['ConveniosCategoriasHistorico'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"ConveniosCategoriasHistorico", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Informacion", "field"=>"nombre", "valor"=>$v['Informacion']['nombre']);
	$fila[] = array("model"=>"ConveniosInformacion", "field"=>"valor", "valor"=>$v['valor']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"convenios_categorias_historicos", "action"=>"add", "ConveniosCategoriasHistorico.categoria_id"=>$this->data['ConveniosCategoria']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Informacion Adicional", "cuerpo"=>$cuerpo));

?>