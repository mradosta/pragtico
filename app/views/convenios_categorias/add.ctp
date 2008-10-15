<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['ConveniosCategoria.id'] = array();
$campos['ConveniosCategoria.convenio_id'] = array("lov"=>array(	"controller"		=>	"convenios",
																"seleccionMultiple"	=> 	0,
																	"camposRetorno"	=>	array(	"Convenio.numero",
																								"Convenio.nombre")));
$campos['ConveniosCategoria.nombre'] = array();
$campos['ConveniosCategoria.jornada'] = array();
$campos['ConveniosCategoria.observacion'] = array();

$fieldsets[] = 	array("campos"=>$campos, "opciones"=>array("fieldset"=>array("legend"=>"Datos de la Categoria", "imagen"=>"categorias.gif")));

$campos = null;
$campos['ConveniosCategoriasHistorico.id'] = array();
$campos['ConveniosCategoriasHistorico.desde'] = array();
$campos['ConveniosCategoriasHistorico.hasta'] = array();
$campos['ConveniosCategoriasHistorico.costo'] = array("label"=>"Costo $");
$campos['ConveniosCategoriasHistorico.observacion'] = array();
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("fieldset"=>array("class"=>"detail", "legend"=>"Historicos", "imagen"=>"historicos.gif")));


$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Categoria")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
$this->addScript($ajax->jsPredefinido(array("tipo"=>"detalle", "agregar"=>true, "quitar"=>true)));

?>