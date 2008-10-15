<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Situacion-codigo'] = array();
$condiciones['Condicion.Situacion-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"situaciones.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Situacion", "field"=>"id", "valor"=>$v['Situacion']['id'], "write"=>$v['Situacion']['write'], "delete"=>$v['Situacion']['delete']);
	$fila[] = array("model"=>"Situacion", "field"=>"codigo", "valor"=>$v['Situacion']['codigo']);
	$fila[] = array("model"=>"Situacion", "field"=>"nombre", "valor"=>$v['Situacion']['nombre']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>