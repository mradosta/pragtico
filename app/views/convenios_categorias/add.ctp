<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.views
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
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

$fieldsets[] = 	array('campos' => $campos, "opciones"=>array('fieldset' => array("legend"=>"Datos de la Categoria", 'imagen' => 'categorias.gif')));

$campos = null;
$campos['ConveniosCategoriasHistorico.id'] = array();
$campos['ConveniosCategoriasHistorico.desde'] = array();
$campos['ConveniosCategoriasHistorico.hasta'] = array();
$campos['ConveniosCategoriasHistorico.costo'] = array("label"=>"Costo $");
$campos['ConveniosCategoriasHistorico.observacion'] = array();
$fieldsets[] = array('campos' => $campos, "opciones"=>array('fieldset' => array("class"=>"detail", "legend"=>"Historicos", 'imagen' => 'historicos.gif')));


$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Categoria")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->element('add/add', array('fieldset' => $fieldset, "migaEdit" => $this->data[0]['ConveniosCategoria']['nombre']));
$this->addScript($ajax->jsPredefinido(array("tipo"=>"detalle", "agregar"=>true, "quitar"=>true)));

?>