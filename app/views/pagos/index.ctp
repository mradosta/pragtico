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
$condiciones['Condicion.Relacion-trabajador_id'] = array('lov' =>
		array(	'controller'		=> 'trabajadores',
				'separadorRetorno'	=> ' ',
				'camposRetorno'		=> array('Trabajador.apellido', 'Trabajador.nombre')));

$condiciones['Condicion.Relacion-empleador_id'] = array('lov' =>
		array(	'controller'		=> 'empleadores',
				'camposRetorno'		=> array('Empleador.nombre')));

$condiciones['Condicion.Pago-relacion_id'] = array('lov '=>
		array('controller'			=> 'relaciones',
			  'camposRetorno'		=> array('Empleador.nombre', 'Trabajador.apellido')));

$condiciones['Condicion.Pago-fecha__desde'] = array('label' => 'Desde', 'type' => 'date');
$condiciones['Condicion.Pago-fecha__hasta'] = array('label' => 'Hasta', 'type' => 'date');
$condiciones['Condicion.Liquidacion-periodo'] = array('type' => 'periodo');
$condiciones['Condicion.Pago-estado'] = array();
$fieldsets[] = array('campos' => $condiciones);

$fieldset = $formulario->pintarFieldsets($fieldsets, array('fieldset'=>array('imagen' => 'pagos.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array('tipo' => 'desglose', 'id' => $v['Pago']['liquidacion_id'], 'update' => 'desglose1', 'imagen'=>array('nombre' => 'liquidaciones.gif', 'alt' => 'liquidacion'), 'url'=>'../liquidaciones/recibo_html');
	$fila[] = array('tipo' => 'desglose', 'id' => $v['Pago']['id'], 'update' => 'desglose2', 'imagen'=>array('nombre' => 'pagos_formas.gif', 'alt' => 'Formas de Pago'), 'url'=>'formas');
	if ($v['Pago']['estado'] === 'Pendiente' && $v['Pago']['moneda'] === 'Pesos') {
		$fila[] = array('tipo' => 'accion', 'valor' => 
				$formulario->link($formulario->image('cheques.gif'), 
						array(	'controller'			=> 'pagos_formas',
								'action'				=> 'add',
								'PagosForma.forma'		=> 'Cheque',
								'PagosForma.pago_id'	=> $v['Pago']['id']), 
						array(	'title' 				=> 'Pago con Cheque')));
	} elseif ($v['Pago']['estado'] === 'Imputado') {
		$fila[] = array('tipo' => 'accion', 'valor' =>
				$formulario->link($formulario->image('revertir_pago.gif'), 
						'revertir_pago/' . $v['Pago']['id'], 
	  					array('title' => 'Revertir Pago')));
	}
	$fila[] = array('model' => 'Pago', 'field' => 'id', 'valor' => $v['Pago']['id'], 'write' => $v['Pago']['write'], 'delete' => $v['Pago']['delete']);
	$fila[] = array('model' => 'Empleador', 'field' => 'nombre', 'valor' => $v['Relacion']['Empleador']['nombre'], 'nombreEncabezado' => 'Empleador');
	$fila[] = array('model' => 'Trabajador', 'field' => 'numero_documento', 'valor' => $v['Relacion']['Trabajador']['numero_documento'], 'class' => 'derecha', 'nombreEncabezado' => 'Documento');
	$fila[] = array('model' => 'Trabajador', 'field' => 'apellido', 'valor' => $v['Relacion']['Trabajador']['apellido'] . ' ' . $v['Relacion']['Trabajador']['nombre'], 'nombreEncabezado' => 'Trabajador');
	$fila[] = array('model' => 'Pago', 'field' => 'fecha', 'valor' => $v['Pago']['fecha']);
	$fila[] = array('model' => 'Pago', 'field' => 'moneda', 'valor' => $v['Pago']['moneda']);
	$fila[] = array('model' => 'Pago', 'field' => 'monto', 'valor' => $v['Pago']['monto'], 'tipoDato' => 'moneda');
	$fila[] = array('model' => 'Pago', 'field' => 'estado', 'valor' => $v['Pago']['estado']);
	if ($v['Pago']['estado'] === 'Imputado' || $v['Pago']['estado'] === 'Cancelado') {
		$cuerpo[] = array('contenido' 	=> $fila, 
						  'opciones' 	=> array('seleccionMultiple'=>false));
	} elseif (empty($v['Relacion']['Trabajador']['cbu'])) {
		$cuerpo[] = array('contenido' => $fila, 'opciones' => 
				array('title' => 'No se podra generar el archivo de soporte por no tener cuenta bancaria', 
					  'class' => 'fila_resaltada'));
	} else {
		$cuerpo[] = $fila;
	}
}

$acciones[] = $formulario->link('Soporte Mag.', null, 
			array(	'id' 		=> 'generar_soporte_magnetico', 
				  	'class' 	=> 'link_boton', 
	  				'title' 	=> 'Generar Soporte Magnetico'));
$acciones[] = $formulario->link('Efectivo', null, 
			array(	'id' 		=> 'pago_efectivo', 
					'class' 	=> 'link_boton', 
	 				'title' 	=> 'Realiza un pago masivo en Efectivo'));
$acciones[] = $formulario->link('Beneficios', null, 
			array(	'id' 		=> 'pago_beneficios', 
					'class' 	=> 'link_boton', 
	 				'title' 	=> 'Realiza un pago masivo con Beneficios'));
$acciones[] = $formulario->link('Deposito', null, 
			array(	'id' 		=> 'pago_deposito', 
					'class' 	=> 'link_boton', 
	 				'title' 	=> 'Realiza un pago masivo con Deposito en la Cuenta del Trabajador'));
$accionesExtra['opciones'] = array('acciones' => $acciones);
$botonesExtra = $formulario->button('Det. Cambio', 
			array(	'id' 		=> 'detalle_cambio', 
					'title' 	=> 'Imprime el Detalle de Cambio'));
echo $this->element('index/index', 
			array(	'botonesExtra' => array('opciones' => 
					array(	'botones' 		=> array('limpiar', 'buscar', $botonesExtra))), 
						  	'accionesExtra' => $accionesExtra, 
							'opcionesTabla' => array('tabla' => array('eliminar' => false, 'modificar' => false)), 
							'condiciones' 	=> $fieldset, 
	   						'cuerpo' 		=> $cuerpo));

/**
* Agrego el evento click asociado al detalle de cambio.
*/
$js = "
	function enviar(action, chequearCantidad) {
		if(chequearCantidad) {
			var c = jQuery('.tabla input[@type=\'checkbox\']').checkbox('contar');
			if (c == 0) {
				alert('Debe seleccionar al menos un pago a imputar.');
				return false;
			}
		}
		jQuery('#form')[0].action = '" . Router::url('/') . $this->params['controller'] . "' + action;
		jQuery('#form')[0].submit();
	}

	jQuery('#detalle_cambio').click(
		function() {
			enviar('/detalle_cambio', false);
		}
	);
	
	jQuery('#pago_beneficios').click(
		function() {
			enviar('/registrar_pago_masivo/beneficios', true);
		}
	);

	jQuery('#pago_deposito').click(
		function() {
			enviar('/registrar_pago_masivo/deposito', true);
		}
	);

	jQuery('#pago_efectivo').click(
		function() {
			enviar('/registrar_pago_masivo/efectivo', true);
		}
	);
	
	jQuery('#generar_soporte_magnetico').click(
		function() {
			enviar('/generar_soporte_magnetico', true);
		}
	);
";
$formulario->addScript($js);

?>