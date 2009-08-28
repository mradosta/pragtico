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
$condiciones['Soporte.empleador_id'] =  array(	'lov'	=>array('controller'		=> 'empleadores',
																'seleccionMultiple'	=> 0,
																'camposRetorno'		=> array(	'Empleador.cuit',
																								'Empleador.nombre')));
$condiciones['Soporte.cuenta_id'] = array('label' => 'Cuenta', 'type' => 'relacionado', 'relacion' => 'Soporte.empleador_id', 'url' => 'pagos/cuentas_relacionado');
$condiciones['Soporte.fecha_acreditacion'] = array('value'=>date('Y-m-d'), 'type' => 'date', 'label' => 'Acreditacion', 'aclaracion' => 'Fecha opcional de acreditacion.');

if (!empty($ids)) {
	$condiciones['Soporte.pago_id'] = array('type' => 'hidden', 'value' => $ids);
}


$fieldsets[] = array('campos' => $condiciones);
$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('legend' => 'Seleccione la cuenta','imagen' => 'bancos.gif')));

$accionesExtra['opciones'] = array('acciones'=>array());
$botonesExtra[] = $appForm->button('Cancelar', array('title' => 'Cancelar', 'class' => 'limpiar', 'onclick' => "document.getElementById('accion').value='cancelar';form.submit();"));
$botonesExtra[] = $appForm->submit('Generar', array('title' => 'Generar archivo de Soporte', 'onclick' => "document.getElementById('accion').value='generar'"));

echo $this->element('index/index', array('opcionesTabla'=>array('tabla'=>array('omitirMensajeVacio' => true)), 'botonesExtra'=>array('opciones' => array('botones' => $botonesExtra)), 'accionesExtra' => $accionesExtra, 'opcionesForm'=>array('action' => 'generar_soporte_magnetico'), 'condiciones' => $fieldset, 'cuerpo' => null));

?>