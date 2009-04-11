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
$condiciones['Condicion.Relacion-empleador_id'] = array(
		'lov'	=> array('controller'	=> 'empleadores',
						'camposRetorno'	=> array('Empleador.cuit', 'Empleador.nombre')));

$condiciones['Condicion.Relacion-trabajador_id'] = array(
		'lov'	=> array('controller'	=> 'trabajadores',
					 	'camposRetorno'	=> array('Trabajador.cuil', 'Trabajador.nombre', 'Trabajador.apellido')));

$condiciones['Condicion.Relacion-id'] = array(
		'label' => 'Relacion',
		'lov'	=> array('controller'	=> 'relaciones',
						'camposRetorno'	=> array(	'Empleador.cuit',
													'Empleador.nombre',
			 										'Trabajador.cuil',
			  										'Trabajador.nombre',
			   										'Trabajador.apellido')));

$condiciones['Condicion.Liquidacion-tipo'] = array('label' => 'Tipo', 'type' => 'select');
//$condiciones['Condicion.Liquidacion-estado'] = array('options' => array('Guardada' => 'Guardada', 'Sin Confirmar' => 'Sin Confirmar'));
$condiciones['Condicion.Liquidacion-estado'] = array('options' => $states, 'type' => 'select', 'multiple' => 'checkbox');
$condiciones['Condicion.Liquidacion-periodo_largo'] = array('label' => 'Periodo Liquidacion', 'type' => 'periodo', 'periodo' => array('1Q', '2Q', 'M', '1S', '2S', 'A'));
$condiciones['Condicion.Liquidacion-periodo_vacacional'] = array('label' => 'Periodo Vacacional', 'type' => 'periodo', 'periodo' => array('A'), 'class' => 'periodo_vacacional');
$fieldsets[] = array('campos' => $condiciones);
$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('legend' => 'Preliquidar','imagen' => 'preliquidar.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
$contain = urlencode(serialize(array('LiquidacionesDetalle' => array('conditions' => array('LiquidacionesDetalle.concepto_imprimir' => array('Si', 'Solo con valor'))))));
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array('tipo' => 'desglose', 'id' => $v['Liquidacion']['id'], 'imagen' => array('nombre' => 'liquidaciones.gif', 'alt' => 'liquidaciones'), 'url' => 'recibo_html');
	$fila[] = array('tipo' => 'desglose', 'id' => $v['Liquidacion']['id'], 'imagen' => array('nombre' => 'liquidaciones.gif', 'alt' => 'liquidaciones (debug)'), 'url' => 'recibo_html_debug');
	$fila[] = array('tipo' => 'desglose', 'id' => $v['Liquidacion']['id'], 'imagen' => array('nombre' => 'prefacturar.gif', 'alt' => 'Facturas'), 'url' => 'facturas');
	$fila[] = array('tipo' => 'desglose', 'id' => $v['Liquidacion']['id'], 'imagen' => array('nombre' => 'observaciones.gif', 'alt' => 'Agregar Observacion'), 'url' => 'agregar_observacion');
	$fila[] = array('tipo'=>'accion', 'valor' => $appForm->link($appForm->image('excel.gif', array('alt' => 'Generar recibo excel', 'title'=>'Generar recibo excel')), array('action' => 'imprimir', 'id' => $v['Liquidacion']['id'])));
	//$fila[] = array('tipo'=>'accion', 'valor'=>$appForm->link($appForm->image('excel.gif', array('alt' => 'Generar recibo excel', 'title'=>'Generar recibo excel')), 'recibo_excel/' . $v['Liquidacion']['id']));
	//$fila[] = array('tipo'=>'accion', 'valor'=>$appForm->link($appForm->image('excel.gif', array('alt' => 'Generar recibo excel', 'title'=>'Generar recibo excel')), array('controller' => 'documentos', 'action' => 'generar', 'model' => 'Liquidacion', 'id' => $v['Liquidacion']['id'], 'contain' => $contain)));

	$fila[] = array('model' => 'Liquidacion', 'field' => 'id', 'valor' => $v['Liquidacion']['id'], 'write' => $v['Liquidacion']['write'], 'delete' => $v['Liquidacion']['delete']);
	$fila[] = array('model' => 'Liquidacion', 'field' => 'tipo', 'valor' => $v['Liquidacion']['tipo']);
	$fila[] = array('model' => 'Liquidacion', 'field' => 'estado', 'valor' => $v['Liquidacion']['estado']);
	$fila[] = array('model' => 'Liquidacion', 'field' => 'ano', 'valor' => $v['Liquidacion']['ano'] . str_pad($v['Liquidacion']['mes'], 2, '0' ,STR_PAD_LEFT) . $v['Liquidacion']['periodo'], 'nombreEncabezado'=>'Periodo');
	$fila[] = array('model' => 'Empleador', 'field' => 'nombre', 'valor' => $v['Relacion']['Empleador']['nombre'], 'nombreEncabezado'=>'Empleador');
	//$fila[] = array('model' => 'Trabajador', 'field' => 'apellido', 'valor' => $v['Relacion']['Trabajador']['numero_documento'], 'nombreEncabezado'=>'Documento');
	$fila[] = array('model' => 'Trabajador', 'field' => 'apellido', 'valor' => $v['Relacion']['Trabajador']['apellido'] . ' ' . $v['Relacion']['Trabajador']['nombre'], 'nombreEncabezado'=>'Trabajador');
	$fila[] = array('model' => 'Liquidacion', 'field' => 'remunerativo', 'valor'=>$v['Liquidacion']['remunerativo'], 'tipoDato' => 'moneda');
	$fila[] = array('model' => 'Liquidacion', 'field' => 'deduccion', 'valor'=>$v['Liquidacion']['deduccion'], 'tipoDato' => 'moneda');
	$fila[] = array('model' => 'Liquidacion', 'field' => 'no_remunerativo', 'valor'=>$v['Liquidacion']['no_remunerativo'], 'tipoDato' => 'moneda');
	$fila[] = array('model' => 'Liquidacion', 'field' => 'total', 'valor'=>$v['Liquidacion']['total'], 'tipoDato' => 'moneda');
	
	if($v['Liquidacion']['estado'] === 'Confirmada') {
		$cuerpo[] = array('contenido'=>$fila, 'opciones' => array('title'=>'Ya se ha liquidado a esta Relacion para el periodo especificado.', 'class'=>'fila_resaltada', 'seleccionMultiple'=>false));
	} else {
		if(!empty($v['LiquidacionesError'])) {
			$fila[] = array('tipo' => 'desglose', 'id' => $v['Liquidacion']['id'], 'update' => 'desglose4', 'imagen' => array('nombre' => 'error_icono.gif', 'alt' => 'Errores'), 'url' => 'errores');
			$cuerpo[] = array('contenido'=>$fila, 'opciones' => array('title'=>'Se han encontrado errores en esta liquidacion.', 'class'=>'fila_resaltada', 'seleccionMultiple'=>true));
		} else {
			$cuerpo[] = $fila;
		}
	}
}

$opcionesTabla =  array('tabla' => array(	'ordenEnEncabezados'=> false,
											'modificar'			=> false,
											'seleccionMultiple'	=> true,
											'eliminar'			=> true,
											'permisos'			=> false));

$accionesExtra['opciones'] = array('acciones' => array($appForm->link('Confirmar', null, array('class' => 'link_boton', 'id' => 'confirmar', 'title' => 'Confirma las preliquidaciones seleccionadas')), $appForm->link('Guardar', null, array('class' => 'link_boton', 'id' => 'guardar', 'title' => 'Guarda las preliquidaciones seleccionadas')), $appForm->link('Imprimir', null, array('class' => 'link_boton', 'id' => 'imprimir', 'title' => 'Imprime las preliquidaciones seleccionadas')), 'eliminar'));
$botonesExtra[] = 'limpiar';
$botonesExtra[] = 'buscar';
$botonesExtra[] = $appForm->submit('Generar', array('id' => 'generar', 'title'=>'Genera una Pre-liquidacion', 'onclick'=>'document.getElementById("accion").value="generar"'));
echo $this->element('index/index', array(
		'botonesExtra'	=> array('opciones' => array('botones'=>$botonesExtra)),
		'accionesExtra'	=> $accionesExtra,
  		'condiciones'	=> $fieldset,
		'cuerpo' 		=> $cuerpo,
  		'opcionesTabla'	=> $opcionesTabla,
		'opcionesForm' 	=> array('action'=>'preliquidar')));
/**
* Agrego el evento click asociado al boton confirmar.
*/
$appForm->addScript('

	/** Prevent from submit without entering a period */
	jQuery("#generar").click(
 		function() {
			if (jQuery("input.periodo").parent().is(":visible")) {
				if (jQuery("input.periodo").val() == "") {
					jQuery("div.error-message", jQuery("input.periodo").parent()).remove();
					var div = jQuery("<div/>").attr("class", "error-message").html("Debe ingresar un periodo valido");
					jQuery("input.periodo").parent().append(div);
					return false;
				}
			}
		}
	);

	/** Shows / Hides period options, depending receipt type */
	function period(type) {

		jQuery(".1q").hide();
		jQuery(".2q").hide();
		jQuery(".m").hide();
		jQuery(".1s").hide();
		jQuery(".2s").hide();
		jQuery(".a").hide();
		jQuery("input.periodo").parent().show();
		jQuery("input.periodo_vacacional").parent().hide();
		
		if (type === "normal") {
			jQuery(".1q").show();
			jQuery(".2q").show();
			jQuery(".m").show();
		} else if (type === "sac") {
			jQuery(".1s").show();
			jQuery(".2s").show();
		} else if (type === "vacaciones") {
			jQuery(".m").show();
			jQuery(".a", jQuery("input.periodo_vacacional").parent()).show();
			jQuery("input.periodo_vacacional").parent().show();
		} else if (type === "especial") {
			jQuery(".1q").show();
			jQuery(".2q").show();
			jQuery(".m").show();
			jQuery(".1s").show();
			jQuery(".2s").show();
			jQuery(".a").show();
		} else if (type === "liquidacion_final") {
			jQuery("input.periodo").parent().hide();
		}
	}
	period(jQuery("#CondicionLiquidacion-tipo").find(":selected").val());

	jQuery("#CondicionLiquidacion-tipo").change(
 		function() {
			jQuery("input.periodo").val("");
			period(jQuery(this).find(":selected").val());
		}
	);
	
	
	jQuery("#confirmar, #guardar, #imprimir").click(
		function() {
			var c = jQuery(".tabla :checkbox").checkbox("contar");
			if (c > 0) {
				if (jQuery(this).attr("id") == "confirmar") {
					jQuery("#form")[0].action = "' . Router::url(array('controller' => $this->params['controller'], 'action' => 'confirmar')) . '";
				} else if (jQuery(this).attr("id") == "guardar") {
					jQuery("#form")[0].action = "' . Router::url(array('controller' => $this->params['controller'], 'action' => 'guardar')) . '";
				} else {
					jQuery("#form")[0].action = "' . Router::url(array('controller' => $this->params['controller'], 'action' => 'imprimir')) . '";
				}
				jQuery("#form")[0].submit();
			} else {
				alert("Debe seleccionar al menos una pre-liquidacion para confirmar.");
			}
		}
	);', 'ready');

?>