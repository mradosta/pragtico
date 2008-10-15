<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['ConveniosCategoriasHistorico.id'] = array();
$campos['ConveniosCategoriasHistorico.convenios_categoria_id'] = array(
																"label"	=> "Categoria",
																"lov"=>array(
																	"controller"		=>	"convenios_categorias",
																	"seleccionMultiple"	=> 	0,
																	"camposRetorno"		=>	array(	"Convenio.nombre",
																									"ConveniosCategoria.nombre")));
$campos['ConveniosCategoriasHistorico.desde'] = array();
$campos['ConveniosCategoriasHistorico.hasta'] = array();
$campos['ConveniosCategoriasHistorico.costo'] = array("label"=>"Costo $");
$campos['ConveniosCategoriasHistorico.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Historico de Categoria", "imagen"=>"historicos.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>