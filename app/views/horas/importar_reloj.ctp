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
 * @version			$Revision: 1455 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2011-07-02 22:07:06 -0300 (Sat, 02 Jul 2011) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Hora.empleador_id'] = array(
		'lov'					=> array(
			'controller'        => 'empleadores',
			'camposRetorno'     => array('Empleador.nombre')
		)
);
$campos['Hora.periodo'] = array('type' => 'periodo', 'aclaracion' => 'Tomara este periodo para los datos ingresados desde el archivo.', 'verificarRequerido' => true);
$campos['Hora.liquidacion_tipo'] = array('type' => 'radio', 'label' => 'Tipo Liquidacion', 'default' => 'Normal');
$campos['Hora.tipo'] = array('type' => 'radio', 'default' => 'Normal');
//$campos['Hora.columnas'] = array('type' => 'memo', 'value' => "cuit =>\ndocumento =>\nlegajo => 1\nfecha/hora => 2\nentrada/salida => 6", 'aclaracion' => 'Indican el orden en el cual se importaran los datos');
$campos['Hora.columnas'] = array('type' => 'memo', 'value' => "legajo => 1\nfecha/hora => 2", 'aclaracion' => 'Indican el orden en el cual se importaran los datos');
$campos['Hora.archivo'] = array('type' => 'file');

$fieldset = $appForm->pintarFieldsets(array(array('campos' => $campos)), array('fieldset' => array('legend' => 'Importar horas desde archivo de reloj', 'imagen' => 'archivo.gif')));


$botonesExtra[] = $appForm->button('Cancelar', array('title' => 'Cancelar', 'class' => 'limpiar', 'onclick' => 'document.getElementById("accion").value="cancelar";form.submit();'));
$botonesExtra[] = $appForm->submit('Importar', array('title' => 'Importar archivo', 'onclick' => 'document.getElementById("accion").value="importar"'));

$opcionesTabla['tabla']['omitirMensajeVacio'] = true;

echo $this->element('index/index',
	array(
		'botonesExtra' 	=> array('opciones' => array('botones' => $botonesExtra)),
		'condiciones' 	=> $fieldset,
		'opcionesTabla' => $opcionesTabla,
		'opcionesForm' 	=> array('enctype' => 'multipart/form-data', 'action' => 'importar_reloj')
	)
);

if (!empty($data)) {

	App::import('Vendor', 'dates', 'pragmatia');

	foreach($data as $k => $v) {

		$time = 0;
		$fila = null;


		if (!empty($v['Relacion']['legajo'])) {
			$name = sprintf('%s - %s, %s', $v['Relacion']['legajo'], $v['Trabajador']['apellido'], $v['Trabajador']['nombre']);
		} else {
			$name = $k;
		}

		if (empty($v['error'])) {

			$fila[] = array('valor' => sprintf('%s - %s, %s', $v['Relacion']['legajo'], $v['Trabajador']['apellido'], $v['Trabajador']['nombre']));


			foreach ($v['records'] as $k => $vv) {

				if ($k > 0) {
					$cuerpo[] = $fila;
					$fila = null;
					$fila[] = array('valor' => '');

				}

				$fila[] = array('valor' => $vv['from'], 'opciones' => array('class' => 'align_right'));
				$fila[] = array('valor' => $vv['to'], 'opciones' => array('class' => 'align_right'));
				$fila[] = array('valor' => str_pad($vv['diff']['horas'], 2, '0', STR_PAD_LEFT) . ':' .  str_pad($vv['diff']['minutos'], 2, '0', STR_PAD_LEFT) . ':' . str_pad($vv['diff']['segundos'], 2, '0', STR_PAD_LEFT), 'opciones' => array('class' => 'align_right'));

					
				$time += ($vv['diff']['horas'] * 60 * 60) + ($vv['diff']['minutos'] * 60) + ($vv['diff']['segundos']);

			}

		} else {

			$fila[] = array('valor' => $name . ' => ' . $v['error']);
		}
		$cuerpo[] = $fila;


		if (empty($v['error'])) {
			$fila = null;
			$fila[] = array('valor' => 'Total:', 'opciones' => array('class' => 'bold'));
			$fila[] = array('valor' => '');
			$fila[] = array('valor' => '');
			$fila[] = array('valor' => Dates::secondsToHMS($time), 'opciones' => array('class' => 'align_right bold'));

			$value = $appForm->input('Hora.relacion_' . $v['Relacion']['id'], array('type' => 'hidden', 'value' => $time / 60 / 60));
			$fila[] = array('valor' => $appForm->input('Control.relation_selected_' . $v['Relacion']['id'], array('type' => 'checkbox', 'div' => false, 'checked' => true, 'label' => false, 'class' => 'float_right')) . $value);
			$cuerpo[] = $fila;
		}

	}

	$fila = null;
	$fila[] = array('type' => 'header', 'valor' => 'Legajo / Relacion');
	$fila[] = array('type' => 'header', 'valor' => 'Desde');
	$fila[] = array('type' => 'header', 'valor' => 'Hasta');
	$fila[] = array('type' => 'header', 'valor' => 'Tiempo');
	$fila[] = array('type' => 'header', 'valor' => 'Confirmado');

	$cuerpo[] = $fila;
	$datos['tabla']['simple'] = true;
	$datos['cuerpo'] = $cuerpo;

	$extra = $appForm->input('Form.liquidacion_tipo', array('type' => 'hidden', 'value' => $liquidacion_tipo));
	$extra .= $appForm->input('Form.periodo', array('type' => 'hidden', 'value' => $periodo));
	$extra .= $appForm->input('Form.tipo', array('type' => 'hidden', 'value' => $tipo));
	//$extra .= $appForm->input('RelacionesConcepto.relacion_id', array('type' => 'hidden', 'value' => $relacion['Relacion']['id']));


	$acciones = $appForm->tag('div', $this->element('add/acciones'), array('class' => 'botones_tablas_from_to'));
	$add = $appForm->tag('div', $appForm->form($appForm->tabla($datos) . $extra . $acciones, array('action' => 'save_reloj')), array('class' => 'unica'));
	echo $appForm->tag('div', $add, array('class' => 'add'));

}



?>