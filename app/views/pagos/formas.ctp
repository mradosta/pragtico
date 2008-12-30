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
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['PagosForma'] as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"accion", "valor"=>$formulario->link($formulario->image('revertir_pago.gif'), "../pagos_formas/revertir_pagos_forma/" . $v['id'], array("title"=>"Revertir Forma de Pago")));
	$fila[] = array('model' => "PagosForma", 'field' => "id", 'valor' => $v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array('model' => "PagosForma", 'field' => "forma", "class" => "izquierda", 'valor' => $v['forma']);
	$fila[] = array('model' => "PagosForma", 'field' => "fecha", 'valor' => $v['fecha']);
	$fila[] = array('model' => "PagosForma", 'field' => "monto", "valor" => $formato->format($v['monto']));
	$fila[] = array('model' => "PagosForma", 'field' => "fecha_pago", 'valor' => $v['fecha']);
	$fila[] = array('model' => "PagosForma", 'field' => "cheque_numero", 'valor' => $v['cheque_numero'], "class"=>"derecha", "nombreEncabezado"=>"Cheque");
	$cuerpo[] = $fila;
}


if($this->data['Pago']['estado'] == "Pendiente") {
	$url = array('controller' => "pagos_formas", 'action' => 'add', "PagosForma.pago_id"=>$this->data['Pago']['id']);
	echo $this->element('desgloses/agregar', array('cuerpo' => $cuerpo, 'url' => $url, 'titulo' => "Formas de Pago"));
}

?>