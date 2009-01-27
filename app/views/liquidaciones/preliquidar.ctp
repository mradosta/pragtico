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
$condiciones['Condicion.Relacion-empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																		"camposRetorno"	=>array("Empleador.cuit",
																								"Empleador.nombre")));

$condiciones['Condicion.Relacion-trabajador_id'] = array(	"lov"=>array("controller"	=>	"trabajadores",
																		"camposRetorno"	=>array("Trabajador.cuil",
																								"Trabajador.nombre",
																								"Trabajador.apellido")));

$condiciones['Condicion.Relacion-id'] = array(	"label" => "Relacion",
												"lov"=>array("controller"	=>	"relaciones",
																		"camposRetorno"	=>array("Empleador.cuit",
																								"Empleador.nombre",
																								"Trabajador.cuil",
																								"Trabajador.nombre",
																								"Trabajador.apellido")));

//$condiciones['Condicion.ConveniosCategoria-jornada'] = array();
//$condiciones['Condicion.Liquidacion-estado'] = array("value"=>"Sin Confirmar", "type"=>"hidden");

//$condiciones['Condicion.Liquidacion-mes'] = array("options"=>$meses);
//$condiciones['Condicion.Liquidacion-ano'] = array("class"=>"derecha");
//$condiciones['Condicion.Liquidacion-periodo'] = array("options"=>$periodos);
$condiciones['Condicion.Liquidacion-tipo'] = array("label"=>"Tipo", "type" => "select");
$condiciones['Condicion.Liquidacion-periodo'] = array("label"=>"Periodo", "type"=>"periodo");
$fieldsets[] = array('campos' => $condiciones);
$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('legend' => "Preliquidar",'imagen' => 'preliquidar.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	//d($v['Liquidacion']);
	$fila[] = array('tipo' => 'desglose', 'id' => $v['Liquidacion']['id'], 'update' => 'desglose1', 'imagen' => array('nombre' => 'liquidaciones.gif', 'alt' => "liquidaciones"), "url"=>"recibo_html");
	$fila[] = array('tipo' => 'desglose', 'id' => $v['Liquidacion']['id'], 'update' => 'desglose2', 'imagen' => array('nombre' => 'liquidaciones.gif', 'alt' => "liquidaciones (debug)"), "url"=>"recibo_html_debug");
	$fila[] = array('tipo' => 'desglose', 'id' => $v['Liquidacion']['id'], 'update' => 'desglose3', 'imagen' => array('nombre' => 'observaciones.gif', 'alt' => "Agregar Observacion"), 'url' => 'agregar_observacion');
	//$fila[] = array('tipo' => 'desglose', 'id' => $id, 'update' => 'desglose3', 'imagen' => array('nombre' => 'observaciones.gif', 'alt' => "Agregar Observacion"), "url"=>"add");
	$fila[] = array("tipo"=>"accion", "valor"=>$appForm->link($appForm->image('excel.gif', array('alt' => "Generar recibo excel", "title"=>"Generar recibo excel")), "recibo_excel/" . $v['Liquidacion']['id']));

	$fila[] = array('model' => 'Liquidacion', 'field' => 'id', 'valor' => $v['Liquidacion']['id'], 'write' => $v['Liquidacion']['write'], 'delete' => $v['Liquidacion']['delete']);
	$fila[] = array('model' => 'Liquidacion', 'field' => 'ano', 'valor' => $v['Liquidacion']['ano'] . str_pad($v['Liquidacion']['mes'], 2, "0" ,STR_PAD_LEFT) . $v['Liquidacion']['periodo'], "nombreEncabezado"=>"Periodo");
	$fila[] = array('model' => 'Trabajador', 'field' => 'apellido', 'valor' => $v['Relacion']['Trabajador']['cuil'] . " - " . $v['Relacion']['Trabajador']['nombre'] . " " . $v['Relacion']['Trabajador']['apellido'], "nombreEncabezado"=>"Trabajador");
	$fila[] = array('model' => 'Empleador', 'field' => 'nombre', 'valor' => $v['Relacion']['Empleador']['cuit'] . " - " . $v['Relacion']['Empleador']['nombre'], "nombreEncabezado"=>"Empleador");
	$fila[] = array('model' => 'Liquidacion', 'field' => 'remunerativo", "valor"=>$formato->format($v['Liquidacion']['remunerativo'], array("before"=>"$ ")));
	$fila[] = array('model' => 'Liquidacion', 'field' => 'deduccion", "valor"=>$formato->format($v['Liquidacion']['deduccion'], array("before"=>"$ ")));
	$fila[] = array('model' => 'Liquidacion', 'field' => 'no_remunerativo", "valor"=>$formato->format($v['Liquidacion']['no_remunerativo'], array("before"=>"$ ")));
	$fila[] = array('model' => 'Liquidacion', 'field' => 'total", "valor"=>$formato->format($v['Liquidacion']['total'], array("before"=>"$ ")));
	
	if($v['Liquidacion']['estado'] === "Confirmada") {
		$cuerpo[] = array("contenido"=>$fila, 'opciones' => array("title"=>"Ya se ha liquidado a esta Relacion para el periodo especificado.", "class"=>"fila_resaltada", "seleccionMultiple"=>false));
	}
	else {
		if(!empty($v['LiquidacionesError'])) {
			$fila[] = array('tipo' => 'desglose', 'id' => $v['Liquidacion']['id'], 'update' => 'desglose4', 'imagen' => array('nombre' => 'error_icono.gif', 'alt' => "Errores"), 'url' => 'errores');
			$cuerpo[] = array("contenido"=>$fila, 'opciones' => array("title"=>"Se han encontrado errores en esta liquidacion.", "class"=>"fila_resaltada", "seleccionMultiple"=>true));
		}
		else {
			$cuerpo[] = $fila;
		}
	}
}

$opcionesTabla =  array("tabla"=> array("ordenEnEncabezados"=> false,
										"modificar"			=> false,
										"seleccionMultiple"	=> true,
										"eliminar"			=> false,
										"permisos"			=> false));

$accionesExtra['opciones'] = array("acciones"=>array($appForm->link("Confirmar", null, array("class"=>"link_boton", "id"=>"confirmar", "title"=>"Confirma las liquidaciones seleccionadas"))));
$botonesExtra[] = $appForm->button("Limpiar", array("title"=>"Limpia las busquedas", "class"=>"limpiar", "onclick"=>"document.getElementById('accion').value='limpiar';form.submit();"));
$botonesExtra[] = $appForm->submit("Generar", array("title"=>"Genera una Pre-liquidacion", "onclick"=>"document.getElementById('accion').value='generar'"));
echo $this->element('index/index', array("botonesExtra"=>array('opciones' => array("botones"=>$botonesExtra)), "accionesExtra"=>$accionesExtra, "condiciones"=>$fieldset, 'cuerpo' => $cuerpo, "opcionesTabla"=>$opcionesTabla, "opcionesForm"=>array("action"=>"preliquidar")));
/**
* Agrego el evento click asociado al boton confirmar.
*/
$appForm->addScript('
	jQuery("#confirmar").click(
		function() {
			var c = jQuery(".tabla input[@type=\'checkbox\']").checkbox("contar");
			if (c>0) {
				jQuery("#form")[0].action = "' . router::url("/") . $this->params['controller'] . "/confirmar" . '";
				jQuery("#form")[0].submit();
			}
			else {
				alert("Debe seleccionar al menos una pre-liquidacion para confirmar.");
			}
		}
	);', 'ready');

?>