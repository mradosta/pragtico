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
foreach ($this->data['Pago'] as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"desglose", "id"=>$v['liquidacion_id'], "update"=>"desglose_1", "imagen"=>array("nombre"=>"liquidar.gif", "alt"=>"liquidacion"), "url"=>'../liquidaciones/preliquidacion');
	$fila[] = array("model"=>"Pago", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Pago", "field"=>"fecha", "valor"=>$v['fecha']);
	$fila[] = array("model"=>"Pago", "field"=>"pago", "valor"=>$v['pago']);
	$fila[] = array("model"=>"Pago", "field"=>"monto", "valor"=>$formato->format($v['monto'], array("before"=>"$ ", "places"=>2)));
	$fila[] = array("model"=>"Pago", "field"=>"estado", "valor"=>$v['estado']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("desgloses/agregar", array("titulo"=>"Pagos", "cuerpo"=>$cuerpo));

?>