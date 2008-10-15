<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Provincia-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"provincias.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"desglose", "id"=>$v['Provincia']['id'], "update"=>"desglose1", "imagen"=>array("nombre"=>"localidades.gif", "alt"=>"Localidades"), "url"=>'localidades');
	$fila[] = array("model"=>"Provincia", "field"=>"id", "valor"=>$v['Provincia']['id'], "write"=>$v['Provincia']['write'], "delete"=>$v['Provincia']['delete']);
	$fila[] = array("model"=>"Provincia", "field"=>"nombre", "valor"=>$v['Provincia']['nombre']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>