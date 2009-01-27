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
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Relacion-empleador_id'] = array(	"lov"=>array("controller"	=> "empleadores",
																		"camposRetorno"	=> array(	"Empleador.cuit",
																									"Empleador.nombre")));

$condiciones['Condicion.Relacion-trabajador_id'] = array(	"lov"=>array("controller"	=> "trabajadores",
																		"camposRetorno"	=> array(	"Trabajador.cuil",
																									"Trabajador.nombre",
																									"Trabajador.apellido")));

$condiciones['Condicion.Relacion-id'] = array(	"label" => "Relacion",
												"lov"=>array("controller"	=> "relaciones",
															"camposRetorno"	=> array(	"Empleador.cuit",
																						"Empleador.nombre",
																						"Trabajador.cuil",
																						"Trabajador.nombre",
																						"Trabajador.apellido")));
$condiciones['Condicion.Liquidacion-periodo'] = array("type"=>"periodo");
$fieldsets[] = array('campos' => $condiciones);
$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('legend' => "Liquidaciones",'imagen' => 'liquidaciones.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$id = $v['Liquidacion']['id'];
	$fila[] = array('tipo' => 'desglose', 'id' => $id, 'update' => 'desglose1', 'imagen' => array('nombre' => 'liquidaciones.gif', 'alt' => "liquidaciones"), 'url' => 'recibo_html');
	$fila[] = array('tipo' => 'desglose', 'id' => $id, 'update' => 'desglose2', 'imagen' => array('nombre' => 'liquidaciones.gif', 'alt' => "liquidaciones (debug)"), 'url' => 'recibo_html_debug');
	$fila[] = array('tipo' => 'desglose', 'id' => $id, 'update' => 'desglose3', 'imagen' => array('nombre' => 'pagos.gif', 'alt' => "Pagos"), 'url' => 'pagos');
	$fila[] = array("tipo"=>"accion", "valor"=>$appForm->link($appForm->image('acciones/excel.gif', array('alt' => "Generar recibo excel", "title"=>"Generar recibo excel")), "recibo_excel/" . $id));
	$fila[] = array("tipo"=>"accion", "valor"=>$appForm->link($appForm->image('acciones/pdf.gif', array('alt' => "Generar recibo pdf", "title"=>"Generar recibo pdf")), "recibo_pdf/" . $id));
	$fila[] = array('model' => 'Liquidacion', 'field' => 'id', 'valor' => $v['Liquidacion']['id'], 'write' => $v['Liquidacion']['write'], 'delete' => $v['Liquidacion']['delete']);
	$fila[] = array('model' => 'Liquidacion', 'field' => 'ano', 'valor' => $v['Liquidacion']['ano'] . str_pad($v['Liquidacion']['mes'], 2, "0" ,STR_PAD_LEFT) . $v['Liquidacion']['periodo'], "nombreEncabezado"=>"Periodo");
	$fila[] = array('model' => 'Empleador', 'field' => 'nombre', 'valor' => $v['Empleador']['nombre'], "nombreEncabezado"=>"Empleador");
	$fila[] = array('model' => 'Trabajador', 'field' => 'apellido', 'valor' => $v['Trabajador']['nombre'] . " " . $v['Trabajador']['apellido'], "nombreEncabezado"=>"Trabajador");
	$fila[] = array('model' => 'Liquidacion', 'field' => 'remunerativo", "valor"=>$formato->format($v['Liquidacion']['remunerativo'], array("before"=>"$ ")));
	$fila[] = array('model' => 'Liquidacion', 'field' => 'deduccion", "valor"=>$formato->format($v['Liquidacion']['deduccion'], array("before"=>"$ ")));
	$fila[] = array('model' => 'Liquidacion', 'field' => 'no_remunerativo", "valor"=>$formato->format($v['Liquidacion']['no_remunerativo'], array("before"=>"$ ")));
	$fila[] = array('model' => 'Liquidacion', 'field' => 'total", "valor"=>$formato->format($v['Liquidacion']['total'], array("before"=>"$ ")));
	$cuerpo[] = $fila;
}

echo $this->element('index/index', array('condiciones' => $fieldset, 'cuerpo' => $cuerpo));
?>