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
foreach ($this->data['DescuentosDetalle'] as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"desglose", "id"=>$v['liquidacion_id'], "update"=>"desglose_1", "imagen"=>array("nombre"=>"recibo_html.gif", "alt"=>"Liquidacion"), "url"=>'../liquidaciones/recibo_html');
	$fila[] = array("model"=>"DescuentosDetalle", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"DescuentosDetalle", "field"=>"fecha", "valor"=>$v['fecha']);
	$fila[] = array("model"=>"DescuentosDetalle", "field"=>"monto", "valor"=>"$ " . $v['monto']);
 	$fila[] = array("model"=>"DescuentosDetalle", "field"=>"observacion", "valor"=>$v['observacion']);
	$cuerpo[] = $fila;
}


/**
* Creo la tabla.
*/
$opcionesTabla =  array("tabla"=>
							array(	"eliminar"			=>true,
									"ordenEnEncabezados"=>false,
									"modificar"			=>true,
									"seleccionMultiple"	=>false,
									"mostrarEncabezados"=>true,
									"zebra"				=>false,
									"mostrarIds"		=>false));

$url = array("controller"=>"descuentos_detalles", "action"=>"add", "DescuentosDetalle.descuento_id"=>$this->data['Descuento']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Detalles", "cuerpo"=>$cuerpo));

?>