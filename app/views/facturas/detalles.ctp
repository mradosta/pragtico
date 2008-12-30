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
foreach ($this->data['FacturasDetalle'] as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => "FacturasDetalle", 'field' => "id", 'valor' => $v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array('model' => "FacturasDetalle", 'field' => "valor", 'valor' => $v['valor']);
	$fila[] = array('model' => "Coeficiente", 'field' => "nombre", 'valor' => $v['Coeficiente']['nombre']);
	$fila[] = array('model' => "FacturasDetalle", 'field' => "subtotal", 'valor' => $v['subtotal'], "tipoDato"=>"moneda");
	$fila[] = array('model' => "FacturasDetalle", 'field' => "valor", "valor"=>$formato->format($v['valor'], array("tipo"=>"number")));
	$fila[] = array('model' => "FacturasDetalle", 'field' => "total", 'valor' => $v['total'], "tipoDato"=>"moneda");
	$cuerpo[] = $fila;
}

echo $this->element('desgloses/agregar', array('titulo' => "Detalle", 'cuerpo' => $cuerpo));

?>