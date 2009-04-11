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
if(!empty($grupos)) {																								
	$condiciones['Condicion.Liquidacion-grupo_id'] = array('options' => $grupos, 'empty' => true);
}
$condiciones['Condicion.Relacion-empleador_id'] = array(
		'lov'	=> array('controller'	=> 'empleadores',
						'camposRetorno'	=> array('Empleador.cuit', 'Empleador.nombre')));

$condiciones['Condicion.Liquidacion-periodo_completo'] = array('type' => 'periodo');
$condiciones['Condicion.Liquidacion-tipo'] = array('label' => 'Tipo', 'type' => 'select');
$condiciones['Condicion.Liquidacion-estado'] = array('aclaracion' => 'Se refiere a que liquidaciones tomar como base para la prefacturacion. Solo se podran confirmar prefacturaciones realizadas en base a liquidaciones Confirmadas');

$fieldsets[] = array('campos' => $condiciones);
$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('legend' => 'Prefacturar', 'imagen' => 'prefacturar.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k => $v) {
	$fila = null;
	$id = $v['Factura']['id'];
	$fila[] = array('tipo' => 'desglose', 'id' => $id, 'update' => 'desglose1', 'imagen' => array('nombre' => 'detalles.gif', 'alt' => 'Detalles'), 'url' => 'detalles');
	$fila[] = array('model' => 'Factura', 'field' => 'id', 'valor'=>$id, 'write'=>$v['Factura']['write'], 'delete'=>$v['Factura']['delete']);
	$fila[] = array('model' => 'Empleador', 'field' => 'cuit', 'valor' => $v['Empleador']['cuit'], 'class' => 'centro');
	$fila[] = array('model' => 'Empleador', 'field' => 'nombre', 'valor' => $v['Empleador']['nombre'], 'nombreEncabezado' => 'Empleador');
	$fila[] = array('model' => 'Factura', 'field' => 'fecha', 'valor' => $v['Factura']['fecha']);
	$fila[] = array('model' => 'Factura', 'field' => 'estado', 'valor' => $v['Factura']['estado']);
	$fila[] = array('model' => 'Factura', 'field' => 'total', 'valor' => $v['Factura']['total'], 'tipoDato' => 'moneda');
	
	if ($v['Factura']['confirmable'] === 'No') {
		$cuerpo[] = array('contenido' 	=> $fila,
						  'opciones' 	=> array(
			'title' 			=> 'No podra confirmar esta Factura por haber sido realizada desde Liquidaciones "Sin Confirmar"',
			'class' 			=> 'fila_resaltada',
			'seleccionMultiple'	=> false));
	} else {
		$cuerpo[] = $fila;
	}
}

$opcionesTabla =  array('tabla' => array(	'ordenEnEncabezados'=> false,
											'modificar'			=> false,
											'seleccionMultiple'	=> true,
											'eliminar'			=> false,
											'permisos'			=> false));
$accionesExtra['opciones'] = array('acciones' => array($appForm->link('Confirmar', null, array('class' => 'link_boton', 'id' => 'confirmar', 'title' => 'Confirma las preliquidaciones seleccionadas')), $appForm->link('Guardar', null, array('class' => 'link_boton', 'id' => 'guardar', 'title' => 'Guarda las preliquidaciones seleccionadas')), $appForm->link('Imprimir', null, array('class' => 'link_boton', 'id' => 'imprimir', 'title' => 'Imprime las preliquidaciones seleccionadas'))));
$botonesExtra[] = 'limpiar';
$botonesExtra[] = 'buscar';
$botonesExtra[] = $appForm->submit('Generar', array('title'=>'Genera una Pre-liquidacion', 'onclick'=>'document.getElementById("accion").value="generar"'));
echo $this->element('index/index', array(
		'botonesExtra' 	=> array('opciones' => array('botones'=>$botonesExtra)),
  		'accionesExtra' => $accionesExtra,
		'condiciones' 	=> $fieldset,
  		'cuerpo' 		=> $cuerpo,
		'opcionesTabla'	=> $opcionesTabla,
		'opcionesForm'	=> array('action' => 'prefacturar')));

?>