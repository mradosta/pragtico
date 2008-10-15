<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Condicion-codigo'] = array();
$condiciones['Condicion.Condicion-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"condiciones.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Condicion", "field"=>"id", "valor"=>$v['Condicion']['id'], "write"=>$v['Condicion']['write'], "delete"=>$v['Condicion']['delete']);
	$fila[] = array("model"=>"Condicion", "field"=>"codigo", "valor"=>$v['Condicion']['codigo']);
	$fila[] = array("model"=>"Condicion", "field"=>"nombre", "valor"=>$v['Condicion']['nombre']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));
?>