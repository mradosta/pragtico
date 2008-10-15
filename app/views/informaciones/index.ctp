<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Informacion-nombre'] = array();
$condiciones['Condicion.Informacion-observacion'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"informaciones.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Informacion", "field"=>"id", "valor"=>$v['Informacion']['id'], "write"=>$v['Informacion']['write'], "delete"=>$v['Informacion']['delete']);
	$fila[] = array("model"=>"Informacion", "field"=>"nombre", "valor"=>$v['Informacion']['nombre']);
	$fila[] = array("model"=>"Informacion", "field"=>"observacion", "valor"=>$v['Informacion']['observacion']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>