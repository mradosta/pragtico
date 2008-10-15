<?php

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