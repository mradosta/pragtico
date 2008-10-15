<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Aseguradora-codigo'] = array();
$condiciones['Condicion.Aseguradora-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"aseguradores.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Aseguradora", "field"=>"id", "valor"=>$v['Aseguradora']['id'], "write"=>$v['Aseguradora']['write'], "delete"=>$v['Aseguradora']['delete']);
	$fila[] = array("model"=>"Aseguradora", "field"=>"codigo", "valor"=>$v['Aseguradora']['codigo']);
	$fila[] = array("model"=>"Aseguradora", "field"=>"nombre", "valor"=>$v['Aseguradora']['nombre']);
	$fila[] = array("model"=>"Aseguradora", "field"=>"observacion", "valor"=>$v['Aseguradora']['observacion']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>