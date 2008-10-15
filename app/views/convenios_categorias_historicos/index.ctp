<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.ConveniosCategoriasHistorico-convenios_categoria_id'] = array(
																				"label"	=>"Categoria",
																				"lov"	=>array("controller"	=>"convenios_categorias",
																				"camposRetorno"	=>array(	"Convenio.nombre",
																											"ConveniosCategoria.nombre")));
$condiciones['Condicion.ConveniosCategoriasHistorico-desde'] = array();
$condiciones['Condicion.ConveniosCategoriasHistorico-hasta'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Historicos de Categorias", "imagen"=>"historicos.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"ConveniosCategoriasHistorico", "field"=>"id", "valor"=>$v['ConveniosCategoriasHistorico']['id'], "write"=>$v['ConveniosCategoriasHistorico']['write'], "delete"=>$v['ConveniosCategoriasHistorico']['delete']);
	$fila[] = array("model"=>"Convenio", "field"=>"nombre", "valor"=>$v['ConveniosCategoria']['Convenio']['nombre'], "nombreEncabezado"=>"Convenio");
	$fila[] = array("model"=>"ConveniosCategoria", "field"=>"nombre", "valor"=>$v['ConveniosCategoria']['nombre'], "nombreEncabezado"=>"Categoria");
	$fila[] = array("model"=>"ConveniosCategoriasHistorico", "field"=>"desde", "valor"=>$v['ConveniosCategoriasHistorico']['desde']);
	$fila[] = array("model"=>"ConveniosCategoriasHistorico", "field"=>"hasta", "valor"=>$v['ConveniosCategoriasHistorico']['hasta']);
	$fila[] = array("model"=>"ConveniosCategoriasHistorico", "field"=>"costo", "valor"=>$v['ConveniosCategoriasHistorico']['costo'], "tipoDato"=>"moneda");
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>