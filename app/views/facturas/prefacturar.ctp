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
	$condiciones['Condicion.Liquidacion-grupo_id'] = array('options'=>$grupos, 'empty'=>true);
}
$condiciones['Condicion.Relacion-empleador_id'] = array(
		'lov'	=> array('controller'	=> 'empleadores',
						'camposRetorno'	=> array('Empleador.cuit', 'Empleador.nombre')));

$condiciones['Condicion.Liquidacion-periodo'] = array('type' => 'periodo');
$condiciones['Condicion.Liquidacion-estado'] = array('type' => 'select', 'multiple' => 'checkbox', 'aclaracion' => 'Se refiere a que liquidaciones tomar como base para la prefacturacion.');

$fieldsets[] = array('campos' => $condiciones);
$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('imagen' => 'liquidaciones.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$id = $v['Factura']['id'];
	$fila[] = array('tipo' => 'desglose', 'id' => $id, 'update' => 'desglose1', 'imagen' => array('nombre' => 'detalles.gif', 'alt' => 'Detalles'), 'url' => 'detalles');
	$fila[] = array('model' => 'Factura', 'field' => 'id', 'valor'=>$id, 'write'=>$v['Factura']['write'], 'delete'=>$v['Factura']['delete']);
	$fila[] = array('model' => 'Empleador', 'field' => 'cuit', 'valor' => $v['Empleador']['cuit']);
	$fila[] = array('model' => 'Empleador', 'field' => 'nombre', 'valor' => $v['Empleador']['nombre'], 'nombreEncabezado' => 'Empleador');
	$fila[] = array('model' => 'Factura', 'field' => 'fecha', 'valor' => $v['Factura']['fecha']);
	$fila[] = array('model' => 'Factura', 'field' => 'estado', 'valor' => $v['Factura']['estado']);
	$fila[] = array('model' => 'Factura', 'field' => 'total', 'valor' => $v['Factura']['total'], 'tipoDato' => 'moneda');
	$cuerpo[] = $fila;
}

echo $this->element('index/index', array('condiciones' => $fieldset, 'cuerpo' => $cuerpo, 'opcionesForm'=>array('action' => 'prefacturar')));

?>