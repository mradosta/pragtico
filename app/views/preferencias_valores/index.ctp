<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Preferencia-nombre'] = array("label"=>"Preferencia");
$condiciones['Condicion.PreferenciasValor-valor'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"valores de la preferencia", "imagen"=>"preferencias.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"PreferenciasValor", "field"=>"id", "valor"=>$v['PreferenciasValor']['id'], "write"=>$v['PreferenciasValor']['write'], "delete"=>$v['PreferenciasValor']['delete']);
	$fila[] = array("model"=>"Preferencia", "field"=>"nombre", "valor"=>$v['Preferencia']['nombre']);
	$fila[] = array("model"=>"PreferenciasValor", "field"=>"valor", "valor"=>$v['PreferenciasValor']['valor']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>